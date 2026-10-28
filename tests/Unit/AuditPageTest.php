<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\View;
use PHPUnit\Framework\TestCase;

final class AuditPageTest extends TestCase
{
    /**
     * @param array<string, mixed> $old
     * @param array<string, string> $errors
     * @param array{type: string, message: string}|null $flash
     */
    private function render(array $old = [], array $errors = [], ?array $flash = null): string
    {
        $view = new View(__DIR__ . '/../../views');
        // The site shares these with every template.
        $view->share(['csrfToken' => 'token', 'honeypot' => 'website_url']);

        return $view->include('pages/audit', [
            'audit' => require __DIR__ . '/../../config/audit.php',
            'config' => require __DIR__ . '/../../config/app.php',
            'old' => $old,
            'errors' => $errors,
            'flash' => $flash,
            'formAction' => '/free-audit',
        ]);
    }

    public function testEveryQuestionHasPlainWordingAnIllustrationAndFourAnswers(): void
    {
        $audit = require __DIR__ . '/../../config/audit.php';
        $html = $this->render();

        foreach (array_keys($audit['questions']) as $qid) {
            $this->assertArrayHasKey($qid, $audit['details'], (string) $qid . ' needs visitor-facing wording');
            $this->assertStringContainsString('data-q="' . $qid . '"', $html);
            $this->assertSame(4, substr_count($html, 'name="answers[' . $qid . ']"'), (string) $qid);
            $this->assertStringContainsString(htmlspecialchars($audit['details'][$qid]['ask'], ENT_QUOTES, 'UTF-8'), $html);
        }
        $this->assertSame(count($audit['questions']) + 1, substr_count($html, 'viewBox="0 0 200 150"'), 'One illustration per question plus the hero');
        $this->assertStringContainsString('data-start="q:0"', $html);
        $this->assertStringContainsString('/js/audit.js', $html);
    }

    public function testAfterAFailedSubmitWithAllAnswersTheFormOpensDirectly(): void
    {
        $audit = require __DIR__ . '/../../config/audit.php';
        $answers = array_fill_keys(array_keys($audit['questions']), 'yes');
        $html = $this->render(['answers' => $answers, 'name' => 'Asha'], ['email' => 'A valid email address is required.']);

        $this->assertStringContainsString('data-start="details"', $html);
        $this->assertSame(count($answers), substr_count($html, 'value="yes" checked'));
    }

    public function testAfterAFailedSubmitWithMissingAnswersItReturnsToTheFirstGap(): void
    {
        $html = $this->render(['answers' => ['q_firewall' => 'yes', 'q_segmentation' => 'no']], ['answers' => 'Please answer every question.']);

        $this->assertStringContainsString('data-start="q:2"', $html);
        $this->assertStringContainsString('Please answer every question.', $html);
    }
}
