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
use HaloSec\Seo\Sitemap;
use HaloSec\Security\AdminAuth;
use HaloSec\Security\CsrfManager;
use HaloSec\Security\LoginThrottle;
use HaloSec\Security\SessionManager;
use HaloSec\Services\FileLeadStorage;
use HaloSec\Services\FormValidator;
use HaloSec\Services\LeadService;
use HaloSec\Services\PdoLeadStorage;
use HaloSec\Env;
use HaloSec\Services\Migrator;
use HaloSec\View;

// Prevent direct web access to .env files under any circumstance
$requestUri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (str_contains(strtolower($requestUri), '.env')) {
    http_response_code(403);
    exit('Access denied.');
}

// .env lives in the app root or one level above it (outside the web root). Never inside public/.
Env::load([
    $root . '/.env',
    dirname($root) . '/.env',
]);

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

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

// Baseline HTTP response security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header('X-XSS-Protection: 1; mode=block');
if ($isHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
header_remove('X-Powered-By');

SessionManager::start();

$config = require $root . '/config/app.php';
HaloSec\Admin\Ui::setTimezone((string) ($config['admin_timezone'] ?? 'Asia/Kolkata'));
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
$dbError = null;
$database = null;
if ($config['database_dsn'] !== '') {
    try {
        $database = new PDO(
            $config['database_dsn'],
            $config['database_username'],
            $config['database_password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false],
        );
        if ($database->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            // The app stores and compares UTC; make MySQL's CURRENT_TIMESTAMP columns (notes, audit log) UTC too.
            $database->exec("SET time_zone = '+00:00'");
        }

        $adminRepository = new PdoLeadStorage($database);
        $leadStorage = $adminRepository;

        // Schema changes and the first admin account are handled by `php bin/admin.php migrate`
        // at deploy time, so nothing here writes to the database on a normal page load.
    } catch (Throwable $e) {
        error_log('Database connection error: ' . $e->getMessage());
        // Show connection details only in development; production pages get a generic hint.
        $dbError = $isDev ? $e->getMessage() : 'Database unavailable. Details are in storage/logs/error.log.';
        $database = null;
        $adminRepository = null;
        $leadStorage = new FileLeadStorage($config['storage_path']);
    }
} else {
    $dbError = 'Database is not configured (DB_* settings in .env).';
    $leadStorage = new FileLeadStorage($config['storage_path']);
}
$leadService = new LeadService($validator, $leadStorage);
if ($adminRepository !== null) {
    try {
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
    } catch (Throwable $e) {
        // Usually means migrations have not been run yet: php bin/admin.php migrate
        error_log('Could not load site settings: ' . $e->getMessage());
    }
}

$rawClientIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
// CF-Connecting-IP can be set by anyone. Only trust it when the site really sits behind Cloudflare
// (TRUST_CLOUDFLARE=true), otherwise attackers could rotate fake IPs to dodge the login lockout.
if (Env::bool('TRUST_CLOUDFLARE')
    && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])
    && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
    $clientIp = (string) $_SERVER['HTTP_CF_CONNECTING_IP'];
} else {
    $clientIp = filter_var($rawClientIp, FILTER_VALIDATE_IP) ? $rawClientIp : '127.0.0.1';
}

$formThrottle = new LoginThrottle($config['auth_path'] . '/forms', 10, 60);
$pages = new PageController($view);
$forms = new FormController($view, $leadService, $csrf, $services, $_SESSION, $formThrottle, $clientIp);
$admin = new AdminController(
    $view,
    new AdminAuth($_SESSION, $database),
    $csrf,
    new LoginThrottle($config['auth_path']),
    $leadStorage,
    $clientIp,
    $adminRepository,
    $dbError,
    Env::bool('ADMIN_REQUIRE_2FA'),
    (string) $config['name'],
);

$post = $_POST;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// One URL per page: /about/ -> /about. Only plain paths are redirected, and leading slashes are collapsed,
// so "//host/" or "/\host/" can't become an open redirect.
if (in_array($method, ['GET', 'HEAD'], true) && $path !== '/' && str_ends_with($path, '/')
    && preg_match('#^[A-Za-z0-9/_.-]+$#', $path) === 1) {
    $query = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
    header('Location: /' . trim($path, '/') . ($query !== '' ? '?' . $query : ''), true, 301);
    exit;
}

