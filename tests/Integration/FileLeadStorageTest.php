<?php

declare(strict_types=1);

namespace HaloSec\Tests\Integration;

use HaloSec\Models\Lead;
use HaloSec\Services\FileLeadStorage;
use PHPUnit\Framework\TestCase;

final class FileLeadStorageTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/halosec_' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            unlink($file);
        }
        if (is_dir($this->dir)) {
            rmdir($this->dir);
        }
    }

    public function testAppendsJsonLines(): void
    {
        $storage = new FileLeadStorage($this->dir);
        $this->assertTrue($storage->save(new Lead('contact', ['name' => 'Ann'], '2026-01-01T00:00:00+00:00')));
        $this->assertTrue($storage->save(new Lead('contact', ['name' => 'Bob'], '2026-01-01T00:00:01+00:00')));

        $lines = file($this->dir . '/contact.jsonl', FILE_IGNORE_NEW_LINES);
        $this->assertIsArray($lines);
        $this->assertCount(2, $lines);
        $decoded = json_decode($lines[1], true);
        $this->assertIsArray($decoded);
        $this->assertSame('Bob', $decoded['data']['name']);
        $this->assertSame('2026-01-01T00:00:01+00:00', $decoded['created_at']);
    }

    public function testRejectsUnknownType(): void
    {
        $this->assertFalse((new FileLeadStorage($this->dir))->save(new Lead('../evil', [], 'now')));
    }

    public function testFailsWhenDirectoryUnwritable(): void
    {
        $this->assertFalse((new FileLeadStorage('/proc/halosec_nope'))->save(new Lead('contact', [], 'now')));
    }

    public function testReaderCountsFiltersAndFindsLeads(): void
    {
        $storage = new FileLeadStorage($this->dir);
        $storage->save(new Lead(Lead::TYPE_CONTACT, ['name' => 'A'], '2024-01-01T00:00:00+00:00'));
        $storage->save(new Lead(Lead::TYPE_CONTACT, ['name' => 'B'], '2024-02-01T00:00:00+00:00'));
        $storage->save(new Lead(Lead::TYPE_AUDIT, ['name' => 'C'], '2024-03-01T00:00:00+00:00'));

        $this->assertSame(2, $storage->counts()[Lead::TYPE_CONTACT]);
        $this->assertSame(0, $storage->counts()[Lead::TYPE_EMERGENCY]);
        $this->assertSame('C', $storage->all()[0]['data']['name']);
        $this->assertCount(2, $storage->all(Lead::TYPE_CONTACT));
        $this->assertSame([], $storage->all('../etc/passwd'));
        $this->assertSame('B', $storage->find(Lead::TYPE_CONTACT, 2)['data']['name'] ?? null);
        $this->assertNull($storage->find(Lead::TYPE_CONTACT, 3));
        $this->assertNull($storage->find('../x', 1));
    }
}
