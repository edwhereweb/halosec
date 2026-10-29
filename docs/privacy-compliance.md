# Privacy compliance (DPDP Act 2023 / DPDP Rules 2025 and GDPR)

What the website does to meet India's Digital Personal Data Protection Act and the EU/UK GDPR, and what the business still has to do. This is engineering documentation, not legal advice: have the privacy policy reviewed before setting `privacy_ready` to true.

## What the site does

| Requirement | How the site meets it | Where |
|---|---|---|
| Notice at the point of collection (DPDP s.5, GDPR Art. 13) | Every public form shows a short notice: purpose, retention, no selling, how to see/correct/delete, link to the policy once it is live | `views/partials/privacy_notice.php` |
| Proof of what the person was shown (DPDP s.6(10), GDPR Art. 7(1)) | Each submission stores `privacy_notice` = the notice version (a date) | `config/app.php` → `privacy.notice_version`, `LeadService` |
| Data minimisation | Forms ask only for what is needed; no IP stored with enquiries; failed admin logins store a hashed IP; form throttle files expire within 24 h | `FormValidator`, `LoginThrottle`, `prune` cron |
| No third-party transfers on page load | Fonts are self-hosted; the YouTube video loads only after the visitor presses play; no analytics or ad cookies | `public/fonts`, `resources/css/app.css`, `views/pages/home.php`, `public/js/site.js` |
| Cookies | Only `halosec_session` (strictly necessary, so no consent banner is needed) | `src/Security/SessionManager.php` |
| Retention / storage limitation (DPDP s.8(7), GDPR Art. 5(1)(e)) | `php bin/admin.php retention` deletes submissions older than `RETENTION_DAYS` (default 730) from the database and the fallback files; run daily from cron | `bin/admin.php`, `docs/deploy-hostinger.md` |
| Right of access / portability (DPDP s.11, GDPR Art. 15, 20) | Admin → submission → **Download their data (JSON)**: every submission and note for that email. CLI: `privacy-export <email>` | `AdminController::exportPersonData` |
| Right to erasure (DPDP s.12, GDPR Art. 17) | Admin → submission → **Delete permanently** (this request or all from the email; type DELETE to confirm). CLI: `privacy-erase <email> --yes`. Removes fields and notes; the audit log keeps only the reference and reason | `AdminController::erasePersonData`, `PdoLeadStorage::eraseSubmissions` |
| Correction | Reply to the person and record the correction as a note, or erase and ask them to resubmit | Admin inbox |
| Withdraw consent | Newsletter unsubscribe link; erasure on request | Newsletter emails, admin |
| Security safeguards (DPDP s.8(5), Rule 6; GDPR Art. 32) | HTTPS, private database, roles, 2FA, audit log, CSRF, throttling | `src/Security`, `docs/admin-dashboard.md` |
| Security logs kept ≥ 1 year (DPDP Rule 6) | `admin_audit_logs`, `security_events` and `failed_login_attempts` are never purged by `retention` | Database |
| Who can act on requests | `privacy.manage` permission: Owner and Admin roles only | `src/Security/RolePolicy.php` |

Backups (`storage/backups`) are kept for `BACKUP_KEEP_DAYS` (default 14), so erased data disappears from them within that time. The policy says so.

## What the business still has to do

1. Fill the `[TO COMPLETE]` items on `/privacy`. Most come from `.env`: `BUSINESS_LEGAL_NAME`, `BUSINESS_*` address, `PRIVACY_EMAIL`, `GRIEVANCE_OFFICER_NAME`, `GRIEVANCE_OFFICER_PHONE`, `HOSTING_PROVIDER`. The email provider line is in `views/pages/privacy.php`.
2. Have the policy reviewed, then set `'privacy_ready' => true` in `config/app.php`. That links it from the footer and every form notice and adds it to the sitemap.
3. Add the `retention` cron job on Hostinger (see `docs/deploy-hostinger.md`).
4. Sign or accept data processing agreements with Hostinger and the email provider.
5. Handle requests: acknowledge quickly, verify the person (reply from the address they used), respond within one month (GDPR) and resolve grievances within 90 days at most (DPDP Rules).
6. Keep a breach response plan: tell affected people without delay, report to the Data Protection Board of India (initial report without delay, full report within 72 hours) and, for EU/UK people, the relevant authority within 72 hours.
7. Delete copies outside the site too (mailbox, CRM, spreadsheets exported from the admin CSV) when someone asks for erasure.
8. Newsletter emails must carry a working unsubscribe link, and unsubscribes must be removed from the mailing tool.

## When you change something

* New form or new field: say why you need it, add it to "What we collect" on `/privacy`, and include `partials/privacy_notice` above the submit button.
* New third-party script, embed, font or analytics tool: it must not load before the visitor opts in, and it must be listed under "Cookies and third parties". Non-essential cookies need a consent banner first.
* Notice or policy wording changed: bump `privacy.notice_version` in `config/app.php` (it is also the policy's "Last updated" date).
