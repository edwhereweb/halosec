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
use HaloSec\View;

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

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
// Swap FileLeadStorage for a PDO-backed LeadStorageInterface implementation to move to a database.
$leadStorage = new FileLeadStorage($config['storage_path']);
$leadService = new LeadService($validator, $leadStorage);

$pages = new PageController($view);
$forms = new FormController($view, $leadService, $csrf, $services, $_SESSION);
$admin = new AdminController(
    $view,
    new AdminAuth($_SESSION, $config['admin_username'], $config['admin_password_hash']),
    $csrf,
    new LoginThrottle($config['auth_path']),
    $leadStorage,
    (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
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

foreach (['GET', 'POST'] as $m) {
    $router->add($m, '/admin/login', static fn () => $admin->login($method, $post));
}
$router->add('GET', '/admin', [$admin, 'dashboard']);
$router->add('GET', '/admin/leads', static fn () => $admin->leads($_GET));
$router->add('GET', '/admin/leads/{type}/{number}', [$admin, 'lead']);
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
