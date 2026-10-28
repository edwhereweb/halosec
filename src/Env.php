<?php

declare(strict_types=1);

namespace HaloSec;

/**
 * Minimal .env loader. Real environment variables always win over values from the file.
 */
final class Env
{
    /**
     * Loads the first file that exists from $candidates. Returns the path loaded, or null.
     *
     * @param list<string> $candidates
     */
    public static function load(array $candidates): ?string
    {
        foreach ($candidates as $file) {
            if (!is_file($file)) {
                continue;
            }
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach (is_array($lines) ? $lines : [] as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if (strlen($value) >= 2
                    && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
                    $value = substr($value, 1, -1);
                }
                if ($key === '' || getenv($key) !== false) {
                    continue;
                }
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }

            return $file;
        }

        return null;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }

        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }
}
