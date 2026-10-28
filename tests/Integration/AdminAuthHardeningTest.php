<?php

declare(strict_types=1);

namespace HaloSec\Tests\Integration;

use HaloSec\Security\AdminAuth;
use HaloSec\Security\Totp;
use HaloSec\Services\Migrator;
use PDO;
use PHPUnit\Framework\TestCase;

final class AdminAuthHardeningTest extends TestCase
{
    private function db(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE admin_users (id INTEGER PRIMARY KEY, username TEXT, password_hash TEXT, totp_secret TEXT NULL, totp_last_step INTEGER NULL, is_active INTEGER, deleted_at TEXT)');
        $pdo->exec('CREATE TABLE admin_roles (id INTEGER PRIMARY KEY, role_key TEXT)');
        $pdo->exec('CREATE TABLE admin_user_roles (admin_user_id INTEGER, admin_role_id INTEGER)');
        $pdo->exec("INSERT INTO admin_roles VALUES (1, 'super_admin'), (2, 'editor')");

        return $pdo;
    }

    private function rowCount(PDO $pdo, string $table): int
    {
        $query = $pdo->query('SELECT COUNT(*) FROM ' . $table);
        $this->assertNotFalse($query);

        return (int) $query->fetchColumn();
    }

    private function addUser(PDO $pdo, int $id, string $name, string $hash, ?int $roleId): void
    {
        $pdo->prepare('INSERT INTO admin_users (id, username, password_hash, is_active) VALUES (?, ?, ?, 1)')->execute([$id, $name, $hash]);
        if ($roleId !== null) {
            $pdo->prepare('INSERT INTO admin_user_roles VALUES (?, ?)')->execute([$id, $roleId]);
        }
    }

    public function testUserWithoutRoleIsNotPromotedToSuperAdmin(): void
    {
        $pdo = $this->db();
        $this->addUser($pdo, 1, 'norole', password_hash('a-long-password-1', PASSWORD_DEFAULT), null);
        $session = [];
        $auth = new AdminAuth($session, $pdo);

        $this->assertFalse($auth->attempt('norole', 'a-long-password-1'));
        $this->assertSame('', $auth->role());
        $this->assertSame(0, $this->rowCount($pdo, 'admin_user_roles'));
    }

    public function testPlaintextStoredPasswordIsRejected(): void
    {
        $pdo = $this->db();
        $this->addUser($pdo, 1, 'plain', 'a-long-password-1', 1);
        $session = [];

        $this->assertFalse((new AdminAuth($session, $pdo))->attempt('plain', 'a-long-password-1'));
    }

    public function testPublishedDefaultPasswordIsRejected(): void
    {
        $pdo = $this->db();
        $this->addUser($pdo, 1, 'admin', '$2y$10$hJd71uauJov9KidGrn13neMbzXieB3FEgJltgFYrELk4bJ2hxvJfe', 1);
        $this->addUser($pdo, 2, 'other', password_hash('admin123', PASSWORD_DEFAULT), 1);
        $session = [];
        $auth = new AdminAuth($session, $pdo);

        $this->assertFalse($auth->attempt('admin', 'admin123'));
        $this->assertFalse($auth->attempt('other', 'admin123'));
    }

    public function testDatabaseErrorDeniesExistingSession(): void
    {
        $pdo = $this->db();
        $this->addUser($pdo, 1, 'boss', password_hash('a-long-password-1', PASSWORD_DEFAULT), 1);
        $session = [];
        $auth = new AdminAuth($session, $pdo);
        $this->assertTrue($auth->attempt('boss', 'a-long-password-1'));
        $this->assertTrue($auth->check());

        $pdo->exec('DROP TABLE admin_user_roles');
        $this->assertFalse($auth->check());
    }

