<?php

declare(strict_types=1);

namespace HaloSec\Services;

interface AdminSubmissionRepositoryInterface extends LeadStorageInterface, LeadReaderInterface
{
    /** @return list<array<string, mixed>> */
    public function listSubmissions(
        ?string $formKey,
        ?string $status,
        int $page,
        int $perPage,
        ?string $fromDate = null,
        ?string $toDate = null,
    ): array;

    public function submissionCount(?string $formKey, ?string $status, ?string $fromDate = null, ?string $toDate = null): int;

    /** @return array<string, mixed>|null */
    public function submission(int $id): ?array;

    /** @return list<array<string, mixed>> */
    public function notes(int $submissionId): array;

    public function changeStatus(int $submissionId, string $status, ?int $actorId): bool;

    /** @param list<int> $submissionIds */
    public function bulkChangeStatus(array $submissionIds, string $status, ?int $actorId): int;

    public function assign(int $submissionId, ?int $adminUserId, ?int $actorId): bool;

    public function addNote(int $submissionId, string $note, ?int $actorId): bool;

    /** @return array<string, int> */
    public function dashboardStats(): array;

    /** @return list<array<string, mixed>> */
    public function securityEvents(int $limit = 50, ?string $severity = null): array;

    /** @return list<array<string, mixed>> */
    public function auditLogs(int $limit = 50): array;

    /** @return array<string, string> */
    public function settings(): array;

    public function updateSetting(string $key, string $value, ?int $actorId): bool;

    /** @return list<array<string, mixed>> */
    public function adminUsers(): array;

    public function createAdminUser(string $username, string $password, string $role, ?int $actorId): bool;

    public function updateAdminUser(int $userId, string $role, bool $active, ?int $actorId): bool;

    public function recordFailedLogin(string $ip, string $username): void;

    /** @return list<array<string, mixed>> */
    public function contentBlocks(): array;

    public function updateContentBlock(string $key, string $content, bool $active, ?int $actorId): bool;
}
