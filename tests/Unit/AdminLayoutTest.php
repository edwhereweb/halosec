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

    public function testAdminInboxUsesTheWorkspaceShellAndOperationalNavigation(): void
    {
        $view = new View(__DIR__ . '/../../views');
        $html = $view->render('pages/admin_submissions', [
            'title' => 'All Inbox',
            'path' => '',
            'type' => null,
            'status' => null,
            'fromDate' => null,
            'toDate' => null,
            'assigneeId' => null,
            'assignees' => [],
            'total' => 0,
            'rows' => [],
            'page' => 1,
            'pages' => 1,
            'canManage' => false,
            'csrfToken' => 'test-token',
            'config' => ['name' => 'HaloSec'],
        ]);

        $this->assertStringContainsString('Admin navigation', $html);
        $this->assertStringContainsString('Content &amp; Settings', $html);
        $this->assertStringContainsString('Failed Logins', $html);
        $this->assertStringContainsString('Admin Users / Roles', $html);
        $this->assertStringContainsString('Assignee', $html);
        $this->assertStringContainsString('Apply filters', $html);
        $this->assertStringNotContainsString('SOC Monitoring 24/7 Active', $html);
    }
}
