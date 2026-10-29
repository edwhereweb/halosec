<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Controllers\FormController;
use HaloSec\Controllers\PageController;
use HaloSec\Security\CsrfManager;
use HaloSec\Services\FileLeadStorage;
use HaloSec\Services\FormValidator;
use HaloSec\Services\LeadService;
use HaloSec\View;
use PHPUnit\Framework\TestCase;

/**
 * GDPR / DPDP guarantees the public site must keep (see docs/privacy-compliance.md).
 */
final class PrivacyComplianceTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $config;

    /** @var array<string, array<string, mixed>> */
    private array $services;

    private View $view;

    protected function setUp(): void
    {
        $this->config = require __DIR__ . '/../../config/app.php';
        $this->services = require __DIR__ . '/../../config/services.php';
        $this->view = new View(__DIR__ . '/../../views');
        $this->view->share([
            'config' => $this->config,
            'services' => $this->services,
            'audit' => require __DIR__ . '/../../config/audit.php',
            'guides' => require __DIR__ . '/../../config/guides.php',
            'csrfToken' => 'test-token',
            'honeypot' => LeadService::HONEYPOT,
        ]);
    }

    public function testEveryPublicFormShowsThePrivacyNotice(): void
    {
        $pages = [
            ['audit', '/free-audit', 'pages/audit', null],
            ['consultation', '/consultation', 'pages/consultation', null],
            ['emergency', '/under-attack', 'pages/emergency', null],
            ['contact', '/contact', 'pages/contact', null],
            ['newsletter', '/newsletter', 'pages/newsletter', null],
            ['enquiry', '/services/endpoint-security', 'pages/service', 'endpoint-security'],
        ];
        foreach ($pages as [$type, $path, $template, $slug]) {
            $html = $this->forms()->handle($type, $path, 'Form', $template, 'GET', [], $slug);
            $this->assertIsString($html);
            $this->assertStringContainsString('You can ask to see, correct or delete them', $html, $path . ' is missing the privacy notice');
        }
    }

    public function testPagesLoadNothingFromGoogleOrYoutubeOnPageView(): void
    {
        $pages = new PageController($this->view);
        foreach ([$pages->home(), $pages->about(), $pages->privacy()] as $html) {
            $this->assertStringNotContainsString('fonts.googleapis.com', $html);
            $this->assertStringNotContainsString('fonts.gstatic.com', $html);
            $this->assertDoesNotMatchRegularExpression('#<(iframe|script|img|link)[^>]+(youtube|ytimg|google)#i', $html);
        }
        foreach (['plus-jakarta-sans-latin', 'space-grotesk-latin'] as $font) {
            $this->assertFileExists(__DIR__ . '/../../public/fonts/' . $font . '.woff2');
        }
    }

    public function testFounderVideoIsClickToLoad(): void
    {
        $html = (new PageController($this->view))->home();

        $this->assertStringContainsString('id="founder-video"', $html);
        $this->assertStringNotContainsString('<iframe', $html);
    }

    public function testPrivacyPolicyCoversBothLaws(): void
    {
        $html = (new PageController($this->view))->privacy();

        foreach (['Digital Personal Data Protection Act', 'GDPR', 'Data Protection Board of India', 'Grievance officer', 'withdraw your consent', 'How long we keep it'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
    }

    public function testNoticeVersionIsADate(): void
    {
        $version = (string) $this->config['privacy']['notice_version'];

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $version);
        $this->assertGreaterThanOrEqual(30, $this->config['privacy']['retention_days']);
    }

    private function forms(): FormController
    {
        $session = [];
        $audit = require __DIR__ . '/../../config/audit.php';
        $validator = new FormValidator(
            $this->config['employee_scales'],
            $this->config['attack_types'],
            $this->config['consultation_topics'],
            array_keys($this->services),
            $audit['questions'],
            $audit['options'],
        );
        $leads = new LeadService($validator, new FileLeadStorage(sys_get_temp_dir() . '/halosec-privacy-test'));

        return new FormController($this->view, $leads, new CsrfManager($session), $this->services, $session);
    }
}
