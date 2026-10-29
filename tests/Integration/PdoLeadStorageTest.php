<?php

declare(strict_types=1);

namespace HaloSec\Tests\Integration;

use HaloSec\Models\Lead;
use HaloSec\Services\PdoLeadStorage;
use PDO;
use PHPUnit\Framework\TestCase;

final class PdoLeadStorageTest extends TestCase
{
    private PDO $pdo;

    private PdoLeadStorage $storage;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec(
            'CREATE TABLE form_submissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                form_key TEXT NOT NULL,
                submitted_at TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT "new",
                assigned_admin_user_id INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->pdo->exec(
            'CREATE TABLE form_submission_fields (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                submission_id INTEGER NOT NULL,
                field_key TEXT NOT NULL,
                field_value TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->pdo->exec(
            'CREATE TABLE admin_submission_notes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                submission_id INTEGER NOT NULL,
                actor_user_id INTEGER,
                note TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->pdo->exec(
            'CREATE TABLE admin_audit_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                actor_user_id INTEGER,
                action TEXT NOT NULL,
                entity_type TEXT NOT NULL,
                entity_id INTEGER,
                details TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->pdo->exec(
            'CREATE TABLE security_events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                severity TEXT NOT NULL,
                event_type TEXT NOT NULL,
                description TEXT NOT NULL,
                related_submission_id INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->pdo->exec(
            'CREATE TABLE failed_login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_hash TEXT NOT NULL,
                account_hash TEXT NOT NULL,
                attempted_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->pdo->exec(
            'CREATE TABLE admin_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT,
                password_hash TEXT,
                is_active INTEGER NOT NULL DEFAULT 1,
                deleted_at TEXT,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->pdo->exec('CREATE TABLE admin_roles (id INTEGER PRIMARY KEY AUTOINCREMENT, role_key TEXT)');
        $this->pdo->exec('CREATE TABLE admin_user_roles (admin_user_id INTEGER, admin_role_id INTEGER)');
        $this->pdo->exec(
            'CREATE TABLE site_settings (
                setting_key TEXT PRIMARY KEY,
                setting_value TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $this->storage = new PdoLeadStorage($this->pdo);
    }

    public function testSubmissionPersistsNormalizedFieldsAndAdminWorkflow(): void
    {
        $this->assertTrue($this->storage->save(new Lead(
            Lead::TYPE_CONTACT,
            ['name' => 'Amina', 'message' => 'Help please'],
            gmdate('c'),
        )));
        $submission = $this->storage->listSubmissions(Lead::TYPE_CONTACT, 'new', 1, 20)[0];
        $id = $submission['submission_id'];

        $this->assertSame('Amina', $submission['data']['name']);
        $this->assertTrue($this->storage->changeStatus($id, 'in_review', null));
        $this->assertTrue($this->storage->addNote($id, 'Contacted the requester.', null));
        $this->assertSame('in_review', ($this->storage->submission($id)['status'] ?? null));
        $this->assertSame('Contacted the requester.', $this->storage->notes($id)[0]['note']);
        $this->assertSame(2, $this->scalar('SELECT COUNT(*) FROM admin_audit_logs'));
    }

    public function testSuspiciousSubmissionCreatesAHighSeverityEvent(): void
    {
        $this->assertTrue($this->storage->save(new Lead(
            Lead::TYPE_CONTACT,
            ['message' => '<script>alert(1)</script>'],
            gmdate('c'),
        )));

        $events = $this->storage->securityEvents();
        $this->assertCount(1, $events);
        $this->assertSame('high', $events[0]['severity']);
        $this->assertSame('suspicious_submission', $events[0]['event_type']);
    }

    public function testBulkStatusUpdatesAreBoundedAndAudited(): void
    {
        $this->storage->save(new Lead(Lead::TYPE_CONTACT, ['email' => 'person@example.com'], gmdate('c')));
        $this->storage->save(new Lead(Lead::TYPE_AUDIT, ['name' => 'Person'], gmdate('c')));
        $ids = array_column($this->storage->listSubmissions(null, null, 1, 10), 'submission_id');

        $this->assertSame(2, $this->storage->bulkChangeStatus($ids, 'spam', null));
        $this->assertSame(0, $this->storage->bulkChangeStatus($ids, 'unknown', null));
        $this->assertSame(2, $this->scalar("SELECT COUNT(*) FROM form_submissions WHERE status = 'spam'"));
    }

    public function testSubmissionListAndCountCanFilterByAssignee(): void
    {
        $this->storage->save(new Lead(Lead::TYPE_CONTACT, ['name' => 'Assigned'], gmdate('c')));
        $this->storage->save(new Lead(Lead::TYPE_AUDIT, ['name' => 'Unassigned'], gmdate('c')));
        $rows = $this->storage->listSubmissions(null, null, 1, 10);
        $this->pdo->prepare('UPDATE form_submissions SET assigned_admin_user_id = :assignee WHERE id = :id')
            ->execute(['assignee' => 7, 'id' => $rows[0]['submission_id']]);

        $assigned = $this->storage->listSubmissions(null, null, 1, 10, null, null, '7');
        $unassigned = $this->storage->listSubmissions(null, null, 1, 10, null, null, 'unassigned');

        $this->assertCount(1, $assigned);
        $this->assertSame(7, $assigned[0]['assigned_admin_user_id']);
        $this->assertSame(1, $this->storage->submissionCount(null, null, null, null, '7'));
        $this->assertCount(1, $unassigned);
        $this->assertNull($unassigned[0]['assigned_admin_user_id']);
        $this->assertSame(1, $this->storage->submissionCount(null, null, null, null, 'unassigned'));
    }

    public function testSecurityEventsCanBeFilteredByEventType(): void
    {
        $this->storage->recordFailedLogin('127.0.0.1', 'operator');
        $this->pdo->exec(
            "INSERT INTO security_events (severity, event_type, description) VALUES ('high', 'suspicious_submission', 'Test event')",
        );

        $failedLogins = $this->storage->securityEvents(50, null, 'admin_login_failed');
        $securityEvents = $this->storage->securityEvents(50, null, 'security_events');

        $this->assertCount(1, $failedLogins);
        $this->assertSame('admin_login_failed', $failedLogins[0]['event_type']);
        $this->assertCount(1, $securityEvents);
        $this->assertSame('suspicious_submission', $securityEvents[0]['event_type']);
    }

    public function testAllReturnsSubmissionsBeyondOnePage(): void
    {
        for ($i = 0; $i < 101; $i++) {
            $this->assertTrue($this->storage->save(new Lead(Lead::TYPE_CONTACT, ['email' => 'person@example.com'], gmdate('c'))));
        }

        $this->assertCount(101, $this->storage->all(Lead::TYPE_CONTACT));
    }

    public function testUserCreationAndLastSuperAdminProtection(): void
    {
        $this->pdo->exec("INSERT INTO admin_roles (role_key) VALUES ('super_admin'), ('editor'), ('viewer')");
        $this->pdo->exec(
            "INSERT INTO admin_users (username, password_hash, is_active) VALUES ('root', 'existing-hash', 1)",
        );
        $this->pdo->exec('INSERT INTO admin_user_roles VALUES (1, 1)');

        $this->assertFalse($this->storage->updateAdminUser(1, 'viewer', false, 1));
        $this->assertTrue($this->storage->createAdminUser('editor1', 'a-very-long-password', 'editor', 1));
        $users = $this->storage->adminUsers();
        $this->assertCount(2, $users);
        $this->assertSame('editor', $users[0]['role_key']);
        $this->assertTrue($this->storage->updateAdminUser((int) $users[0]['id'], 'viewer', false, 1));
    }

    public function testExportAndEraseEverythingForAnEmail(): void
    {
        $this->storage->save(new Lead(Lead::TYPE_CONTACT, ['email' => 'Person@Example.com', 'message' => '<script>x</script>'], gmdate('c')));
        $this->storage->save(new Lead(Lead::TYPE_AUDIT, ['email' => 'person@example.com', 'name' => 'Person'], gmdate('c')));
        $this->storage->save(new Lead(Lead::TYPE_CONTACT, ['email' => 'other@example.com'], gmdate('c')));
        $ids = $this->storage->submissionIdsForEmail('PERSON@example.com');
        $this->storage->addNote($ids[0], 'Called them.', null);

        $this->assertCount(2, $ids);
        $export = $this->storage->exportForEmail('person@example.com', 7);
        $this->assertCount(2, $export);
        $this->assertSame('Called them.', $export[0]['notes'][0]['note']);

        $this->assertSame(2, $this->storage->eraseSubmissions($ids, 7, 'request'));
        $this->assertSame([], $this->storage->submissionIdsForEmail('person@example.com'));
        $this->assertSame(1, $this->scalar('SELECT COUNT(*) FROM form_submissions'));
        $this->assertSame(0, $this->scalar("SELECT COUNT(*) FROM form_submission_fields WHERE field_value LIKE '%person@%'"));
        $this->assertSame(0, $this->scalar('SELECT COUNT(*) FROM admin_submission_notes'));
        // The security event survives, unlinked from the erased submission.
        $this->assertSame(1, $this->scalar('SELECT COUNT(*) FROM security_events WHERE related_submission_id IS NULL'));
        $this->assertSame(2, $this->scalar("SELECT COUNT(*) FROM admin_audit_logs WHERE action = 'submission.erased'"));
        $this->assertSame(0, $this->scalar("SELECT COUNT(*) FROM admin_audit_logs WHERE details LIKE '%person@%'"));
    }

    public function testRetentionErasesOnlyOldSubmissions(): void
    {
        $this->storage->save(new Lead(Lead::TYPE_CONTACT, ['email' => 'old@example.com'], '2023-01-01T00:00:00+00:00'));
        $this->storage->save(new Lead(Lead::TYPE_CONTACT, ['email' => 'new@example.com'], gmdate('c')));

        $this->assertSame(1, $this->storage->eraseSubmissionsBefore(gmdate('Y-m-d H:i:s', time() - 730 * 86400)));
        $this->assertSame([], $this->storage->submissionIdsForEmail('old@example.com'));
        $this->assertCount(1, $this->storage->submissionIdsForEmail('new@example.com'));
    }

    public function testEmailLookupIgnoresInvalidInput(): void
    {
        $this->storage->save(new Lead(Lead::TYPE_CONTACT, ['email' => 'a@example.com'], gmdate('c')));

        $this->assertSame([], $this->storage->submissionIdsForEmail(''));
        $this->assertSame([], $this->storage->submissionIdsForEmail('%'));
        $this->assertSame(0, $this->storage->eraseSubmissions([], null, 'request'));
    }

    private function scalar(string $sql): int
    {
        $query = $this->pdo->query($sql);
        $this->assertNotFalse($query);

        return (int) $query->fetchColumn();
    }
}
