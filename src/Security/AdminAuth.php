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
    private const IDLE_TIMEOUT = 1800;

    /**
     * @param array<string, mixed> $session
     */
    public function __construct(
        private array &$session,
        private string $username,
        private string $passwordHash,
        private string $role = 'super_admin',
        private ?PDO $database = null,
    ) {
    }

    public function attempt(string $username, string $password): bool
    {
        if ($this->database !== null) {
            return $this->attemptDatabase($username, $password);
        }
        $configured = $this->username !== ''
            && $this->passwordHash !== ''
            && in_array($this->role, RolePolicy::ROLES, true);
        // Always run password_verify so timing does not reveal which part was wrong.
        $hash = $configured ? $this->passwordHash : '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $passwordOk = password_verify($password, $hash);
        $userOk = hash_equals($this->username, trim($username));

        if (!$configured || !$passwordOk || !$userOk) {
            return false;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $this->session[self::KEY] = ['user' => $this->username, 'user_id' => null, 'role' => $this->role, 'seen' => time()];

        return true;
    }

    public function check(?int $now = null): bool
    {
        $auth = $this->session[self::KEY] ?? null;
        if (!is_array($auth)
            || !is_string($auth['user'] ?? null)
            || ($this->database === null && ($auth['user'] ?? null) !== $this->username)
            || !in_array($auth['role'] ?? null, RolePolicy::ROLES, true)
            || ($this->database === null && $this->username === '')) {
            return false;
        }
        if ($this->database !== null) {
            $userId = $auth['user_id'] ?? null;
            $role = is_int($userId) ? $this->databaseRole($userId) : null;
            if ($role === null) {
                unset($this->session[self::KEY]);

                return false;
            }
            $this->session[self::KEY]['role'] = $role;
        }
        $now ??= time();
        if (!is_int($auth['seen'] ?? null) || $now - $auth['seen'] > self::IDLE_TIMEOUT) {
            unset($this->session[self::KEY]);

            return false;
        }
        $this->session[self::KEY]['seen'] = $now;

        return true;
    }

    public function logout(): void
    {
        $this->session = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
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
        if (strlen($password) > 1024) {
            return false;
        }
        $query = $this->database?->prepare(
            'SELECT id, username, password_hash FROM admin_users WHERE username = :username AND is_active = 1 AND deleted_at IS NULL',
        );
        $query?->execute(['username' => trim($username)]);
        $user = $query?->fetch(PDO::FETCH_ASSOC);
        $hash = is_array($user) && is_string($user['password_hash'] ?? null)
            ? $user['password_hash']
            : '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $validPassword = password_verify($password, $hash);
        if (!is_array($user) || !$validPassword) {
            return false;
        }
        $userId = (int) $user['id'];
        $role = $this->databaseRole($userId);
        if ($role === null) {
            return false;
        }
        if (password_needs_rehash($hash, $this->passwordAlgorithm())) {
            $update = $this->database?->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
            $update?->execute(['hash' => password_hash($password, $this->passwordAlgorithm()), 'id' => $userId]);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $this->session[self::KEY] = [
            'user' => (string) $user['username'],
            'user_id' => $userId,
            'role' => $role,
            'seen' => time(),
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

            return is_string($role) ? $role : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function passwordAlgorithm(): string|int|array
    {
        return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
    }
}
