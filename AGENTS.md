# AGENTS.md

## Commands

* Install Dependencies: `composer install`
* Lint & Fix: `vendor/bin/php-cs-fixer fix src/`
* Static Analysis: `vendor/bin/phpstan analyse src tests --level=8`
* Test all: `vendor/bin/phpunit`
* Test single file: `vendor/bin/phpunit tests/Unit/SpecificTest.php`
* Dev server: `php -S localhost:8000 -t public/` (local development only) — PHP binary may need full path, e.g. `& "C:\Users\...\php.exe" -S localhost:8000 -t public/`
* CSS Build: `npm install` once, then `npm run build` after changing Tailwind classes in `views/` (writes `public/css/app.css`, which is committed; CI fails if it is stale)

## Testing

* Framework: PHPUnit
* Test location: `tests/` split into `tests/Unit/` and `tests/Integration/`
* Naming: Test files must use the `Test.php` suffix (e.g., `UserServiceTest.php`)
* Fixtures/Mocks: Placed in `tests/Fixtures/`
* Coverage: Run `vendor/bin/phpunit --coverage-html coverage/` (maintain >80% coverage on core domain logic)
* Database Testing: Use a dedicated test database or in-memory SQLite for integration tests to prevent polluting production/dev data.

## Project Structure

* `public/` - **WEB ROOT**. The only directory exposed to the web. Contains `index.php` and compiled assets. (This maps to Hostinger's `public_html`).
* `src/` - PSR-4 autoloaded core PHP codebase.
* `src/Controllers/` - HTTP request routing and handlers
* `src/Models/` - Database abstractions and domain entities
* `src/Services/` - Business logic and third-party API integrations
* `src/Security/` - Authentication, authorization, and CSRF token managers
* `views/` - PHP template files or lightweight template engine views (kept completely out of the web root)
* `views/layout.php` - Global layout: utility bar, nav, mobile action bar, footer. Design tokens live in `tailwind.config.js`, components in `resources/css/app.css`.
* `views/pages/` - Per-route page templates
* `views/partials/` - Reusable components (page_header, halo, partners, field, enquiry_card, flash, submit, etc.)
* `config/` - Configuration arrays and environment variable mappings
* `designref/design.html` - Previous design reference, kept for history. The current design system is described in the Design System section below.
* `storage/` - Logs, cache, and private uploads. **Must be writable** by the web server but completely inaccessible via URL.
* `tests/` - PHPUnit test suites

## Design System ("Quiet Halo")

Calm, product-led design inspired by Apple and Google (large type, generous white space, tiles, clear chips), kept on the HaloSec neon-lime and sky-blue brand. The visual reference is the HaloSec Redesign canvas (Home, Endpoint Security, Under Attack, Free Audit, Mobile). `designref/design.html` is the previous design and is kept only for history. Do not deviate from these tokens without updating this section.

### Where things live
* **Tokens**: `tailwind.config.js` (colors, fonts, shadows, radii, animations)
* **Components**: `resources/css/app.css` (`@layer components`), compiled into `public/css/app.css` by `npm run build`
* **Behaviour**: `public/js/site.js` (progressive enhancement only; every page works without JS)
* `views/layout.php` holds no inline `<style>`.

### Typography
* **Body**: Plus Jakarta Sans 400–800 (`font-sans`)
* **Display**: Space Grotesk 500–700 (`font-display`), used via `.display` / `.h-section` with tight negative tracking

### Colour tokens (`brand-*`)
| Token | Hex | Usage |
|---|---|---|
| `ink` | `#0b0f17` | Text, dark tiles, dark buttons, footer |
| `mist` | `#f5f6f8` | Alternate section background |
| `cta` / `cta-hover` | `#d4f844` / `#c3e62f` | Primary CTA, highlighter marker |
| `neon` | `#ccff00` | Accents on dark (halo arc, labels) |
| `blue` | `#0369a1` | Links, blue tiles, CTA band |
| `red` | `#b42318` | Emergency only (Under attack, call buttons) |
| `body` / `muted` | `#3d4655` / `#5b6474` | Secondary text (both pass 4.5:1 on white and mist) |

### Components (`resources/css/app.css`)
| Class | Purpose |
|---|---|
| `.wrap`, `.section` | Max-width container (`max-w-6xl`) and section spacing |
| `.display`, `.h-section`, `.lead`, `.eyebrow`, `.marker` | Type scale; `.marker` is the lime highlighter behind a word |
| `.btn` + `.btn-lg` / `.btn-sm` + `.btn-cta` / `.btn-dark` / `.btn-blue` / `.btn-outline` / `.btn-white` / `.btn-danger` / `.btn-danger-soft` | Pill buttons (min 44px tall) |
| `.chip` + `.chip-mist` / `.chip-lime` / `.chip-red`, `.dot-live` + `.ping` | Badges and live status dot |
| `.tile` + `.tile-white` / `-mist` / `-ink` / `-blue` / `-sky` / `-lime` / `-cta`, `.tile-hover`, `.tile-title`, `.tile-text` | Bento tiles (28px radius) |
| `.card-form`, `.label`, `.input`, `.opt` (+ `.opt-danger`, `.opt-yes`, `.opt-no`) | Forms; `.opt` is a radio/toggle chip |
| `.hp` | Honeypot field |

### Partials
* `partials/halo` — animated rings behind heroes (`tone`: light / blue / dark)
* `partials/page_header` — inner-page hero
* `partials/partners` — Zoho / Sophos badges; drop official logos into `public/images/partners/` as `zoho-partner.(svg|png|webp)` and `sophos-silver-partner.(svg|png|webp)` and they replace the text badges automatically
* `partials/service_subnav`, `partials/enquiry_card` — service page sticky sub-nav and enquiry form (with optional scope chips)
* `partials/cta_band` — closing blue call-to-action band
* `partials/field` supports `type => 'chips'` (radio chips) plus `hint`, `placeholder`, `autocomplete`

### Layout conventions
* **Global**: utility bar (SOC status + Emergency Hotline) → sticky frosted nav with "Under attack?" and "Free audit" → `<main>` → dark footer. On phones a bottom action bar shows "24/7" call + "Get a free audit" (hidden on `/under-attack`).
* **Under attack** stays one tap away on every page. Red is reserved for emergency UI.
* **Heroes**: white with `partials/halo`, chip, `.display` headline, `.lead`, CTA pair. Emergency hero uses `bg-red-50`.
* **Sections** alternate white and `bg-brand-mist`. Service grids are bento tiles with mixed tile colours.
* **Forms**: `.card-form`, chips instead of selects where options are short, one primary full-width button. The free audit is a 3-step form (`form[data-stepper]`).
* **Motion**: halo spin, float, marquee, rise, grow; all disabled under `prefers-reduced-motion`.

### Editable content blocks (admin → Content & Settings)
`home.hero_headline_1` / `_2` / `_3` (hero lines, `_2` gets the marker), `home.hero_prefix` + `home.hero_title` (Securious tagline), `home.audit_cta_label`, `home.emergency_banner_enabled`.

## SEO

* Page `<title>`, meta description, canonical and robots tags are set in `views/layout.php` from the `title`, `description`, `path` and `noindex` values each controller passes. Every indexable page needs its own description (160 characters or fewer).
* Services: `config/services.php` keys `seo_title`, `meta_description`, `h1`, `related` (slugs shown under "Related services") and `faqs` (rendered on the page and emitted as FAQPage data). Adding a service there adds it to the nav, footer, structured data and sitemap.
* Structured data (JSON-LD) is built by `src/Seo/StructuredData.php`. Address, area served and social profiles come from the `BUSINESS_*` and `SOCIAL_LINKS` env vars and are left out until set.
* `sitemap.xml` is generated by `src/Seo/Sitemap.php`. Add new public pages to its page list.
* `/privacy` stays noindexed and unlinked until `privacy_ready` is set to true in `config/app.php`.

## Code Style & Security

* Standard: PSR-12 coding style enforced by PHP-CS-Fixer.
* Strict Types: Every PHP file must begin with `declare(strict_types=1);`.
* Typing: Enforce parameter types and explicit return types on all functions/methods.
* Database Security: **Always** use PDO with prepared statements. Absolutely no SQL string concatenation.
* XSS Prevention: Escape all HTML output natively using `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
* Passwords: Hash strictly using `password_hash()` with `PASSWORD_DEFAULT`.
* Sessions: Use secure session cookies (`HttpOnly`, `Secure`, `SameSite=Strict`) and call `session_regenerate_id(true)` upon privilege level changes.
* Naming: `camelCase` for variables and methods, `PascalCase` for Classes/Interfaces/Traits.

## Git Workflow & Auto-Deployment

* Branching: Branch from `main`, prefix with `feat/`, `fix/`, or `hotfix/`.
* Commits: Conventional commit format (e.g., `feat: implement secure password reset flow`).
* CI Pipeline: GitHub Actions runs PHPUnit, PHPStan, and linting on every pull request. PRs require a passing CI to merge.
* Deployment: Merging to `main` triggers Hostinger's Git auto-deployment (webhook), which pulls the repo into `public_html`; the root `.htaccess` routes requests into `public/`. Hostinger runs no build step, so new database migrations are applied by `php bin/admin.php migrate` (cron or SSH). See `docs/deploy-hostinger.md`.
* Environment: Never commit `.env` to version control. Maintain an updated `.env.example`. Production secrets are injected directly via Hostinger's File Manager or SSH.
* Build Steps: There are no runtime Composer packages; production runs without `vendor/` using the fallback autoloader in `public/index.php`. Do not add runtime dependencies without adding a build/upload step. Tailwind CSS is compiled locally (`npm run build`) and the output `public/css/app.css` is committed, because Hostinger cannot build it. Never load Tailwind from its CDN.
