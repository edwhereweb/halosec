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
            'title' => 'About HaloSec & Our Founders',
            'path' => '/about',
            'description' => 'Why HaloSec exists: honest, expert and accessible cybersecurity for growing businesses. '
                . 'Read our founders note, vision and values.',
        ]);
    }

    public function services(): string
    {
        return $this->view->render('pages/services', [
            'title' => 'Cybersecurity Services',
            'path' => '/services',
            'description' => 'Endpoint security, DLP, network security, penetration testing, SOC setup, vulnerability '
                . 'assessments, security audits and outsourced security teams from HaloSec.',
        ]);
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
