<?php

declare(strict_types=1);

namespace HaloSec\Services;

use HaloSec\Admin\Ui;
use HaloSec\Models\Lead;
use PDO;
use Throwable;

final class PdoLeadStorage implements AdminSubmissionRepositoryInterface
{
    private const STATUSES = ['new', 'in_review', 'resolved', 'spam', 'archived'];

    public function __construct(private PDO $pdo)
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function save(Lead $lead): bool
    {
        if (!in_array($lead->type, Lead::TYPES, true)) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();
            $insert = $this->pdo->prepare(
                'INSERT INTO form_submissions (form_key, submitted_at, status) VALUES (:form_key, :submitted_at, :status)',
            );
            $insert->execute([
                'form_key' => $lead->type,
                'submitted_at' => $this->timestamp($lead->createdAt),
                'status' => 'new',
            ]);
            $id = (int) $this->pdo->lastInsertId();
            $field = $this->pdo->prepare(
                'INSERT INTO form_submission_fields (submission_id, field_key, field_value) VALUES (:submission_id, :field_key, :field_value)',
            );
            foreach ($lead->data as $key => $value) {
                $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($encoded === false) {
                    throw new \RuntimeException('Could not encode submission field.');
                }
                $field->execute([
                    'submission_id' => $id,
                    'field_key' => substr((string) $key, 0, 100),
                    'field_value' => $encoded,
                ]);
            }
            $this->registerSubmissionEvents($id, $lead->type, $lead->data);
            $this->pdo->commit();

            return true;
        } catch (Throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }

    public function counts(): array
    {
        $counts = array_fill_keys(Lead::TYPES, 0);
        $query = $this->runQuery('SELECT form_key, COUNT(*) AS total FROM form_submissions GROUP BY form_key');
        foreach ($query->fetchAll() as $row) {
            if (isset($counts[$row['form_key']])) {
                $counts[$row['form_key']] = (int) $row['total'];
            }
        }

        return $counts;
    }

    public function all(?string $type = null): array
    {
        $count = $this->submissionCount($type, null);
        $rows = [];
        for ($page = 1; $page <= (int) ceil($count / 100); $page++) {
            array_push($rows, ...$this->listSubmissions($type, null, $page, 100));
        }

        return $rows;
    }

    public function find(string $type, int $number): ?array
    {
        if (!in_array($type, Lead::TYPES, true) || $number < 1) {
            return null;
        }
        $row = $this->submission($number);

        return $row !== null && $row['form_key'] === $type ? $row : null;
    }

    public function listSubmissions(
        ?string $formKey,
        ?string $status,
        int $page,
        int $perPage,
        ?string $fromDate = null,
        ?string $toDate = null,
        ?string $assigneeId = null,
        ?string $search = null,
    ): array {
        [$where, $params] = $this->filters($formKey, $status, $fromDate, $toDate, $assigneeId, $search);
        $offset = (max(1, $page) - 1) * max(1, min($perPage, 100));
        $query = $this->pdo->prepare(
            'SELECT id, form_key, submitted_at, status, assigned_admin_user_id FROM form_submissions'
            . $where . ' ORDER BY submitted_at DESC, id DESC LIMIT ' . max(1, min($perPage, 100)) . ' OFFSET ' . $offset,
        );
        $query->execute($params);

        $rows = [];
        foreach ($query->fetchAll() as $row) {
            $rows[] = $this->withFields($row);
        }

        return $rows;
    }

    public function submissionCount(
        ?string $formKey,
        ?string $status,
        ?string $fromDate = null,
        ?string $toDate = null,
        ?string $assigneeId = null,
        ?string $search = null,
    ): int {
        [$where, $params] = $this->filters($formKey, $status, $fromDate, $toDate, $assigneeId, $search);
        $query = $this->pdo->prepare('SELECT COUNT(*) FROM form_submissions' . $where);
        $query->execute($params);

        return (int) $query->fetchColumn();
    }

