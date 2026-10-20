<?php

declare(strict_types=1);

/**
 * HaloSec CLI Admin & Security Utility.
 *
 * Usage:
 *   php bin/admin.php hash <password>
 *   php bin/admin.php create <username> <password> [role]
 *   php bin/admin.php reset <username> <password>
 */

$root = dirname(__DIR__);

// CLI only
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Error: This script must be run from the command line." . PHP_EOL);
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

// Load .env
$envFile = $root . '/.env';
if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '' && !str_starts_with($line, '#') && str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if (getenv($key) === false) {
                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

$config = require $root . '/config/app.php';

$algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;

$action = $argv[1] ?? 'help';

switch ($action) {
    case 'hash':
        $password = $argv[2] ?? '';
        if ($password === '') {
            fwrite(STDERR, "Usage: php bin/admin.php hash <password>\n");
            exit(1);
        }
        $hash = password_hash($password, $algo);
        echo "Password Hash (" . ($algo === PASSWORD_DEFAULT ? "bcrypt" : "argon2id") . "):\n";
        echo $hash . PHP_EOL;
        break;

    case 'create':
    case 'reset':
        $username = trim($argv[2] ?? '');
        $password = $argv[3] ?? '';
        $role = $argv[4] ?? 'super_admin';

        if ($username === '' || strlen($password) < 12) {
            fwrite(STDERR, "Error: Username required and password must be at least 12 characters.\n");
            fwrite(STDERR, "Usage: php bin/admin.php {$action} <username> <password> [super_admin|admin|editor|viewer]\n");
            exit(1);
        }

        if ($config['database_dsn'] === '') {
            fwrite(STDERR, "Error: DB_DSN is not configured in .env. Please configure database credentials first.\n");
            exit(1);
        }

        try {
            $pdo = new PDO(
                $config['database_dsn'],
                $config['database_username'],
                $config['database_password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );

            $storage = new \HaloSec\Services\PdoLeadStorage($pdo);

            // Check if user exists
            $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE username = :username AND deleted_at IS NULL');
            $stmt->execute(['username' => $username]);
            $existingId = $stmt->fetchColumn();

            if ($existingId !== false) {
                // Update
                $success = $storage->updateAdminUser((int) $existingId, $role, true, null, $password);
                if ($success) {
                    echo "Success: Updated existing admin user '{$username}' (Role: {$role}, Password updated).\n";
                } else {
                    fwrite(STDERR, "Error: Failed to update admin user.\n");
                    exit(1);
                }
            } else {
                // Create
                $success = $storage->createAdminUser($username, $password, $role, null);
                if ($success) {
                    echo "Success: Created new admin user '{$username}' with role '{$role}'.\n";
                } else {
                    fwrite(STDERR, "Error: Failed to create admin user.\n");
                    exit(1);
                }
            }
        } catch (Throwable $e) {
            fwrite(STDERR, "Database Error: " . $e->getMessage() . PHP_EOL);
            exit(1);
        }
        break;

    default:
        echo "HaloSec Admin CLI\n\n";
        echo "Commands:\n";
        echo "  php bin/admin.php hash <password>               Generate a secure password hash for .env\n";
        echo "  php bin/admin.php create <username> <password>  Create or reset an admin user in MySQL\n";
        echo "  php bin/admin.php reset <username> <password>   Reset password for an existing admin user\n";
        break;
}
