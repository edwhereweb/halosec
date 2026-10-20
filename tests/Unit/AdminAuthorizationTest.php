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
        $session = [];
        $auth = new AdminAuth($session, 'viewer', password_hash('viewer-password', PASSWORD_DEFAULT), 'viewer');
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
        $this->assertStringContainsString('All Inbox', $controller->submissions([]));
        $this->assertSame('Forbidden', $controller->settings('GET', []));
        $this->assertSame(403, http_response_code());
        http_response_code(200);
    }
}
