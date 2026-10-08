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
        return $this->view->render('pages/home', ['title' => 'We are Securious', 'path' => '/']);
    }

    public function about(): string
    {
        return $this->view->render('pages/about', ['title' => 'About & Founders Note', 'path' => '/about']);
    }

    public function services(): string
    {
        return $this->view->render('pages/services', ['title' => 'Services', 'path' => '/services']);
    }

    public function notFound(): string
    {
        return $this->view->render('pages/404', ['title' => 'Page not found', 'path' => '']);
    }
}
