<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Controllers\AdminController;
use HaloSec\Security\AdminAuth;
use HaloSec\Security\CsrfManager;
use HaloSec\Security\LoginThrottle;
use HaloSec\Services\LeadReaderInterface;
use HaloSec\View;
use PHPUnit\Framework\TestCase;

final class AdminAuthorizationTest extends TestCase
{
    public function testViewerCannotOpenSettingsButCanReadTheInbox(): void
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE admin_users (id INTEGER PRIMARY KEY, username TEXT, password_hash TEXT, is_active INTEGER, deleted_at TEXT)');
        $pdo->exec('CREATE TABLE admin_roles (id INTEGER PRIMARY KEY, role_key TEXT)');
        $pdo->exec('CREATE TABLE admin_user_roles (admin_user_id INTEGER, admin_role_id INTEGER)');
        $pdo->exec("INSERT INTO admin_users VALUES (1, 'viewer', '" . password_hash('viewer-password', PASSWORD_DEFAULT) . "', 1, NULL)");
        $pdo->exec("INSERT INTO admin_roles VALUES (1, 'viewer')");
        $pdo->exec('INSERT INTO admin_user_roles VALUES (1, 1)');

        $session = [];
        $auth = new AdminAuth($session, $pdo);
        $this->assertTrue($auth->attempt('viewer', 'viewer-password'));
        $csrf = new CsrfManager($session);
        $reader = new class implements LeadReaderInterface {
            public function counts(): array
            {
                return [];
            }

            public function all(?string $type = null): array
            {
                return [];
            }

            public function find(string $type, int $number): ?array
            {
                return null;
            }
        };
        $view = new View(__DIR__ . '/../../views');
        $view->share([
            'config' => require __DIR__ . '/../../config/app.php',
            'services' => require __DIR__ . '/../../config/services.php',
            'audit' => require __DIR__ . '/../../config/audit.php',
            'csrfToken' => $csrf->token(),
        ]);
        $controller = new AdminController(
            $view,
            $auth,
            $csrf,
            new LoginThrottle(sys_get_temp_dir() . '/halosec_authz_test'),
            $reader,
            '127.0.0.1',
        );

        http_response_code(200);
        $inbox = $controller->submissions([]);
        $this->assertStringContainsString('<h1 class="text-[28px] font-bold tracking-tight text-slate-900">Inbox', $inbox);
        // Viewers get no links to pages they cannot open.
        $this->assertStringNotContainsString('href="/admin/users"', $inbox);
        $this->assertStringNotContainsString('href="/admin/settings"', $inbox);
        $this->assertStringNotContainsString('href="/admin/audit"', $inbox);
        $this->assertStringContainsString('href="/admin/analytics"', $inbox);
        $this->assertSame('Forbidden', $controller->settings('GET', []));
        $this->assertSame(403, http_response_code());
        http_response_code(200);
    }
}
