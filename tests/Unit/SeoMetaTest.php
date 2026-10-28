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

final class SeoMetaTest extends TestCase
{
    /** @var array<string, array<string, mixed>> */
    private array $services;

    private View $view;

    protected function setUp(): void
    {
        putenv('APP_URL=https://halosec.in');
        $this->services = require __DIR__ . '/../../config/services.php';
        $this->view = new View(__DIR__ . '/../../views');
        $this->view->share([
            'config' => require __DIR__ . '/../../config/app.php',
            'services' => $this->services,
            'audit' => require __DIR__ . '/../../config/audit.php',
            'csrfToken' => 'test-token',
            'honeypot' => LeadService::HONEYPOT,
        ]);
    }

    protected function tearDown(): void
    {
        putenv('APP_URL');
    }

    public function testEveryServicePageIsItsOwnCanonicalUrl(): void
    {
        $descriptions = [];
        foreach (array_keys($this->services) as $slug) {
            $html = $this->servicePage($slug);

            $this->assertSame('https://halosec.in/services/' . $slug, $this->canonical($html), $slug);
            $this->assertStringContainsString('content="index, follow', $html, $slug);
            $descriptions[] = $this->description($html);
        }

        $this->assertSame($descriptions, array_unique($descriptions), 'Each service needs its own meta description.');
    }

    public function testServicePageStillHighlightsServicesInTheNav(): void
    {
        $html = $this->servicePage('soc-setup');

        $this->assertMatchesRegularExpression('#class="[^"]*bg-white text-slate-900 shadow-sm[^"]*" href="/services"#', $html);
    }

    public function testCorePagesHaveDistinctTitlesAndDescriptions(): void
    {
        $pages = new PageController($this->view);
        $home = $pages->home();
        $services = $pages->services();
        $about = $pages->about();

        $this->assertSame('https://halosec.in/', $this->canonical($home));
        $this->assertStringContainsString('<title>Cybersecurity Services, 24/7 SOC &amp; Penetration Testing | HaloSec</title>', $home);
        $this->assertCount(3, array_unique([$this->description($home), $this->description($services), $this->description($about)]));
        foreach ([$home, $services, $about] as $html) {
            $this->assertLessThanOrEqual(160, mb_strlen(html_entity_decode($this->description($html))));
        }
    }

    public function testErrorPagesAreNoindexWithoutCanonical(): void
    {
        $html = (new PageController($this->view))->notFound();

        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $html);
        $this->assertStringNotContainsString('rel="canonical"', $html);
    }

    public function testUtilityPagesCanBeNoindexed(): void
    {
        $html = $this->forms()->handle('security_report', '/security-report', 'Security report', 'pages/emergency', 'GET', [], null, '', true);

        $this->assertIsString($html);
        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $html);
    }

    public function testLayoutUsesTheBuiltStylesheetNotTheTailwindCdn(): void
    {
        $html = (new PageController($this->view))->home();

        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html);
        $this->assertMatchesRegularExpression('#<link rel="stylesheet" href="/css/app\.css\?v=\d+">#', $html);
        $this->assertFileExists(__DIR__ . '/../../public/css/app.css');
    }

    private function servicePage(string $slug): string
    {
        $html = $this->forms()->handle('enquiry', '/services/' . $slug, 'Service', 'pages/service', 'GET', [], $slug);
        $this->assertIsString($html);

        return $html;
    }

    private function forms(): FormController
    {
        $session = [];
        $config = require __DIR__ . '/../../config/app.php';
        $audit = require __DIR__ . '/../../config/audit.php';
        $validator = new FormValidator(
            $config['employee_scales'],
            $config['attack_types'],
            $config['consultation_topics'],
            array_keys($this->services),
            $audit['questions'],
            $audit['options'],
        );
        $leads = new LeadService($validator, new FileLeadStorage(sys_get_temp_dir() . '/halosec-seo-test'));

        return new FormController($this->view, $leads, new CsrfManager($session), $this->services, $session);
    }

    private function canonical(string $html): string
    {
        $this->assertSame(1, preg_match('#<link rel="canonical" href="([^"]+)">#', $html, $m));

        return $m[1] ?? '';
    }

    private function description(string $html): string
    {
        $this->assertSame(1, preg_match('#<meta name="description" content="([^"]+)">#', $html, $m));

        return $m[1] ?? '';
    }
}
