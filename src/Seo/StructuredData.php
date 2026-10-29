<?php

declare(strict_types=1);

namespace HaloSec\Seo;

/**
 * Builds the schema.org JSON-LD graph for a public page.
 *
 * Every page gets Organization, WebSite, ProfessionalService and a WebPage node. Inner pages add a
 * BreadcrumbList; service pages add a Service node and, when the service has FAQs, an FAQPage.
 * Guide pages add an Article node and, when the guide has FAQs, an FAQPage.
 * Address, social profiles and area served only appear once they are set in the environment.
 */
final class StructuredData
{
    /**
     * @param array<string, mixed>                $config   config/app.php
     * @param array<string, array<string, mixed>> $services config/services.php
     * @param array<string, mixed>|null           $service  the current service, on /services/{slug}
     * @param array<string, mixed>|null           $guide    the current guide (config/guides.php), on /guides/{slug}
     *
     * @return array<string, mixed>
     */
    public static function build(
        string $baseUrl,
        string $path,
        string $pageName,
        string $description,
        array $config,
        array $services,
        ?array $service = null,
        ?array $guide = null,
    ): array {
        $orgId = $baseUrl . '/#organization';
        $siteId = $baseUrl . '/#website';
        $url = $baseUrl . ($path === '/' ? '/' : $path);
        $email = (string) ($config['contact_email'] ?? '');
        $phone = (string) ($config['emergency_phone'] ?? '');
        $address = self::address($config);
        $sameAs = array_values(array_filter((array) ($config['social_links'] ?? []), 'is_string'));
        $areaServed = (string) ($config['area_served'] ?? '');

        $organization = array_filter([
            '@type' => 'Organization',
            '@id' => $orgId,
            'name' => (string) ($config['name'] ?? 'HaloSec'),
            'url' => $baseUrl . '/',
            'logo' => $baseUrl . '/images/halosec-logo.png',
            'slogan' => (string) ($config['tagline'] ?? ''),
            'description' => 'Cybersecurity services for growing businesses: 24/7 SOC monitoring, endpoint and network security, penetration testing and security audits.',
            'email' => $email,
            'address' => $address,
            'sameAs' => $sameAs,
            'contactPoint' => $phone === '' ? null : [
                '@type' => 'ContactPoint',
                'telephone' => $phone,
                'contactType' => 'emergency',
                'availableLanguage' => 'English',
                'hoursAvailable' => [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    'opens' => '00:00',
                    'closes' => '23:59',
                ],
            ],
        ], [self::class, 'present']);

        $catalog = [];
        foreach ($services as $slug => $item) {
            $catalog[] = [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => (string) ($item['nav_label'] ?? $item['title'] ?? ''),
                    'url' => $baseUrl . '/services/' . $slug,
                ],
            ];
        }

