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
