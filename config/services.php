<?php

declare(strict_types=1);

return [
    'endpoint-security' => [
        'faqs' => [
            ['How many systems do I need to get started?', 'A minimum of 5 systems.'],
            ['How is this different from normal antivirus?', 'Normal antivirus works alone on single PCs. Endpoint Security gives you central control over all systems, ransomware rollback and strict policy management from one place.'],
            ['Can I block USB drives and external devices?', 'Yes. You can centrally block USB ports, web access, cameras and unapproved software across your company.'],
        ],
        'related' => ['dlp-implementation', 'network-security', 'security-team-outsourcing'],
        'nav_label' => 'Endpoint Security',
        'meta_description' => 'Centrally managed endpoint security for 5 to hundreds of devices: ransomware rollback, USB and web control, zero-day protection and custom group policies.',
        'title' => 'Endpoint Security Solutions for Your Business',
        'short' => 'Centralized endpoint defense, ransomware rollback, and strict policy management from 5 to hundreds of systems.',
        'icon' => 'EDR',
        'intro' => 'Looking to secure your company’s devices and data without the headache of managing each system manually? Our Endpoint Security Services give you total control, strong protection, and total peace of mind—whether you have 5 systems or hundreds.',
        'points' => [
            'Centrally Managed: Single dashboard control across 5 to hundreds of systems',
            'Ransomware Protection & Rollback: Stops encryption and restores files safely',
            'Total Device & Web Control: Centrally block USBs, cameras, and PowerShell',
            'Protection Against New Threats: Zero-day threat and unpatched exploit defense',
            'Custom Group Policies: Tailored access levels and confidentiality rules',
        ],
    ],
    'dlp-implementation' => [
        'related' => ['endpoint-security', 'security-audits', 'network-security'],
        'nav_label' => 'Data Loss Prevention',
        'meta_description' => 'Data Loss Prevention implementation: discover and classify sensitive data, then enforce DLP policies across endpoints, email, USB, web and cloud.',
        'title' => 'DLP Implementation (Data Loss Prevention)',
        'short' => 'Discover, classify and protect sensitive data before it leaves your organisation.',
        'icon' => 'DLP',
        'intro' => 'We map where your sensitive data lives and implement Data Loss Prevention policies across '
            . 'endpoints, email and cloud so confidential information stays where it belongs.',
        'points' => ['Data discovery and classification', 'Policy design for email, USB, web and cloud', 'Incident workflow and reporting', 'Regulatory alignment'],
    ],
    'network-security' => [
        'h1' => 'Network Security Services',
        'related' => ['endpoint-security', 'soc-setup', 'security-testing'],
        'nav_label' => 'Network Security',
        'seo_title' => 'Network Security Services: Firewalls & Zero Trust',
        'meta_description' => 'Firewall and UTM deployment, network segmentation, VPN and zero trust access, Wi-Fi and branch security, with traffic monitoring and alerting.',
        'title' => 'Network Security',
        'short' => 'Firewalls, segmentation and monitoring that keep your internal network defensible.',
        'icon' => 'NET',
        'intro' => 'From perimeter firewalls to internal segmentation and secure remote access, we build '
            . 'networks that limit attacker movement and give you visibility.',
        'points' => ['Firewall and UTM deployment', 'Network segmentation and VPN / zero trust access', 'Wi-Fi and branch security', 'Traffic monitoring and alerting'],
    ],
    'security-testing' => [
        'h1' => 'Penetration Testing & Security Testing',
        'related' => ['digital-vulnerability-assessments', 'security-audits', 'network-security'],
        'nav_label' => 'Security Testing',
        'seo_title' => 'VAPT & Penetration Testing Services',
        'meta_description' => 'Web application, API, mobile and network penetration testing (OWASP Top 10) with prioritised, reproducible findings, remediation guidance and retests.',
        'title' => 'Security Testing',
        'short' => 'Web application penetration testing and offensive assessments with actionable fixes.',
        'icon' => 'PEN',
        'intro' => 'Our testers attack your applications and infrastructure the way real adversaries do, including '
            . 'Web Application Penetration Testing, then hand your team prioritised, reproducible findings.',
        'points' => ['Web application penetration testing (OWASP Top 10)', 'API and mobile app testing', 'Internal / external network testing', 'Retest and remediation guidance'],
    ],
    'soc-setup' => [
        'h1' => 'SOC Setup: Build Your Security Operations Center',
        'related' => ['security-team-outsourcing', 'network-security', 'endpoint-security'],
        'nav_label' => 'SOC Setup',
        'seo_title' => 'SOC Setup: Build Your Security Operations Center',
        'meta_description' => 'End-to-end Security Operations Center setup: SIEM / XDR onboarding, detection use cases, alert tuning, incident response playbooks and analyst training.',
        'title' => 'SOC Setup',
        'short' => 'Stand up a Security Operations Center with the tooling, playbooks and people to run it.',
        'icon' => 'SOC',
        'intro' => 'We implement a Security Operations Center end to end: log sources, SIEM / XDR tooling, '
            . 'detection use cases, escalation playbooks and analyst training.',
        'points' => ['SIEM / XDR design and onboarding', 'Detection use cases and alert tuning', 'Incident response playbooks', 'Analyst enablement and handover'],
    ],
    'digital-vulnerability-assessments' => [
        'related' => ['security-testing', 'security-audits', 'endpoint-security'],
        'nav_label' => 'Vulnerability Assessments',
        'meta_description' => 'Digital vulnerability assessments that rank your exposure with a P-risk score, with executive and technical reports showing what to fix first.',
        'title' => 'Digital Vulnerability Assessments',
        'short' => 'P-risk score based assessments that rank exposure and show where to act first.',
        'icon' => 'P-R',
        'intro' => 'We scan and analyse your digital footprint and express exposure as a P-risk score, so '
            . 'leadership and engineers share one prioritised view of what to fix first.',
        'points' => ['Asset and exposure discovery', 'P-risk score with severity ranking', 'Executive and technical reports', 'Re-assessment to track improvement'],
    ],
    'security-audits' => [
        'related' => ['digital-vulnerability-assessments', 'security-testing', 'dlp-implementation'],
        'nav_label' => 'Security Audits',
        'seo_title' => 'Security Audits & Domain Vulnerability Research',
        'meta_description' => 'Independent security audits plus research into your domains, DNS and email for leaked credentials, spoofing risks and misconfigurations.',
        'title' => 'Security Audits & Client Domain Vulnerability Research',
        'short' => 'Independent audits and research into your domains for leaks, spoofing and weak points.',
        'icon' => 'AUD',
        'intro' => 'We audit your controls against recognised frameworks and research your client-facing domains '
            . 'for exposed assets, misconfigurations, leaked credentials and impersonation risks.',
        'points' => ['Control and policy audit', 'Domain, DNS and email security research', 'Leaked credential and exposure checks', 'Gap report with remediation roadmap'],
    ],
    'security-team-outsourcing' => [
        'related' => ['soc-setup', 'endpoint-security', 'digital-vulnerability-assessments'],
        'nav_label' => 'Security Team Outsourcing',
        'seo_title' => 'Outsourced Security Team: Hire a Security Expert',
        'meta_description' => 'Hire certified cybersecurity experts (CEH, CompTIA, Sophos) hourly or monthly. Flexible outsourced security team support for SMBs without a full-time hire.',
        'title' => 'Security Team Outsourcing (Hire a Security Team Member)',
        'short' => 'Flexible cybersecurity experts for SMBs. Pay hourly or monthly for certified talent without a full-time hire.',
        'icon' => 'HIRE',
        'intro' => 'Hiring a full-time, in-house security team is way too expensive for most growing businesses. Halosec offers flexible Security Team Outsourcing—hire certified cybersecurity experts tailored to your exact scope and budget, only when you need them.',
        'points' => [
            'Certified Engineers: CEH, CompTIA Security+/CySA+, Cloud, and Sophos certified',
            '100% Customized: Choose exact skills, certifications, and support level needed',
            'Flexible Plans: Pay hourly or set up fixed-hour monthly engagements',
            'Scalable Support: Easily scale up or down as your business workload shifts',
        ],
    ],
];
