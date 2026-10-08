<?php

declare(strict_types=1);

return [
    'endpoint-security' => [
        'title' => 'Endpoint Security Implementation',
        'short' => 'Deploy and tune next-gen endpoint protection across every laptop, server and mobile device.',
        'icon' => 'EDR',
        'intro' => 'We design, deploy and harden endpoint protection so ransomware, malware and malicious '
            . 'insiders are stopped at the device — backed by policy tuning and ongoing health checks.',
        'points' => ['Sophos endpoint rollout and policy design', 'Device hardening and patch posture review', 'Ransomware rollback and isolation', 'Staff onboarding and runbooks'],
    ],
    'dlp-implementation' => [
        'title' => 'DLP Implementation (Data Loss Prevention)',
        'short' => 'Discover, classify and protect sensitive data before it leaves your organisation.',
        'icon' => 'DLP',
        'intro' => 'We map where your sensitive data lives and implement Data Loss Prevention policies across '
            . 'endpoints, email and cloud so confidential information stays where it belongs.',
        'points' => ['Data discovery and classification', 'Policy design for email, USB, web and cloud', 'Incident workflow and reporting', 'Regulatory alignment'],
    ],
    'network-security' => [
        'title' => 'Network Security',
        'short' => 'Firewalls, segmentation and monitoring that keep your internal network defensible.',
        'icon' => 'NET',
        'intro' => 'From perimeter firewalls to internal segmentation and secure remote access, we build '
            . 'networks that limit attacker movement and give you visibility.',
        'points' => ['Firewall and UTM deployment', 'Network segmentation and VPN / zero trust access', 'Wi-Fi and branch security', 'Traffic monitoring and alerting'],
    ],
    'security-testing' => [
        'title' => 'Security Testing',
        'short' => 'Web application penetration testing and offensive assessments with actionable fixes.',
        'icon' => 'PEN',
        'intro' => 'Our testers attack your applications and infrastructure the way real adversaries do, including '
            . 'Web Application Penetration Testing, then hand your team prioritised, reproducible findings.',
        'points' => ['Web application penetration testing (OWASP Top 10)', 'API and mobile app testing', 'Internal / external network testing', 'Retest and remediation guidance'],
    ],
    'soc-setup' => [
        'title' => 'SOC Setup',
        'short' => 'Stand up a Security Operations Center with the tooling, playbooks and people to run it.',
        'icon' => 'SOC',
        'intro' => 'We implement a Security Operations Center end to end: log sources, SIEM / XDR tooling, '
            . 'detection use cases, escalation playbooks and analyst training.',
        'points' => ['SIEM / XDR design and onboarding', 'Detection use cases and alert tuning', 'Incident response playbooks', 'Analyst enablement and handover'],
    ],
    'digital-vulnerability-assessments' => [
        'title' => 'Digital Vulnerability Assessments',
        'short' => 'P-risk score based assessments that rank exposure and show where to act first.',
        'icon' => 'P-R',
        'intro' => 'We scan and analyse your digital footprint and express exposure as a P-risk score, so '
            . 'leadership and engineers share one prioritised view of what to fix first.',
        'points' => ['Asset and exposure discovery', 'P-risk score with severity ranking', 'Executive and technical reports', 'Re-assessment to track improvement'],
    ],
    'security-audits' => [
        'title' => 'Security Audits & Client Domain Vulnerability Research',
        'short' => 'Independent audits and research into your domains for leaks, spoofing and weak points.',
        'icon' => 'AUD',
        'intro' => 'We audit your controls against recognised frameworks and research your client-facing domains '
            . 'for exposed assets, misconfigurations, leaked credentials and impersonation risks.',
        'points' => ['Control and policy audit', 'Domain, DNS and email security research', 'Leaked credential and exposure checks', 'Gap report with remediation roadmap'],
    ],
    'security-team-outsourcing' => [
        'title' => 'Security Team Outsourcing (Hire a Security Team Member)',
        'short' => 'Part-time security professionals for SMBs without an in-house team — for defined hours.',
        'icon' => 'HIRE',
        'intro' => 'Small and mid-sized businesses rarely need a full-time security hire. Engage a HaloSec consultant '
            . 'for a defined number of hours each week or month and get experienced security leadership and hands-on support.',
        'points' => ['Part-time / fixed-hour security consultants', 'Ideal for SMBs without an in-house security team', 'Security officer, analyst or engineer roles', 'Flexible monthly engagement'],
    ],
];