    public function testTwoFactorLoginFlowAndReplayProtection(): void
    {
        $pdo = $this->db();
        $this->addUser($pdo, 1, 'boss', password_hash('a-long-password-1', PASSWORD_DEFAULT), 1);
        $session = [];
        $auth = new AdminAuth($session, $pdo);

        // Enroll.
        $this->assertTrue($auth->attempt('boss', 'a-long-password-1'));
        $this->assertFalse($auth->hasTwoFactor());
        $secret = $auth->beginTwoFactorEnrollment();
        $now = time();
        $this->assertFalse($auth->confirmTwoFactorEnrollment('000000', $now - 600));
        $this->assertTrue($auth->confirmTwoFactorEnrollment(Totp::code($secret, intdiv($now, 30)), $now));
        $this->assertTrue($auth->hasTwoFactor());
        $auth->logout();

        // Password alone no longer signs in.
        $this->assertTrue($auth->attempt('boss', 'a-long-password-1'));
        $this->assertTrue($auth->awaitingTwoFactor());
        $this->assertFalse($auth->check());

        // The code used during enrollment cannot be replayed; the next one works.
        $this->assertFalse($auth->verifyTwoFactor(Totp::code($secret, intdiv($now, 30)), $now));
        $later = $now + 30;
        $this->assertTrue($auth->verifyTwoFactor(Totp::code($secret, intdiv($later, 30)), $later));
        $this->assertTrue($auth->check());
        $this->assertTrue($auth->hasTwoFactor());
    }

    public function testTwoFactorStepGivesUpAfterTooManyWrongCodes(): void
    {
        $pdo = $this->db();
        $this->addUser($pdo, 1, 'boss', password_hash('a-long-password-1', PASSWORD_DEFAULT), 1);
        $pdo->exec("UPDATE admin_users SET totp_secret = '" . Totp::generateSecret() . "'");
        $session = [];
        $auth = new AdminAuth($session, $pdo);

        $this->assertTrue($auth->attempt('boss', 'a-long-password-1'));
        for ($i = 0; $i < AdminAuth::MAX_CODE_ATTEMPTS; $i++) {
            $this->assertFalse($auth->verifyTwoFactor('000000'));
        }
        $this->assertFalse($auth->verifyTwoFactor('000000'));
        $this->assertFalse($auth->awaitingTwoFactor());
    }

    public function testChangePassword(): void
    {
        $pdo = $this->db();
        $this->addUser($pdo, 1, 'boss', password_hash('a-long-password-1', PASSWORD_DEFAULT), 1);
        $session = [];
        $auth = new AdminAuth($session, $pdo);
        $auth->attempt('boss', 'a-long-password-1');

        $this->assertFalse($auth->changePassword('wrong-current-pw', 'a-new-long-password'));
        $this->assertFalse($auth->changePassword('a-long-password-1', 'short'));
        $this->assertTrue($auth->changePassword('a-long-password-1', 'a-new-long-password'));
        $auth->logout();
        $this->assertTrue($auth->attempt('boss', 'a-new-long-password'));
    }

    public function testMigratorSplitsStatementsAndRunsEachFileOnce(): void
    {
        $this->assertSame(
            ['CREATE TABLE a (id INT)', "INSERT INTO a VALUES (1)"],
            Migrator::statements("-- comment\nCREATE TABLE a (id INT);\n\nINSERT INTO a VALUES (1);\n"),
        );

        $dir = sys_get_temp_dir() . '/halosec_mig_' . bin2hex(random_bytes(4));
        mkdir($dir);
        file_put_contents($dir . '/001_a.sql', "CREATE TABLE t (id INTEGER);\nINSERT INTO t VALUES (1);\n");
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $migrator = new Migrator($pdo, $dir);

        $this->assertSame(['001_a.sql'], $migrator->migrate());
        $this->assertSame([], $migrator->migrate());
        $this->assertSame(1, $this->rowCount($pdo, 't'));

        unlink($dir . '/001_a.sql');
        rmdir($dir);
    }
}
