# AGENTS.md

## Commands

* Install Dependencies: `composer install`
* Lint & Fix: `vendor/bin/php-cs-fixer fix src/`
* Static Analysis: `vendor/bin/phpstan analyse src tests --level=8`
* Test all: `vendor/bin/phpunit`
* Test single file: `vendor/bin/phpunit tests/Unit/SpecificTest.php`
* Dev server: `php -S localhost:8000 -t public/` (local development only)
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
* `config/` - Configuration arrays and environment variable mappings
* `storage/` - Logs, cache, and private uploads. **Must be writable** by the web server but completely inaccessible via URL.
* `tests/` - PHPUnit test suites

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
