-- Two-factor authentication (TOTP) for admin users.
ALTER TABLE admin_users ADD COLUMN totp_secret VARCHAR(64) NULL AFTER password_hash;
ALTER TABLE admin_users ADD COLUMN totp_last_step BIGINT NULL AFTER totp_secret;
