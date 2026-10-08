# AGENTS.md

## Commands

* Install Dependencies: `composer install`
* Lint & Fix: `vendor/bin/php-cs-fixer fix src/`
* Static Analysis: `vendor/bin/phpstan analyse src tests --level=8`
* Test all: `vendor/bin/phpunit`
* Test single file: `vendor/bin/phpunit tests/Unit/SpecificTest.php`
* Dev server: `php -S localhost:8000 -t public/` (local development only) — PHP binary may need full path, e.g. `& "C:\Users\...\php.exe" -S localhost:8000 -t public/`
* Frontend Build (if mixing JS/CSS): `npm run build`

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
* `views/layout.php` - Global layout: header, nav, Emergency SOC banner, footer. All design tokens live here.
* `views/pages/` - Per-route page templates
* `views/partials/` - Reusable form components (page_header, field, flash, submit, etc.)
* `config/` - Configuration arrays and environment variable mappings
* `designref/design.html` - **Single-file HTML design reference**. All design decisions (colors, CSS classes, section layouts) should be cross-checked against this file.
* `storage/` - Logs, cache, and private uploads. **Must be writable** by the web server but completely inaccessible via URL.
* `tests/` - PHPUnit test suites

## Design System

The design reference is `designref/design.html`. All frontend work must match or extend it. Do not deviate from these tokens without updating this section.

### Typography
* **Body font**: Plus Jakarta Sans (weights 300–800) — loaded via Google Fonts
* **Display / brand font**: Space Grotesk (weights 500, 700) — used for large stat numbers and logo
* Tailwind font aliases: `font-sans` → Plus Jakarta Sans, `font-display` → Space Grotesk

### Colour Palette
| Token | Hex | Usage |
|---|---|---|
| `#1884e8` / `#3ca0fc` | Sky blue | Hero gradient top |
| `#76bdff` / `#d8edff` | Light sky | Hero gradient bottom |
| `#e4fc68` / `#ccff00` | Neon lime | Primary CTA, selection highlight |
| `#c9ef30` / `#d4f844` | Lime hover | CTA hover state |
| `slate-900` | `#0f172a` | Dark section text, dark cards |
| `slate-950` | `#020617` | Footer background |
| `rose-600` | `#e11d48` | Emergency SOC banner background |
| `red-600` / `red-700` | — | Under-attack hero, emergency badge |
| `sky-600` / `sky-700` | — | Primary links, Sophos brand |
| `lime-300` | — | Active nav link color |

Custom brand tokens registered in Tailwind config:
```js
brand: { blue:'#0369a1', sky:'#0ea5e9', dark:'#090e17', lime:'#d9f99d', neon:'#ccff00', accent:'#84cc16' }
```

### Custom CSS Classes (defined in `views/layout.php` `<style>` block)
| Class | Purpose |
|---|---|
| `.sky-hero-backdrop` | Full sky gradient background for hero sections |
| `.cloud-layer` | Bottom white radial gradient cloud fade |
| `.cloud-puff-left` | Left atmospheric cloud blob (blur) |
| `.cloud-puff-right` | Right atmospheric cloud blob (blur) |
| `.perspective-container` | 3D perspective wrapper for tilt card arc |
| `.tilt-left-far` | Far-left 3D tilt (rotateY 20deg, rotateZ -3deg, scale 0.9) |
| `.tilt-left` | Near-left 3D tilt (rotateY 12deg, rotateZ -1.5deg, scale 0.96) |
| `.tilt-center` | Center elevated card (translateY -8px, scale 1.03) |
| `.tilt-right` | Near-right 3D tilt (mirror of tilt-left) |
| `.tilt-right-far` | Far-right 3D tilt (mirror of tilt-left-far) |
| `.tilt-card` | Hover override — flattens any tilt to front-face on hover |
| `.status-pulse` | `pulseSoft` animation for emergency alert icon |
| `.hero-title` | Text shadow contrast definition for hero headlines |
| `.hero-lead` | Text shadow contrast definition for hero subtitle/lead text |
| `.hp` | Honeypot field — visually hidden (accessibility safe) |

Custom shadow tokens registered in Tailwind:
* `shadow-card-glass` — soft card glass shadow
* `shadow-floating-sky` — elevated sky-tinted shadow for hero tilt cards

### Layout Conventions
* **Max width**: `max-w-7xl` for main page sections, `max-w-6xl` for content-heavy sections, `max-w-5xl` for 2-column contact layouts, `max-w-3xl` for single-column form pages
* **Hero sections**: Always use `.sky-hero-backdrop` + `.cloud-layer` + `.cloud-puff-left` + `.cloud-puff-right` + `rounded-b-[48px]` + `shadow-2xl`
* **Emergency / under-attack hero**: Use `bg-gradient-to-br from-red-700 via-red-600 to-rose-600` with `rounded-b-[48px]`
* **Form cards**: `rounded-3xl bg-white border border-slate-200 shadow-lg p-8` with feature pill badges (`✓ ...`) at the top
* **Service/feature cards**: `rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-sky-300 transition-all duration-200`
* **Bento stat grid**: `md:grid-cols-12` with col-span-4 for each of 3 columns; stacked cards in the right col
* **CTA buttons (primary)**: `bg-[#e4fc68] hover:bg-[#c9ef30] text-slate-900 font-bold text-xs uppercase px-7 py-3.5 rounded-full`
* **CTA buttons (dark ghost)**: `bg-black/25 hover:bg-black/35 backdrop-blur-md text-white border border-white/30`
* **Pill badge (hero)**: `inline-flex items-center gap-2 bg-white/20 backdrop-blur-md border border-white/40 px-3.5 py-1.5 rounded-full`
* **Animate-ping badge dot**: Use `<span class="relative flex h-2 w-2">` with animate-ping outer + solid inner span

### Global Layout Sections (all pages)
1. **Header** — absolute top, frosted glass nav capsule, logo wordmark, CTA pair (`Under Attack?` & `Free Audit ↗`)
2. **Page content** (`<main id="main">`)
3. **Footer** — `bg-slate-950`, 4-column grid (brand 2-col with tagline & accredited partnerships, core services links, get-in-touch links) with copyright bar

### Component Conventions
* **Get Started Cards** (Home page): 3-column card grid with distinctive color accents (Free audit with neon lime CTA, Consultation with dark CTA, Under Attack with red alert card).
* **Accredited Partnerships**: Clean partner cards (`Zoho Partners` & `Sophos Silver Partners`) with hover border transitions (`hover:border-sky-300`).
* **Founders Note CTA**: Dark container (`bg-slate-900 rounded-3xl`) with ambient blur glow and neon lime CTA button.

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
* Deployment: Merging to `main` triggers an automated GitHub Action (via FTP/SSH) or Hostinger Git Webhook directly to the shared hosting environment.
* Environment: Never commit `.env` to version control. Maintain an updated `.env.example`. Production secrets are injected directly via Hostinger's File Manager or SSH.
* Build Steps: Post-deployment scripts must execute `composer install --no-dev --optimize-autoloader` to ensure a lightweight, fast production vendor directory.
