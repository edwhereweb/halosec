<?php

declare(strict_types=1);

return [
    'name' => 'HaloSec',
    'tagline' => 'The Aura of Security for Your Business',
    'emergency_phone' => getenv('EMERGENCY_PHONE') ?: '+917356543520',
    'contact_email' => getenv('CONTACT_EMAIL') ?: 'hello@halosec.example',
    'storage_path' => dirname(__DIR__) . '/storage/leads',
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
