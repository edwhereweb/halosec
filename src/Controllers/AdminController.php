<?php

declare(strict_types=1);

namespace HaloSec\Controllers;

use HaloSec\Models\Lead;
use HaloSec\Security\AdminAuth;
use HaloSec\Security\CsrfManager;
use HaloSec\Security\LoginThrottle;
use HaloSec\Security\RolePolicy;
use HaloSec\Services\AdminSubmissionRepositoryInterface;
use HaloSec\Services\LeadReaderInterface;
use HaloSec\View;

/**
 * Admin authentication and operations. Every protected action re-checks authentication and permission.
 */
final class AdminController
{
    public const PER_PAGE = 15;

    public function __construct(
        private View $view,
        private AdminAuth $auth,
        private CsrfManager $csrf,
        private LoginThrottle $throttle,
        private LeadReaderInterface $leads,
        private string $clientId,
        private ?AdminSubmissionRepositoryInterface $repository = null,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     */
    public function login(string $method, array $post): string
    {
        $this->noCache();
        if ($this->auth->check()) {
            return $this->redirect('/admin');
        }

        $old = [];
        $flash = null;
        if ($method === 'POST') {
            $username = is_string($post['username'] ?? null) ? trim($post['username']) : '';
            $password = is_string($post['password'] ?? null) ? $post['password'] : '';
            $old = ['username' => mb_substr($username, 0, 254)];

            if (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
                http_response_code(419);
                $flash = 'Your session expired. Please try again.';
            } elseif ($this->throttle->isLocked($this->clientId)) {
                http_response_code(429);
                header('Retry-After: 900');
                $flash = 'Too many failed attempts. Please try again later.';
            } elseif ($username === '' || $password === '' || strlen($password) > 1024) {
                http_response_code(422);
                $flash = 'Enter your username and password.';
            } elseif ($this->auth->attempt($username, $password)) {
                $this->throttle->clear($this->clientId);
                $this->csrf->rotate();

                return $this->redirect('/admin');
            } else {
                $this->throttle->recordFailure($this->clientId);
                $this->repository?->recordFailedLogin($this->clientId, $username);
                http_response_code(401);
                $flash = 'Invalid username or password.';
            }
        }

        return $this->view->render('pages/admin_login', [
            'title' => 'Admin login',
            'path' => '',
            'formAction' => '/admin/login',
            'old' => $old,
            'errors' => [],
            'flash' => $flash === null ? null : ['type' => 'error', 'message' => $flash],
        ]);
    }

    /**
     * @param array<string, mixed> $post
     */
    public function logout(array $post): string
    {
        $this->noCache();
        if (!$this->auth->check()) {
            return $this->redirect('/admin/login');
        }
        if (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
            http_response_code(419);

            return 'Invalid request';
        }
        $this->auth->logout();

        return $this->redirect('/admin/login');
    }

    public function dashboard(): string
    {
        if (($denied = $this->guard('dashboard')) !== null) {
            return $denied;
        }

        $stats = $this->repository?->dashboardStats();
        $recent = $this->repository?->listSubmissions(null, null, 1, 20) ?? array_slice($this->leads->all(), 0, 8);
        return $this->view->render('pages/admin_dashboard', [
            'title' => 'Dashboard',
            'path' => '',
            'counts' => $this->leads->counts(),
            'recent' => $recent,
            'stats' => $stats,
        ]);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function leads(array $query): string
    {
        if (($denied = $this->guard('submissions.read')) !== null) {
            return $denied;
        }

        $type = is_string($query['type'] ?? null) && in_array($query['type'], Lead::TYPES, true) ? $query['type'] : null;
        $page = is_string($query['page'] ?? null) && ctype_digit($query['page']) ? (int) $query['page'] : 1;
        $all = [];
        if ($this->repository === null) {
            $all = $this->leads->all($type);
            $total = count($all);
        } else {
            $total = $this->repository->submissionCount($type, null);
        }
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, $page), $pages);
        $rows = $this->repository === null
            ? array_slice($all, ($page - 1) * self::PER_PAGE, self::PER_PAGE)
            : $this->repository->listSubmissions($type, null, $page, self::PER_PAGE);

        return $this->view->render('pages/admin_leads', [
            'title' => 'All Inbox',
            'path' => '',
            'type' => $type,
            'total' => $total,
            'rows' => $rows,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    public function lead(string $type, string $number): string|int
    {
        if (($denied = $this->guard('submissions.read')) !== null) {
            return $denied;
        }
        $lead = ctype_digit($number) ? $this->leads->find($type, (int) $number) : null;
        if ($lead === null) {
            return 404;
        }

        return $this->view->render('pages/admin_lead', ['title' => 'Submission Detail', 'path' => '', 'lead' => $lead]);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function submissions(array $query): string
    {
        if (($denied = $this->guard('submissions.read')) !== null) {
            return $denied;
        }
        $formKey = is_string($query['type'] ?? null) && in_array($query['type'], Lead::TYPES, true)
            ? $query['type']
            : null;
        $statuses = ['new', 'in_review', 'resolved', 'spam', 'archived'];
        $status = is_string($query['status'] ?? null) && in_array($query['status'], $statuses, true)
            ? $query['status']
            : null;
        $fromDate = $this->dateFilter($query['from'] ?? null);
        $toDate = $this->dateFilter($query['to'] ?? null);
        $assigneeId = $this->assigneeFilter($query['assignee'] ?? null);
        $page = is_string($query['page'] ?? null) && ctype_digit($query['page']) ? max(1, (int) $query['page']) : 1;
        $allRows = [];
        if ($this->repository === null) {
            $allRows = $this->leads->all($formKey);
            $allRows = array_values(array_filter($allRows, static function (array $row) use ($status, $fromDate, $toDate, $assigneeId): bool {
                $rowStatus = (string) ($row['status'] ?? 'new');
                $submittedDate = substr((string) ($row['created_at'] ?? ''), 0, 10);
                if (($status !== null && $status !== $rowStatus)
                    || ($fromDate !== null && $submittedDate < $fromDate)
                    || ($toDate !== null && $submittedDate > $toDate)) {
                    return false;
                }
                if ($assigneeId === 'unassigned') {
                    return empty($row['assigned_admin_user_id']);
                }

                return $assigneeId === null || (string) ($row['assigned_admin_user_id'] ?? '') === $assigneeId;
            }));
            $total = count($allRows);
        } else {
            $total = $this->repository->submissionCount($formKey, $status, $fromDate, $toDate, $assigneeId);
        }
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $rows = $this->repository === null
            ? array_slice($allRows, ($page - 1) * self::PER_PAGE, self::PER_PAGE)
            : $this->repository->listSubmissions($formKey, $status, $page, self::PER_PAGE, $fromDate, $toDate, $assigneeId);

        return $this->view->render('pages/admin_submissions', [
            'title' => 'All Inbox',
            'path' => '',
            'type' => $formKey,
            'status' => $status,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'assigneeId' => $assigneeId,
            'assignees' => $this->repository?->adminUsers() ?? [],
            'total' => $total,
            'rows' => $rows,
            'page' => $page,
            'pages' => $pages,
            'canManage' => RolePolicy::allows($this->auth->role(), 'submissions.manage'),
        ]);
    }

    public function submission(string $id): string|int
    {
        if (($denied = $this->guard('submissions.read')) !== null) {
            return $denied;
        }
        $repository = $this->repository;
        if (!ctype_digit($id) || $repository === null) {
            return 404;
        }
        $record = $repository->submission((int) $id);
        if ($record === null) {
            return 404;
        }

        return $this->view->render('pages/admin_submission', [
            'title' => 'Submission Detail',
            'path' => '',
            'submission' => $record,
            'notes' => $repository->notes((int) $id),
            'users' => $repository->adminUsers(),
            'canManage' => RolePolicy::allows($this->auth->role(), 'submissions.manage'),
            'canAssign' => RolePolicy::allows($this->auth->role(), 'submissions.assign'),
        ]);
    }

    /** @param array<string, mixed> $post */
    public function changeSubmissionStatus(string $id, array $post): string
    {
        if (($denied = $this->guard('submissions.manage')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $status = is_string($post['status'] ?? null) ? $post['status'] : '';
        if ($this->repository === null || !ctype_digit($id)
            || !$this->repository->changeStatus((int) $id, $status, $this->auth->userId())) {
            http_response_code(422);

            return 'Unable to update submission.';
        }

        return $this->redirect('/admin/submissions/' . $id);
    }

    /** @param array<string, mixed> $post */
    public function assignSubmission(string $id, array $post): string
    {
        if (($denied = $this->guard('submissions.assign')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $assignee = $post['assigned_admin_user_id'] ?? '';
        $assigneeId = $assignee === '' ? null : (is_string($assignee) && ctype_digit($assignee) ? (int) $assignee : -1);
        if ($this->repository === null || !ctype_digit($id) || $assigneeId === -1
            || !$this->repository->assign((int) $id, $assigneeId, $this->auth->userId())) {
            http_response_code(422);

            return 'Unable to assign submission.';
        }

        return $this->redirect('/admin/submissions/' . $id);
    }

    /** @param array<string, mixed> $post */
    public function addSubmissionNote(string $id, array $post): string
    {
        if (($denied = $this->guard('submissions.manage')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $note = is_string($post['note'] ?? null) ? $post['note'] : '';
        if ($this->repository === null || !ctype_digit($id)
            || !$this->repository->addNote((int) $id, $note, $this->auth->userId())) {
            http_response_code(422);

            return 'Unable to save note.';
        }

        return $this->redirect('/admin/submissions/' . $id);
    }

    /** @param array<string, mixed> $post */
    public function bulkChangeSubmissionStatus(array $post): string
    {
        if (($denied = $this->guard('submissions.manage')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $rawIds = is_array($post['submission_ids'] ?? null) ? $post['submission_ids'] : [];
        $ids = [];
        foreach (array_slice($rawIds, 0, 100) as $rawId) {
            if (is_string($rawId) && ctype_digit($rawId) && (int) $rawId > 0) {
                $ids[] = (int) $rawId;
            }
        }
        $status = is_string($post['status'] ?? null) ? $post['status'] : '';
        $changed = $this->repository?->bulkChangeStatus($ids, $status, $this->auth->userId()) ?? 0;
        if ($changed === 0) {
            http_response_code(422);

            return 'No submissions were updated.';
        }

        return $this->redirect('/admin/submissions');
    }

    /** @param array<string, mixed> $query */
    public function exportSubmissions(array $query): string
    {
        if (($denied = $this->guard('submissions.read')) !== null) {
            return $denied;
        }
        if ($this->repository === null) {
            http_response_code(503);

            return 'Database storage is required for CSV export.';
        }
        $formKey = is_string($query['type'] ?? null) && in_array($query['type'], Lead::TYPES, true)
            ? $query['type']
            : null;
        $status = is_string($query['status'] ?? null) && in_array($query['status'], ['new', 'in_review', 'resolved', 'spam', 'archived'], true)
            ? $query['status']
            : null;
        $fromDate = $this->dateFilter($query['from'] ?? null);
        $toDate = $this->dateFilter($query['to'] ?? null);
        $assigneeId = $this->assigneeFilter($query['assignee'] ?? null);
        $count = $this->repository->submissionCount($formKey, $status, $fromDate, $toDate, $assigneeId);
        $stream = fopen('php://output', 'wb');
        if ($stream === false) {
            http_response_code(500);

            return 'Could not create export.';
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="halosec-submissions.csv"');
        header('X-Content-Type-Options: nosniff');
        fputcsv($stream, ['id', 'form_key', 'submitted_at', 'status', 'assigned_admin_user_id', 'fields']);
        for ($page = 1; $page <= (int) ceil($count / 100); $page++) {
            foreach ($this->repository->listSubmissions($formKey, $status, $page, 100, $fromDate, $toDate, $assigneeId) as $row) {
                $fields = json_encode($row['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
                fputcsv($stream, [
                    $row['id'],
                    $row['form_key'],
                    $row['created_at'],
                    $row['status'],
                    $row['assigned_admin_user_id'] ?? '',
                    $this->spreadsheetSafe($fields),
                ]);
            }
        }
        fclose($stream);

        return '';
    }

    /** @param array<string, mixed> $query */
    public function security(array $query): string
    {
        if (($denied = $this->guard('security.read')) !== null) {
            return $denied;
        }

        $eventType = is_string($query['event'] ?? null)
            && in_array($query['event'], ['admin_login_failed', 'security_events'], true)
            ? $query['event']
            : null;

        return $this->view->render('pages/admin_security', [
            'title' => $eventType === 'admin_login_failed' ? 'Failed Logins' : 'Security Events',
            'path' => '',
            'events' => $this->repository?->securityEvents(
                50,
                is_string($query['severity'] ?? null) ? $query['severity'] : null,
                $eventType,
            ) ?? [],
            'eventType' => $eventType ?? '',
            'severity' => is_string($query['severity'] ?? null) ? $query['severity'] : '',
        ]);
    }

    public function audit(): string
    {
        if (($denied = $this->guard('audit.read')) !== null) {
            return $denied;
        }

        return $this->view->render('pages/admin_audit', [
            'title' => 'Audit Logs',
            'path' => '',
            'entries' => $this->repository?->auditLogs() ?? [],
        ]);
    }

    /** @param array<string, mixed> $post */
    public function settings(string $method, array $post): string
    {
        if (($denied = $this->guard('settings.manage')) !== null) {
            return $denied;
        }
        $message = null;
        if ($method === 'POST') {
            if (!$this->validCsrf($post)) {
                http_response_code(419);

                return 'Invalid request';
            }
            $key = is_string($post['key'] ?? null) ? $post['key'] : '';
            $value = is_string($post['value'] ?? null) ? $post['value'] : '';
            if ($this->repository === null || !$this->repository->updateSetting($key, $value, $this->auth->userId())) {
                http_response_code(422);
                $message = 'Unable to save this setting.';
            } else {
                $message = 'Setting saved.';
            }
        }

        return $this->view->render('pages/admin_settings', [
            'title' => 'Site Settings',
            'path' => '',
            'settings' => $this->repository?->settings() ?? [],
            'blocks' => $this->repository?->contentBlocks() ?? [],
            'message' => $message,
        ]);
    }

    /** @param array<string, mixed> $post */
    public function users(string $method, array $post): string
    {
        if (($denied = $this->guard('users.manage')) !== null) {
            return $denied;
        }
        $message = null;
        if ($method === 'POST') {
            if (!$this->validCsrf($post)) {
                http_response_code(419);

                return 'Invalid request';
            }
            $username = is_string($post['username'] ?? null) ? $post['username'] : '';
            $password = is_string($post['password'] ?? null) ? $post['password'] : '';
            $role = is_string($post['role'] ?? null) ? $post['role'] : '';
            if ($this->repository === null
                || !$this->repository->createAdminUser($username, $password, $role, $this->auth->userId())) {
                http_response_code(422);
                $message = 'Unable to create user. Use a unique username and a password of at least 12 characters.';
            } else {
                $message = 'Admin user created.';
            }
        }

        return $this->view->render('pages/admin_users', [
            'title' => 'Admin Users / Roles',
            'path' => '',
            'users' => $this->repository?->adminUsers() ?? [],
            'message' => $message,
        ]);
    }

    /** @param array<string, mixed> $post */
    public function updateUser(string $id, array $post): string
    {
        if (($denied = $this->guard('users.manage')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $role = is_string($post['role'] ?? null) ? $post['role'] : '';
        $active = ($post['active'] ?? null) === '1';
        $actorId = $this->auth->userId();
        if ($this->repository === null || !ctype_digit($id) || (int) $id === $actorId && (!$active || $role !== 'super_admin')
            || !$this->repository->updateAdminUser((int) $id, $role, $active, $actorId)) {
            http_response_code(422);

            return 'Unable to update this user. Keep at least one active super administrator.';
        }

        return $this->redirect('/admin/users');
    }

    /** @param array<string, mixed> $post */
    public function updateContentBlock(array $post): string
    {
        if (($denied = $this->guard('settings.manage')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $key = is_string($post['key'] ?? null) ? $post['key'] : '';
        $content = is_string($post['content'] ?? null) ? $post['content'] : '';
        $active = ($post['active'] ?? null) === '1';
        if ($this->repository === null
            || !$this->repository->updateContentBlock($key, $content, $active, $this->auth->userId())) {
            http_response_code(422);

            return 'Unable to save content block.';
        }

        return $this->redirect('/admin/settings');
    }

    private function guard(string $permission): ?string
    {
        $this->noCache();
        if (!$this->auth->check()) {
            return $this->redirect('/admin/login');
        }
        if (!RolePolicy::allows($this->auth->role(), $permission)) {
            http_response_code(403);

            return 'Forbidden';
        }

        return null;
    }

    /** @param array<string, mixed> $post */
    private function validCsrf(array $post): bool
    {
        if ($this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
            return true;
        }
        http_response_code(419);

        return false;
    }

    private function spreadsheetSafe(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/', $value) === 1 ? "'" . $value : $value;
    }

    private function dateFilter(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value ? $value : null;
    }

    private function assigneeFilter(mixed $value): ?string
    {
        if ($value === 'unassigned') {
            return 'unassigned';
        }

        return is_string($value) && ctype_digit($value) && (int) $value > 0 ? $value : null;
    }

    private function redirect(string $to): string
    {
        header('Location: ' . $to, true, 303);

        return '';
    }

    private function noCache(): void
    {
        header('Cache-Control: no-store');
        header('X-Robots-Tag: noindex, nofollow');
    }
}
