# Deploying to Hostinger shared hosting

The app needs PHP 8.1+ (pick 8.3 in hPanel → Advanced → PHP Configuration) with `pdo_mysql` and `mbstring`, a MySQL database, and SSH access (Premium plan or higher).

## 1. Folder layout: keep the code out of the web root

Only `public/` should be reachable from the web. The `.env` file, `src/`, `storage/` and `vendor/` must not be.

```
/home/u123456789/domains/halosec.in/
├── app/                 ← this repository (git clone), including .env and storage/
│   └── public/          ← the only web-facing folder
└── public_html  →  symlink to app/public
```

One-time setup over SSH (hPanel → Advanced → SSH Access):

```sh
cd ~/domains/halosec.in
git clone https://github.com/<org>/halosec.git app        # or a deploy key for a private repo
cd app
composer install --no-dev --optimize-autoloader
cp .env.example .env && chmod 600 .env && nano .env       # fill in DB_* and the rest
mkdir -p storage/{leads,logs,auth,backups,uploads}
chmod -R 750 storage
cd .. && mv public_html public_html.bak && ln -s app/public public_html
```

If symlinks are not possible, put the whole repository inside `public_html/`. The root `.htaccess` then routes every request into `public/` and blocks `src/`, `config/`, `storage/`, dotfiles and so on. The symlink layout is safer, so use it when you can.

## 2. Database

1. hPanel → Databases → MySQL Databases: create a database and user. Put the details in `.env` (`DB_HOST=localhost`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`).
2. Apply the schema: `php bin/admin.php migrate`. Run this after every deploy (the deploy workflow does it for you). The website itself never changes the schema.
3. Create the first administrator:
   ```sh
   php bin/admin.php create yourname 'a-long-unique-password' super_admin
   ```
   There is no default account. The old `admin` / `admin123` seed is blocked from signing in. `migrate` lists any account that still uses it.
4. Sign in at `/admin/login` and open **My Account & 2FA** to turn on two-factor authentication. Set `ADMIN_REQUIRE_2FA=true` in `.env` once every admin has enrolled. With it on, every admin must set up 2FA before using the dashboard.

## 3. Automatic deploys from GitHub

`.github/workflows/deploy.yml` runs after CI passes on `main`. It connects over SSH, checks out the new commit, runs `composer install --no-dev` and runs `php bin/admin.php migrate`.

1. Create a key pair locally: `ssh-keygen -t ed25519 -f halosec_deploy -N ""`.
2. hPanel → SSH Access → add `halosec_deploy.pub`.
3. GitHub → Settings → Secrets → Actions: add `HOSTINGER_SSH_HOST`, `HOSTINGER_SSH_PORT` (usually `65002`), `HOSTINGER_SSH_USER`, `HOSTINGER_SSH_KEY` (contents of `halosec_deploy`), and `HOSTINGER_APP_PATH` (e.g. `/home/u123456789/domains/halosec.in/app`).
4. Optionally add required reviewers to the `production` environment so each deploy needs a click.

`git reset --hard` on the server leaves ignored files alone, so `.env`, `storage/` and `vendor/` survive deploys.

## 4. Cron jobs (hPanel → Advanced → Cron Jobs)

| Schedule | Command |
|---|---|
| Daily 02:30 | `/usr/bin/php /home/u123456789/domains/halosec.in/app/bin/admin.php backup` |
| Hourly | `/usr/bin/php /home/u123456789/domains/halosec.in/app/bin/admin.php prune` |

`backup` writes a gzipped SQL dump to `storage/backups/` and deletes dumps older than `BACKUP_KEEP_DAYS` (default 14). Hostinger's own daily or weekly backups are a second copy. Download a backup off the server now and then.

## 5. Production checklist

- [ ] `APP_ENV=production` (errors are logged to `storage/logs/error.log`, not shown)
- [ ] hPanel → Security → SSL installed. The `.htaccess` files force HTTPS and send HSTS.
- [ ] `TRUST_CLOUDFLARE=true` **only** if the domain is proxied through Cloudflare
- [ ] Every admin has 2FA, then set `ADMIN_REQUIRE_2FA=true`
- [ ] `https://halosec.in/.env`, `/storage/`, `/src/` and `/vendor/` all return 403 or 404
- [ ] Backup cron is running and a restore has been tested once

## Recovery

- Locked out by the login throttle: `php bin/admin.php unblock`
- Lost 2FA device: `php bin/admin.php 2fa-reset <username>`
- Forgotten password: `php bin/admin.php reset <username> '<new password>'`
