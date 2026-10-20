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

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

SessionManager::start();

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
    $database = new PDO(
        $config['database_dsn'],
        $config['database_username'],
        $config['database_password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false],
    );
    $adminRepository = new PdoLeadStorage($database);
    $leadStorage = $adminRepository;
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
