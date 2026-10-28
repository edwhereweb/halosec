<?php

declare(strict_types=1);

namespace HaloSec\Security;

use PDO;
use Throwable;

/**
 * Session-backed authentication for database admin users, with optional TOTP two-factor login.
 *
 * Fails closed: a user without an active account and a recognised role cannot sign in or stay signed in,
 * and any database error denies access.
 */
final class AdminAuth
{
    private const KEY = '_admin_auth';
    private const PENDING_KEY = '_admin_2fa_pending';
    private const ENROLL_KEY = '_admin_2fa_enroll';
    public const IDLE_TIMEOUT = 1800; // 30 minutes
    public const ABSOLUTE_TIMEOUT = 28800; // 8 hours absolute limit
    public const PENDING_TIMEOUT = 300; // 5 minutes to enter the 2FA code
    public const MIN_PASSWORD_LENGTH = 12;
    public const MAX_CODE_ATTEMPTS = 5;

    /**
     * Hashes that were once shipped in this repository as default seeds. Accounts still using them
     * are refused until the password is reset with `php bin/admin.php reset`.
     */
    private const KNOWN_DEFAULT_HASHES = [
        '$2y$10$hJd71uauJov9KidGrn13neMbzXieB3FEgJltgFYrELk4bJ2hxvJfe',
        '$2y$10$ThpOVvR6hlTif2f21.U9auTelfS5IBT3Dvs6uz3fDzqu2LfM9elti',
    ];
    private const KNOWN_DEFAULT_PASSWORDS = ['admin123'];
    private const DUMMY_HASH = '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';

    /**
     * @param array<string, mixed> $session
     */
    public function __construct(
        private array &$session,
        private ?PDO $database = null,
    ) {
    }

    /**
     * Verifies the password. Returns true when the password is correct; the user is then either signed in,
     * or (when they have 2FA enabled) waiting for a code — see awaitingTwoFactor().
     */
    public function attempt(string $username, string $password): bool
    {
        if ($this->database === null) {
            return false;
        }

        try {
            return $this->attemptDatabase($username, $password);
        } catch (Throwable $e) {
            error_log('AdminAuth attemptDatabase error: ' . $e->getMessage());

            return false;
        }
    }

    /** @phpstan-impure */
    public function awaitingTwoFactor(?int $now = null): bool
    {
        $pending = $this->session[self::PENDING_KEY] ?? null;

        return is_array($pending)
            && is_int($pending['user_id'] ?? null)
            && is_int($pending['at'] ?? null)
            && ($now ?? time()) - $pending['at'] <= self::PENDING_TIMEOUT;
    }

    public function verifyTwoFactor(string $code, ?int $now = null): bool
    {
        if (!$this->awaitingTwoFactor($now) || $this->database === null) {
            unset($this->session[self::PENDING_KEY]);

            return false;
        }
        /** @var array{user_id:int, user:string, role:string, at:int, tries?:int} $pending */
        $pending = $this->session[self::PENDING_KEY];
        $tries = ($pending['tries'] ?? 0) + 1;
        if ($tries > self::MAX_CODE_ATTEMPTS) {
            unset($this->session[self::PENDING_KEY]);

            return false;
        }
        $this->session[self::PENDING_KEY]['tries'] = $tries;
        try {
            $user = $this->findUserById($pending['user_id']);
            $secret = is_array($user) && is_string($user['totp_secret'] ?? null) ? $user['totp_secret'] : '';
            $step = Totp::verify($secret, $code, $now);
            $lastStep = is_array($user) && isset($user['totp_last_step']) ? (int) $user['totp_last_step'] : -1;
            if ($step === null || $step <= $lastStep) {
                return false;
            }
            $this->storeLastStep($pending['user_id'], $step);
            $role = $this->databaseRole($pending['user_id']);
            if ($role === null) {
                unset($this->session[self::PENDING_KEY]);

                return false;
            }
        } catch (Throwable $e) {
            error_log('AdminAuth verifyTwoFactor error: ' . $e->getMessage());

            return false;
        }
        unset($this->session[self::PENDING_KEY]);
        $this->completeLogin($pending['user_id'], $pending['user'], $role, true, $now);

        return true;
    }

