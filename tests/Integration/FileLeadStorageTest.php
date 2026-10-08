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
}
