# HaloSec

Multi-page PHP marketing website for HaloSec — *The Aura of Security for Your Business*.

## Run locally

```
composer install
php -S localhost:8000 -t public/ public/index.php
```

## Quality gates

```
vendor/bin/php-cs-fixer fix src/
vendor/bin/phpstan analyse src tests --level=8
vendor/bin/phpunit
```

## Lead storage

Submissions (audit, service enquiry, consultation, emergency, contact) are validated server-side and
appended as one JSON object per line (JSONL, with timestamp) to `storage/leads/<type>.jsonl`
using an exclusive file lock. `storage/` must be writable by the web server and must not be served
over HTTP (`public/` is the only web root; `storage/.htaccess` denies access as a safeguard).
Storage sits behind `HaloSec\Services\LeadStorageInterface`, so a PDO implementation can replace
`FileLeadStorage` in `public/index.php` without touching controllers.

## Admin

A single admin account is configured through environment variables (no credentials in the repo):

```
php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"   # generate the hash
ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH=<hash from above>
```

If either is unset, admin login is disabled. Quote the hash if your shell/env file treats `$` specially.

- `/admin/login` — sign in (CSRF-protected, generic error messages, session ID regenerated on success,
  5 failed attempts per IP in 15 minutes locks login for the rest of the window; state kept in `storage/auth/`).
- `/admin` — dashboard: lead counts per type and the most recent leads.
- `/admin/leads` — all leads, filterable by type, paginated; `/admin/leads/<type>/<n>` shows one lead.
- `POST /admin/logout` (CSRF-protected) destroys the session and cookie. Sessions expire after 30 minutes idle.

Every admin route re-checks authentication and responds with `Cache-Control: no-store`. Admin pages are read-only.

## Layout

`public/` web root · `src/Controllers|Services|Models|Security` · `views/` · `config/` · `storage/` · `tests/`
