<?php

declare(strict_types=1);

namespace HaloSec\Security;

/**
 * Single-admin, session-backed authentication. The password is only ever compared against a
 * password_hash() value supplied through configuration.
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
    ) {
    }

    public function attempt(string $username, string $password): bool
    {
        $configured = $this->username !== '' && $this->passwordHash !== '';
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
        $this->session[self::KEY] = ['user' => $this->username, 'seen' => time()];

        return true;
    }

    public function check(?int $now = null): bool
    {
        $auth = $this->session[self::KEY] ?? null;
        if (!is_array($auth) || ($auth['user'] ?? null) !== $this->username || $this->username === '') {
            return false;
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
}
