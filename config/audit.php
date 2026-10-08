<?php

declare(strict_types=1);

return [
    'options' => ['yes' => 'Yes', 'partial' => 'Partially', 'no' => 'No', 'unsure' => 'Not sure'],
    'questions' => [
        'q_firewall' => 'Is your internal network protected by a managed firewall with up-to-date rules?',
        'q_segmentation' => 'Is your internal network segmented (e.g. guest Wi-Fi, servers and staff devices separated)?',
        'q_endpoint' => 'Do all laptops, desktops and servers run centrally managed endpoint protection?',
        'q_patching' => 'Are operating systems and critical software patched on a regular schedule?',
        'q_mfa' => 'Is multi-factor authentication enforced for email, VPN and admin accounts?',
        'q_backup' => 'Do you keep tested, offline or immutable backups of critical systems?',
        'q_inventory' => 'Do you maintain an up-to-date inventory of devices and internet-facing infrastructure?',
        'q_monitoring' => 'Are logs and security alerts monitored by someone (in-house or outsourced)?',
        'q_training' => 'Have employees received phishing and security awareness training in the past year?',
        'q_incident' => 'Do you have a documented incident response plan?',
    ],
];
