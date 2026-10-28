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

Submissions are validated server-side. With `DB_DSN` configured, the PDO adapter stores submissions
and normalized fields in MySQL/MariaDB for the admin inbox. Without a DSN, submissions are appended
as JSONL to `storage/leads/<type>.jsonl` using an exclusive file lock. `storage/` must be writable
by the web server and must not be served over HTTP (`public/` is the only web root;
`storage/.htaccess` denies access as a safeguard).

## Admin

Admin accounts live in MySQL. After configuring `DB_*` in `.env`:

```
php bin/admin.php migrate                                   # create/upgrade tables
php bin/admin.php create yourname 'long-password' super_admin
```

- `/admin/login` — sign in (CSRF-protected, generic error messages, session ID regenerated on success,
  5 failed attempts per IP in 15 minutes locks login for the rest of the window; state kept in `storage/auth/`).
- `/admin/account` — change password and set up two-factor authentication (TOTP). `ADMIN_REQUIRE_2FA=true` makes it mandatory.
- `/admin` — operations dashboard.
- `/admin/submissions` — unified frontend submissions inbox (database mode).
- `/admin/users`, `/admin/settings`, `/admin/security`, and `/admin/audit` — role-protected operations pages.
- `/admin/leads` remains available for legacy JSONL browsing.
- `POST /admin/logout` (CSRF-protected) destroys the session and cookie. Sessions expire after 30 minutes idle.

Every protected admin action re-checks authentication and authorization and responds with `Cache-Control: no-store`.
Deployment to Hostinger via Git auto-deployment (setup, cron, releases, rollback): [docs/deploy-hostinger.md](docs/deploy-hostinger.md).
See [docs/admin-dashboard.md](docs/admin-dashboard.md) and [docs/admin-submissions.md](docs/admin-submissions.md) for MySQL setup, roles, migration, and form wiring.

## Layout

`public/` web root · `src/Controllers|Services|Models|Security` · `views/` · `config/` · `storage/` · `tests/`
