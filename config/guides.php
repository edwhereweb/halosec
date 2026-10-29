<?php

declare(strict_types=1);

/*
 * Security guides shown at /guides and /guides/{slug}.
 *
 * Each guide needs a body template at views/guides/{slug with _ instead of -}.php.
 * Keys:
 *   title            H1 and page name
 *   seo_title        <title> (optional, falls back to title)
 *   meta_description 160 characters or fewer
 *   category         cluster name shown as a chip and used to group the /guides list
 *   lead             one or two sentences under the H1, also the card text on /guides
 *   published / updated  Y-m-d, shown on the page and used in Article structured data
 *   read_minutes     rough reading time
 *   service          main service slug from config/services.php (the guide's call to action)
 *   related_services more service slugs to link at the end
 *   summary          "Key takeaways" bullets at the top (short, factual: what AI answers quote)
 *   faqs             [question, answer] pairs, rendered on the page and emitted as FAQPage data
 *
 * Adding a guide here adds it to /guides, the sitemap and the "Guides" block on its service page.
 */
return [
    'dpdp-act-compliance-checklist' => [
        'title' => 'DPDP Act Compliance Checklist for Small Businesses',
        'seo_title' => 'DPDP Act Compliance Checklist for SMBs (2026)',
        'meta_description' => 'A plain-language DPDP Act checklist for Indian SMBs: what the 2025 Rules require, the May 2027 deadline, breach reporting, penalties and 12 steps to comply.',
        'category' => 'Compliance',
        'lead' => 'What India’s Digital Personal Data Protection Act and the 2025 Rules mean for a company with 5 to 500 people, and the 12 steps to be ready before the main obligations apply in May 2027.',
        'published' => '2026-10-29',
        'updated' => '2026-10-29',
        'read_minutes' => 11,
        'service' => 'security-audits',
        'related_services' => ['dlp-implementation', 'endpoint-security', 'soc-setup'],
        'summary' => [
            'The DPDP Act, 2023 applies to almost every Indian business that holds customer or employee data in digital form. There is no general small-business exemption.',
            'The DPDP Rules were notified in November 2025. Most obligations, including security safeguards and breach reporting, apply 18 months later, around May 2027.',
            'You must notify affected people and the Data Protection Board of a personal data breach without delay, with a detailed report to the Board within 72 hours.',
            'Penalties go up to ₹250 crore for failing to take reasonable security safeguards and ₹200 crore for failing to report a breach.',
            'Most of the work is knowing what data you hold, collecting clear consent, securing it, and deleting what you no longer need.',
        ],
        'faqs' => [
            [
                'Does the DPDP Act apply to small businesses?',
                'Yes. The Act applies to any organisation that processes digital personal data in India, whatever its size. The government can exempt some startups from certain duties, but there is no blanket exemption for small businesses.',
            ],
            [
                'When does the DPDP Act come into force?',
                'The Act was passed in August 2023 and the DPDP Rules were notified in November 2025. The Data Protection Board provisions started straight away, consent manager rules follow after 12 months, and most obligations on businesses apply after 18 months, around May 2027.',
            ],
            [
                'Do I need a Data Protection Officer under the DPDP Act?',
                'Only Significant Data Fiduciaries, a category the government designates for large or high-risk processors, must appoint a Data Protection Officer based in India. Every other business must publish the contact details of a person who can answer questions about how it handles personal data.',
            ],
            [
                'How quickly must a data breach be reported under the DPDP Rules?',
                'Affected individuals and the Data Protection Board must be told without delay. A detailed report, covering what happened, the likely impact and what you have done about it, must reach the Board within 72 hours of you becoming aware of the breach.',
            ],
            [
                'Is CERT-In reporting still required if I report under the DPDP Act?',
                'Yes. The CERT-In directions of 2022 still require many cyber incidents to be reported to CERT-In within 6 hours of noticing them. A personal data breach may need to be reported to both CERT-In and the Data Protection Board.',
            ],
            [
                'What are the penalties under the DPDP Act?',
                'The highest penalty is up to ₹250 crore for failing to take reasonable security safeguards to prevent a breach. Failing to notify a breach can cost up to ₹200 crore, as can breaking the rules on children’s data. Other breaches of the Act carry up to ₹50 crore.',
            ],
        ],
    ],
];
