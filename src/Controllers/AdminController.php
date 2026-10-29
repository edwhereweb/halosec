<?php

declare(strict_types=1);

namespace HaloSec\Controllers;

use HaloSec\Admin\Ui;
use HaloSec\Models\Lead;
use HaloSec\Security\AdminAuth;
use HaloSec\Security\CsrfManager;
use HaloSec\Security\LoginThrottle;
use HaloSec\Security\RolePolicy;
use HaloSec\Services\AdminAnalytics;
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
        private ?string $dbError = null,
        private bool $requireTwoFactor = false,
        private string $issuer = 'HaloSec',
    ) {
    }

    /**
     * Second login step for users with two-factor authentication enabled.
     *
     * @param array<string, mixed> $post
     */
    public function loginTwoFactor(string $method, array $post): string
    {
        $this->noCache();
        if ($this->auth->check()) {
            return $this->redirect('/admin');
        }
        if (!$this->auth->awaitingTwoFactor()) {
            return $this->redirect('/admin/login');
        }
        $flash = null;
        if ($method === 'POST') {
            $code = is_string($post['code'] ?? null) ? $post['code'] : '';
            if (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
                http_response_code(419);
                $flash = 'Your session expired. Please try again.';
            } elseif ($this->throttle->isLocked($this->clientId)) {
                http_response_code(429);
                header('Retry-After: 900');
                $flash = 'Too many failed attempts. Please try again later.';
            } elseif ($this->auth->verifyTwoFactor($code)) {
                $this->throttle->clear($this->clientId);
                $this->csrf->rotate();

                return $this->redirect('/admin');
            } else {
                $this->throttle->recordFailure($this->clientId);
                if (!$this->auth->awaitingTwoFactor()) {
                    return $this->redirect('/admin/login');
                }
                http_response_code(401);
                $flash = 'That code is not valid. Enter the current 6-digit code from your authenticator app.';
            }
        }

        return $this->view->render('pages/admin_login_2fa', [
            'title' => 'Two-factor verification',
            'path' => '/admin/login/2fa',
            'formAction' => '/admin/login/2fa',
            'flash' => $flash === null ? null : ['type' => 'error', 'message' => $flash],
        ]);
    }

    /**
     * Signed-in user's own account: change password and manage two-factor authentication.
     *
     * @param array<string, mixed> $post
     */
    public function account(string $method, array $post): string
    {
        if (($denied = $this->guard('account')) !== null) {
            return $denied;
        }
        $message = null;
        $error = false;
        if ($method === 'POST') {
            if (!$this->validCsrf($post)) {
                return 'Invalid request';
            }
            $action = is_string($post['action'] ?? null) ? $post['action'] : '';
            $str = static fn (string $key): string => is_string($post[$key] ?? null) ? $post[$key] : '';
            switch ($action) {
                case 'password':
                    if ($str('new_password') !== $str('confirm_password')) {
                        [$error, $message] = [true, 'The new passwords do not match.'];
                    } elseif ($this->auth->changePassword($str('current_password'), $str('new_password'))) {
                        $this->csrf->rotate();
                        $message = 'Password changed.';
                    } else {
                        [$error, $message] = [true, 'Password not changed. Check your current password; the new one must be at least 12 characters.'];
                    }
                    break;
                case '2fa_start':
                    $this->auth->beginTwoFactorEnrollment();
                    break;
                case '2fa_confirm':
                    if ($this->auth->confirmTwoFactorEnrollment($str('code'))) {
                        $message = 'Two-factor authentication is now on.';
                    } else {
                        [$error, $message] = [true, 'That code did not match. Check the time on your phone and try again.'];
                    }
                    break;
                case '2fa_disable':
                    if ($this->requireTwoFactor) {
                        [$error, $message] = [true, 'Two-factor authentication is required for all admin accounts.'];
                    } elseif ($this->auth->disableTwoFactor($str('current_password'), $str('code'))) {
                        $message = 'Two-factor authentication is now off.';
                    } else {
                        [$error, $message] = [true, 'Could not turn off two-factor authentication. Check your password and code.'];
                    }
                    break;
                default:
                    [$error, $message] = [true, 'Unknown action.'];
            }
            if ($error) {
                http_response_code(422);
            }
        }
        $secret = $this->auth->hasTwoFactor() ? null : $this->auth->pendingEnrollmentSecret();

        return $this->render('pages/admin_account', 'account', [
            'title' => 'My account',
            'username' => $this->auth->username(),
            'hasTwoFactor' => $this->auth->hasTwoFactor(),
            'requireTwoFactor' => $this->requireTwoFactor,
            'enrollSecret' => $secret,
            'enrollUri' => $secret === null ? null : \HaloSec\Security\Totp::provisioningUri($secret, $this->auth->username(), $this->issuer),
            'message' => $message,
            'messageIsError' => $error,
        ]);
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
            $old = ['username' => function_exists('mb_substr') ? mb_substr($username, 0, 254, 'UTF-8') : substr($username, 0, 254)];

            if (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
                http_response_code(419);
                $flash = 'Your session expired. Please try again.';
            } elseif ($this->throttle->isLocked($this->clientId)) {
                // Never check passwords while locked, otherwise the lockout does not stop brute force.
                http_response_code(429);
                header('Retry-After: 900');
                $flash = 'Too many failed attempts. Please try again later.';
            } elseif ($username === '' || $password === '' || strlen($password) > 1024) {
                http_response_code(422);
                $flash = 'Enter your username and password.';
            } elseif ($this->auth->attempt($username, $password)) {
                $this->csrf->rotate();
                if ($this->auth->awaitingTwoFactor()) {
                    return $this->redirect('/admin/login/2fa');
                }
                $this->throttle->clear($this->clientId);

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
            'path' => '/admin/login',
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
        $analytics = new AdminAnalytics();
        $period = $analytics->period(30);
        $rows = $this->analyticsRows($period['prev_from']);
        $current = $analytics->between($rows, $period['from'], $period['to']);
        $previous = $analytics->between($rows, $period['prev_from'], $period['from']);
        $repo = $this->repository;
        $me = $this->auth->userId();
        $week = $analytics->period(7);

        return $this->render('pages/admin_dashboard', 'dashboard', [
            'title' => 'Dashboard',
            'summary' => $analytics->summary($current, $previous),
            'series' => $analytics->series($current, 30),
            'byType' => $analytics->countBy(array_values(array_filter($current, static fn (array $r): bool => $r['status'] !== 'spam')), 'form_key'),
            'medianResponse' => $repo === null ? null : $analytics->medianResponseHours($repo->firstResponses($period['from'])),
            'previousMedianResponse' => $repo === null ? null : $analytics->medianResponseHours(
                array_values(array_filter($repo->firstResponses($period['prev_from']), static fn (array $p): bool => $p['submitted_at'] < $period['from'])),
            ),
            'urgent' => $repo?->listSubmissions('urgent', 'open', 1, 5) ?? [],
            'unassigned' => $repo?->listSubmissions(null, 'new', 1, 6, null, null, 'unassigned') ?? [],
            'mine' => $repo === null || $me === null ? [] : $repo->listSubmissions(null, 'open', 1, 6, null, null, (string) $me),
            'recent' => $repo?->listSubmissions(null, null, 1, 8) ?? array_slice($this->leads->all(), 0, 8),
            'failedLogins7d' => $repo?->failedLoginCount($week['from']) ?? 0,
            'isDatabase' => $repo !== null,
            'dbError' => $this->dbError,
        ]);
    }

    /** @param array<string, mixed> $query */
    public function analytics(array $query): string
    {
        if (($denied = $this->guard('dashboard')) !== null) {
            return $denied;
        }
        $days = AdminAnalytics::range($query['range'] ?? null);
        $analytics = new AdminAnalytics();
        $period = $analytics->period($days);
        $rows = $this->analyticsRows($period['prev_from']);
        $current = $analytics->between($rows, $period['from'], $period['to']);
        $previous = $analytics->between($rows, $period['prev_from'], $period['from']);
        $notSpam = array_values(array_filter($current, static fn (array $r): bool => $r['status'] !== 'spam'));
        $repo = $this->repository;
        $values = static fn (array $forms, string $field): array => $repo?->fieldValues($forms, $field, $period['from']) ?? [];

        $serviceNames = [];
        foreach ($this->services() as $slug => $service) {
            $serviceNames[$slug] = (string) ($service['nav_label'] ?? $service['title'] ?? $slug);
        }
        $services = [];
        foreach ($analytics->distribution($values(['enquiry'], 'service'), 8) as $slug => $count) {
            $services[$serviceNames[$slug] ?? (string) $slug] = $count;
        }
        $teamNames = [];
        foreach ($repo?->adminUsers() ?? [] as $user) {
            $teamNames[(int) $user['id']] = (string) $user['username'];
        }
        $workload = [];
        $handled = [];
        foreach ($current as $row) {
            $owner = $row['assigned_admin_user_id'] === null ? 'Unassigned' : ($teamNames[(int) $row['assigned_admin_user_id']] ?? 'Former member');
            if (in_array($row['status'], Ui::OPEN_STATUSES, true)) {
                $workload[$owner] = ($workload[$owner] ?? 0) + 1;
            } elseif ($row['status'] === 'resolved' && $owner !== 'Unassigned') {
                $handled[$owner] = ($handled[$owner] ?? 0) + 1;
            }
        }
        arsort($workload);
        arsort($handled);
        $scales = [];
        $scaleCounts = array_count_values($values(['audit'], 'employee_scale'));
        foreach ((array) ($this->config()['employee_scales'] ?? []) as $scale) {
            $scales[(string) $scale] = (int) ($scaleCounts[(string) $scale] ?? 0);
        }

        return $this->render('pages/admin_analytics', 'analytics', [
            'title' => 'Analytics',
            'days' => $days,
            'rangeLabel' => AdminAnalytics::RANGES[$days],
            'summary' => $analytics->summary($current, $previous),
            'series' => $analytics->series($current, $days),
            'byType' => $analytics->countBy($notSpam, 'form_key'),
            'byStatus' => $analytics->countBy($current, 'status'),
            'services' => $services,
            'industries' => $analytics->distribution($values(['audit'], 'industry')),
            'scales' => $scales,
            'topics' => $analytics->distribution($values(['consultation'], 'topic')),
            'attacks' => $analytics->distribution($values(['emergency', 'security_report'], 'attack_type')),
            'scores' => $analytics->scoreBands(array_map('intval', array_filter($values(['audit'], 'score'), 'is_numeric'))),
            'heatmap' => $analytics->heatmap($notSpam),
            'workload' => $workload,
            'handled' => $handled,
            'medianResponse' => $repo === null ? null : $analytics->medianResponseHours($repo->firstResponses($period['from'])),
            'failedLogins' => $repo?->failedLoginCount($period['from']) ?? 0,
            'isDatabase' => $repo !== null,
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
        $all = $this->leads->all($type);
        $total = count($all);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, $page), $pages);

        return $this->render('pages/admin_leads', 'inbox', [
            'title' => 'Inbox',
            'type' => $type,
            'total' => $total,
            'rows' => array_slice($all, ($page - 1) * self::PER_PAGE, self::PER_PAGE),
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

        return $this->render('pages/admin_lead', 'inbox', ['title' => 'Submission', 'lead' => $lead]);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function submissions(array $query): string
    {
        if (($denied = $this->guard('submissions.read')) !== null) {
            return $denied;
        }
        if ($this->repository === null) {
            return $this->leads($query);
        }
        $filters = $this->inboxFilters($query);
        $repo = $this->repository;
        $page = is_string($query['page'] ?? null) && ctype_digit($query['page']) ? max(1, (int) $query['page']) : 1;
        $total = $repo->submissionCount($filters['type'], $filters['statusFilter'], $filters['from'], $filters['to'], $filters['assigneeFilter'], $filters['q']);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $counts = $repo->statusCounts($filters['type'], $filters['assigneeFilter'], $filters['q']);
        $counts = ['open' => ($counts['new'] ?? 0) + ($counts['in_review'] ?? 0)] + $counts + ['all' => array_sum($counts)];

        $active = match (true) {
            $filters['type'] === 'urgent' => 'urgent',
            $filters['assignee'] === 'me' => 'mine',
            $filters['assignee'] === 'unassigned' => 'unassigned',
            default => 'inbox',
        };

        return $this->render('pages/admin_submissions', $active, [
            'title' => match ($active) {
                'urgent' => 'Emergencies',
                'mine' => 'Assigned to me',
                'unassigned' => 'Unassigned',
                default => 'Inbox',
            },
            'filters' => $filters,
            'statusCounts' => $counts,
            'assignees' => $repo->adminUsers(),
            'total' => $total,
            'rows' => $repo->listSubmissions($filters['type'], $filters['statusFilter'], $page, self::PER_PAGE, $filters['from'], $filters['to'], $filters['assigneeFilter'], $filters['q']),
            'page' => $page,
            'pages' => $pages,
            'canManage' => RolePolicy::allows($this->auth->role(), 'submissions.manage'),
            'saved' => is_string($query['saved'] ?? null) ? $query['saved'] : null,
            'erased' => is_string($query['erased'] ?? null) && ctype_digit($query['erased']) ? (int) $query['erased'] : null,
        ]);
    }

    /** @param array<string, mixed> $query */
    public function submission(string $id, array $query = []): string|int
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
        $saved = is_string($query['saved'] ?? null) ? $query['saved'] : null;

        return $this->render('pages/admin_submission', Ui::isUrgent($record['form_key'], $record['status']) ? 'urgent' : 'inbox', [
            'title' => Ui::contactName($record['data']),
            'submission' => $record,
            'timeline' => $repository->timeline((int) $id),
            'neighbours' => $repository->neighbours((int) $id),
            'users' => $repository->adminUsers(),
            'me' => $this->auth->userId(),
            'canManage' => RolePolicy::allows($this->auth->role(), 'submissions.manage'),
            'canAssign' => RolePolicy::allows($this->auth->role(), 'submissions.assign'),
            'canPrivacy' => RolePolicy::allows($this->auth->role(), 'privacy.manage'),
            'personSubmissions' => is_string($record['data']['email'] ?? null)
                ? count($repository->submissionIdsForEmail($record['data']['email']))
                : 0,
            'flashMessage' => match ($saved) {
                'status' => 'Status updated.',
                'assigned' => 'Assignment updated.',
                'note' => 'Note added.',
                default => null,
            },
            'services' => $this->services(),
            'auditQuestions' => (array) ($this->auditConfig()['questions'] ?? []),
            'auditOptions' => (array) ($this->auditConfig()['options'] ?? []),
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

        return $this->redirect('/admin/submissions/' . $id . '?saved=status');
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
        if ($assignee === 'me') {
            $assignee = (string) ($this->auth->userId() ?? '');
        }
        $assigneeId = $assignee === '' ? null : (is_string($assignee) && ctype_digit($assignee) ? (int) $assignee : -1);
        if ($this->repository === null || !ctype_digit($id) || $assigneeId === -1
            || !$this->repository->assign((int) $id, $assigneeId, $this->auth->userId())) {
            http_response_code(422);

            return 'Unable to assign submission.';
        }

        return $this->redirect('/admin/submissions/' . $id . '?saved=assigned');
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

        return $this->redirect('/admin/submissions/' . $id . '?saved=note');
    }

    /**
     * Access / portability request: everything we hold for this submission's email, as JSON.
     *
     * @param array<string, mixed> $post
     */
    public function exportPersonData(string $id, array $post): string|int
    {
        if (($denied = $this->guard('privacy.manage')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $record = $this->repository !== null && ctype_digit($id) ? $this->repository->submission((int) $id) : null;
        if ($record === null || $this->repository === null) {
            return 404;
        }
        $email = is_string($record['data']['email'] ?? null) ? $record['data']['email'] : '';
        $records = $email !== ''
            ? $this->repository->exportForEmail($email, $this->auth->userId())
            : [[
                'reference' => $record['id'],
                'form' => Ui::formLabel($record['form_key']),
                'received_utc' => $record['created_at'],
                'data' => $record['data'],
            ]];
        $json = json_encode([
            'exported_utc' => gmdate('c'),
            'controller' => (string) ($this->config()['privacy']['legal_name'] ?? '') ?: (string) ($this->config()['name'] ?? 'HaloSec'),
            'contact' => (string) ($this->config()['privacy']['email'] ?? ''),
            'submissions' => $records,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="halosec-personal-data-' . $id . '-' . gmdate('Y-m-d') . '.json"');
        header('X-Content-Type-Options: nosniff');

        return $json === false ? '{}' : $json;
    }

    /**
     * Erasure request: permanently deletes this submission, or every submission from the same email.
     *
     * @param array<string, mixed> $post
     */
    public function erasePersonData(string $id, array $post): string|int
    {
        if (($denied = $this->guard('privacy.manage')) !== null) {
            return $denied;
        }
        if (!$this->validCsrf($post)) {
            return 'Invalid request';
        }
        $record = $this->repository !== null && ctype_digit($id) ? $this->repository->submission((int) $id) : null;
        if ($record === null || $this->repository === null) {
            return 404;
        }
        if (($post['confirm'] ?? '') !== 'DELETE') {
            http_response_code(422);

            return 'Type DELETE to confirm. Nothing was deleted.';
        }
        $email = is_string($record['data']['email'] ?? null) ? $record['data']['email'] : '';
        $ids = ($post['scope'] ?? '') === 'person' && $email !== ''
            ? $this->repository->submissionIdsForEmail($email)
            : [(int) $id];
        $erased = $this->repository->eraseSubmissions($ids, $this->auth->userId(), 'request');

        return $this->redirect('/admin/submissions?erased=' . $erased);
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
        $back = is_string($post['return'] ?? null) && str_starts_with($post['return'], '/admin/submissions')
            && !str_contains($post['return'], '//') ? $post['return'] : '/admin/submissions';

        return $this->redirect($back . (str_contains($back, '?') ? '&' : '?') . 'saved=' . $changed);
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
        $f = $this->inboxFilters($query);
        $count = $this->repository->submissionCount($f['type'], $f['statusFilter'], $f['from'], $f['to'], $f['assigneeFilter'], $f['q']);
        $stream = fopen('php://output', 'wb');
        if ($stream === false) {
            http_response_code(500);

            return 'Could not create export.';
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="halosec-submissions-' . gmdate('Y-m-d') . '.csv"');
        header('X-Content-Type-Options: nosniff');
        $names = [];
        foreach ($this->repository->adminUsers() as $user) {
            $names[(int) $user['id']] = (string) $user['username'];
        }
        fputcsv($stream, ['id', 'type', 'received (' . Ui::zoneAbbr() . ')', 'status', 'assignee', 'name', 'email', 'phone', 'company', 'all fields']);
        for ($page = 1; $page <= (int) ceil($count / 100); $page++) {
            foreach ($this->repository->listSubmissions($f['type'], $f['statusFilter'], $page, 100, $f['from'], $f['to'], $f['assigneeFilter'], $f['q']) as $row) {
                $fields = json_encode($row['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
                $data = $row['data'];
                fputcsv($stream, array_map(fn (mixed $v): string => $this->spreadsheetSafe((string) $v), [
                    $row['submission_id'],
                    Ui::formLabel($row['form_key']),
                    Ui::dateTime($row['created_at']),
                    Ui::statusLabel($row['status']),
                    $row['assigned_admin_user_id'] === null ? '' : ($names[$row['assigned_admin_user_id']] ?? $row['assigned_admin_user_id']),
                    is_scalar($data['name'] ?? null) ? $data['name'] : '',
                    is_scalar($data['email'] ?? null) ? $data['email'] : '',
                    is_scalar($data['phone'] ?? null) ? $data['phone'] : '',
                    is_scalar($data['company'] ?? null) ? $data['company'] : '',
                    $fields,
                ]));
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

        $eventType = is_string($query['event'] ?? null) && $query['event'] === 'admin_login_failed'
            ? 'admin_login_failed'
            : 'security_events';
        $severity = is_string($query['severity'] ?? null) && in_array($query['severity'], ['info', 'low', 'medium', 'high', 'critical'], true)
            ? $query['severity']
            : null;
        $analytics = new AdminAnalytics();
        $repo = $this->repository;

        return $this->render('pages/admin_security', 'security', [
            'title' => 'Security log',
            'events' => $repo?->securityEvents(100, $severity, $eventType) ?? [],
            'eventType' => $eventType,
            'severity' => $severity ?? '',
            'failed24h' => $repo?->failedLoginCount(gmdate('Y-m-d H:i:s', time() - 86400)) ?? 0,
            'failed7d' => $repo?->failedLoginCount($analytics->period(7)['from']) ?? 0,
            'failed30d' => $repo?->failedLoginCount($analytics->period(30)['from']) ?? 0,
            'isDatabase' => $repo !== null,
            'dbError' => $this->dbError,
        ]);
    }

    public function audit(): string
    {
        if (($denied = $this->guard('audit.read')) !== null) {
            return $denied;
        }

        return $this->render('pages/admin_audit', 'audit', [
            'title' => 'Audit log',
            'entries' => $this->repository?->auditLogs(200) ?? [],
            'isDatabase' => $this->repository !== null,
            'dbError' => $this->dbError,
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
        $saved = $_GET['saved'] ?? null;

        return $this->render('pages/admin_settings', 'content', [
            'title' => 'Website content',
            'settings' => $this->repository?->settings() ?? [],
            'blocks' => $this->repository?->contentBlocks() ?? [],
            'message' => $message ?? ($saved === 'block' ? 'Website text saved. It is live now.' : null),
            'isDatabase' => $this->repository !== null,
        ]);
    }

    /** @param array<string, mixed> $post */
    public function users(string $method, array $post): string
    {
        if (($denied = $this->guard('users.manage')) !== null) {
            return $denied;
        }
        $message = null;
        $error = false;
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
                $message = 'Could not add this person. Use a unique username and a password of at least 12 characters.';
                $error = true;
            } else {
                $message = $username . ' can now sign in. Ask them to set up 2FA under My account.';
            }
        }
        $saved = $_GET['saved'] ?? null;

        return $this->render('pages/admin_users', 'users', [
            'title' => 'Team & roles',
            'users' => $this->repository?->adminUsers() ?? [],
            'message' => $message ?? ($saved === '1' ? 'Changes saved.' : null),
            'messageIsError' => $error,
            'me' => $this->auth->userId(),
            'requireTwoFactor' => $this->requireTwoFactor,
            'isDatabase' => $this->repository !== null,
            'dbError' => $this->dbError,
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
        $password = is_string($post['password'] ?? null) && trim($post['password']) !== '' ? trim($post['password']) : null;
        $actorId = $this->auth->userId();
        if ($this->repository === null || !ctype_digit($id) || (int) $id === $actorId && (!$active || $role !== 'super_admin')
            || !$this->repository->updateAdminUser((int) $id, $role, $active, $actorId, $password)) {
            http_response_code(422);

            return 'Unable to update this user. Passwords must be at least 12 characters. Keep at least one active super administrator.';
        }

        return $this->redirect('/admin/users?saved=1');
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

        return $this->redirect('/admin/settings?saved=block');
    }

    /**
     * Renders an admin page inside the admin shell, with the sidebar's counts and the signed-in user.
     *
     * @param array<string, mixed> $data
     */
    private function render(string $template, string $active, array $data): string
    {
        $role = $this->auth->role();
        $can = [];
        foreach (['dashboard', 'submissions.read', 'submissions.manage', 'settings.manage', 'security.read', 'audit.read', 'users.manage'] as $permission) {
            $can[$permission] = RolePolicy::allows($role, $permission);
        }
        try {
            $counts = $this->repository?->navCounts($this->auth->userId());
        } catch (\Throwable) {
            $counts = null;
        }

        return $this->view->render($template, $data + [
            'path' => '',
            'nav' => [
                'active' => $active,
                'counts' => $counts,
                'can' => $can,
                'username' => $this->auth->username(),
                'role' => $role,
                'hasTwoFactor' => $this->auth->hasTwoFactor(),
            ],
        ]);
    }

    /**
     * Rows for analytics: from the database, or from file storage (where everything counts as "new").
     *
     * @return list<array<string, mixed>>
     */
    private function analyticsRows(string $fromUtc): array
    {
        if ($this->repository !== null) {
            return $this->repository->analyticsRows($fromUtc);
        }
        $rows = [];
        foreach ($this->leads->all() as $lead) {
            $at = gmdate('Y-m-d H:i:s', strtotime((string) ($lead['created_at'] ?? '')) ?: 0);
            if ($at >= $fromUtc) {
                $rows[] = ['form_key' => (string) ($lead['type'] ?? ''), 'status' => 'new', 'submitted_at' => $at, 'assigned_admin_user_id' => null];
            }
        }

        return $rows;
    }

    /**
     * Normalised inbox filters from the query string.
     *
     * @param array<string, mixed> $query
     *
     * @return array{type: string|null, status: string, statusFilter: string|null, assignee: string|null, assigneeFilter: string|null, from: string|null, to: string|null, q: string|null}
     */
    private function inboxFilters(array $query): array
    {
        $type = is_string($query['type'] ?? null) && ($query['type'] === 'urgent' || in_array($query['type'], Lead::TYPES, true))
            ? $query['type']
            : null;
        $status = is_string($query['status'] ?? null) && in_array($query['status'], array_merge(['open', 'all'], Ui::STATUSES), true)
            ? $query['status']
            : 'open';
        $assignee = ($query['assignee'] ?? null) === 'me' ? 'me' : $this->assigneeFilter($query['assignee'] ?? null);
        $q = is_string($query['q'] ?? null) && trim($query['q']) !== '' ? mb_substr(trim($query['q']), 0, 100) : null;

        return [
            'type' => $type,
            'status' => $status,
            'statusFilter' => $status === 'all' ? null : $status,
            'assignee' => $assignee,
            'assigneeFilter' => $assignee === 'me' ? (string) ($this->auth->userId() ?? '0') : $assignee,
            'from' => $this->dateFilter($query['from'] ?? null),
            'to' => $this->dateFilter($query['to'] ?? null),
            'q' => $q,
        ];
    }

    /** @return array<string, array<string, mixed>> */
    private function services(): array
    {
        $services = $this->view->shared('services');

        return is_array($services) ? $services : [];
    }

    /** @return array<string, mixed> */
    private function config(): array
    {
        $config = $this->view->shared('config');

        return is_array($config) ? $config : [];
    }

    /** @return array<string, mixed> */
    private function auditConfig(): array
    {
        $audit = $this->view->shared('audit');

        return is_array($audit) ? $audit : [];
    }

    private function guard(string $permission): ?string
    {
        $this->noCache();
        if (!$this->auth->check()) {
            return $this->redirect('/admin/login');
        }
        if ($this->requireTwoFactor && $permission !== 'account' && !$this->auth->hasTwoFactor()) {
            return $this->redirect('/admin/account?setup=2fa');
        }
        if ($permission !== 'account' && !RolePolicy::allows($this->auth->role(), $permission)) {
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