    public function check(?int $now = null): bool
    {
        $auth = $this->session[self::KEY] ?? null;
        if (!is_array($auth)
            || !is_string($auth['user'] ?? null)
            || !is_int($auth['user_id'] ?? null)
            || $this->database === null
            || !in_array($auth['role'] ?? null, RolePolicy::ROLES, true)) {
            return false;
        }

        $now ??= time();
        if (!is_int($auth['seen'] ?? null) || $now - $auth['seen'] > self::IDLE_TIMEOUT) {
            $this->logout();

            return false;
        }
        $loginAt = $auth['login_at'] ?? $auth['seen'];
        if (is_int($loginAt) && $now - $loginAt > self::ABSOLUTE_TIMEOUT) {
            $this->logout();

            return false;
        }

        // Re-read the role on every request so deactivation or demotion takes effect immediately.
        $role = $this->databaseRole($auth['user_id']);
        if ($role === null) {
            $this->logout();

            return false;
        }
        $this->session[self::KEY]['role'] = $role;
        $this->session[self::KEY]['seen'] = $now;

        return true;
    }

    public function logout(): void
    {
        $this->session = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            $params = session_get_cookie_params();
            setcookie((string) session_name(), '', [
                'expires' => time() - 3600,
                'path' => $params['path'],
                'secure' => $params['secure'],
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_destroy();
        }
    }

    public function role(): string
    {
        $auth = $this->session[self::KEY] ?? null;

        return is_array($auth) && is_string($auth['role'] ?? null) ? $auth['role'] : '';
    }

    public function userId(): ?int
    {
        $auth = $this->session[self::KEY] ?? null;
        $id = is_array($auth) ? ($auth['user_id'] ?? null) : null;

        return is_int($id) ? $id : null;
    }

    public function username(): string
    {
        $auth = $this->session[self::KEY] ?? null;

        return is_array($auth) && is_string($auth['user'] ?? null) ? $auth['user'] : '';
    }

    public function hasTwoFactor(): bool
    {
        $auth = $this->session[self::KEY] ?? null;

        return is_array($auth) && ($auth['2fa'] ?? false) === true;
    }

    /**
     * Starts 2FA enrollment for the signed-in user and returns the new secret (kept in the session until confirmed).
     */
    public function beginTwoFactorEnrollment(): string
    {
        $secret = $this->pendingEnrollmentSecret();
        if ($secret === null) {
            $secret = Totp::generateSecret();
            $this->session[self::ENROLL_KEY] = $secret;
        }

        return $secret;
    }

    public function pendingEnrollmentSecret(): ?string
    {
        $secret = $this->session[self::ENROLL_KEY] ?? null;

        return is_string($secret) && $secret !== '' ? $secret : null;
    }

    public function confirmTwoFactorEnrollment(string $code, ?int $now = null): bool
    {
        $secret = $this->pendingEnrollmentSecret();
        $userId = $this->userId();
        if ($secret === null || $userId === null || $this->database === null) {
            return false;
        }
        $step = Totp::verify($secret, $code, $now);
        if ($step === null) {
            return false;
        }
        try {
            $query = $this->database->prepare(
                'UPDATE admin_users SET totp_secret = :secret, totp_last_step = :step WHERE id = :id',
            );
            $query->execute(['secret' => $secret, 'step' => $step, 'id' => $userId]);
        } catch (Throwable $e) {
            error_log('AdminAuth confirmTwoFactorEnrollment error: ' . $e->getMessage());

            return false;
        }
        unset($this->session[self::ENROLL_KEY]);
        $this->session[self::KEY]['2fa'] = true;

        return true;
    }

    public function disableTwoFactor(string $password, string $code, ?int $now = null): bool
    {
        $userId = $this->userId();
        if ($userId === null || $this->database === null) {
            return false;
        }
        try {
            $user = $this->findUserById($userId);
            if (!is_array($user)
                || !password_verify($password, (string) $user['password_hash'])
                || Totp::verify((string) ($user['totp_secret'] ?? ''), $code, $now) === null) {
                return false;
            }
            $query = $this->database->prepare(
                'UPDATE admin_users SET totp_secret = NULL, totp_last_step = NULL WHERE id = :id',
            );
            $query->execute(['id' => $userId]);
        } catch (Throwable $e) {
            error_log('AdminAuth disableTwoFactor error: ' . $e->getMessage());

            return false;
        }
        $this->session[self::KEY]['2fa'] = false;

        return true;
    }

    public function changePassword(string $current, string $new): bool
    {
        $userId = $this->userId();
        if ($userId === null || $this->database === null || !self::acceptablePassword($new) || $current === $new) {
            return false;
        }
        try {
            $user = $this->findUserById($userId);
            if (!is_array($user) || !password_verify($current, (string) $user['password_hash'])) {
                return false;
            }
            $query = $this->database->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
            $query->execute(['hash' => password_hash($new, self::passwordAlgorithm()), 'id' => $userId]);
        } catch (Throwable $e) {
            error_log('AdminAuth changePassword error: ' . $e->getMessage());

            return false;
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        return true;
    }

    public static function acceptablePassword(string $password): bool
    {
        return strlen($password) >= self::MIN_PASSWORD_LENGTH
            && strlen($password) <= 1024
            && !in_array(strtolower($password), self::KNOWN_DEFAULT_PASSWORDS, true);
    }

    public static function isKnownDefaultHash(string $hash): bool
    {
        return in_array($hash, self::KNOWN_DEFAULT_HASHES, true);
    }

    public static function passwordAlgorithm(): string|int|null
    {
        return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
    }

    private function attemptDatabase(string $username, string $password): bool
    {
        unset($this->session[self::PENDING_KEY]);
        if (strlen($password) > 1024 || $this->database === null) {
            return false;
        }
        $query = $this->database->prepare(
            'SELECT * FROM admin_users WHERE LOWER(username) = LOWER(:username) AND is_active = 1 AND deleted_at IS NULL',
        );
        $query->execute(['username' => trim($username)]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        $storedHash = is_array($user) && is_string($user['password_hash'] ?? null) ? $user['password_hash'] : '';
        // Only real password hashes are accepted; always run password_verify to keep timing uniform.
        $isHash = str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$argon2');
        $validPassword = password_verify($password, $isHash ? $storedHash : self::DUMMY_HASH);
        if (!is_array($user) || !$isHash || !$validPassword) {
            return false;
        }
        if (self::isKnownDefaultHash($storedHash) || in_array(strtolower($password), self::KNOWN_DEFAULT_PASSWORDS, true)) {
            error_log('AdminAuth: refused login for "' . $user['username'] . '" because it uses a published default password.');

            return false;
        }
        $userId = (int) $user['id'];
        $role = $this->databaseRole($userId);
        if ($role === null) {
            return false;
        }
        if (password_needs_rehash($storedHash, self::passwordAlgorithm())) {
            $update = $this->database->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
            $update->execute(['hash' => password_hash($password, self::passwordAlgorithm()), 'id' => $userId]);
        }

        $secret = is_string($user['totp_secret'] ?? null) ? $user['totp_secret'] : '';
        if ($secret !== '') {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            $this->session[self::PENDING_KEY] = [
                'user_id' => $userId,
                'user' => (string) $user['username'],
                'role' => $role,
                'at' => time(),
            ];

            return true;
        }

        $this->completeLogin($userId, (string) $user['username'], $role, false);

        return true;
    }

    private function completeLogin(int $userId, string $username, string $role, bool $twoFactor, ?int $now = null): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $now ??= time();
        $this->session[self::KEY] = [
            'user' => $username,
            'user_id' => $userId,
            'role' => $role,
            'seen' => $now,
            'login_at' => $now,
            '2fa' => $twoFactor,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findUserById(int $userId): ?array
    {
        $query = $this->statement('SELECT * FROM admin_users WHERE id = :id AND is_active = 1 AND deleted_at IS NULL');
        $query->execute(['id' => $userId]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        return is_array($user) ? $user : null;
    }

    private function storeLastStep(int $userId, int $step): void
    {
        $this->statement('UPDATE admin_users SET totp_last_step = :step WHERE id = :id')
            ->execute(['step' => $step, 'id' => $userId]);
    }

    private function statement(string $sql): \PDOStatement
    {
        $statement = $this->database?->prepare($sql);
        if (!$statement instanceof \PDOStatement) {
            throw new \RuntimeException('Database unavailable.');
        }

        return $statement;
    }

    /**
     * The user's highest-privilege role, or null when the account is inactive, has no recognised role,
     * or the lookup fails.
     */
    private function databaseRole(int $userId): ?string
    {
        try {
            $query = $this->statement(
                "SELECT r.role_key FROM admin_user_roles ur
                 JOIN admin_roles r ON r.id = ur.admin_role_id
                 JOIN admin_users u ON u.id = ur.admin_user_id
                 WHERE u.id = :id AND u.is_active = 1 AND u.deleted_at IS NULL
                   AND r.role_key IN ('super_admin', 'admin', 'editor', 'viewer')
                 ORDER BY CASE r.role_key WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 WHEN 'editor' THEN 2 ELSE 3 END
                 LIMIT 1",
            );
            $query->execute(['id' => $userId]);
            $role = $query->fetchColumn();

            return is_string($role) && in_array($role, RolePolicy::ROLES, true) ? $role : null;
        } catch (Throwable $e) {
            error_log('AdminAuth role lookup error: ' . $e->getMessage());

            return null;
        }
    }
}
