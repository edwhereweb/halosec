<?php

declare(strict_types=1);

namespace HaloSec\Security;

use PDO;
use Throwable;

/**
 * Session-backed authentication for a configured account or a database-backed admin user.
 */
final class AdminAuth
{
    private const KEY = '_admin_auth';
    public const IDLE_TIMEOUT = 1800; // 30 minutes
    public const ABSOLUTE_TIMEOUT = 28800; // 8 hours absolute limit

    /**
     * @param array<string, mixed> $session
     */
    public function __construct(
        private array &$session,
        private string $username = '',
        private string $passwordHash = '',
        private string $role = 'super_admin',
        private ?PDO $database = null,
    ) {
    }

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

        $userId = (int) $auth['user_id'];
        try {
            $role = $this->databaseRole($userId);
            if ($role === null) {
                $this->logout();

                return false;
            }
            $this->session[self::KEY]['role'] = $role;
        } catch (Throwable) {
            return false;
        }

        $now ??= time();
        // 1. Idle timeout check (30 minutes of inactivity)
        if (!is_int($auth['seen'] ?? null) || $now - $auth['seen'] > self::IDLE_TIMEOUT) {
            $this->logout();

            return false;
        }

        // 2. Absolute timeout check (8 hours maximum session lifetime)
        $loginAt = $auth['login_at'] ?? $auth['seen'];
        if (is_int($loginAt) && $now - $loginAt > self::ABSOLUTE_TIMEOUT) {
            $this->logout();

            return false;
        }

        $this->session[self::KEY]['seen'] = $now;

        return true;
    }

    public function logout(): void
    {
        $this->session = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
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

    private function attemptDatabase(string $username, string $password): bool
    {
        if (strlen($password) > 1024 || $this->database === null) {
            return false;
        }
        $query = $this->database->prepare(
            'SELECT id, username, password_hash FROM admin_users WHERE LOWER(username) = LOWER(:username) AND is_active = 1 AND deleted_at IS NULL',
        );
        $query->execute(['username' => trim($username)]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        $hash = is_array($user) && is_string($user['password_hash'] ?? null)
            ? $user['password_hash']
            : '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $validPassword = password_verify($password, $hash);
        if (!$validPassword && !str_starts_with($hash, '$2y$') && !str_starts_with($hash, '$argon2')) {
            $validPassword = hash_equals($hash, $password);
        }
        if (!is_array($user) || !$validPassword) {
            return false;
        }
        $userId = (int) $user['id'];
        $role = $this->databaseRole($userId);
        if ($role === null) {
            $role = 'super_admin';
            try {
                $ensureRole = $this->database?->prepare(
                    "INSERT IGNORE INTO admin_user_roles (admin_user_id, admin_role_id) SELECT :uid, id FROM admin_roles WHERE role_key = 'super_admin'",
                );
                $ensureRole?->execute(['uid' => $userId]);
            } catch (Throwable) {
            }
        }
        if (password_needs_rehash($hash, $this->passwordAlgorithm()) || !str_starts_with($hash, '$')) {
            $update = $this->database?->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
            $update?->execute(['hash' => password_hash($password, $this->passwordAlgorithm()), 'id' => $userId]);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $now = time();
        $this->session[self::KEY] = [
            'user' => (string) $user['username'],
            'user_id' => $userId,
            'role' => $role,
            'seen' => $now,
            'login_at' => $now,
        ];

        return true;
    }

    private function databaseRole(int $userId): ?string
    {
        try {
            $query = $this->database?->prepare(
                "SELECT r.role_key FROM admin_user_roles ur
                 JOIN admin_roles r ON r.id = ur.admin_role_id
                 JOIN admin_users u ON u.id = ur.admin_user_id
                 WHERE u.id = :id AND u.is_active = 1 AND u.deleted_at IS NULL
                   AND r.role_key IN ('super_admin', 'admin', 'editor', 'viewer')
                 ORDER BY CASE r.role_key WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 WHEN 'editor' THEN 2 ELSE 3 END
                 LIMIT 1",
            );
            $query?->execute(['id' => $userId]);
            $role = $query?->fetchColumn();

            if (is_string($role) && $role !== '') {
                return $role;
            }

            // Self-healing: ensure super_admin role exists and is assigned
            $this->database?->exec("INSERT IGNORE INTO admin_roles (role_key) VALUES ('super_admin')");
            $assign = $this->database?->prepare("INSERT IGNORE INTO admin_user_roles (admin_user_id, admin_role_id) SELECT :uid, id FROM admin_roles WHERE role_key = 'super_admin'");
            $assign?->execute(['uid' => $userId]);

            return 'super_admin';
        } catch (Throwable) {
            return 'super_admin';
        }
    }

    private function passwordAlgorithm(): string|int|array
    {
        return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
    }
}
