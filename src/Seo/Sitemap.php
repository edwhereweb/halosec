<?php

declare(strict_types=1);

namespace HaloSec\Seo;

/**
 * Builds sitemap.xml from the public pages and config/services.php, so new services appear automatically.
 * <lastmod> is the newest modification time of the files that make up each page (Hostinger's git pull
 * updates the times of changed files), which is the only sitemap hint Google uses.
 */
final class Sitemap
{
    /**
     * @param array<string, array<string, mixed>> $services
     */
    public static function xml(string $baseUrl, string $root, array $services, bool $includePrivacy = false): string
    {
        $layout = [$root . '/views/layout.php'];
        $pages = [
            '/' => ['views/pages/home.php'],
            '/services' => ['views/pages/services.php', 'config/services.php'],
        ];
        foreach (array_keys($services) as $slug) {
            $custom = 'views/pages/services/' . str_replace('-', '_', $slug) . '.php';
            $pages['/services/' . $slug] = [is_file($root . '/' . $custom) ? $custom : 'views/pages/service.php', 'config/services.php'];
        }
        $pages += [
            '/free-audit' => ['views/pages/audit.php', 'config/audit.php'],
            '/consultation' => ['views/pages/consultation.php'],
            '/under-attack' => ['views/pages/emergency.php'],
            '/about' => ['views/pages/about.php'],
            '/contact' => ['views/pages/contact.php'],
        ];
        if ($includePrivacy) {
            $pages['/privacy'] = ['views/pages/privacy.php'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($pages as $path => $files) {
            $times = [];
            foreach (array_merge($layout, array_map(static fn (string $f): string => $root . '/' . $f, $files)) as $file) {
                $time = is_file($file) ? filemtime($file) : false;
                if ($time !== false) {
                    $times[] = $time;
                }
            }
            $xml .= '  <url><loc>' . htmlspecialchars($baseUrl . $path, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
            if ($times !== []) {
                $xml .= '<lastmod>' . gmdate('Y-m-d', max($times)) . '</lastmod>';
            }
            $xml .= "</url>\n";
        }

        return $xml . "</urlset>\n";
    }
}
