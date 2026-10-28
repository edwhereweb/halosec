# Admin dashboard

## Architecture

Admin routes are registered in `public/index.php` and handled by `AdminController`. Every protected controller action checks the current session and a `RolePolicy` permission. Mutating requests require the existing session CSRF token. Sessions use `HttpOnly`, `SameSite=Strict`, and `Secure` when HTTPS is detected; successful authentication regenerates the session ID. Login attempts are throttled per client IP.

When `DB_DSN` is configured, `PdoLeadStorage` writes normalized submissions and admin workflow data to MySQL/MariaDB with prepared statements. When no DSN is configured, the existing JSONL storage remains available for the legacy read-only lead browser. Database-backed admin authentication and workflow features require the migration below.

Roles:

| Role | Permissions |
|---|---|
| `super_admin` | All dashboard, submission, settings, security, audit, and user-management permissions |
| `admin` | Dashboard, submission read/manage/assign, settings, security, and audit |
| `editor` | Dashboard, submission read/manage, and security |
| `viewer` | Dashboard, submission read, and security |

Admin sign-in requires the database. Only active accounts with a recognized role can sign in, and the role is re-checked on every request, so deactivating or demoting a user takes effect immediately. Login is throttled per client IP (5 failures in 15 minutes). While locked, no password is checked.

## Hostinger deployment and schema

See [deploy-hostinger.md](deploy-hostinger.md) for the full setup. In short:

1. Back up the database before applying changes.
2. Configure the `DB_*` settings in `.env` (outside the web root, `chmod 600`).
3. Run `php bin/admin.php migrate`. It applies `database/migrations/*.sql` in order and records them in `schema_migrations`. A cron job runs it every 15 minutes as a safety net. Web requests never change the schema.
4. Create the first administrator: `php bin/admin.php create <username> '<password>' super_admin` (12+ characters). There is no default account. Accounts that still use the old published `admin123` seed are refused at login until reset.
5. Each admin turns on two-factor authentication under **My Account & 2FA** (`/admin/account`). Set `ADMIN_REQUIRE_2FA=true` to make it mandatory.
6. Keep `storage/` writable and outside the public web root. No queue worker is required.

Recovery: `php bin/admin.php unblock` (login lockout), `php bin/admin.php 2fa-reset <user>` (lost device), `php bin/admin.php reset <user> '<password>'`.

If the database is temporarily unavailable, form submissions fall back to protected file storage instead of failing.

## Dashboard and operations

- `/admin` shows submission KPIs (today, open, spam, high-severity events, and unresolved over 24 hours) plus recent submissions.
- `/admin/submissions` is the All Inbox and filters by submission type, status, assignee (including unassigned), and date range. The grouped admin navigation links directly to each submission type.
- `/admin/submissions/{id}` supports status changes, assignment, internal notes, and note history.
- The inbox supports bulk resolve/spam/archive and CSV export. Exported user data is serialized as CSV and formula-leading values are escaped.
- `/admin/settings` manages the allowlisted non-secret site settings (`site.name`, `site.tagline`, `site.contact_email`, `site.emergency_phone`) and plain-text content blocks. Content is HTML-escaped when used in frontend templates.
- `/admin/users` creates accounts and changes their role/active state. It prevents self-lockout and prevents disabling/demoting the last active super administrator.
- `/admin/security?event=security_events` shows security events; `/admin/security?event=admin_login_failed` shows failed logins. `/admin/audit` lists admin mutations.

## Table glossary

`admin_users`, `admin_roles`, and `admin_user_roles` hold accounts and role assignments. `form_definitions` describes known form keys. `form_submissions` stores form key, timestamp, workflow status, and assignee; `form_submission_fields` stores each normalized value. `admin_submission_notes` holds internal notes. `admin_audit_logs` records admin mutations. `security_events` stores alerts, and `failed_login_attempts` stores hashed IP/account identifiers. `site_settings` and `content_blocks` hold non-secret configuration and text. `admin_sessions` is available for future server-side session persistence; native PHP sessions are currently used.
