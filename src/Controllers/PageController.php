<?php

declare(strict_types=1);

namespace HaloSec\Controllers;

use HaloSec\View;

final class PageController
{
    public function __construct(private View $view)
    {
    }

    public function home(): string
    {
        return $this->view->render('pages/home', [
            'title' => '',
            'path' => '/',
            'description' => 'HaloSec protects growing businesses with 24/7 SOC monitoring, endpoint and network security, '
                . 'penetration testing and security audits. Get a free audit today.',
        ]);
    }

    public function about(): string
    {
        return $this->view->render('pages/about', [
            'title' => 'About Us: Founders, Vision & Values',
            'path' => '/about',
            'description' => 'Why HaloSec exists: honest, expert and accessible cybersecurity for growing businesses. '
                . 'Read our founders’ note, vision, values and accredited partnerships.',
        ]);
    }

    public function services(): string
    {
        return $this->view->render('pages/services', [
            'title' => 'Cybersecurity Services for Growing Businesses',
            'path' => '/services',
            'description' => 'Endpoint security, DLP, network security, penetration testing, SOC setup, vulnerability '
                . 'assessments, security audits and outsourced security teams from HaloSec.',
        ]);
    }

    public function guides(): string
    {
        return $this->view->render('pages/guides', [
            'title' => 'Cybersecurity Guides for Growing Businesses',
            'path' => '/guides',
            'description' => 'Plain-language cybersecurity and compliance guides for Indian businesses with 5 to 500 people, '
                . 'starting with a step-by-step DPDP Act compliance checklist.',
        ]);
    }

    /**
     * Returns null for an unknown slug, which the router turns into a 404.
     */
    public function guide(string $slug): ?string
    {
        $guides = $this->guideList();
        $template = 'guides/' . str_replace('-', '_', $slug);
        if (!isset($guides[$slug]) || !is_file(dirname(__DIR__, 2) . '/views/' . $template . '.php')) {
            return null;
        }
        $guide = $guides[$slug];

        return $this->view->render('pages/guide', [
            'title' => (string) ($guide['seo_title'] ?? $guide['title']),
            'path' => '/guides/' . $slug,
            'description' => (string) $guide['meta_description'],
            'slug' => $slug,
            'guide' => $guide,
            'bodyTemplate' => $template,
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function guideList(): array
    {
        $guides = $this->view->shared('guides');

        return is_array($guides) ? $guides : [];
    }

    /**
     * Stays noindexed until config 'privacy_ready' is true (the page still has placeholders to fill in).
     */
    public function privacy(bool $ready = false): string
    {
        return $this->view->render('pages/privacy', [
            'title' => 'Privacy Policy',
            'path' => '/privacy',
            'description' => 'How HaloSec collects, uses, stores and protects the personal data you share through '
                . 'our website forms, and how to exercise your rights.',
            'noindex' => !$ready,
        ]);
    }

    public function notFound(): string
    {
        return $this->view->render('pages/404', ['title' => 'Page not found', 'path' => '']);
    }

    public function serverError(): string
    {
        return $this->view->render('pages/500', ['title' => 'Server Error', 'path' => '']);
    }
}
