<?php

declare(strict_types=1);

$root = dirname(__DIR__);
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

use HaloSec\Controllers\AdminController;
use HaloSec\Controllers\FormController;
use HaloSec\Controllers\PageController;
use HaloSec\Models\Lead;
use HaloSec\Router;
use HaloSec\Security\AdminAuth;
use HaloSec\Security\CsrfManager;
use HaloSec\Security\LoginThrottle;
use HaloSec\Security\SessionManager;
use HaloSec\Services\FileLeadStorage;
use HaloSec\Services\FormValidator;
use HaloSec\Services\LeadService;
use HaloSec\Services\PdoLeadStorage;
use HaloSec\View;

$envFile = $root . '/.env';
if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if ((str_starts_with($value, '"') && str_ends_with($value, '"'))
                    || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Production error logging configuration
$isDev = (getenv('APP_ENV') ?: 'production') === 'development';
if ($isDev) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
    $logDir = $root . '/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    ini_set('error_log', $logDir . '/error.log');
}

// Baseline HTTP response security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header_remove('X-Powered-By');

SessionManager::start();

$config = require $root . '/config/app.php';
$services = require $root . '/config/services.php';
$audit = require $root . '/config/audit.php';

$csrf = new CsrfManager($_SESSION);
$view = new View($root . '/views');
$view->share([
    'config' => $config,
    'services' => $services,
    'audit' => $audit,
    'csrfToken' => $csrf->token(),
    'honeypot' => LeadService::HONEYPOT,
]);

