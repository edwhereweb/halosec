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
     * Rows always contain id, type, created_at and data; database-backed rows also carry workflow
     * fields such as status and assigned_admin_user_id.
     *
     * @return list<array<string, mixed>>
     */
    public function all(?string $type = null): array;

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $type, int $number): ?array;
}
