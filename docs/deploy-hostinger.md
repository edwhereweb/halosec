# Deploying to Hostinger (Git auto-deployment)

Hostinger's **Git** feature pulls `main` into `public_html` whenever GitHub sends a webhook. Hostinger only runs `git pull`: no build step, no `composer install`, no database migration.

- **Dependencies.** The app has no runtime Composer packages, and `public/index.php` falls back to its own autoloader, so `vendor/` is not needed on the server.
- **Web root.** The whole repository lands in `public_html`. The root `.htaccess` forwards every request into `public/` and returns 403 for `src/`, `config/`, `storage/`, `vendor/`, `.git/` and the other internal folders.
- **Secrets.** `.env` goes one folder **above** `public_html`, so it is never inside the web root. The app looks there automatically.
- **Database.** Schema changes are applied by `php bin/admin.php migrate`. Run it after a deploy that adds a file to `database/migrations/` (see step 6).

Replace `u123456789` and `halosec.in` below with your account and domain.

## 1. Before you start

- [ ] hPanel → **Files → Backups**: download a files backup and a database backup.
- [ ] hPanel → **Advanced → PHP Configuration**: PHP **8.3**, with `pdo_mysql` and `mbstring` enabled.
- [ ] hPanel → **Databases → MySQL Databases**: a database and user exist. Note the name, user and password.
- [ ] hPanel → **Security → SSL**: certificate installed.
- [ ] hPanel → **Advanced → SSH Access**: enabled. You need it for the `bin/admin.php` commands.

## 2. Connect the repository (one time)

1. hPanel → **Advanced → Git**.
2. Private repository: copy the **SSH key** Hostinger shows. In GitHub → **repo → Settings → Deploy keys → Add deploy key**, paste it with **write access off**.
3. Create the repository entry:
   - Repository: `git@github.com:edwhereweb/halosec.git`
   - Branch: `main`
   - Install path: leave **empty**, which installs into `public_html`. Hostinger requires the folder to be empty, so move any old files out first. Keep the old `public_html/uploads` to copy back later.
4. Click **Deploy** once, then turn on **Auto Deployment**. Copy the **webhook URL** it shows.
5. GitHub → **repo → Settings → Webhooks → Add webhook**: paste the URL, content type `application/json`, event **Just the push event**.

Hostinger deploys on **every push to `main`** and does not wait for CI. Protect the branch so only CI-green PRs reach it: GitHub → **Settings → Branches → Add rule** for `main` → *Require a pull request* and *Require status checks to pass* (select `test`).

## 3. Server configuration (one time, over SSH)

```sh
ssh -p 65002 u123456789@YOUR_SERVER_IP
cd ~/domains/halosec.in

# Secrets live OUTSIDE public_html
cp public_html/.env.example .env
chmod 600 .env
nano .env        # APP_ENV=production, APP_URL, DB_HOST=localhost, DB_NAME, DB_USER, DB_PASSWORD

# Writable private storage (git ignores its contents, so pulls never touch it)
cd public_html
mkdir -p storage/leads storage/logs storage/auth storage/backups storage/uploads
chmod -R 750 storage

# Restore uploads from the old site if there were any
# cp -r ~/old_site/uploads/* public/uploads/
```

Check that `php -v` reports 8.3. If it doesn't, use the full path to Hostinger's PHP 8.3 binary in the commands below and in the cron jobs.

## 4. Database and first admin (one time)

```sh
cd ~/domains/halosec.in/public_html
php bin/admin.php migrate
php bin/admin.php create yourname 'a-long-unique-password' super_admin
```

There is no default account. If `migrate` warns that some accounts use the old `admin123` password, reset each one (`php bin/admin.php reset <user> '<new password>'`) or deactivate it in **Admin Users / Roles**.

## 5. Cron jobs (hPanel → Advanced → Cron Jobs)

| When | Command |
|---|---|
| Every 15 minutes | `cd /home/u123456789/domains/halosec.in/public_html && /usr/bin/php bin/admin.php migrate >> storage/logs/migrate.log 2>&1` |
| Daily 02:30 | `/usr/bin/php /home/u123456789/domains/halosec.in/public_html/bin/admin.php backup` |
| Hourly | `/usr/bin/php /home/u123456789/domains/halosec.in/public_html/bin/admin.php prune` |

The `migrate` cron is a safety net. When nothing is pending it does nothing, and it applies new migrations within 15 minutes of a deploy. `backup` writes a gzipped SQL dump to `storage/backups/` and keeps `BACKUP_KEEP_DAYS` days (default 14). Download one now and then, so a copy exists off the server.

## 6. Every release

1. If you changed Tailwind classes in `views/`, run `npm run build` and commit `public/css/app.css`. CI fails if you forget.
2. Open a PR and wait for CI to pass, then merge into `main`.
3. Hostinger pulls the new code within seconds. Check hPanel → Git for the latest commit.
4. If the PR added a file in `database/migrations/`, run `php bin/admin.php migrate` over SSH straight away, or wait for the cron.
5. Open the site and `/admin` to check it.

## 7. Verify after the first deploy

- [ ] `https://halosec.in` loads, and a test form submission appears in `/admin/submissions`
- [ ] These return **403 or 404**: `/.env`, `/.git/config`, `/storage/`, `/src/`, `/vendor/`, `/composer.json`, `/README.md`
- [ ] `/admin/login` works. **My Account & 2FA** → set up 2FA, sign out, and sign back in with a code
- [ ] When every admin has 2FA, set `ADMIN_REQUIRE_2FA=true` in `~/domains/halosec.in/.env`
- [ ] `TRUST_CLOUDFLARE=true` only if the domain is proxied through Cloudflare
- [ ] The backup cron has produced a file in `storage/backups/`

## Rollback and recovery

- **Bad release:** revert the PR on GitHub (*Revert* button → merge). Hostinger deploys the revert automatically. Migrations are not rolled back, so restore the database backup if a migration was the problem.
- **Locked out by the login throttle:** `php bin/admin.php unblock`
- **Lost 2FA device:** `php bin/admin.php 2fa-reset <username>`
- **Forgotten password:** `php bin/admin.php reset <username> '<new password>'`
- **Errors:** `public_html/storage/logs/error.log`
