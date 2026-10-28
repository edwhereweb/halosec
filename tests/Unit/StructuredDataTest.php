<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Seo\Sitemap;
use HaloSec\Seo\StructuredData;
use PHPUnit\Framework\TestCase;

final class StructuredDataTest extends TestCase
{
    private const BASE = 'https://halosec.in';

    /** @var array<string, array<string, mixed>> */
    private array $services;

    /** @var array<string, mixed> */
    private array $config;

    protected function setUp(): void
    {
        $this->services = require __DIR__ . '/../../config/services.php';
        $this->config = ['name' => 'HaloSec', 'contact_email' => 'hello@halosec.in', 'emergency_phone' => '+917356543520'];
    }

    public function testHomeHasCoreNodesAndNoBreadcrumb(): void
    {
        $types = $this->types(StructuredData::build(self::BASE, '/', 'Home', 'Desc', $this->config, $this->services));

        $this->assertSame(['Organization', 'WebSite', 'ProfessionalService', 'WebPage'], $types);
    }

    public function testServicePageHasBreadcrumbServiceAndFaq(): void
    {
        $service = $this->services['endpoint-security'];
        $graph = StructuredData::build(self::BASE, '/services/endpoint-security', 'Endpoint', 'Desc', $this->config, $this->services, $service);
        $nodes = $this->byType($graph);

        $this->assertSame(
            ['Home', 'Services', 'Endpoint Security'],
            array_column($nodes['BreadcrumbList']['itemListElement'], 'name'),
        );
        $this->assertSame(self::BASE . '/services/endpoint-security', $nodes['Service']['url']);
        $this->assertSame(['@id' => self::BASE . '/#organization'], $nodes['Service']['provider']);
        $this->assertCount(count($service['faqs']), $nodes['FAQPage']['mainEntity']);
    }

    public function testServiceWithoutFaqsHasNoFaqPage(): void
    {
        $graph = StructuredData::build(self::BASE, '/services/soc-setup', 'SOC', 'Desc', $this->config, $this->services, $this->services['soc-setup']);

        $this->assertNotContains('FAQPage', $this->types($graph));
    }

    public function testAboutAndContactUseSpecificPageTypes(): void
    {
        $about = $this->byType(StructuredData::build(self::BASE, '/about', 'About', 'Desc', $this->config, $this->services));
        $contact = $this->byType(StructuredData::build(self::BASE, '/contact', 'Contact', 'Desc', $this->config, $this->services));

        $this->assertArrayHasKey('AboutPage', $about);
        $this->assertArrayHasKey('ContactPage', $contact);
    }

    public function testAddressAndSocialLinksOnlyAppearWhenConfigured(): void
    {
        $bare = $this->byType(StructuredData::build(self::BASE, '/', 'Home', 'Desc', $this->config, $this->services));
        $this->assertArrayNotHasKey('address', $bare['Organization']);
        $this->assertArrayNotHasKey('sameAs', $bare['Organization']);

        $config = $this->config + [
            'address' => ['locality' => 'Kochi', 'country' => 'IN', 'street' => ''],
            'social_links' => ['https://www.linkedin.com/company/example'],
        ];
        $full = $this->byType(StructuredData::build(self::BASE, '/', 'Home', 'Desc', $config, $this->services));
        $this->assertSame(['@type' => 'PostalAddress', 'addressLocality' => 'Kochi', 'addressCountry' => 'IN'], $full['Organization']['address']);
        $this->assertSame(['https://www.linkedin.com/company/example'], $full['Organization']['sameAs']);
    }

    public function testJsonCannotBreakOutOfTheScriptTag(): void
    {
        $graph = StructuredData::build(self::BASE, '/x', '</script><script>alert(1)</script>', 'Desc', $this->config, $this->services);
        $json = StructuredData::toJson($graph);

        $this->assertStringNotContainsString('</script>', $json);
        $this->assertIsArray(json_decode($json, true));
    }

    public function testSitemapListsEveryServiceWithLastmodAndHidesUnreadyPrivacyPage(): void
    {
        $root = dirname(__DIR__, 2);
        $xml = Sitemap::xml(self::BASE, $root, $this->services);
        $doc = simplexml_load_string($xml);
        $this->assertNotFalse($doc);

        $locs = [];
        foreach ($doc->url as $url) {
            $locs[] = (string) $url->loc;
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', (string) $url->lastmod);
        }
        foreach (array_keys($this->services) as $slug) {
            $this->assertContains(self::BASE . '/services/' . $slug, $locs);
        }
        $this->assertContains(self::BASE . '/', $locs);
        $this->assertNotContains(self::BASE . '/privacy', $locs);
        $this->assertStringContainsString('/privacy</loc>', Sitemap::xml(self::BASE, $root, $this->services, true));
    }

    /**
     * @param array<string, mixed> $graph
     *
     * @return list<string>
     */
    private function types(array $graph): array
    {
        return array_column($graph['@graph'], '@type');
    }

    /**
     * @param array<string, mixed> $graph
     *
     * @return array<string, array<string, mixed>>
     */
    private function byType(array $graph): array
    {
        return array_column($graph['@graph'], null, '@type');
    }
}