$validator = new FormValidator(
    $config['employee_scales'],
    $config['attack_types'],
    $config['consultation_topics'],
    array_keys($services),
    $audit['questions'],
    $audit['options'],
);
$adminRepository = null;
$database = null;
if ($config['database_dsn'] !== '') {
    try {
        $database = new PDO(
            $config['database_dsn'],
            $config['database_username'],
            $config['database_password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false],
        );

        // Auto-run database schema migration if tables do not exist yet
        $tablesExist = false;
        try {
            $checkTable = $database->query("SHOW TABLES LIKE 'admin_users'");
            $tablesExist = (bool) $checkTable->fetch();
        } catch (Throwable) {
            $tablesExist = false;
        }

        if (!$tablesExist) {
            $migrationFile = $root . '/database/migrations/001_admin_dashboard.sql';
            if (is_file($migrationFile)) {
                $sqlContent = (string) file_get_contents($migrationFile);
                $lines = explode("\n", $sqlContent);
                $buffer = '';
                try {
                    $database->exec('SET FOREIGN_KEY_CHECKS = 0');
                } catch (Throwable) {}
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                        continue;
                    }
                    $buffer .= $line . "\n";
                    if (str_ends_with($trimmed, ';')) {
                        try {
                            $database->exec(trim($buffer));
                        } catch (Throwable $e) {
                            error_log('Auto-migration statement error: ' . $e->getMessage());
                        }
                        $buffer = '';
                    }
                }
                try {
                    $database->exec('SET FOREIGN_KEY_CHECKS = 1');
                } catch (Throwable) {}
            }
        }

        $adminRepository = new PdoLeadStorage($database);
        $leadStorage = $adminRepository;

        // Auto-bootstrap admin user into database if not yet seeded or if .env hash needs to override default seed
        // Ensure standard roles exist in MySQL
        try {
            $database->exec("INSERT IGNORE INTO admin_roles (role_key) VALUES ('super_admin'), ('admin'), ('editor'), ('viewer')");
        } catch (Throwable) {}

        // Auto-bootstrap admin user into database if not yet seeded or if .env hash needs to override default seed
        $adminUser = $config['admin_username'] !== '' ? $config['admin_username'] : 'admin';
        $adminHash = $config['admin_password_hash'] !== '' ? $config['admin_password_hash'] : '$2y$10$hJd71uauJov9KidGrn13neMbzXieB3FEgJltgFYrELk4bJ2hxvJfe';

        $checkStmt = $database->prepare('SELECT id, password_hash FROM admin_users WHERE LOWER(username) = LOWER(:username) AND deleted_at IS NULL');
        $checkStmt->execute(['username' => $adminUser]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($existing)) {
            $insertUser = $database->prepare('INSERT INTO admin_users (username, password_hash, is_active) VALUES (:username, :hash, 1)');
            $insertUser->execute([
                'username' => $adminUser,
                'hash' => str_starts_with($adminHash, '$') ? $adminHash : password_hash($adminHash, PASSWORD_DEFAULT),
            ]);
            $newUserId = (int) $database->lastInsertId();
            $assignRole = $database->prepare("INSERT IGNORE INTO admin_user_roles (admin_user_id, admin_role_id) SELECT :uid, id FROM admin_roles WHERE role_key = :role");
            $assignRole->execute([
                'uid' => $newUserId,
                'role' => $config['admin_role'] ?: 'super_admin',
            ]);
        } elseif ($existing['password_hash'] === '$2y$10$ThpOVvR6hlTif2f21.U9auTelfS5IBT3Dvs6uz3fDzqu2LfM9elti'
            && $adminHash !== '$2y$10$ThpOVvR6hlTif2f21.U9auTelfS5IBT3Dvs6uz3fDzqu2LfM9elti') {
            $updatePass = $database->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
            $updatePass->execute([
                'hash' => str_starts_with($adminHash, '$') ? $adminHash : password_hash($adminHash, PASSWORD_DEFAULT),
                'id' => (int) $existing['id'],
            ]);
        }

        // Ensure every active admin user has at least one role mapped
        try {
            $database->exec("INSERT IGNORE INTO admin_user_roles (admin_user_id, admin_role_id) SELECT u.id, r.id FROM admin_users u JOIN admin_roles r ON r.role_key = 'super_admin' WHERE u.deleted_at IS NULL AND NOT EXISTS (SELECT 1 FROM admin_user_roles ur WHERE ur.admin_user_id = u.id)");
        } catch (Throwable) {}
    } catch (Throwable $e) {
        error_log('Database connection error: ' . $e->getMessage());
        $database = null;
        $adminRepository = null;
        $leadStorage = new FileLeadStorage($config['storage_path']);
    }
} else {
    $leadStorage = new FileLeadStorage($config['storage_path']);
}
$leadService = new LeadService($validator, $leadStorage);
if ($adminRepository !== null) {
    $settings = $adminRepository->settings();
    foreach ([
        'site.name' => 'name',
        'site.tagline' => 'tagline',
        'site.contact_email' => 'contact_email',
        'site.emergency_phone' => 'emergency_phone',
    ] as $settingKey => $configKey) {
        if (isset($settings[$settingKey])) {
            $config[$configKey] = $settings[$settingKey];
        }
    }
    $blocks = [];
    foreach ($adminRepository->contentBlocks() as $block) {
        $key = (string) $block['block_key'];
        if ($key === 'home.emergency_banner_enabled') {
            $blocks[$key] = (int) $block['is_active'] === 1 ? (string) $block['content'] : '0';
        } elseif ((int) $block['is_active'] === 1) {
            $blocks[$key] = (string) $block['content'];
        }
    }
    $view->share(['config' => $config, 'contentBlocks' => $blocks]);
}

