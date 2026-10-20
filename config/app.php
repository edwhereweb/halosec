<?php

declare(strict_types=1);

return [
    'name' => 'HaloSec',
    'tagline' => 'The Aura of Security for Your Business',
    'app_env' => getenv('APP_ENV') ?: 'production',
    'app_url' => getenv('APP_URL') ?: 'https://halosec.in',
    'emergency_phone' => getenv('EMERGENCY_PHONE') ?: '+917356543520',
    'contact_email' => getenv('CONTACT_EMAIL') ?: 'hello@halosec.in',
    'storage_path' => dirname(__DIR__) . '/storage/leads',
    'auth_path' => dirname(__DIR__) . '/storage/auth',
    'logs_path' => dirname(__DIR__) . '/storage/logs',
    'admin_username' => (string) getenv('ADMIN_USERNAME'),
    'admin_password_hash' => (string) getenv('ADMIN_PASSWORD_HASH'),
    'admin_role' => (string) (getenv('ADMIN_ROLE') ?: 'super_admin'),
    'database_dsn' => (string) getenv('DB_DSN'),
    'database_username' => (string) getenv('DB_USERNAME'),
    'database_password' => (string) getenv('DB_PASSWORD'),
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
