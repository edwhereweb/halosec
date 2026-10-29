<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Controllers\FormController;
use HaloSec\Controllers\PageController;
use HaloSec\Security\CsrfManager;
use HaloSec\Seo\Sitemap;
use HaloSec\Seo\StructuredData;
use HaloSec\Services\FileLeadStorage;
use HaloSec\Services\FormValidator;
use HaloSec\Services\LeadService;
use HaloSec\View;
use PHPUnit\Framework\TestCase;

final class GuidesTest extends TestCase
{
    private const BASE = 'https://halosec.in';
    private const SLUG = 'dpdp-act-compliance-checklist';

    /** @var array<string, array<string, mixed>> */
    private array $services;

    /** @var array<string, array<string, mixed>> */
    private array $guides;

    private View $view;

    protected function setUp(): void
    {
        putenv('APP_URL=https://halosec.in');
        $this->services = require __DIR__ . '/../../config/services.php';
        $this->guides = require __DIR__ . '/../../config/guides.php';
        $this->view = new View(__DIR__ . '/../../views');
        $this->view->share([
            'config' => require __DIR__ . '/../../config/app.php',
            'services' => $this->services,
            'audit' => require __DIR__ . '/../../config/audit.php',
            'guides' => $this->guides,
            'csrfToken' => 'test-token',
            'honeypot' => LeadService::HONEYPOT,
        ]);
    }

    protected function tearDown(): void
    {
        putenv('APP_URL');
    }

    public function testEveryGuideIsCompleteAndPointsAtRealServices(): void
    {
        $this->assertNotEmpty($this->guides);
        $descriptions = [];
        foreach ($this->guides as $slug => $guide) {
            $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $slug);
            $this->assertFileExists(__DIR__ . '/../../views/guides/' . str_replace('-', '_', $slug) . '.php', $slug);
            foreach (['title', 'meta_description', 'category', 'lead', 'published', 'updated', 'read_minutes', 'service'] as $key) {
                $this->assertNotEmpty($guide[$key] ?? null, $slug . ' needs ' . $key);
            }
            $this->assertLessThanOrEqual(160, mb_strlen((string) $guide['meta_description']), $slug);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', (string) $guide['published']);
            $this->assertGreaterThanOrEqual($guide['published'], $guide['updated'], $slug);
            $this->assertArrayHasKey($guide['service'], $this->services, $slug);
            foreach ((array) ($guide['related_services'] ?? []) as $related) {
                $this->assertArrayHasKey($related, $this->services, $slug);
            }
            foreach ((array) ($guide['faqs'] ?? []) as $faq) {
                $this->assertIsArray($faq);
                $this->assertCount(2, $faq);
            }
            $descriptions[] = $guide['meta_description'];
        }
        $this->assertSame($descriptions, array_unique($descriptions));
    }

    public function testGuidePageIsIndexableWithItsOwnCanonicalAndArticleMeta(): void
    {
        $html = $this->guidePage();
        $guide = $this->guides[self::SLUG];

        $this->assertStringContainsString('<link rel="canonical" href="https://halosec.in/guides/' . self::SLUG . '">', $html);
        $this->assertStringContainsString('content="index, follow', $html);
        $this->assertStringContainsString('<meta property="og:type" content="article">', $html);
        $this->assertStringContainsString('<title>' . View::e($guide['seo_title']) . ' | HaloSec</title>', $html);
        $this->assertStringContainsString('<h1 class="display', $html);
        $this->assertMatchesRegularExpression('#href="/guides" aria-current="page"#', $html);
    }

    public function testGuidePageLinksToItsServiceTheAuditAndEmergencyHelp(): void
    {
        $html = $this->guidePage();

        $this->assertStringContainsString('href="/services/' . $this->guides[self::SLUG]['service'] . '"', $html);
        $this->assertStringContainsString('href="/free-audit"', $html);
        $this->assertStringContainsString('href="/under-attack"', $html);
        foreach ($this->guides[self::SLUG]['faqs'] as [$question]) {
            $this->assertStringContainsString(View::e($question), $html);
        }
    }

    public function testGuidePageEmitsArticleFaqAndBreadcrumbJsonLd(): void
    {
        $this->assertSame(1, preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $this->guidePage(), $m));
        $data = json_decode($m[1] ?? '', true);
        $this->assertIsArray($data);
        $nodes = array_column($data['@graph'], null, '@type');

        $this->assertSame('2026-10-29', $nodes['Article']['datePublished']);
        $this->assertSame(['@id' => self::BASE . '/#organization'], $nodes['Article']['author']);
        $this->assertSame(['Home', 'Guides', $this->guides[self::SLUG]['title']], array_column($nodes['BreadcrumbList']['itemListElement'], 'name'));
        $this->assertCount(count($this->guides[self::SLUG]['faqs']), $nodes['FAQPage']['mainEntity']);
    }

    public function testUnknownGuideIsNotFound(): void
    {
        $this->assertNull((new PageController($this->view))->guide('no-such-guide'));
    }

    public function testGuidesIndexListsEveryGuide(): void
    {
        $html = (new PageController($this->view))->guides();

        $this->assertStringContainsString('<link rel="canonical" href="https://halosec.in/guides">', $html);
        foreach (array_keys($this->guides) as $slug) {
            $this->assertStringContainsString('href="/guides/' . $slug . '"', $html);
        }
        $this->assertStringContainsString('"CollectionPage"', $html);
    }

    public function testServicePageLinksToGuidesOnItsTopic(): void
    {
        $slug = $this->guides[self::SLUG]['service'];
        $html = $this->forms()->handle('enquiry', '/services/' . $slug, 'Service', 'pages/service', 'GET', [], $slug);

        $this->assertIsString($html);
        $this->assertStringContainsString('Guides on this topic.', $html);
        $this->assertStringContainsString('href="/guides/' . self::SLUG . '"', $html);
    }

    public function testServicePageWithoutGuidesShowsNoGuidesBlock(): void
    {
        $html = $this->forms()->handle('enquiry', '/services/security-testing', 'Service', 'pages/service', 'GET', [], 'security-testing');

        $this->assertIsString($html);
        $this->assertStringNotContainsString('Guides on this topic.', $html);
    }

    public function testSitemapListsGuidesIndexAndEveryGuide(): void
    {
        $xml = Sitemap::xml(self::BASE, dirname(__DIR__, 2), $this->services, false, $this->guides);

        $this->assertStringContainsString('<loc>' . self::BASE . '/guides</loc>', $xml);
        foreach (array_keys($this->guides) as $slug) {
            $this->assertStringContainsString('<loc>' . self::BASE . '/guides/' . $slug . '</loc>', $xml);
        }
        $this->assertStringNotContainsString('/guides/missing', Sitemap::xml(self::BASE, dirname(__DIR__, 2), $this->services, false, ['missing' => []]));
    }

    public function testNonGuidePagesHaveNoArticleNode(): void
    {
        $graph = StructuredData::build(self::BASE, '/about', 'About', 'Desc', ['name' => 'HaloSec'], $this->services);

        $this->assertNotContains('Article', array_column($graph['@graph'], '@type'));
    }

    private function guidePage(): string
    {
        $html = (new PageController($this->view))->guide(self::SLUG);
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
        $leads = new LeadService($validator, new FileLeadStorage(sys_get_temp_dir() . '/halosec-guides-test'));

        return new FormController($this->view, $leads, new CsrfManager($session), $this->services, $session);
    }
}