        $business = array_filter([
            '@type' => 'ProfessionalService',
            '@id' => $baseUrl . '/#business',
            'name' => (string) ($config['name'] ?? 'HaloSec'),
            'url' => $baseUrl . '/',
            'image' => $baseUrl . '/images/og-default.png',
            'telephone' => $phone,
            'email' => $email,
            'address' => $address,
            'areaServed' => $areaServed,
            'parentOrganization' => ['@id' => $orgId],
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Cybersecurity services',
                'itemListElement' => $catalog,
            ],
        ], [self::class, 'present']);

        $graph = [
            $organization,
            [
                '@type' => 'WebSite',
                '@id' => $siteId,
                'url' => $baseUrl . '/',
                'name' => (string) ($config['name'] ?? 'HaloSec'),
                'publisher' => ['@id' => $orgId],
            ],
            $business,
        ];

        $page = array_filter([
            '@type' => match ($path) {
                '/about' => 'AboutPage',
                '/contact' => 'ContactPage',
                '/services', '/guides' => 'CollectionPage',
                default => 'WebPage',
            },
            '@id' => $url . '#webpage',
            'url' => $url,
            'name' => $pageName,
            'description' => $description,
            'isPartOf' => ['@id' => $siteId],
            'about' => $path === '/' || $path === '/about' ? ['@id' => $orgId] : null,
            'breadcrumb' => $path === '/' ? null : ['@id' => $url . '#breadcrumb'],
        ], [self::class, 'present']);
        $graph[] = $page;

        if ($path !== '/') {
            $graph[] = self::breadcrumbs($baseUrl, $path, $url, $pageName, $service, $guide);
        }

        if ($guide !== null) {
            $published = (string) ($guide['published'] ?? '');
            $mainService = (string) ($guide['service'] ?? '');
            $author = (array) ($guide['author'] ?? []);
            $graph[] = array_filter([
                '@type' => 'Article',
                '@id' => $url . '#article',
                'headline' => (string) ($guide['title'] ?? $pageName),
                'description' => $description,
                'url' => $url,
                'datePublished' => $published,
                'dateModified' => (string) ($guide['updated'] ?? $published),
                'articleSection' => (string) ($guide['category'] ?? ''),
                'inLanguage' => 'en-IN',
                'image' => $baseUrl . '/images/og-default.png',
                'author' => isset($author['name'])
                    ? array_filter(['@type' => 'Person', 'name' => (string) $author['name'], 'description' => (string) ($author['bio'] ?? ''), 'worksFor' => ['@id' => $orgId]], [self::class, 'present'])
                    : ['@id' => $orgId],
                'publisher' => ['@id' => $orgId],
                'isPartOf' => ['@id' => $siteId],
                'mainEntityOfPage' => ['@id' => $url . '#webpage'],
                'about' => isset($services[$mainService]) ? ['@id' => $baseUrl . '/services/' . $mainService . '#service'] : null,
            ], [self::class, 'present']);

            $faqPage = self::faqPage($url, (array) ($guide['faqs'] ?? []));
            if ($faqPage !== null) {
                $graph[] = $faqPage;
            }
        }

        if ($service !== null) {
            $graph[] = array_filter([
                '@type' => 'Service',
                '@id' => $url . '#service',
                'name' => (string) ($service['nav_label'] ?? $service['title'] ?? $pageName),
                'serviceType' => (string) ($service['nav_label'] ?? $service['title'] ?? $pageName),
                'description' => (string) ($service['meta_description'] ?? $service['short'] ?? $description),
                'url' => $url,
                'provider' => ['@id' => $orgId],
                'areaServed' => $areaServed,
                'mainEntityOfPage' => ['@id' => $url . '#webpage'],
            ], [self::class, 'present']);

            $faqPage = self::faqPage($url, (array) ($service['faqs'] ?? []));
            if ($faqPage !== null) {
                $graph[] = $faqPage;
            }
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    /**
     * Encodes the graph for a <script type="application/ld+json"> block. JSON_HEX_TAG stops any
     * "</script>" inside a value from closing the tag early.
     *
     * @param array<string, mixed> $graph
     */
    public static function toJson(array $graph): string
    {
        return (string) json_encode(
            $graph,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT,
        );
    }

    /**
     * Builds an FAQPage node from [question, answer] pairs, or null when there are none.
     *
     * @param array<mixed> $pairs
     *
     * @return array<string, mixed>|null
     */
    private static function faqPage(string $url, array $pairs): ?array
    {
        $faqs = [];
        foreach ($pairs as $faq) {
            if (is_array($faq) && isset($faq[0], $faq[1])) {
                $faqs[] = [
                    '@type' => 'Question',
                    'name' => (string) $faq[0],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => (string) $faq[1]],
                ];
            }
        }

        return $faqs === [] ? null : ['@type' => 'FAQPage', '@id' => $url . '#faq', 'url' => $url, 'mainEntity' => $faqs];
    }

    /**
     * @param array<string, mixed>|null $service
     * @param array<string, mixed>|null $guide
     *
     * @return array<string, mixed>
     */
    private static function breadcrumbs(string $baseUrl, string $path, string $url, string $pageName, ?array $service, ?array $guide = null): array
    {
        $trail = [['Home', $baseUrl . '/']];
        if ($service !== null) {
            $trail[] = ['Services', $baseUrl . '/services'];
            $trail[] = [(string) ($service['nav_label'] ?? $service['title'] ?? $pageName), $url];
        } elseif ($guide !== null) {
            $trail[] = ['Guides', $baseUrl . '/guides'];
            $trail[] = [(string) ($guide['title'] ?? $pageName), $url];
        } else {
            $trail[] = [$pageName, $url];
        }

        $items = [];
        foreach ($trail as $i => [$name, $itemUrl]) {
            $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => $itemUrl];
        }

        return ['@type' => 'BreadcrumbList', '@id' => $url . '#breadcrumb', 'itemListElement' => $items];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, string>|null
     */
    private static function address(array $config): ?array
    {
        $address = (array) ($config['address'] ?? []);
        $fields = array_filter([
            'streetAddress' => (string) ($address['street'] ?? ''),
            'addressLocality' => (string) ($address['locality'] ?? ''),
            'addressRegion' => (string) ($address['region'] ?? ''),
            'postalCode' => (string) ($address['postal_code'] ?? ''),
            'addressCountry' => (string) ($address['country'] ?? ''),
        ], static fn (string $v): bool => $v !== '');

        if (!isset($fields['addressLocality'])) {
            return null;
        }

        return ['@type' => 'PostalAddress'] + $fields;
    }

    private static function present(mixed $value): bool
    {
        return $value !== null && $value !== '' && $value !== [];
    }
}
