<?php

declare(strict_types=1);

/**
 * HaloSec CLI: deployment, admin accounts, backups and housekeeping.
 * Run `php bin/admin.php` with no arguments for the command list.
 */

use HaloSec\Env;
use HaloSec\Security\AdminAuth;
use HaloSec\Services\Migrator;
use HaloSec\Services\PdoLeadStorage;

$root = dirname(__DIR__);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, 'Error: This script must be run from the command line.' . PHP_EOL);
    exit(1);
}

$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        if (str_starts_with($class, 'HaloSec\\')) {
            $file = $root . '/src/' . str_replace('\\', '/', substr($class, 8)) . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    });
}

Env::load([$root . '/.env', dirname($root) . '/.env']);
$config = require $root . '/config/app.php';

$fail = static function (string $message): never {
    fwrite(STDERR, 'Error: ' . $message . PHP_EOL);
    exit(1);
};

$connect = static function () use ($config, $fail): PDO {
    if ($config['database_dsn'] === '') {
        $fail('Database is not configured. Set DB_HOST/DB_NAME/DB_USER/DB_PASSWORD (or DB_DSN) in .env.');
    }
    try {
        return new PDO(
            $config['database_dsn'],
            $config['database_username'],
            $config['database_password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false],
        );
    } catch (Throwable $e) {
        $fail('Could not connect to the database: ' . $e->getMessage());
    }
};

$action = $argv[1] ?? 'help';

switch ($action) {
    case 'hash':
        $password = $argv[2] ?? '';
        if (!AdminAuth::acceptablePassword($password)) {
            $fail('Usage: php bin/admin.php hash <password>  (12+ characters)');
        }
        echo password_hash($password, AdminAuth::passwordAlgorithm()) . PHP_EOL;
        break;

    case 'migrate':
        $pdo = $connect();
        try {
            $applied = (new Migrator($pdo, $root . '/database/migrations'))->migrate();
        } catch (Throwable $e) {
            $fail($e->getMessage());
        }
        echo $applied === [] ? "Database is up to date.\n" : 'Applied: ' . implode(', ', $applied) . PHP_EOL;

        // Optional first-admin bootstrap from .env (only creates; never overwrites an existing account).
        $username = trim($config['admin_username']);
        $hash = $config['admin_password_hash'];
        if ($username !== '' && $hash !== '') {
            if (!str_starts_with($hash, '$2y$') && !str_starts_with($hash, '$argon2')) {
                fwrite(STDERR, "Warning: ADMIN_PASSWORD_HASH is not a password hash; skipped. Generate one with: php bin/admin.php hash '<password>'\n");
            } elseif (AdminAuth::isKnownDefaultHash($hash)) {
                fwrite(STDERR, "Warning: ADMIN_PASSWORD_HASH is a published default; skipped. Generate a new one.\n");
            } else {
                $exists = $pdo->prepare('SELECT id FROM admin_users WHERE LOWER(username) = LOWER(:u) AND deleted_at IS NULL');
                $exists->execute(['u' => $username]);
                if ($exists->fetchColumn() === false) {
                    $pdo->prepare('INSERT INTO admin_users (username, password_hash, is_active) VALUES (:u, :h, 1)')
                        ->execute(['u' => $username, 'h' => $hash]);
                    $pdo->prepare('INSERT INTO admin_user_roles (admin_user_id, admin_role_id) SELECT :id, id FROM admin_roles WHERE role_key = :r')
                        ->execute(['id' => (int) $pdo->lastInsertId(), 'r' => in_array($config['admin_role'], ['super_admin', 'admin', 'editor', 'viewer'], true) ? $config['admin_role'] : 'super_admin']);
                    echo "Created admin user '{$username}' from .env.\n";
                }
            }
        }

        // Flag accounts still using a password that was once published in this repository.
        $weak = [];
        foreach ($pdo->query('SELECT username, password_hash FROM admin_users WHERE deleted_at IS NULL')->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (AdminAuth::isKnownDefaultHash((string) $row['password_hash'])) {
                $weak[] = (string) $row['username'];
            }
        }
        if ($weak !== []) {
            fwrite(STDERR, 'WARNING: these accounts use the old default password and are blocked from signing in: '
                . implode(', ', $weak) . PHP_EOL . "Reset them: php bin/admin.php reset <username> '<new password>'\n");
        }
        break;

    case 'create':
    case 'reset':
        $username = trim($argv[2] ?? '');
        $password = $argv[3] ?? '';
        $role = $argv[4] ?? 'super_admin';
        if ($username === '' || !AdminAuth::acceptablePassword($password)) {
            $fail("Usage: php bin/admin.php {$action} <username> <password> [super_admin|admin|editor|viewer]  (password 12+ characters)");
        }
        $pdo = $connect();
        $storage = new PdoLeadStorage($pdo);
        $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE LOWER(username) = LOWER(:username) AND deleted_at IS NULL');
        $stmt->execute(['username' => $username]);
        $existingId = $stmt->fetchColumn();
        if ($existingId !== false) {
            if (!isset($argv[4])) {
                // Keep the user's current role when none is given.
                $current = $pdo->prepare('SELECT r.role_key FROM admin_user_roles ur JOIN admin_roles r ON r.id = ur.admin_role_id WHERE ur.admin_user_id = :id LIMIT 1');
                $current->execute(['id' => (int) $existingId]);
                $role = (string) ($current->fetchColumn() ?: 'super_admin');
            }
            if (!$storage->updateAdminUser((int) $existingId, $role, true, null, $password)) {
                $fail('Failed to update admin user.');
            }
            echo "Updated '{$username}' (role: {$role}, password changed).\n";
        } elseif ($action === 'reset') {
            $fail("No admin user named '{$username}'. Use 'create' instead.");
        } else {
            if (!$storage->createAdminUser($username, $password, $role, null)) {
                $fail('Failed to create admin user (username may be invalid).');
            }
            echo "Created '{$username}' with role '{$role}'.\n";
        }
        break;

    case '2fa-reset':
        $username = trim($argv[2] ?? '');
        if ($username === '') {
            $fail('Usage: php bin/admin.php 2fa-reset <username>');
        }
        $pdo = $connect();
        $stmt = $pdo->prepare('UPDATE admin_users SET totp_secret = NULL, totp_last_step = NULL WHERE LOWER(username) = LOWER(:u) AND deleted_at IS NULL');
        $stmt->execute(['u' => $username]);
        echo $stmt->rowCount() > 0 ? "Two-factor authentication removed for '{$username}'. They can set it up again under My Account.\n" : "No change (unknown user or 2FA was already off).\n";
        break;

    case 'backup':
        $pdo = $connect();
        $dir = $root . '/storage/backups';
        if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
            $fail("Cannot create {$dir}");
        }
        $file = $dir . '/halosec-' . date('Ymd-His') . '.sql.gz';
        $gz = gzopen($file, 'wb6');
        if ($gz === false) {
            $fail("Cannot write {$file}");
        }
        gzwrite($gz, "-- HaloSec database backup " . date('c') . "\nSET FOREIGN_KEY_CHECKS=0;\n\n");
        foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
            $table = (string) $table;
            $create = $pdo->query('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`')->fetch(PDO::FETCH_NUM);
            gzwrite($gz, "DROP TABLE IF EXISTS `{$table}`;\n" . $create[1] . ";\n\n");
            $rows = $pdo->query('SELECT * FROM `' . str_replace('`', '``', $table) . '`');
            while (($row = $rows->fetch(PDO::FETCH_ASSOC)) !== false) {
                $values = array_map(static fn ($v): string => $v === null ? 'NULL' : (string) $pdo->quote((string) $v), $row);
                gzwrite($gz, "INSERT INTO `{$table}` (`" . implode('`, `', array_keys($row)) . '`) VALUES (' . implode(', ', $values) . ");\n");
            }
            gzwrite($gz, "\n");
        }
        gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n");
        gzclose($gz);
        chmod($file, 0640);
        echo "Backup written: {$file}\n";
        // fall through to prune old backups

    case 'prune':
        $keepDays = max(1, (int) (getenv('BACKUP_KEEP_DAYS') ?: 14));
        $removed = 0;
        foreach (glob($root . '/storage/backups/*.sql.gz') ?: [] as $old) {
            if (filemtime($old) < time() - $keepDays * 86400 && unlink($old)) {
                $removed++;
            }
        }
        // Throttle files older than a day are expired anyway.
        foreach (array_merge(glob($config['auth_path'] . '/*.json') ?: [], glob($config['auth_path'] . '/forms/*.json') ?: []) as $old) {
            if (filemtime($old) < time() - 86400 && unlink($old)) {
                $removed++;
            }
        }
        echo "Pruned {$removed} old file(s).\n";
        break;

    case 'unblock':
    case 'clear-throttle':
        $count = 0;
        foreach (glob($config['auth_path'] . '/*.json') ?: [] as $file) {
            if (is_file($file) && unlink($file)) {
                $count++;
            }
        }
        echo "Cleared {$count} login lockout file(s).\n";
        break;

    default:
        echo <<<TXT
        HaloSec Admin CLI

        Deployment
          php bin/admin.php migrate                         Apply database migrations (run after every deploy)

        Accounts
          php bin/admin.php create <user> <password> [role] Create an admin (role: super_admin|admin|editor|viewer)
          php bin/admin.php reset <user> <password>         Reset an existing admin's password
          php bin/admin.php 2fa-reset <user>                Remove 2FA from an account (lost phone)
          php bin/admin.php hash <password>                 Print a password hash for ADMIN_PASSWORD_HASH
          php bin/admin.php unblock                         Clear login lockouts

        Maintenance (cron)
          php bin/admin.php backup                          Gzipped SQL dump to storage/backups, then prune
          php bin/admin.php prune                           Delete old backups and expired lockout files

        TXT;
        break;
}
