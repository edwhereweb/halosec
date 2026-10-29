# On-page SEO report: all public pages

Checked 2026-10-29 by rendering every public URL on the local dev server and reading the HTML (title, meta description, H1 to H3, images, links inside `<main>`, JSON-LD types).

## Site-wide baseline (already good)

- Every indexable page has a unique `<title>`, a meta description of 160 characters or fewer, one H1, a canonical tag and JSON-LD (Organization, WebSite, ProfessionalService, plus page-specific types: Service, FAQPage, Article, BreadcrumbList, CollectionPage, AboutPage, ContactPage).
- URLs are short, hyphenated and keyword-bearing (`/services/endpoint-security`, `/guides/dpdp-act-compliance-checklist`).
- The only image with empty `alt` is the decorative halo mark (`halosec-mark-light.png`), which is correct.
- `/privacy` is noindexed until `privacy_ready` is true, as intended.

## Summary

| Page | Target keyword (inferred) | Before | After | Changed |
|---|---|---|---|---|
| `/` | cybersecurity services | 88 | 88 | none needed |
| `/services` | cybersecurity services for businesses | 70 | 76 | title |
| `/services/endpoint-security` | endpoint security solutions | 86 | 88 | FAQ heading |
| `/services/dlp-implementation` | DLP implementation / data loss prevention | 72 | 78 | title, H2 |
| `/services/network-security` | network security services | 76 | 78 | H2 |
| `/services/security-testing` | VAPT / penetration testing services | 78 | 80 | H2 |
| `/services/soc-setup` | SOC setup / security operations center | 80 | 82 | H2 |
| `/services/digital-vulnerability-assessments` | vulnerability assessment services | 68 | 78 | title, description, H2 |
| `/services/security-audits` | security audit services | 74 | 78 | description, H2 |
| `/services/security-team-outsourcing` | outsourced security team | 84 | 84 | none needed |
| `/free-audit` | free cybersecurity audit | 84 | 84 | none needed |
| `/consultation` | cybersecurity consultation | 66 | 78 | H1, internal links |
| `/under-attack` | cyber attack incident response | 82 | 82 | none (kept focused on the emergency) |
| `/about` | about HaloSec | 62 | 76 | title, description, internal links |
| `/contact` | contact HaloSec | 72 | 78 | title, internal links |
| `/guides` | cybersecurity guides | 60 | 76 | H1, description (was inaccurate) |
| `/guides/dpdp-act-compliance-checklist` | DPDP Act compliance checklist | 90 | 92 | step heading text |

Scores are estimates of on-page factors only (no backlinks or speed).

---

## Fixes applied

### `/services`
- **Title before**: `Cybersecurity Services | HaloSec` (32 chars, too short)
- **Title after**: `Cybersecurity Services for Growing Businesses | HaloSec` (55)

### `/services/dlp-implementation`
- **Title before**: `DLP Implementation (Data Loss Prevention) | HaloSec`
- **Title after**: `DLP Implementation Services: Data Loss Prevention | HaloSec` (59)
- **H2**: "What’s included." now reads "What’s included in Data Loss Prevention." to search engines and screen readers (the extra words are `sr-only`, same pattern the site already uses on H1s). This applies to every service page that uses the shared template (DLP, network, testing, SOC, vulnerability assessments, audits).

### `/services/digital-vulnerability-assessments`
- **Title before**: `Digital Vulnerability Assessments | HaloSec` (43)
- **Title after**: `Vulnerability Assessment Services & P-Risk Scoring | HaloSec` (60)
- **Description before**: `Digital vulnerability assessments that rank your exposure with a P-risk score, with executive and technical reports showing what to fix first.`
- **Description after**: `Vulnerability assessment services that rank your digital exposure with a P-risk score, plus executive and technical reports showing what to fix first.` (150; leads with the searched phrase)

### `/services/security-audits`
- **Description before**: `Independent security audits plus research into your domains, DNS and email for leaked credentials, spoofing risks and misconfigurations.` (136)
- **Description after**: `Independent cybersecurity audits against recognised frameworks, plus domain, DNS and email checks for leaked credentials, spoofing and misconfigurations.` (153)

