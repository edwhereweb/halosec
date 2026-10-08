<?php

declare(strict_types=1);

namespace HaloSec\Security;

/**
 * File-backed failed-login throttle keyed by client identifier (hashed). After $maxAttempts
 * failures within $window seconds the client is locked out until the window expires.
 */
final class LoginThrottle
{
    public function __construct(
        private string $directory,
        private int $maxAttempts = 5,
        private int $window = 900,
    ) {
    }

    public function isLocked(string $client, ?int $now = null): bool
    {
        return count($this->recent($client, $now ?? time())) >= $this->maxAttempts;
    }

    public function recordFailure(string $client, ?int $now = null): void
    {
        $now ??= time();
        $times = $this->recent($client, $now);
        $times[] = $now;
        $this->write($client, $times);
    }

    public function clear(string $client): void
    {
        $file = $this->file($client);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    private function file(string $client): string
    {
        return rtrim($this->directory, '/\\') . '/' . hash('sha256', $client) . '.json';
    }

    /**
     * @return list<int>
     */
    private function recent(string $client, int $now): array
    {
        $raw = @file_get_contents($this->file($client));
        $data = $raw === false ? null : json_decode($raw, true);
        if (!is_array($data)) {
            return [];
        }

        return array_values(array_filter(
            $data,
            fn ($t): bool => is_int($t) && $now - $t < $this->window,
        ));
    }

    /**
     * @param list<int> $times
     */
    private function write(string $client, array $times): void
    {
        if (!is_dir($this->directory) && !@mkdir($this->directory, 0750, true) && !is_dir($this->directory)) {
            return;
        }
        @file_put_contents($this->file($client), json_encode($times), LOCK_EX);
    }
}
