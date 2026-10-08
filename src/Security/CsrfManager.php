<?php

declare(strict_types=1);

namespace HaloSec\Security;

/**
 * Session-backed CSRF token manager. Operates on a session array reference so it is testable.
 */
final class CsrfManager
{
    public const FIELD = '_csrf';
    private const KEY = '_csrf_token';

    /**
     * @param array<string, mixed> $session
     */
    public function __construct(private array &$session)
    {
    }

    public function token(): string
    {
        $token = $this->session[self::KEY] ?? null;
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session[self::KEY] = $token;
        }

        return $token;
    }

    public function isValid(mixed $submitted): bool
    {
        $token = $this->session[self::KEY] ?? null;

        return is_string($submitted) && is_string($token) && $token !== '' && hash_equals($token, $submitted);
    }
}
