<?php

declare(strict_types=1);

namespace HaloSec\Tests\Integration;

use HaloSec\Models\Lead;
use HaloSec\Services\PdoLeadStorage;
use PDO;
use PHPUnit\Framework\TestCase;

/** The admin inbox, dashboard and timeline queries against a real (SQLite) database. */
final class AdminRepositoryTest extends TestCase
{
    private PDO $pdo;

    private PdoLeadStorage $repo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        $this->pdo->exec(<<<'SQL'
            CREATE TABLE form_submissions (id INTEGER PRIMARY KEY AUTOINCREMENT, form_key TEXT NOT NULL, submitted_at TEXT NOT NULL, status TEXT NOT NULL DEFAULT 'new', assigned_admin_user_id INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP, updated_at TEXT DEFAULT CURRENT_TIMESTAMP);
            CREATE TABLE form_submission_fields (id INTEGER PRIMARY KEY AUTOINCREMENT, submission_id INTEGER NOT NULL, field_key TEXT NOT NULL, field_value TEXT NOT NULL);
            CREATE TABLE admin_submission_notes (id INTEGER PRIMARY KEY AUTOINCREMENT, submission_id INTEGER NOT NULL, actor_user_id INTEGER, note TEXT NOT NULL, created_at TEXT DEFAULT CURRENT_TIMESTAMP);
            CREATE TABLE admin_audit_logs (id INTEGER PRIMARY KEY AUTOINCREMENT, actor_user_id INTEGER, action TEXT NOT NULL, entity_type TEXT NOT NULL, entity_id INTEGER, details TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP);
            CREATE TABLE security_events (id INTEGER PRIMARY KEY AUTOINCREMENT, severity TEXT NOT NULL, event_type TEXT NOT NULL, description TEXT NOT NULL, related_submission_id INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP);
            CREATE TABLE failed_login_attempts (id INTEGER PRIMARY KEY AUTOINCREMENT, ip_hash TEXT NOT NULL, account_hash TEXT NOT NULL, attempted_at TEXT DEFAULT CURRENT_TIMESTAMP);
            CREATE TABLE admin_users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, password_hash TEXT, is_active INTEGER NOT NULL DEFAULT 1, totp_secret TEXT, deleted_at TEXT, updated_at TEXT DEFAULT CURRENT_TIMESTAMP);
            CREATE TABLE admin_roles (id INTEGER PRIMARY KEY AUTOINCREMENT, role_key TEXT);
            CREATE TABLE admin_user_roles (admin_user_id INTEGER, admin_role_id INTEGER);
            INSERT INTO admin_users (id, username, password_hash, totp_secret) VALUES (1, 'anjali', 'x', 'SECRET'), (2, 'rahul', 'x', NULL);
            SQL);
        $this->repo = new PdoLeadStorage($this->pdo);
        $this->save('contact', ['name' => 'Asha Nair', 'email' => 'asha@nila.example', 'message' => '50% off_ offer'], '-3 hours');
        $this->save('emergency', ['name' => 'Ravi Menon', 'email' => 'ravi@kochi.example', 'attack_type' => 'Ransomware', 'message' => 'Encrypted'], '-2 days');
        $this->save('enquiry', ['name' => 'Divya Rao', 'email' => 'divya@kochi.example', 'service' => 'soc-setup', 'message' => 'Quote'], '-1 hour');
        $this->save('audit', ['name' => 'Joseph', 'email' => 'j@x.example', 'industry' => 'Finance', 'score' => 30, 'answers' => ['q_mfa' => 'no']], '-5 days');
    }

    /** @param array<string, mixed> $data */
    private function save(string $type, array $data, string $when): void
    {
        $this->assertTrue($this->repo->save(new Lead($type, $data, gmdate('c', (int) strtotime($when)))));
    }

    private function idOf(string $type): int
    {
        $query = $this->pdo->prepare('SELECT id FROM form_submissions WHERE form_key = :type');
        $query->execute(['type' => $type]);

        return (int) $query->fetchColumn();
    }

    public function testSearchMatchesFieldsIdAndTreatsWildcardsLiterally(): void
    {
        $this->assertSame(2, $this->repo->submissionCount(null, null, null, null, null, 'kochi'));
        $this->assertSame('Asha Nair', $this->repo->listSubmissions(null, null, 1, 10, null, null, null, 'ASHA')[0]['data']['name']);
        $this->assertSame(1, $this->repo->submissionCount(null, null, null, null, null, '#' . $this->idOf('enquiry')));
        // "%" and "_" are literal characters, not LIKE wildcards.
        $this->assertSame(1, $this->repo->submissionCount(null, null, null, null, null, '50%'));
        $this->assertSame(0, $this->repo->submissionCount(null, null, null, null, null, 'f_o'));
    }

    public function testOpenAndUrgentPseudoFilters(): void
    {
        $this->repo->changeStatus($this->idOf('contact'), 'resolved', 1);

        $this->assertSame(3, $this->repo->submissionCount(null, 'open'));
        $this->assertSame(1, $this->repo->submissionCount('urgent', 'open'));
        $counts = $this->repo->statusCounts(null);
        $this->assertSame(3, $counts['new']);
        $this->assertSame(1, $counts['resolved']);
    }

    public function testNavCountsForTheSignedInAdmin(): void
    {
        $this->repo->assign($this->idOf('enquiry'), 1, 1);
        $this->pdo->exec("UPDATE form_submissions SET status = 'spam' WHERE form_key = 'audit'");

        $counts = $this->repo->navCounts(1);
        $this->assertSame(3, $counts['open']);
        $this->assertSame(1, $counts['urgent']);
        $this->assertSame(1, $counts['mine']);
        $this->assertSame(2, $counts['unassigned']);
        $this->assertSame(1, $counts['stale']);
        $this->assertSame(0, $this->repo->navCounts(2)['mine']);
    }

    public function testTimelineMergesNotesAndChangesWithNamesNewestFirst(): void
    {
        $id = $this->idOf('emergency');
        $this->pdo->exec("INSERT INTO admin_audit_logs (actor_user_id, action, entity_type, entity_id, details, created_at) VALUES (1, 'submission.status_changed', 'form_submission', $id, '{\"status\":\"in_review\"}', '2026-01-01 10:00:00')");
        $this->pdo->exec("INSERT INTO admin_audit_logs (actor_user_id, action, entity_type, entity_id, details, created_at) VALUES (1, 'submission.assigned', 'form_submission', $id, '{\"assignee_id\":2}', '2026-01-01 10:05:00')");
        $this->pdo->exec("INSERT INTO admin_submission_notes (submission_id, actor_user_id, note, created_at) VALUES ($id, 2, 'Called back', '2026-01-01 10:10:00')");

        $timeline = $this->repo->timeline($id);
        $this->assertSame(['note', 'event', 'event'], array_column($timeline, 'kind'));
        $this->assertSame('rahul', $timeline[0]['actor']);
        $this->assertSame('Assigned to rahul', $timeline[1]['text']);
        $this->assertSame('Status → In progress', $timeline[2]['text']);
        $this->assertSame('anjali', $timeline[2]['actor']);
    }

    public function testAnalyticsQueries(): void
    {
        $from = gmdate('Y-m-d H:i:s', (int) strtotime('-30 days'));
        $this->assertCount(4, $this->repo->analyticsRows($from));
        $this->assertCount(2, $this->repo->analyticsRows(gmdate('Y-m-d H:i:s', (int) strtotime('-1 day'))));
        $this->assertSame(['soc-setup'], $this->repo->fieldValues(['enquiry'], 'service', $from));
        $this->assertSame(['30'], $this->repo->fieldValues(['audit'], 'score', $from));
        $this->assertSame([], $this->repo->fieldValues(['not-a-form'], 'service', $from));

        $id = $this->idOf('contact');
        $this->repo->changeStatus($id, 'in_review', 1);
        $pairs = $this->repo->firstResponses($from);
        $this->assertCount(1, $pairs);
        $this->assertGreaterThanOrEqual($pairs[0]['submitted_at'], $pairs[0]['first_action_at']);
    }

    public function testAdminUsersReportTwoFactorAndNeighboursNavigate(): void
    {
        $users = array_column($this->repo->adminUsers(), 'has_two_factor', 'username');
        $this->assertTrue($users['anjali']);
        $this->assertFalse($users['rahul']);

        $query = $this->pdo->prepare('SELECT id FROM form_submissions ORDER BY id');
        $query->execute();
        $ids = array_map('intval', $query->fetchAll(PDO::FETCH_COLUMN));
        $this->assertSame(['older' => null, 'newer' => $ids[1]], $this->repo->neighbours($ids[0]));
        $this->assertSame(['older' => $ids[2], 'newer' => null], $this->repo->neighbours($ids[3]));
    }
}
