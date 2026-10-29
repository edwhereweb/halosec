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
    'sophos-endpoint-price-india' => [
        'title' => 'Sophos Endpoint Price in India (2026): Editions, Licensing and How to Buy',
        'seo_title' => 'Sophos Endpoint Price in India: 2026 Guide',
        'meta_description' => 'Sophos Endpoint price in India for 2026: the new editions, what a user licence covers, typical partner prices and how to buy from a Sophos partner in Kochi.',
        'category' => 'Endpoint',
        'lead' => 'Intercept X has a new name, Essentials is gone, and quotes vary a lot between partners. What Sophos Endpoint costs an Indian business in 2026, which edition to pick, and how buying, setup and support work.',
        'published' => '2026-10-30',
        'updated' => '2026-10-30',
        'read_minutes' => 10,
        'service' => 'endpoint-security',
        'related_services' => ['dlp-implementation', 'security-team-outsourcing', 'soc-setup'],
        'author' => [
            'name' => 'Manu Francis',
            'bio' => '10+ years in cybersecurity and IT infrastructure',
        ],
        'disclaimer' => 'Prices are indicative ranges for India as of October 2026, before 18% GST. They vary by partner, edition and number of users, so always get a written quote. Sophos product names and licensing rules are taken from Sophos’s own announcements and licensing policy.',
        'summary' => [
            'Sophos renamed Intercept X Advanced to Sophos Endpoint in late 2025. Intercept X Essentials can no longer be bought or renewed.',
            'Sophos Endpoint is licensed per user, per year. One user licence covers that person’s computers; servers need their own licence.',
            'In India, Sophos Endpoint typically costs about ₹1,500 to ₹4,000 per user per year before GST. The more users you license, the lower the price per user.',
            'A 3-year term rarely costs much less per year, but it locks your price for all three years.',
            'Ransomware rollback, USB and web control, and data loss prevention (DLP) rules are part of Sophos Endpoint, not extra add-ons.',
        ],
        'faqs' => [
            [
                'Is Sophos Intercept X still available in India?',
                'Yes, under a new name. Intercept X Advanced is now sold as Sophos Endpoint, for users and for servers. Intercept X Essentials was discontinued: new orders stopped on 1 November 2025 and renewals on 7 January 2026.',
            ],
            [
                'How much does Sophos Endpoint cost per user in India?',
                'Typically about ₹1,500 to ₹4,000 per user per year before 18% GST, depending on the partner, the edition and how many users you license. Some partners quote from around ₹1,200 for the basic edition. EDR, XDR and MDR cost more and are quoted on your setup.',
            ],
            [
                'Does one Sophos user licence cover more than one computer?',
                'Yes. Sophos licenses Sophos Endpoint per user, and its licensing policy lets each user protect a reasonable number of computers, such as a desktop and a laptop. Servers are licensed separately, per server.',
            ],
            [
                'Is a 3-year Sophos licence cheaper than renewing every year?',
                'Usually only slightly. The real benefit is that the price is locked for three years, so you are protected from yearly price increases.',
            ],
            [
                'Can I try Sophos Endpoint before buying?',
                'Yes. HaloSec sets up a 30-day trial in a Sophos Central account so you can see the dashboard, policies and alerts on your own computers before you pay.',
            ],
            [
                'Does Sophos Endpoint include DLP and USB blocking?',
                'Yes. Sophos Endpoint includes device control for blocking USB drives and other peripherals, web control, and data loss prevention rules that watch where sensitive files go. They need to be set up for your company, which is part of deployment.',
            ],
            [
                'My Intercept X Essentials licence is expiring. What should I do?',
                'Essentials can no longer be renewed, so move to Sophos Endpoint at renewal. You keep the same Sophos Central account and agents; the policies simply gain the extra protection that Essentials lacked.',
            ],
            [
                'Do you support Sophos customers outside Kochi?',
                'Yes. HaloSec supports customers remotely anywhere in India and provides on-site support anywhere in Kerala. For on-site visits outside Kochi, travel costs are billed to the client.',
            ],
        ],
    ],
];
