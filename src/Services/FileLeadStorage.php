<?php

declare(strict_types=1);

namespace HaloSec\Services;

use HaloSec\Models\Lead;

/**
 * Appends one JSON record per line to <directory>/<type>.jsonl using an exclusive lock.
 */
final class FileLeadStorage implements LeadStorageInterface, LeadReaderInterface
{
    public function __construct(private string $directory)
    {
    }

    public function save(Lead $lead): bool
    {
        if (!in_array($lead->type, Lead::TYPES, true)) {
            return false;
        }

        if (!is_dir($this->directory) && !@mkdir($this->directory, 0750, true) && !is_dir($this->directory)) {
            return false;
        }

        $line = json_encode($lead->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($line === false) {
            return false;
        }

        $path = rtrim($this->directory, '/\\') . '/' . $lead->type . '.jsonl';
        $handle = @fopen($path, 'ab');
        if ($handle === false) {
            return false;
        }

        $ok = false;
        if (flock($handle, LOCK_EX)) {
            $ok = fwrite($handle, $line . "\n") !== false;
            fflush($handle);
            flock($handle, LOCK_UN);
        }
        fclose($handle);

        return $ok;
    }

    public function counts(): array
    {
        $counts = [];
        foreach (Lead::TYPES as $type) {
            $counts[$type] = count($this->read($type));
        }

        return $counts;
    }

    public function all(?string $type = null): array
    {
        $types = $type === null ? Lead::TYPES : (in_array($type, Lead::TYPES, true) ? [$type] : []);
        $leads = [];
        foreach ($types as $t) {
            array_push($leads, ...$this->read($t));
        }
        usort($leads, static fn (array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));

        return $leads;
    }

    public function find(string $type, int $number): ?array
    {
        foreach ($this->read($type) as $lead) {
            if ($lead['id'] === $type . '-' . $number) {
                return $lead;
            }
        }

        return null;
    }

    /**
     * Fallback-file leads whose email matches (case-insensitive), for access requests.
     *
     * @return list<array<string, mixed>>
     */
    public function forEmail(string $email): array
    {
        $email = mb_strtolower(trim($email));

        return array_values(array_filter(
            $this->all(),
            static fn (array $lead): bool => $email !== '' && is_string($lead['data']['email'] ?? null)
                && mb_strtolower(trim($lead['data']['email'])) === $email,
        ));
    }

    /** Permanently removes leads from this email (erasure request). */
    public function eraseEmail(string $email): int
    {
        $email = mb_strtolower(trim($email));
        if ($email === '') {
            return 0;
        }

        return $this->eraseWhere(static fn (array $row): bool => is_string($row['data']['email'] ?? null)
            && mb_strtolower(trim($row['data']['email'])) === $email);
    }

    /** Permanently removes leads received before the cutoff (retention). */
    public function eraseBefore(string $cutoffUtc): int
    {
        $cutoff = strtotime($cutoffUtc . (str_contains($cutoffUtc, 'T') ? '' : ' UTC'));
        if ($cutoff === false) {
            return 0;
        }

        return $this->eraseWhere(static function (array $row) use ($cutoff): bool {
            $at = strtotime((string) ($row['created_at'] ?? ''));

            return $at !== false && $at < $cutoff;
        });
    }

    /**
     * Rewrites each file without the matching lines, under an exclusive lock.
     *
     * @param callable(array<string, mixed>): bool $shouldErase
     */
    private function eraseWhere(callable $shouldErase): int
    {
        $erased = 0;
        foreach (Lead::TYPES as $type) {
            $path = rtrim($this->directory, '/\\') . '/' . $type . '.jsonl';
            if (!is_file($path)) {
                continue;
            }
            $handle = @fopen($path, 'r+b');
            if ($handle === false || !flock($handle, LOCK_EX)) {
                continue;
            }
            $keep = [];
            while (($line = fgets($handle)) !== false) {
                $row = json_decode(rtrim($line, "\r\n"), true);
                if (is_array($row) && $shouldErase($row)) {
                    $erased++;
                    continue;
                }
                $keep[] = $line;
            }
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, implode('', $keep));
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);
        }

        return $erased;
    }

    /**
     * @return list<array{id: string, type: string, created_at: string, data: array<string, mixed>}>
     */
    private function read(string $type): array
    {
        if (!in_array($type, Lead::TYPES, true)) {
            return [];
        }
        $lines = @file(rtrim($this->directory, '/\\') . '/' . $type . '.jsonl', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $leads = [];
        foreach ($lines as $index => $line) {
            $row = json_decode($line, true);
            if (!is_array($row) || !is_array($row['data'] ?? null)) {
                continue;
            }
            $leads[] = [
                'id' => $type . '-' . ($index + 1),
                'type' => $type,
                'created_at' => is_string($row['created_at'] ?? null) ? $row['created_at'] : '',
                'data' => $row['data'],
            ];
        }

        return $leads;
    }
}
