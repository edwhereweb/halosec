<?php

declare(strict_types=1);

return [
    'name' => 'HaloSec',
    'tagline' => 'The Aura of Security for Your Business',
    'app_env' => getenv('APP_ENV') ?: 'production',
    'app_url' => getenv('APP_URL') ?: 'https://halosec.in',
    // Times in the admin area are shown in this zone (stored times stay UTC).
    'admin_timezone' => getenv('ADMIN_TIMEZONE') ?: 'Asia/Kolkata',
    'emergency_phone' => getenv('EMERGENCY_PHONE') ?: '+917356543520',
    'contact_email' => getenv('CONTACT_EMAIL') ?: 'hello@halosec.in',
    // Public business details for search engines (structured data). Leave unset until they are real.
    'address' => [
        'street' => (string) (getenv('BUSINESS_STREET') ?: ''),
        'locality' => (string) (getenv('BUSINESS_CITY') ?: ''),
        'region' => (string) (getenv('BUSINESS_REGION') ?: ''),
        'postal_code' => (string) (getenv('BUSINESS_POSTAL_CODE') ?: ''),
        'country' => (string) (getenv('BUSINESS_COUNTRY') ?: ''),
    ],
    'area_served' => (string) (getenv('BUSINESS_AREA_SERVED') ?: ''),
    // Comma-separated profile URLs, e.g. LinkedIn company page.
    'social_links' => array_values(array_filter(array_map('trim', explode(',', (string) (getenv('SOCIAL_LINKS') ?: ''))))),
    // Flip to true once views/pages/privacy.php has been completed and reviewed.
    // Until then /privacy is noindexed and not linked from the footer or sitemap.
    'privacy_ready' => false,
    // Privacy (GDPR / DPDP). Empty values show as [TO COMPLETE] on the draft policy.
    'privacy' => [
        // Change this date whenever the wording of the form notice or the policy changes.
        // It is saved with every submission as a record of what the person was shown.
        'notice_version' => '2026-10-29',
        'legal_name' => (string) (getenv('BUSINESS_LEGAL_NAME') ?: ''),
        'email' => (string) (getenv('PRIVACY_EMAIL') ?: getenv('CONTACT_EMAIL') ?: 'hello@halosec.in'),
        'officer_name' => (string) (getenv('GRIEVANCE_OFFICER_NAME') ?: ''),
        'officer_phone' => (string) (getenv('GRIEVANCE_OFFICER_PHONE') ?: ''),
        'hosting' => (string) (getenv('HOSTING_PROVIDER') ?: ''),
        // Submissions older than this are permanently deleted by `php bin/admin.php retention`.
        'retention_days' => max(30, (int) (getenv('RETENTION_DAYS') ?: 730)),
    ],
    'storage_path' => dirname(__DIR__) . '/storage/leads',
    'auth_path' => dirname(__DIR__) . '/storage/auth',
    'logs_path' => dirname(__DIR__) . '/storage/logs',
    'admin_username' => (string) ($_ENV['ADMIN_USERNAME'] ?? $_SERVER['ADMIN_USERNAME'] ?? getenv('ADMIN_USERNAME') ?: ''),
    'admin_password_hash' => (string) ($_ENV['ADMIN_PASSWORD_HASH'] ?? $_SERVER['ADMIN_PASSWORD_HASH'] ?? getenv('ADMIN_PASSWORD_HASH') ?: ''),
    'admin_role' => (string) ($_ENV['ADMIN_ROLE'] ?? $_SERVER['ADMIN_ROLE'] ?? getenv('ADMIN_ROLE') ?: 'super_admin'),
    'database_dsn' => (static function (): string {
        $dsn = (string) ($_ENV['DB_DSN'] ?? $_SERVER['DB_DSN'] ?? getenv('DB_DSN') ?: '');
        if ($dsn !== '') {
            return $dsn;
        }
        $dbName = (string) ($_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE')
            ?: $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?: '');
        if ($dbName === '') {
            return '';
        }
        $dbHost = (string) ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost');
        $dbPort = (string) ($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?: '3306');

        return "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    })(),
    'database_username' => (string) ($_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? getenv('DB_USERNAME')
        ?: $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? getenv('DB_USER') ?: ''),
    'database_password' => (string) ($_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD')
        ?: $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? getenv('DB_PASS') ?: ''),
    'founder_video_id' => (string) (getenv('FOUNDER_VIDEO_ID') ?: 'b5ad0QrOyhQ'),
    'employee_scales' => ['1 to 5', '5 to 15', '1 to 50', '50 to 100', '100 to 500', '500+'],
    'attack_types' => [
        'Ransomware',
        'Phishing / account compromise',
        'Data breach / leak',
        'Website defacement / hack',
        'DDoS',
        'Other / not sure',
    ],
    'consultation_topics' => [
        'Security strategy & roadmap',
        'Endpoint / network security',
        'Compliance & audit readiness',
        'SOC / monitoring',
        'Other',
    ],
];
