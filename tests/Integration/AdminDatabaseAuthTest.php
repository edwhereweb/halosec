<?php

declare(strict_types=1);

namespace HaloSec\Tests\Integration;

use HaloSec\Security\AdminAuth;
use PDO;
use PHPUnit\Framework\TestCase;

final class AdminDatabaseAuthTest extends TestCase
{
    public function testDatabaseLoginLoadsRoleAndRevokesDisabledAccount(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE admin_users (id INTEGER PRIMARY KEY, username TEXT, password_hash TEXT, is_active INTEGER, deleted_at TEXT)');
        $pdo->exec('CREATE TABLE admin_roles (id INTEGER PRIMARY KEY, role_key TEXT)');
        $pdo->exec('CREATE TABLE admin_user_roles (admin_user_id INTEGER, admin_role_id INTEGER)');
        $pdo->exec("INSERT INTO admin_users VALUES (1, 'editor1', '" . password_hash('very-long-password', PASSWORD_DEFAULT) . "', 1, NULL)");
        $pdo->exec("INSERT INTO admin_roles VALUES (1, 'editor')");
        $pdo->exec('INSERT INTO admin_user_roles VALUES (1, 1)');
        $session = [];
        $auth = new AdminAuth($session, $pdo);

        $this->assertFalse($auth->attempt('editor1', 'wrong-password'));
        $this->assertTrue($auth->attempt('editor1', 'very-long-password'));
        $this->assertSame('editor', $auth->role());
        $this->assertSame(1, $auth->userId());
        $this->assertTrue($auth->check());
        $pdo->exec('UPDATE admin_users SET is_active = 0 WHERE id = 1');
        $this->assertFalse($auth->check());
    }
}