$pages = new PageController($view);
$forms = new FormController($view, $leadService, $csrf, $services, $_SESSION);
$admin = new AdminController(
    $view,
    new AdminAuth($_SESSION, $config['admin_username'], $config['admin_password_hash'], $config['admin_role'], $database),
    $csrf,
    new LoginThrottle($config['auth_path']),
    $leadStorage,
    (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
    $adminRepository,
);

$post = $_POST;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$form = static fn (string $type, string $route, string $title, string $tpl): callable
    => static fn () => $forms->handle($type, $route, $title, $tpl, $method, $post);

$router = new Router();
$router->add('GET', '/', [$pages, 'home']);
$router->add('GET', '/about', [$pages, 'about']);
$router->add('GET', '/services', [$pages, 'services']);
$router->add('GET', '/robots.txt', static function (): string {
    header('Content-Type: text/plain; charset=utf-8');
    return (string) file_get_contents(__DIR__ . '/robots.txt');
});
$router->add('GET', '/sitemap.xml', static function (): string {
    header('Content-Type: application/xml; charset=utf-8');
    return (string) file_get_contents(__DIR__ . '/sitemap.xml');
});
foreach (['GET', 'POST'] as $m) {
    $router->add($m, '/free-audit', $form(Lead::TYPE_AUDIT, '/free-audit', 'Free Cybersecurity Audit', 'pages/audit'));
    $router->add($m, '/consultation', $form(Lead::TYPE_CONSULTATION, '/consultation', 'Book a Consultation', 'pages/consultation'));
    $router->add($m, '/under-attack', $form(Lead::TYPE_EMERGENCY, '/under-attack', 'Under a Cyber Attack', 'pages/emergency'));
    $router->add($m, '/contact', $form(Lead::TYPE_CONTACT, '/contact', 'Contact Us', 'pages/contact'));
    $router->add($m, '/services/{slug}', static fn (string $slug) => $forms->handle(
        Lead::TYPE_ENQUIRY,
        '/services/' . $slug,
        'Service',
        'pages/service',
        $method,
        $post,
        $slug,
    ));
}
$router->add('GET', '/newsletter', $form(Lead::TYPE_NEWSLETTER, '/newsletter', 'Newsletter', 'pages/newsletter'));
$router->add('POST', '/newsletter', $form(Lead::TYPE_NEWSLETTER, '/newsletter', 'Newsletter', 'pages/newsletter'));
$router->add('GET', '/security-report', $form(Lead::TYPE_SECURITY_REPORT, '/security-report', 'Security report', 'pages/emergency'));
$router->add('POST', '/security-report', $form(Lead::TYPE_SECURITY_REPORT, '/security-report', 'Security report', 'pages/emergency'));

foreach (['GET', 'POST'] as $m) {
    $router->add($m, '/admin/login', static fn () => $admin->login($method, $post));
}
$router->add('GET', '/admin', [$admin, 'dashboard']);
$router->add('GET', '/admin/leads', static fn () => $admin->leads($_GET));
$router->add('GET', '/admin/leads/{type}/{number}', [$admin, 'lead']);
$router->add('GET', '/admin/submissions', static fn () => $admin->submissions($_GET));
$router->add('GET', '/admin/submissions/export', static fn () => $admin->exportSubmissions($_GET));
$router->add('POST', '/admin/submissions/bulk', static fn () => $admin->bulkChangeSubmissionStatus($post));
$router->add('GET', '/admin/submissions/{id}', [$admin, 'submission']);
$router->add('POST', '/admin/submissions/{id}/status', static fn (string $id) => $admin->changeSubmissionStatus($id, $post));
$router->add('POST', '/admin/submissions/{id}/assignment', static fn (string $id) => $admin->assignSubmission($id, $post));
$router->add('POST', '/admin/submissions/{id}/notes', static fn (string $id) => $admin->addSubmissionNote($id, $post));
$router->add('GET', '/admin/security', static fn () => $admin->security($_GET));
$router->add('GET', '/admin/audit', [$admin, 'audit']);
$router->add('POST', '/admin/users/{id}', static fn (string $id) => $admin->updateUser($id, $post));
foreach (['GET', 'POST'] as $m) {
    $router->add($m, '/admin/settings', static fn () => $admin->settings($method, $post));
    $router->add($m, '/admin/users', static fn () => $admin->users($method, $post));
}
$router->add('POST', '/admin/content-blocks', static fn () => $admin->updateContentBlock($post));
$router->add('POST', '/admin/logout', static fn () => $admin->logout($post));

try {
    $result = $router->dispatch($method, $path);

    if ($result === 405) {
        http_response_code(405);
        header('Allow: GET, POST');
        echo 'Method not allowed';
    } elseif ($result === null || $result === 404) {
        http_response_code(404);
        echo $pages->notFound();
    } else {
        echo $result;
    }
} catch (Throwable $e) {
    error_log('Unhandled application error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    if ((getenv('APP_ENV') ?: 'production') === 'development') {
        echo '<h1>500 Internal Server Error</h1><pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        echo $pages->serverError();
    }
}