$form = static fn (string $type, string $route, string $title, string $tpl, string $description = '', bool $noindex = false): callable
    => static fn () => $forms->handle($type, $route, $title, $tpl, $method, $post, null, $description, $noindex);

$router = new Router();
$router->add('GET', '/', [$pages, 'home']);
$router->add('GET', '/about', [$pages, 'about']);
$router->add('GET', '/services', [$pages, 'services']);
$router->add('GET', '/robots.txt', static function (): string {
    header('Content-Type: text/plain; charset=utf-8');
    return (string) file_get_contents(__DIR__ . '/robots.txt');
});
$router->add('GET', '/sitemap.xml', static function () use ($config, $services, $root): string {
    header('Content-Type: application/xml; charset=utf-8');
    return Sitemap::xml(rtrim((string) $config['app_url'], '/'), $root, $services, (bool) $config['privacy_ready']);
});
$router->add('GET', '/privacy', static fn () => $pages->privacy((bool) $config['privacy_ready']));
foreach (['GET', 'POST'] as $m) {
    $router->add($m, '/free-audit', $form(
        Lead::TYPE_AUDIT,
        '/free-audit',
        'Free Cybersecurity Audit for Your Business',
        'pages/audit',
        'Get a free, no-obligation cybersecurity audit of your network, cloud and endpoints, with an actionable report from Zoho and Sophos certified architects.',
    ));
    $router->add($m, '/consultation', $form(
        Lead::TYPE_CONSULTATION,
        '/consultation',
        'Book a Cybersecurity Consultation',
        'pages/consultation',
        'Talk to a senior HaloSec security architect about your infrastructure, compliance posture and security roadmap. Book a consultation online.',
    ));
    $router->add($m, '/under-attack', $form(
        Lead::TYPE_EMERGENCY,
        '/under-attack',
        'Under Cyber Attack? 24/7 Incident Response',
        'pages/emergency',
        'Ransomware, phishing or a data breach? Call the HaloSec 24/7 incident response hotline or report the attack online for immediate triage and isolation.',
    ));
    $router->add($m, '/contact', $form(
        Lead::TYPE_CONTACT,
        '/contact',
        'Contact HaloSec Cybersecurity',
        'pages/contact',
        'Contact HaloSec about cybersecurity services, partnerships or general enquiries. Email us, call our hotline or send a message and we will reply promptly.',
    ));
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
$router->add('GET', '/newsletter', $form(Lead::TYPE_NEWSLETTER, '/newsletter', 'Newsletter', 'pages/newsletter', '', true));
$router->add('POST', '/newsletter', $form(Lead::TYPE_NEWSLETTER, '/newsletter', 'Newsletter', 'pages/newsletter', '', true));
$router->add('GET', '/security-report', $form(Lead::TYPE_SECURITY_REPORT, '/security-report', 'Security report', 'pages/emergency', '', true));
$router->add('POST', '/security-report', $form(Lead::TYPE_SECURITY_REPORT, '/security-report', 'Security report', 'pages/emergency', '', true));

foreach (['GET', 'POST'] as $m) {
    $router->add($m, '/admin/login', static fn () => $admin->login($method, $post));
    $router->add($m, '/admin/login/2fa', static fn () => $admin->loginTwoFactor($method, $post));
    $router->add($m, '/admin/account', static fn () => $admin->account($method, $post));
}
$router->add('GET', '/admin', [$admin, 'dashboard']);
$router->add('GET', '/admin/analytics', static fn () => $admin->analytics($_GET));
$router->add('GET', '/admin/leads', static fn () => $admin->leads($_GET));
$router->add('GET', '/admin/leads/{type}/{number}', [$admin, 'lead']);
$router->add('GET', '/admin/submissions', static fn () => $admin->submissions($_GET));
$router->add('GET', '/admin/submissions/export', static fn () => $admin->exportSubmissions($_GET));
$router->add('POST', '/admin/submissions/bulk', static fn () => $admin->bulkChangeSubmissionStatus($post));
$router->add('GET', '/admin/submissions/{id}', static fn (string $id) => $admin->submission($id, $_GET));
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
