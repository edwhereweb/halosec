<?php

declare(strict_types=1);

namespace HaloSec\Services;

use HaloSec\Models\Lead;

/**
 * Appends one JSON record per line to <directory>/<type>.jsonl using an exclusive lock.
 */
final class FileLeadStorage implements LeadStorageInterface
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
}
