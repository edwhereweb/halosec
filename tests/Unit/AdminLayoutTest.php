<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\View;
use PHPUnit\Framework\TestCase;

final class AdminLayoutTest extends TestCase
{
    public function testEmergencyBannerToggleHidesTheWholeBanner(): void
    {
        $view = new View(__DIR__ . '/../../views');
        $html = $view->include('layout', [
            'config' => ['name' => 'HaloSec', 'emergency_phone' => '+1234567890'],
            'services' => [],
            'contentBlocks' => ['home.emergency_banner_enabled' => '0'],
            'content' => '<p>Content</p>',
        ]);

        $this->assertStringNotContainsString('SOC Monitoring 24/7 Active', $html);
        $this->assertStringContainsString('Emergency Hotline', $html);
    }

    public function testSubmissionPageOffersPrivacyExportAndEraseOnlyWithPermission(): void
    {
        $view = new View(__DIR__ . '/../../views');
        $render = static fn (bool $canPrivacy): string => $view->render('pages/admin_submission', [
            'title' => 'Asha Nair',
            'path' => '',
            'submission' => [
                'id' => 'contact-7', 'submission_id' => 7, 'form_key' => 'contact', 'type' => 'contact',
                'created_at' => gmdate('Y-m-d H:i:s'), 'status' => 'new', 'assigned_admin_user_id' => null,
                'data' => ['name' => 'Asha Nair', 'email' => 'asha@example.com', 'message' => 'Hi', 'privacy_notice' => '2026-10-29'],
            ],
            'timeline' => [],
            'neighbours' => ['older' => null, 'newer' => null],
            'users' => [],
            'me' => 1,
            'canManage' => true,
            'canAssign' => true,
            'canPrivacy' => $canPrivacy,
            'personSubmissions' => 3,
            'flashMessage' => null,
            'services' => [],
            'auditQuestions' => [],
            'auditOptions' => [],
            'csrfToken' => 'test-token',
            'config' => ['name' => 'HaloSec'],
            'nav' => ['active' => 'inbox', 'counts' => null, 'can' => [], 'username' => 'anjali', 'role' => 'admin', 'hasTwoFactor' => true],
        ]);

        $html = $render(true);
        $this->assertStringContainsString('action="/admin/submissions/7/privacy-export"', $html);
        $this->assertStringContainsString('action="/admin/submissions/7/erase"', $html);
        $this->assertStringContainsString('All 3 requests from this email', $html);
        $this->assertStringContainsString('Privacy notice shown', $html);
        $this->assertStringNotContainsString('/erase"', $render(false));
    }

    public function testAdminInboxUsesTheWorkspaceShellAndOperationalNavigation(): void
    {
        $view = new View(__DIR__ . '/../../views');
        $html = $view->render('pages/admin_submissions', [
            'title' => 'Inbox',
            'path' => '',
            'filters' => ['type' => null, 'status' => 'open', 'statusFilter' => 'open', 'assignee' => null, 'assigneeFilter' => null, 'from' => null, 'to' => null, 'q' => null],
            'statusCounts' => ['open' => 3, 'new' => 2, 'in_review' => 1, 'resolved' => 0, 'spam' => 0, 'archived' => 0, 'all' => 3],
            'assignees' => [['id' => 1, 'username' => 'anjali', 'is_active' => 1]],
            'total' => 1,
            'rows' => [[
                'submission_id' => 7,
                'form_key' => 'emergency',
                'created_at' => gmdate('Y-m-d H:i:s', time() - 3600),
                'status' => 'new',
                'assigned_admin_user_id' => 1,
                'data' => ['name' => 'Asha Nair', 'company' => 'Nila Hospitals', 'message' => 'Files are encrypted'],
            ]],
            'page' => 1,
            'pages' => 1,
            'canManage' => true,
            'saved' => null,
            'csrfToken' => 'test-token',
            'config' => ['name' => 'HaloSec'],
            'nav' => [
                'active' => 'inbox',
                'counts' => ['new' => 2, 'open' => 3, 'unassigned' => 0, 'urgent' => 1, 'mine' => 1, 'stale' => 0],
                'can' => ['dashboard' => true, 'submissions.read' => true, 'settings.manage' => true, 'security.read' => true, 'audit.read' => true, 'users.manage' => true],
                'username' => 'anjali',
                'role' => 'admin',
                'hasTwoFactor' => true,
            ],
        ]);

        $this->assertStringContainsString('Admin navigation', $html);
        foreach (['Dashboard', 'Analytics', 'Emergencies', 'Assigned to me', 'Website content', 'Security log', 'Audit log', 'Team &amp; roles'] as $label) {
            $this->assertStringContainsString($label, $html);
        }
        $this->assertStringContainsString('1 open emergency', $html);
        $this->assertStringContainsString('Asha Nair', $html);
        $this->assertStringContainsString('Files are encrypted', $html);
        $this->assertStringContainsString('href="/admin/submissions/7"', $html);
        $this->assertStringContainsString('In progress', $html);
        $this->assertStringContainsString('data-bulk', $html);
        $this->assertStringNotContainsString('SOC Monitoring 24/7 Active', $html);
        $this->assertStringContainsString('/js/admin.js', $html);
    }
}
