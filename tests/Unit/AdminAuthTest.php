<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Security\AdminAuth;
use HaloSec\Security\LoginThrottle;
use PHPUnit\Framework\TestCase;

final class AdminAuthTest extends TestCase
{
    private function createDb(string $username, string $password): \PDO
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE admin_users (id INTEGER PRIMARY KEY, username TEXT, password_hash TEXT, is_active INTEGER, deleted_at TEXT)');
        $pdo->exec('CREATE TABLE admin_roles (id INTEGER PRIMARY KEY, role_key TEXT)');
        $pdo->exec('CREATE TABLE admin_user_roles (admin_user_id INTEGER, admin_role_id INTEGER)');
        $pdo->exec("INSERT INTO admin_users VALUES (1, '{$username}', '" . password_hash($password, PASSWORD_DEFAULT) . "', 1, NULL)");
        $pdo->exec("INSERT INTO admin_roles VALUES (1, 'super_admin')");
        $pdo->exec('INSERT INTO admin_user_roles VALUES (1, 1)');

        return $pdo;
    }

    public function testLoginSuccessAndFailure(): void
    {
        $session = [];
        $pdo = $this->createDb('admin', 's3cret-pass');
        $auth = new AdminAuth($session, $pdo);

        $this->assertFalse($auth->attempt('admin', 'wrong'));
        $this->assertFalse($auth->attempt('other', 's3cret-pass'));
        $this->assertFalse($auth->check());
        $this->assertTrue($auth->attempt('admin', 's3cret-pass'));
        $this->assertTrue($auth->check());
    }

    public function testUnconfiguredCredentialsNeverAuthenticate(): void
    {
        $session = [];
        $auth = new AdminAuth($session);

        $this->assertFalse($auth->attempt('admin', 'admin123'));
        $this->assertFalse($auth->attempt('', ''));
        $this->assertFalse($auth->check());
    }

    public function testIdleTimeoutAndLogout(): void
    {
        $session = [];
        $pdo = $this->createDb('admin', 'pw');
        $auth = new AdminAuth($session, $pdo);
        $auth->attempt('admin', 'pw');

        $this->assertFalse($auth->check(time() + 4000));
        $auth->attempt('admin', 'pw');
        $auth->logout();
        $this->assertSame([], $session);
        $this->assertFalse($auth->check());
    }

    public function testThrottleLocksAfterMaxFailuresAndExpires(): void
    {
        $dir = sys_get_temp_dir() . '/halosec_auth_' . bin2hex(random_bytes(4));
        $throttle = new LoginThrottle($dir, 3, 60);

        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse($throttle->isLocked('1.2.3.4', 100));
            $throttle->recordFailure('1.2.3.4', 100);
        }
        $this->assertTrue($throttle->isLocked('1.2.3.4', 101));
        $this->assertFalse($throttle->isLocked('5.6.7.8', 101));
        $this->assertFalse($throttle->isLocked('1.2.3.4', 200));

        $throttle->clear('1.2.3.4');
        foreach (glob($dir . '/*') ?: [] as $f) {
            unlink($f);
        }
        rmdir($dir);
    }
}
