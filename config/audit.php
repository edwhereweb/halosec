<?php

declare(strict_types=1);

return [
    'options' => ['yes' => 'Yes', 'partial' => 'Partially', 'no' => 'No', 'unsure' => 'Not sure'],
    // Full wording, stored with each submission and shown to the team in the admin area.
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
    // What visitors see on the audit page: a short area, a plain-language question and why it matters.
    'details' => [
        'q_firewall' => [
            'area' => 'Network', 'title' => 'Firewall',
            'ask' => 'Is your office network behind a managed firewall with up-to-date rules?',
            'why' => 'A firewall is your front door. Old rules leave it propped open.',
        ],
        'q_segmentation' => [
            'area' => 'Network', 'title' => 'Network zones',
            'ask' => 'Are guest Wi-Fi, servers and staff devices kept on separate networks?',
            'why' => 'If one laptop gets infected, separate zones stop it reaching everything else.',
        ],
        'q_endpoint' => [
            'area' => 'Devices', 'title' => 'Device protection',
            'ask' => 'Does every laptop, desktop and server run centrally managed protection?',
            'why' => 'Ransomware usually starts on a single unprotected device.',
        ],
        'q_patching' => [
            'area' => 'Devices', 'title' => 'Software updates',
            'ask' => 'Are Windows, apps and critical software updated on a regular schedule?',
            'why' => 'Most breaches use holes that already had a fix available.',
        ],
        'q_mfa' => [
            'area' => 'Accounts', 'title' => 'Two-step sign-in',
            'ask' => 'Do email, VPN and admin accounts need a second step, like a code on your phone?',
            'why' => 'It stops the vast majority of stolen-password attacks.',
        ],
        'q_backup' => [
            'area' => 'Recovery', 'title' => 'Backups',
            'ask' => 'Do you keep tested backups that ransomware can’t reach (offline or immutable)?',
            'why' => 'Good backups turn a ransomware disaster into a bad afternoon.',
        ],
        'q_inventory' => [
            'area' => 'Visibility', 'title' => 'Asset list',
            'ask' => 'Do you keep an up-to-date list of your devices and internet-facing systems?',
            'why' => 'You can’t protect what you don’t know you have.',
        ],
        'q_monitoring' => [
            'area' => 'Visibility', 'title' => 'Monitoring',
            'ask' => 'Does someone watch your security alerts and logs, in-house or outsourced?',
            'why' => 'Attackers often hide inside for weeks. Someone watching catches them early.',
        ],
        'q_training' => [
            'area' => 'People', 'title' => 'Phishing training',
            'ask' => 'Has your team had phishing or security awareness training in the past year?',
            'why' => 'Most attacks start with one convincing email to one person.',
        ],
        'q_incident' => [
            'area' => 'Recovery', 'title' => 'Incident plan',
            'ask' => 'Do you have a written plan for what to do during a cyber attack?',
            'why' => 'In a crisis, a one-page plan saves hours when every minute counts.',
        ],
    ],
];
