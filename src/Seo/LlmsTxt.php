<?php

declare(strict_types=1);

namespace HaloSec\Seo;

/**
 * Builds /llms.txt (llmstxt.org): a Markdown summary of the site with links to its key pages.
 * Also served for the homepage when a client sends "Accept: text/markdown".
 */
final class LlmsTxt
{
    /**
     * @param array<string, mixed>                $config   config/app.php
     * @param array<string, array<string, mixed>> $services config/services.php
     * @param array<string, array<string, mixed>> $guides   config/guides.php
     */
    public static function markdown(string $baseUrl, array $config, array $services, array $guides): string
    {
        $md = "# HaloSec\n\n"
            . "> Cybersecurity services for growing businesses in India: endpoint security, network security, "
            . "penetration testing (VAPT), SOC setup, vulnerability assessments, security audits and outsourced "
            . "security engineers. Sophos Authorized Partner and Zoho Partner, based in Kochi, Kerala, with a 24/7 "
            . "incident response hotline.\n\n"
            . '- Email: ' . (string) ($config['contact_email'] ?? '') . "\n"
            . '- 24/7 emergency hotline: ' . (string) ($config['emergency_phone'] ?? '') . "\n\n"
            . "## Services\n\n";
        foreach ($services as $slug => $s) {
            $md .= self::link((string) ($s['nav_label'] ?? $s['title'] ?? $slug), $baseUrl . '/services/' . $slug, (string) ($s['meta_description'] ?? ''));
        }

        $md .= "\n## Get help\n\n"
            . self::link('Free security audit', $baseUrl . '/free-audit', 'A short questionnaire with an instant score and a written report.')
            . self::link('Book a consultation', $baseUrl . '/consultation', 'Talk to a senior security architect.')
            . self::link('Under attack? Incident response', $baseUrl . '/under-attack', 'Report an active attack for 24/7 triage.')
            . self::link('Contact', $baseUrl . '/contact', 'General enquiries and partnerships.');

        if ($guides !== []) {
            $md .= "\n## Guides\n\n";
            foreach ($guides as $slug => $g) {
                $md .= self::link((string) $g['title'], $baseUrl . '/guides/' . $slug, (string) ($g['meta_description'] ?? ''));
            }
        }

        return $md . "\n## Optional\n\n"
            . self::link('About HaloSec', $baseUrl . '/about', 'Founders note, values and partner accreditations.')
            . self::link('All services', $baseUrl . '/services', '')
            . self::link('Sitemap', $baseUrl . '/sitemap.xml', '');
    }

    private static function link(string $title, string $url, string $note): string
    {
        return '- [' . $title . '](' . $url . ')' . ($note !== '' ? ': ' . $note : '') . "\n";
    }
}
