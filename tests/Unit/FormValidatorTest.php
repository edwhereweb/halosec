<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Models\Lead;
use HaloSec\Services\FormValidator;
use PHPUnit\Framework\TestCase;

final class FormValidatorTest extends TestCase
{
    private function validator(): FormValidator
    {
        $config = require __DIR__ . '/../../config/app.php';
        $audit = require __DIR__ . '/../../config/audit.php';

        return new FormValidator(
            $config['employee_scales'],
            $config['attack_types'],
            $config['consultation_topics'],
            ['soc-setup'],
            $audit['questions'],
            $audit['options'],
        );
    }

    /**
     * @return array<string, string>
     */
    private function answers(string $value): array
    {
        $out = [];
        foreach (['q_firewall', 'q_segmentation', 'q_endpoint', 'q_patching', 'q_mfa', 'q_backup', 'q_inventory', 'q_monitoring', 'q_training', 'q_incident'] as $id) {
            $out[$id] = $value;
        }

        return $out;
    }

    public function testEmployeeScaleOptionsAreExact(): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        $this->assertSame(['1 to 5', '5 to 15', '1 to 50', '50 to 100', '100 to 500', '500+'], $config['employee_scales']);
    }

    public function testContactRequiresFields(): void
    {
        $result = $this->validator()->validate(Lead::TYPE_CONTACT, ['email' => 'nope']);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('name', $result->errors);
        $this->assertArrayHasKey('email', $result->errors);
        $this->assertArrayHasKey('message', $result->errors);
    }

    public function testAuditValidSubmissionIsScored(): void
    {
        $result = $this->validator()->validate(Lead::TYPE_AUDIT, [
            'name' => 'Ann',
            'email' => 'ann@example.com',
            'company' => 'Acme',
            'employee_scale' => '500+',
            'answers' => $this->answers('yes'),
        ]);

        $this->assertTrue($result->isValid());
        $this->assertSame(100, $result->data['score']);
    }

    public function testAuditRejectsBadScaleAndMissingAnswers(): void
    {
        $result = $this->validator()->validate(Lead::TYPE_AUDIT, [
            'name' => 'Ann',
            'email' => 'ann@example.com',
            'company' => 'Acme',
            'employee_scale' => '2 to 3',
            'answers' => ['q_mfa' => 'maybe'],
        ]);

        $this->assertArrayHasKey('employee_scale', $result->errors);
        $this->assertArrayHasKey('answers', $result->errors);
    }

    public function testScoreMixesAnswers(): void
    {
        $v = $this->validator();
        $this->assertSame(0, $v->score($this->answers('no')));
        $this->assertSame(50, $v->score($this->answers('partial')));
    }

    public function testEmergencyRequiresPhone(): void
    {
        $result = $this->validator()->validate(Lead::TYPE_EMERGENCY, [
            'name' => 'Ann',
            'email' => 'ann@example.com',
            'attack_type' => 'Ransomware',
            'message' => 'Files encrypted',
        ]);

        $this->assertArrayHasKey('phone', $result->errors);
    }

    public function testEnquiryRejectsUnknownService(): void
    {
        $result = $this->validator()->validate(Lead::TYPE_ENQUIRY, [
            'service' => 'unknown',
            'name' => 'Ann',
            'email' => 'ann@example.com',
            'message' => 'Hello',
        ]);

        $this->assertArrayHasKey('service', $result->errors);
    }

    public function testCleanStripsControlCharsAndTruncates(): void
    {
        $this->assertSame('ab', FormValidator::clean("  a\x00b\n ", 100, false));
        $this->assertSame("a\nb", FormValidator::clean("a\r\nb", 100, true));
        $this->assertSame('abc', FormValidator::clean('abcdef', 3, false));
        $this->assertSame('', FormValidator::clean(['x'], 10, false));
    }

    public function testUnknownFormTypeFails(): void
    {
        $this->assertArrayHasKey('form', $this->validator()->validate('bogus', [])->errors);
    }
}
