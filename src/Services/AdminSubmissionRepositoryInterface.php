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
        ?string $assigneeId = null,
        ?string $search = null,
    ): array;

    public function submissionCount(
        ?string $formKey,
        ?string $status,
        ?string $fromDate = null,
        ?string $toDate = null,
        ?string $assigneeId = null,
        ?string $search = null,
    ): int;

    /** @return array<string, mixed>|null */
    public function submission(int $id): ?array;

    /** @return list<array<string, mixed>> */
    public function notes(int $submissionId): array;

    public function changeStatus(int $submissionId, string $status, ?int $actorId): bool;

    /** @param list<int> $submissionIds */
    public function bulkChangeStatus(array $submissionIds, string $status, ?int $actorId): int;

    public function assign(int $submissionId, ?int $adminUserId, ?int $actorId): bool;

    public function addNote(int $submissionId, string $note, ?int $actorId): bool;

    /**
     * Every submission whose email field matches (case-insensitive).
     *
     * @return list<int>
     */
    public function submissionIdsForEmail(string $email): array;

    /**
     * A person's data in a portable shape, for access / portability requests.
     *
     * @return list<array<string, mixed>>
     */
    public function exportForEmail(string $email, ?int $actorId = null): array;

    /**
     * Permanently deletes submissions with their fields and notes. Returns how many were removed.
     *
     * @param list<int> $submissionIds
     */
    public function eraseSubmissions(array $submissionIds, ?int $actorId, string $reason): int;

    /** Permanently deletes every submission received before the cutoff (retention). */
    public function eraseSubmissionsBefore(string $cutoffUtc, ?int $actorId = null): int;

    /** @return array<string, int> */
    public function dashboardStats(): array;

    /** @return list<array<string, mixed>> */
    public function securityEvents(int $limit = 50, ?string $severity = null, ?string $eventType = null): array;

    /** @return list<array<string, mixed>> */
    public function auditLogs(int $limit = 50): array;

    /** @return array<string, string> */
    public function settings(): array;

    public function updateSetting(string $key, string $value, ?int $actorId): bool;

    /** @return list<array<string, mixed>> */
    public function adminUsers(): array;

    public function createAdminUser(string $username, string $password, string $role, ?int $actorId): bool;

    public function updateAdminUser(int $userId, string $role, bool $active, ?int $actorId, ?string $password = null): bool;

    public function recordFailedLogin(string $ip, string $username): void;

    /** @return list<array<string, mixed>> */
    public function contentBlocks(): array;

    public function updateContentBlock(string $key, string $content, bool $active, ?int $actorId): bool;

    /**
     * Lightweight rows for analytics since a UTC time.
     *
     * @return list<array{form_key: string, status: string, submitted_at: string, assigned_admin_user_id: int|null}>
     */
    public function analyticsRows(string $fromUtc): array;

    /**
     * Decoded scalar values of one field (e.g. "industry") on non-spam submissions of the given forms.
     *
     * @param list<string> $formKeys
     *
     * @return list<string>
     */
    public function fieldValues(array $formKeys, string $fieldKey, string $fromUtc): array;

    /** @return list<array{submitted_at: string, first_action_at: string}> */
    public function firstResponses(string $fromUtc): array;

    public function failedLoginCount(string $fromUtc): int;

    /** @return array{new: int, open: int, unassigned: int, urgent: int, mine: int, stale: int} */
    public function navCounts(?int $userId): array;

    /** @return array<string, int> status => count for the given filters */
    public function statusCounts(?string $formKey, ?string $assigneeId = null, ?string $search = null): array;

    /** @return list<array{kind: string, actor: string, text: string, created_at: string}> newest first */
    public function timeline(int $submissionId): array;

    /** @return array{older: int|null, newer: int|null} */
    public function neighbours(int $submissionId): array;
}
