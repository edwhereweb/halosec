# Unified frontend submissions

## Frontend-to-admin flow

All current lead forms use `FormController` and `LeadService`. `FormValidator` normalizes accepted input, rejects invalid values, and the honeypot silently discards bot submissions. With MySQL configured, the PDO adapter inserts a `form_submissions` row and its normalized fields in `form_submission_fields` in one transaction. Patterns such as script tags, `javascript:` URLs, and SQL union-select strings create a high-severity security event; the submitted field value remains visible only to authenticated admins and is escaped in HTML views. JSONL remains a legacy storage mode when the database is not enabled.

Built-in keys include `audit`, `enquiry`, `consultation`, `emergency`, `contact`, `newsletter`, and `security_report`. The newsletter and security report routes use the same CSRF, validation, and persistence path as other forms.

## Wiring a new form

1. Add a stable key to `Lead::TYPES` and add the display name to `form_definitions` (or the SQL migration for a newly shipped form).
2. Add the field allowlist, normalization, and validation rules to `FormValidator`; do not persist raw `$_POST` data.
3. Register the GET/POST route in `public/index.php` using `FormController::handle` and an existing/relevant view template.
4. Confirm the form includes the existing CSRF field and honeypot partial. Use the corresponding key as the form type so it appears in admin filters.
5. Add validation and ingestion tests for the new fields and verify that the admin detail template escapes them.

Unknown form keys are intentionally rejected by validation. Avoid accepting arbitrary request keys or HTML as trusted content.

## Admin workflow

The All Inbox filters by form, status, assignee (including unassigned), and date range, and paginates results. Each record has a source key, submitted timestamp, status (`new`, `in_review`, `resolved`, `spam`, `archived`), optional admin assignee, normalized fields, and internal notes. Editors can change status and add notes; assignment, settings, audit, and user administration require stronger roles. Every workflow update writes an audit entry. Assignment IDs must refer to active database users.

CSV export includes the selected filter and a JSON representation of normalized fields. Bulk actions are limited to 100 selected records per request, require CSRF, and accept only supported statuses.