    public function submission(int $id): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT id, form_key, submitted_at, status, assigned_admin_user_id FROM form_submissions WHERE id = :id',
        );
        $query->execute(['id' => $id]);
        $row = $query->fetch();

        return is_array($row) ? $this->withFields($row) : null;
    }

    public function notes(int $submissionId): array
    {
        $query = $this->pdo->prepare(
            'SELECT note, created_at, actor_user_id FROM admin_submission_notes WHERE submission_id = :id ORDER BY id DESC',
        );
        $query->execute(['id' => $submissionId]);

        return $query->fetchAll();
    }

    public function changeStatus(int $submissionId, string $status, ?int $actorId): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }
        if ($this->submission($submissionId) === null) {
            return false;
        }
        $query = $this->pdo->prepare('UPDATE form_submissions SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $query->execute(['status' => $status, 'id' => $submissionId]);
        $this->audit($actorId, 'submission.status_changed', 'form_submission', $submissionId, ['status' => $status]);

        return true;
    }

    public function bulkChangeStatus(array $submissionIds, string $status, ?int $actorId): int
    {
        if (!in_array($status, self::STATUSES, true)) {
            return 0;
        }
        $changed = 0;
        foreach (array_slice(array_unique($submissionIds), 0, 100) as $id) {
            if ($this->changeStatus($id, $status, $actorId)) {
                $changed++;
            }
        }

        return $changed;
    }

    public function assign(int $submissionId, ?int $adminUserId, ?int $actorId): bool
    {
        if ($this->submission($submissionId) === null) {
            return false;
        }
        if ($adminUserId !== null) {
            $user = $this->pdo->prepare('SELECT id FROM admin_users WHERE id = :id AND is_active = 1 AND deleted_at IS NULL');
            $user->execute(['id' => $adminUserId]);
            if ($user->fetchColumn() === false) {
                return false;
            }
        }
        $query = $this->pdo->prepare(
            'UPDATE form_submissions SET assigned_admin_user_id = :assignee, updated_at = CURRENT_TIMESTAMP WHERE id = :id',
        );
        $query->bindValue(':assignee', $adminUserId, $adminUserId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $query->bindValue(':id', $submissionId, PDO::PARAM_INT);
        $query->execute();
        $this->audit($actorId, 'submission.assigned', 'form_submission', $submissionId, ['assignee_id' => $adminUserId]);

        return true;
    }

    public function addNote(int $submissionId, string $note, ?int $actorId): bool
    {
        if ($this->submission($submissionId) === null || trim($note) === '') {
            return false;
        }
        $query = $this->pdo->prepare(
            'INSERT INTO admin_submission_notes (submission_id, actor_user_id, note) VALUES (:submission_id, :actor, :note)',
        );
        $query->execute(['submission_id' => $submissionId, 'actor' => $actorId, 'note' => mb_substr(trim($note), 0, 4000)]);
        $this->audit($actorId, 'submission.note_added', 'form_submission', $submissionId);

        return true;
    }

    public function dashboardStats(): array
    {
        $today = gmdate('Y-m-d 00:00:00');
        $query = $this->pdo->prepare(
            "SELECT
                SUM(CASE WHEN submitted_at >= :today THEN 1 ELSE 0 END) AS today_count,
                SUM(CASE WHEN status IN ('new', 'in_review') THEN 1 ELSE 0 END) AS open_count,
                SUM(CASE WHEN status = 'spam' THEN 1 ELSE 0 END) AS spam_count,
                SUM(CASE WHEN status IN ('new', 'in_review') AND submitted_at < :aging THEN 1 ELSE 0 END) AS aging_count
             FROM form_submissions",
        );
        $query->execute(['today' => $today, 'aging' => gmdate('Y-m-d H:i:s', time() - 86400)]);
        $row = $query->fetch() ?: [];
        $high = (int) $this->runQuery(
            "SELECT COUNT(*) FROM security_events WHERE severity IN ('high', 'critical') AND event_type <> 'security_report_received'",
        )->fetchColumn();
        $high += (int) $this->runQuery(
            "SELECT COUNT(*) FROM form_submissions WHERE form_key IN ('emergency', 'security_report') AND status NOT IN ('spam', 'archived')",
        )->fetchColumn();

        return [
            'today' => (int) ($row['today_count'] ?? 0),
            'open' => (int) ($row['open_count'] ?? 0),
            'spam' => (int) ($row['spam_count'] ?? 0),
            'aging' => (int) ($row['aging_count'] ?? 0),
            'high_security' => $high,
        ];
    }

    public function securityEvents(int $limit = 50, ?string $severity = null, ?string $eventType = null): array
    {
        $sql = 'SELECT severity, event_type, description, created_at FROM (
                SELECT severity, event_type, description, created_at FROM security_events
                UNION ALL
                SELECT \'medium\' AS severity, \'admin_login_failed\' AS event_type,
                    \'An admin login attempt failed.\' AS description, attempted_at AS created_at
                    FROM failed_login_attempts
             ) AS events';
        $params = [];
        if (in_array($severity, ['info', 'low', 'medium', 'high', 'critical'], true)) {
            $sql .= ' WHERE severity = :severity';
            $params['severity'] = $severity;
        }
        if (in_array($eventType, ['admin_login_failed', 'security_events'], true)) {
            $sql .= isset($params['severity']) ? ' AND' : ' WHERE';
            if ($eventType === 'admin_login_failed') {
                $sql .= ' event_type = :event_type';
            } else {
                $sql .= ' event_type <> :event_type';
            }
            $params['event_type'] = 'admin_login_failed';
        }
        $query = $this->pdo->prepare(
            $sql . ' ORDER BY created_at DESC LIMIT ' . max(1, min($limit, 100)),
        );
        $query->execute($params);

        return $query->fetchAll();
    }

    public function auditLogs(int $limit = 50): array
    {
        $query = $this->runQuery(
            'SELECT a.actor_user_id, u.username AS actor_username, a.action, a.entity_type, a.entity_id, a.details, a.created_at
             FROM admin_audit_logs a LEFT JOIN admin_users u ON u.id = a.actor_user_id ORDER BY a.created_at DESC, a.id DESC LIMIT '
            . max(1, min($limit, 200)),
        );

        return $query->fetchAll();
    }

    public function settings(): array
    {
        $query = $this->runQuery('SELECT setting_key, setting_value FROM site_settings ORDER BY setting_key');
        $settings = [];
        foreach ($query->fetchAll() as $row) {
            $settings[(string) $row['setting_key']] = (string) $row['setting_value'];
        }

        return $settings;
    }

    public function updateSetting(string $key, string $value, ?int $actorId): bool
    {
        $allowedKeys = ['site.name', 'site.tagline', 'site.contact_email', 'site.emergency_phone'];
        if (!in_array($key, $allowedKeys, true)) {
            return false;
        }
        if ($key === 'site.contact_email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        if ($key === 'site.emergency_phone' && preg_match('/^\+?[0-9][0-9 ()\-]{5,28}$/', $value) !== 1) {
            return false;
        }
        $query = $this->pdo->prepare('SELECT setting_key FROM site_settings WHERE setting_key = :key');
        $query->execute(['key' => $key]);
        if ($query->fetchColumn() === false) {
            $query = $this->pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :value)');
        } else {
            $query = $this->pdo->prepare(
                'UPDATE site_settings SET setting_value = :value, updated_at = CURRENT_TIMESTAMP WHERE setting_key = :key',
            );
        }
        $query->execute(['key' => $key, 'value' => mb_substr($value, 0, 4000)]);
        $this->audit($actorId, 'site_setting.updated', 'site_setting', null, ['key' => $key]);

        return true;
    }

    public function adminUsers(): array
    {
        $query = $this->runQuery(
            "SELECT u.id, u.username, u.is_active,
                (SELECT r.role_key FROM admin_user_roles ur JOIN admin_roles r ON r.id = ur.admin_role_id
                 WHERE ur.admin_user_id = u.id
                 ORDER BY CASE r.role_key WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 WHEN 'editor' THEN 2 ELSE 3 END
                 LIMIT 1) AS role_key
             FROM admin_users u
             WHERE u.deleted_at IS NULL ORDER BY u.username",
        );
        $users = $query->fetchAll();
        $withTwoFactor = [];
        try {
            // totp_secret arrives with migration 002; older databases simply show 2FA as off.
            $withTwoFactor = array_map('intval', $this->runQuery('SELECT id FROM admin_users WHERE totp_secret IS NOT NULL')->fetchAll(\PDO::FETCH_COLUMN));
        } catch (\Throwable) {
        }
        foreach ($users as $i => $user) {
            $users[$i]['has_two_factor'] = in_array((int) $user['id'], $withTwoFactor, true);
        }

        return $users;
    }

    public function createAdminUser(string $username, string $password, string $role, ?int $actorId): bool
    {
        $username = trim($username);
        if (preg_match('/^[a-zA-Z0-9_.@-]{3,190}$/', $username) !== 1
            || strlen($password) < 12
            || strlen($password) > 1024
            || !in_array($role, ['super_admin', 'admin', 'editor', 'viewer'], true)) {
            return false;
        }
        try {
            $this->pdo->beginTransaction();
            $roleQuery = $this->pdo->prepare('SELECT id FROM admin_roles WHERE role_key = :role');
            $roleQuery->execute(['role' => $role]);
            $roleId = $roleQuery->fetchColumn();
            if ($roleId === false) {
                $this->pdo->rollBack();

                return false;
            }
            $userQuery = $this->pdo->prepare(
                'INSERT INTO admin_users (username, password_hash, is_active) VALUES (:username, :password_hash, 1)',
            );
            $userQuery->execute([
                'username' => $username,
                'password_hash' => password_hash(
                    $password,
                    defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT,
                ),
            ]);
            $userId = (int) $this->pdo->lastInsertId();
            $assignRole = $this->pdo->prepare(
                'INSERT INTO admin_user_roles (admin_user_id, admin_role_id) VALUES (:user_id, :role_id)',
            );
            $assignRole->execute(['user_id' => $userId, 'role_id' => $roleId]);
            $this->audit($actorId, 'admin_user.created', 'admin_user', $userId, ['username' => $username, 'role' => $role]);
            $this->pdo->commit();

            return true;
        } catch (Throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }

    public function updateAdminUser(int $userId, string $role, bool $active, ?int $actorId, ?string $password = null): bool
    {
        if ($userId < 1 || !in_array($role, ['super_admin', 'admin', 'editor', 'viewer'], true)) {
            return false;
        }
        if ($password !== null && $password !== '' && (strlen($password) < 12 || strlen($password) > 1024)) {
            return false;
        }
        try {
            $this->pdo->beginTransaction();
            if ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
                $superAdminLock = $this->pdo->prepare(
                    "SELECT id FROM admin_roles WHERE role_key = 'super_admin' FOR UPDATE",
                );
            } else {
                $superAdminLock = $this->pdo->prepare(
                    "UPDATE admin_roles SET role_key = role_key WHERE role_key = 'super_admin'",
                );
            }
            $superAdminLock->execute();
            $userQuery = $this->pdo->prepare(
                "SELECT u.id, u.is_active,
                    MAX(CASE WHEN r.role_key = 'super_admin' THEN 1 ELSE 0 END) AS is_super_admin
                 FROM admin_users u
                 LEFT JOIN admin_user_roles ur ON ur.admin_user_id = u.id
                 LEFT JOIN admin_roles r ON r.id = ur.admin_role_id
                 WHERE u.id = :id AND u.deleted_at IS NULL GROUP BY u.id, u.is_active",
            );
            $userQuery->execute(['id' => $userId]);
            $user = $userQuery->fetch();
            if (!is_array($user)) {
                $this->pdo->rollBack();

                return false;
            }
            if ((int) $user['is_active'] === 1 && (int) $user['is_super_admin'] === 1
                && (!$active || $role !== 'super_admin')) {
                $admins = $this->pdo->prepare(
                    "SELECT COUNT(*) FROM admin_users u
                     JOIN admin_user_roles ur ON ur.admin_user_id = u.id
                     JOIN admin_roles r ON r.id = ur.admin_role_id
                     WHERE u.is_active = 1 AND u.deleted_at IS NULL AND r.role_key = 'super_admin' AND u.id <> :id",
                );
                $admins->execute(['id' => $userId]);
                if ((int) $admins->fetchColumn() === 0) {
                    $this->pdo->rollBack();

                    return false;
                }
            }
            $roleQuery = $this->pdo->prepare('SELECT id FROM admin_roles WHERE role_key = :role');
            $roleQuery->execute(['role' => $role]);
            $roleId = $roleQuery->fetchColumn();
            if ($roleId === false) {
                $this->pdo->rollBack();

                return false;
            }
            $this->pdo->prepare('DELETE FROM admin_user_roles WHERE admin_user_id = :id')->execute(['id' => $userId]);
            $this->pdo->prepare('INSERT INTO admin_user_roles (admin_user_id, admin_role_id) VALUES (:user_id, :role_id)')
                ->execute(['user_id' => $userId, 'role_id' => $roleId]);
            $this->pdo->prepare('UPDATE admin_users SET is_active = :active, updated_at = CURRENT_TIMESTAMP WHERE id = :id')
                ->execute(['active' => $active ? 1 : 0, 'id' => $userId]);

            if ($password !== null && $password !== '') {
                $passStmt = $this->pdo->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
                $passStmt->execute([
                    'hash' => password_hash(
                        $password,
                        defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT,
                    ),
                    'id' => $userId,
                ]);
                $this->audit($actorId, 'admin_user.password_changed', 'admin_user', $userId, []);
            }

            $this->audit($actorId, 'admin_user.updated', 'admin_user', $userId, ['role' => $role, 'active' => $active]);
            $this->pdo->commit();

            return true;
        } catch (Throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }

    public function recordFailedLogin(string $ip, string $username): void
    {
        $query = $this->pdo->prepare(
            'INSERT INTO failed_login_attempts (ip_hash, account_hash) VALUES (:ip_hash, :account_hash)',
        );
        $query->execute([
            'ip_hash' => hash('sha256', $ip),
            'account_hash' => hash('sha256', strtolower(trim($username))),
        ]);
    }

    public function contentBlocks(): array
    {
        $query = $this->runQuery(
            'SELECT block_key, content, is_active, updated_at FROM content_blocks WHERE deleted_at IS NULL ORDER BY block_key',
        );

        return $query->fetchAll();
    }

    public function updateContentBlock(string $key, string $content, bool $active, ?int $actorId): bool
    {
        if (preg_match('/^[a-z0-9_.-]{1,100}$/i', $key) !== 1 || preg_match('/secret|password|token|credential/i', $key) === 1) {
            return false;
        }
        $query = $this->pdo->prepare('SELECT id FROM content_blocks WHERE block_key = :key AND deleted_at IS NULL');
        $query->execute(['key' => $key]);
        $id = $query->fetchColumn();
        if ($id === false) {
            $query = $this->pdo->prepare('INSERT INTO content_blocks (block_key, content, is_active) VALUES (:key, :content, :active)');
        } else {
            $query = $this->pdo->prepare(
                'UPDATE content_blocks SET content = :content, is_active = :active, updated_at = CURRENT_TIMESTAMP WHERE id = :id',
            );
        }
        $params = ['key' => $key, 'content' => mb_substr($content, 0, 4000), 'active' => $active ? 1 : 0];
        if ($id !== false) {
            unset($params['key']);
            $params['id'] = (int) $id;
        }
        $query->execute($params);
        $this->audit($actorId, 'content_block.updated', 'content_block', $id === false ? (int) $this->pdo->lastInsertId() : (int) $id, ['key' => $key]);

        return true;
    }

    public function analyticsRows(string $fromUtc): array
    {
        $query = $this->pdo->prepare(
            'SELECT form_key, status, submitted_at, assigned_admin_user_id FROM form_submissions
             WHERE submitted_at >= :from ORDER BY submitted_at LIMIT 50000',
        );
        $query->execute(['from' => $fromUtc]);
        $rows = [];
        foreach ($query->fetchAll() as $row) {
            $rows[] = [
                'form_key' => (string) $row['form_key'],
                'status' => (string) $row['status'],
                'submitted_at' => (string) $row['submitted_at'],
                'assigned_admin_user_id' => $row['assigned_admin_user_id'] === null ? null : (int) $row['assigned_admin_user_id'],
            ];
        }

        return $rows;
    }

    public function fieldValues(array $formKeys, string $fieldKey, string $fromUtc): array
    {
        $formKeys = array_values(array_intersect($formKeys, Lead::TYPES));
        if ($formKeys === []) {
            return [];
        }
        $placeholders = [];
        $params = ['field_key' => $fieldKey, 'from' => $fromUtc];
        foreach ($formKeys as $i => $key) {
            $placeholders[] = ':form_' . $i;
            $params['form_' . $i] = $key;
        }
        $query = $this->pdo->prepare(
            "SELECT f.field_value FROM form_submission_fields f JOIN form_submissions s ON s.id = f.submission_id
             WHERE f.field_key = :field_key AND s.submitted_at >= :from AND s.status <> 'spam'
             AND s.form_key IN (" . implode(', ', $placeholders) . ') LIMIT 50000',
        );
        $query->execute($params);
        $values = [];
        foreach ($query->fetchAll(\PDO::FETCH_COLUMN) as $raw) {
            $decoded = json_decode((string) $raw, true);
            if (is_scalar($decoded)) {
                $values[] = (string) $decoded;
            }
        }

        return $values;
    }

    public function firstResponses(string $fromUtc): array
    {
        $query = $this->pdo->prepare(
            "SELECT s.submitted_at, MIN(a.created_at) AS first_action_at
             FROM form_submissions s
             JOIN admin_audit_logs a ON a.entity_type = 'form_submission' AND a.entity_id = s.id
             WHERE s.submitted_at >= :from AND s.form_key <> 'newsletter'
             GROUP BY s.id, s.submitted_at",
        );
        $query->execute(['from' => $fromUtc]);
        $pairs = [];
        foreach ($query->fetchAll() as $row) {
            $pairs[] = ['submitted_at' => (string) $row['submitted_at'], 'first_action_at' => (string) $row['first_action_at']];
        }

        return $pairs;
    }

    public function failedLoginCount(string $fromUtc): int
    {
        $query = $this->pdo->prepare('SELECT COUNT(*) FROM failed_login_attempts WHERE attempted_at >= :from');
        $query->execute(['from' => $fromUtc]);

        return (int) $query->fetchColumn();
    }

    public function navCounts(?int $userId): array
    {
        $query = $this->pdo->prepare(
            "SELECT
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS new_count,
                SUM(CASE WHEN status IN ('new', 'in_review') THEN 1 ELSE 0 END) AS open_count,
                SUM(CASE WHEN status IN ('new', 'in_review') AND assigned_admin_user_id IS NULL THEN 1 ELSE 0 END) AS unassigned_count,
                SUM(CASE WHEN status IN ('new', 'in_review') AND form_key IN ('emergency', 'security_report') THEN 1 ELSE 0 END) AS urgent_count,
                SUM(CASE WHEN status IN ('new', 'in_review') AND assigned_admin_user_id = :me THEN 1 ELSE 0 END) AS mine_count,
                SUM(CASE WHEN status IN ('new', 'in_review') AND submitted_at < :stale THEN 1 ELSE 0 END) AS stale_count
             FROM form_submissions",
        );
        $query->execute(['me' => $userId ?? 0, 'stale' => gmdate('Y-m-d H:i:s', time() - 86400)]);
        $row = $query->fetch() ?: [];

        return [
            'new' => (int) ($row['new_count'] ?? 0),
            'open' => (int) ($row['open_count'] ?? 0),
            'unassigned' => (int) ($row['unassigned_count'] ?? 0),
            'urgent' => (int) ($row['urgent_count'] ?? 0),
            'mine' => (int) ($row['mine_count'] ?? 0),
            'stale' => (int) ($row['stale_count'] ?? 0),
        ];
    }

    public function statusCounts(?string $formKey, ?string $assigneeId = null, ?string $search = null): array
    {
        [$where, $params] = $this->filters($formKey, null, null, null, $assigneeId, $search);
        $query = $this->pdo->prepare('SELECT status, COUNT(*) AS total FROM form_submissions' . $where . ' GROUP BY status');
        $query->execute($params);
        $counts = array_fill_keys(self::STATUSES, 0);
        foreach ($query->fetchAll() as $row) {
            if (isset($counts[$row['status']])) {
                $counts[$row['status']] = (int) $row['total'];
            }
        }

        return $counts;
    }

    public function timeline(int $submissionId): array
    {
        $names = [];
        foreach ($this->adminUsers() as $user) {
            $names[(int) $user['id']] = (string) $user['username'];
        }
        $actor = static fn (mixed $id): string => $id === null ? 'System' : ($names[(int) $id] ?? 'Former team member');
        $entries = [];
        $notes = $this->pdo->prepare('SELECT note, actor_user_id, created_at FROM admin_submission_notes WHERE submission_id = :id');
        $notes->execute(['id' => $submissionId]);
        foreach ($notes->fetchAll() as $row) {
            $entries[] = ['kind' => 'note', 'actor' => $actor($row['actor_user_id']), 'text' => (string) $row['note'], 'created_at' => (string) $row['created_at']];
        }
        $actions = $this->pdo->prepare(
            "SELECT action, actor_user_id, details, created_at FROM admin_audit_logs
             WHERE entity_type = 'form_submission' AND entity_id = :id AND action <> 'submission.note_added'",
        );
        $actions->execute(['id' => $submissionId]);
        foreach ($actions->fetchAll() as $row) {
            $details = json_decode((string) ($row['details'] ?? ''), true);
            $details = is_array($details) ? $details : [];
            $text = match ((string) $row['action']) {
                'submission.status_changed' => 'Status → ' . Ui::statusLabel((string) ($details['status'] ?? '')),
                'submission.assigned' => ($details['assignee_id'] ?? null) === null
                    ? 'Unassigned'
                    : 'Assigned to ' . ($names[(int) $details['assignee_id']] ?? 'a former team member'),
                default => Ui::actionLabel((string) $row['action']),
            };
            $entries[] = ['kind' => 'event', 'actor' => $actor($row['actor_user_id']), 'text' => $text, 'created_at' => (string) $row['created_at']];
        }
        usort($entries, static fn (array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));

        return $entries;
    }

    public function neighbours(int $submissionId): array
    {
        $older = $this->pdo->prepare('SELECT id FROM form_submissions WHERE id < :id ORDER BY id DESC LIMIT 1');
        $older->execute(['id' => $submissionId]);
        $newer = $this->pdo->prepare('SELECT id FROM form_submissions WHERE id > :id ORDER BY id ASC LIMIT 1');
        $newer->execute(['id' => $submissionId]);
        $o = $older->fetchColumn();
        $n = $newer->fetchColumn();

        return ['older' => $o === false ? null : (int) $o, 'newer' => $n === false ? null : (int) $n];
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function withFields(array $row): array
    {
        $query = $this->pdo->prepare(
            'SELECT field_key, field_value FROM form_submission_fields WHERE submission_id = :id ORDER BY id',
        );
        $query->execute(['id' => $row['id']]);
        $data = [];
        foreach ($query->fetchAll() as $field) {
            $decoded = json_decode((string) $field['field_value'], true);
            $data[(string) $field['field_key']] = $decoded ?? (string) $field['field_value'];
        }

        return [
            'id' => $row['form_key'] . '-' . $row['id'],
            'submission_id' => (int) $row['id'],
            'type' => (string) $row['form_key'],
            'form_key' => (string) $row['form_key'],
            'created_at' => (string) $row['submitted_at'],
            'status' => (string) ($row['status'] ?? 'new'),
            'assigned_admin_user_id' => $row['assigned_admin_user_id'] === null ? null : (int) $row['assigned_admin_user_id'],
            'data' => $data,
        ];
    }

    /** @return array{string, array<string, int|string>} */
    private function filters(
        ?string $formKey,
        ?string $status,
        ?string $fromDate,
        ?string $toDate,
        ?string $assigneeId = null,
        ?string $search = null,
    ): array {
        $where = [];
        $params = [];
        if ($formKey === 'urgent') {
            $where[] = "form_key IN ('emergency', 'security_report')";
        } elseif ($formKey !== null && in_array($formKey, Lead::TYPES, true)) {
            $where[] = 'form_key = :form_key';
            $params['form_key'] = $formKey;
        }
        if ($status === 'open') {
            $where[] = "status IN ('new', 'in_review')";
        } elseif ($status !== null && in_array($status, self::STATUSES, true)) {
            $where[] = 'status = :status';
            $params['status'] = $status;
        }
        $search = $search === null ? '' : trim($search);
        if ($search !== '') {
            if (preg_match('/^#?(\d{1,12})$/', $search, $m) === 1) {
                $where[] = 'id = :search_id';
                $params['search_id'] = (int) $m[1];
            } else {
                // Field values are stored JSON-encoded, so a plain LIKE matches inside them.
                $like = '%' . strtr(mb_substr($search, 0, 100), ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
                $where[] = "id IN (SELECT submission_id FROM form_submission_fields WHERE field_value LIKE :search ESCAPE '!')";
                $params['search'] = $like;
            }
        }
        if ($this->isDate($fromDate)) {
            $where[] = 'submitted_at >= :from_date';
            $params['from_date'] = $fromDate . ' 00:00:00';
        }
        if ($this->isDate($toDate)) {
            $nextDay = (new \DateTimeImmutable($toDate))->modify('+1 day')->format('Y-m-d');
            $where[] = 'submitted_at < :to_date';
            $params['to_date'] = $nextDay . ' 00:00:00';
        }
        if ($assigneeId === 'unassigned') {
            $where[] = 'assigned_admin_user_id IS NULL';
        } elseif (is_string($assigneeId) && ctype_digit($assigneeId) && (int) $assigneeId > 0) {
            $where[] = 'assigned_admin_user_id = :assignee_id';
            $params['assignee_id'] = $assigneeId;
        }

        return [$where === [] ? '' : ' WHERE ' . implode(' AND ', $where), $params];
    }

    private function runQuery(string $sql): \PDOStatement
    {
        $statement = $this->pdo->query($sql);
        if ($statement === false) {
            throw new \RuntimeException('Query failed: ' . $sql);
        }

        return $statement;
    }

    /** @phpstan-assert-if-true string $date */
    private function isDate(?string $date): bool
    {
        if ($date === null) {
            return false;
        }
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function registerSubmissionEvents(int $submissionId, string $formKey, array $data): void
    {
        if (in_array($formKey, [Lead::TYPE_EMERGENCY, Lead::TYPE_SECURITY_REPORT], true)) {
            $event = $this->pdo->prepare(
                "INSERT INTO security_events (severity, event_type, description, related_submission_id) VALUES ('high', 'security_report_received', 'A security report was submitted for review.', :id)",
            );
            $event->execute(['id' => $submissionId]);
        }
        $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        if (preg_match('/<\s*script\b|javascript\s*:|UNION\s+SELECT/i', $content) !== 1) {
            return;
        }
        $query = $this->pdo->prepare(
            "INSERT INTO security_events (severity, event_type, description, related_submission_id) VALUES ('high', 'suspicious_submission', 'Submission contains suspicious input patterns.', :id)",
        );
        $query->execute(['id' => $submissionId]);
    }

    /**
     * @param array<string, mixed>|null $details
     */
    private function audit(?int $actorId, string $action, string $entity, ?int $entityId, ?array $details = null): void
    {
        $query = $this->pdo->prepare(
            'INSERT INTO admin_audit_logs (actor_user_id, action, entity_type, entity_id, details) VALUES (:actor, :action, :entity, :entity_id, :details)',
        );
        $query->execute([
            'actor' => $actorId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'details' => $details === null ? null : json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    private function timestamp(string $timestamp): string
    {
        try {
            return (new \DateTimeImmutable($timestamp))->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return gmdate('Y-m-d H:i:s');
        }
    }
}
