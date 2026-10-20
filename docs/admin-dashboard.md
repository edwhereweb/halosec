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

If the database is not configured, the legacy environment-configured admin remains available using `ADMIN_USERNAME`, `ADMIN_PASSWORD_HASH`, and `ADMIN_ROLE`. With a database configured, only active accounts with a recognized database role can sign in.

## Hostinger deployment and schema

1. Back up the database before applying changes.
2. Import `/database/migrations/001_admin_dashboard.sql` into the application database using Hostinger phpMyAdmin. The DDL uses MySQL/MariaDB syntax and `IF NOT EXISTS`/`INSERT IGNORE` so it can be safely re-applied.
3. Configure `DB_DSN` (for example, `mysql:host=localhost;dbname=halosec;charset=utf8mb4`), `DB_USERNAME`, and `DB_PASSWORD` in Hostinger's environment configuration. Do not put production secrets in the repository, the web root, or admin-managed settings.
4. Create or manage administrator accounts:
   - **Option A (Web Dashboard)**: Log in with the initial admin credentials (`admin` / `admin123`) or credentials defined in `.env`. Navigate to `/admin/users` to add new team members or update existing passwords directly.
   - **Option B (CLI Helper)**: Generate password hashes or create users via CLI:
     ```sh
     # Generate password hash:
     php bin/admin.php hash "YourSecurePassword123!"

     # Create or reset admin user directly:
     php bin/admin.php create admin "YourSecurePassword123!" super_admin
     ```
   - **Option C (Auto-bootstrap from `.env`)**: Simply set `ADMIN_USERNAME` and `ADMIN_PASSWORD_HASH` in `.env`. HaloSec will automatically seed or sync this account in MySQL on first request.

5. Ensure `storage/` remains writable and outside the public web root. `public/` remains the only web root. No queue worker is required; form writes and admin actions are synchronous.

If DB connection or persistence is temporarily unavailable, form submissions fall back safely to protected storage rather than crashing the website.

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
