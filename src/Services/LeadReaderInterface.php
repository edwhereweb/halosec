<?php

declare(strict_types=1);

namespace HaloSec\Services;

interface LeadReaderInterface
{
    /**
     * Number of stored leads per lead type.
     *
     * @return array<string, int>
     */
    public function counts(): array;

    /**
     * Leads newest first, optionally filtered by type.
     *
     * @return list<array{id: string, type: string, created_at: string, data: array<string, mixed>}>
     */
    public function all(?string $type = null): array;

    /**
     * @return array{id: string, type: string, created_at: string, data: array<string, mixed>}|null
     */
    public function find(string $type, int $number): ?array;
}