### `/services/endpoint-security`
- **H2 before**: `Questions` → **after**: `Questions about endpoint security` (suffix is `sr-only`; visible heading unchanged)

### `/consultation`
- **H1 before**: `Book a consultation` → **after**: `Book a cybersecurity consultation` (matches the title keyword)
- **Internal links**: added a line under "What you’ll get" linking to the free audit, services and guides (was 0 links in the page body).

### `/about`
- **Title before**: `About HaloSec & Our Founders | HaloSec` (brand twice) → **after**: `About Us: Founders, Vision & Values | HaloSec` (45)
- **Description after**: `Why HaloSec exists: honest, expert and accessible cybersecurity for growing businesses. Read our founders’ note, vision, values and accredited partnerships.` (156)
- **Internal links**: the founders’ note now links to endpoint security, penetration testing, SOC setup, security team outsourcing and all services (was 1 link).

### `/contact`
- **Title before**: `Contact HaloSec Cybersecurity | HaloSec` (brand twice) → **after**: `Contact Us: Cybersecurity Enquiries | HaloSec` (45)
- **Internal links**: added links to services and consultation.

### `/guides`
- **H1 before**: `Security, explained plainly.` → **after**: `Cybersecurity guides, explained plainly.`
- **Description before** promised guides on ransomware response, endpoint security, VAPT and SOC that do not exist yet (a mismatch Google can treat as misleading).
- **Description after**: `Plain-language cybersecurity and compliance guides for Indian businesses with 5 to 500 people, starting with a step-by-step DPDP Act compliance checklist.` (154). Update it when new guides ship.

### `/guides/dpdp-act-compliance-checklist`
- The 12 step headings rendered as text like "1Map the personal data you hold" because the number badge sat flush against the words. Added a space so the heading text reads "1 Map the personal data you hold". No visual change (the heading is a flex row).

---

## Recommended next (needs your input)

These need business facts or new copy, so I did not invent them.

### 1. Add FAQs to the six service pages that have none
Only endpoint security has FAQs (and FAQPage structured data). Adding 3 to 5 FAQs per service in `config/services.php` → `faqs` gives each page a rich-result and AI-answer opportunity. Suggested questions; answers need your real numbers:

- **DLP**: How long does a DLP rollout take? Does DLP work with Microsoft 365 / Google Workspace? Will DLP slow down staff? Does DLP help with DPDP Act compliance?
- **Network security**: Which firewall brands do you deploy? Can you secure branch offices and remote staff? What is zero trust access?
- **Security testing (VAPT)**: What is the difference between a vulnerability assessment and a penetration test? How long does a web app pen test take? Do you retest after fixes? Is the report accepted for audits / client due diligence?
- **SOC setup**: Build our own SOC or use a managed SOC? Which SIEM / XDR tools do you work with? How long does SOC setup take?
- **Vulnerability assessments**: What is a P-risk score? How often should we run an assessment? What do the executive and technical reports include?
- **Security audits**: Which frameworks do you audit against (ISO 27001, CERT-In, DPDP)? What does domain vulnerability research find? How long does an audit take?

### 2. Thicken the thinnest pages
- `/services` hub (about 210 words): add a short intro (who you help, how to choose) and a "which service do I need?" block.
- Network security, security testing and vulnerability assessments (about 290 words each): add "Who it’s for", "What you receive" (deliverables) and one short client example, like the endpoint page.

### 3. Decide on location targeting
Copy targets Indian businesses (DPDP, `en_IN`). Once the `BUSINESS_*` address env vars are set, consider adding the city or "India" to the home and services titles if you mainly sell locally.

### 4. Grow the guides hub
`/guides` has one guide. The topics the old description promised (ransomware response, endpoint security vs antivirus, VAPT explained, running a SOC) are good next guides; each one links into a service page.

### 5. Optional
Per-page Open Graph images (all pages share `og-default.png`), so shared links to service pages and guides look distinct on LinkedIn and WhatsApp.
