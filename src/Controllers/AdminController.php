<?php

declare(strict_types=1);

namespace HaloSec\Controllers;

use HaloSec\Models\Lead;
use HaloSec\Security\AdminAuth;
use HaloSec\Security\CsrfManager;
use HaloSec\Security\LoginThrottle;
use HaloSec\Services\LeadReaderInterface;
use HaloSec\View;

/**
 * Admin login/logout, dashboard and read-only lead browsing. Every action except login re-checks auth.
 */
final class AdminController
{
    public const PER_PAGE = 15;

    public function __construct(
        private View $view,
        private AdminAuth $auth,
        private CsrfManager $csrf,
        private LoginThrottle $throttle,
        private LeadReaderInterface $leads,
        private string $clientId,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     */
    public function login(string $method, array $post): string
    {
        $this->noCache();
        if ($this->auth->check()) {
            return $this->redirect('/admin');
        }

        $old = [];
        $flash = null;
        if ($method === 'POST') {
            $username = is_string($post['username'] ?? null) ? trim($post['username']) : '';
            $password = is_string($post['password'] ?? null) ? $post['password'] : '';
            $old = ['username' => mb_substr($username, 0, 254)];

            if (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
                http_response_code(419);
                $flash = 'Your session expired. Please try again.';
            } elseif ($this->throttle->isLocked($this->clientId)) {
                http_response_code(429);
                header('Retry-After: 900');
                $flash = 'Too many failed attempts. Please try again later.';
            } elseif ($username === '' || $password === '' || strlen($password) > 1024) {
                http_response_code(422);
                $flash = 'Enter your username and password.';
            } elseif ($this->auth->attempt($username, $password)) {
                $this->throttle->clear($this->clientId);
                $this->csrf->rotate();

                return $this->redirect('/admin');
            } else {
                $this->throttle->recordFailure($this->clientId);
                http_response_code(401);
                $flash = 'Invalid username or password.';
            }
        }

        return $this->view->render('pages/admin_login', [
            'title' => 'Admin login',
            'path' => '',
            'formAction' => '/admin/login',
            'old' => $old,
            'errors' => [],
            'flash' => $flash === null ? null : ['type' => 'error', 'message' => $flash],
        ]);
    }

    /**
     * @param array<string, mixed> $post
     */
    public function logout(array $post): string
    {
        $this->noCache();
        if (!$this->auth->check()) {
            return $this->redirect('/admin/login');
        }
        if (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
            http_response_code(419);

            return 'Invalid request';
        }
        $this->auth->logout();

        return $this->redirect('/admin/login');
    }

    public function dashboard(): string
    {
        if (($denied = $this->guard()) !== null) {
            return $denied;
        }

        return $this->view->render('pages/admin_dashboard', [
            'title' => 'Admin dashboard',
            'path' => '',
            'counts' => $this->leads->counts(),
            'recent' => array_slice($this->leads->all(), 0, 8),
        ]);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function leads(array $query): string
    {
        if (($denied = $this->guard()) !== null) {
            return $denied;
        }

        $type = is_string($query['type'] ?? null) && in_array($query['type'], Lead::TYPES, true) ? $query['type'] : null;
        $all = $this->leads->all($type);
        $pages = max(1, (int) ceil(count($all) / self::PER_PAGE));
        $page = is_string($query['page'] ?? null) && ctype_digit($query['page']) ? (int) $query['page'] : 1;
        $page = min(max(1, $page), $pages);

        return $this->view->render('pages/admin_leads', [
            'title' => 'Leads',
            'path' => '',
            'type' => $type,
            'total' => count($all),
            'rows' => array_slice($all, ($page - 1) * self::PER_PAGE, self::PER_PAGE),
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    public function lead(string $type, string $number): string|int
    {
        if (($denied = $this->guard()) !== null) {
            return $denied;
        }
        $lead = ctype_digit($number) ? $this->leads->find($type, (int) $number) : null;
        if ($lead === null) {
            return 404;
        }

        return $this->view->render('pages/admin_lead', ['title' => 'Lead', 'path' => '', 'lead' => $lead]);
    }

    private function guard(): ?string
    {
        $this->noCache();

        return $this->auth->check() ? null : $this->redirect('/admin/login');
    }

    private function redirect(string $to): string
    {
        header('Location: ' . $to, true, 303);

        return '';
    }

    private function noCache(): void
    {
        header('Cache-Control: no-store');
        header('X-Robots-Tag: noindex, nofollow');
    }
}
