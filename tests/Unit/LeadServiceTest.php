<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Models\Lead;
use HaloSec\Services\FormValidator;
use HaloSec\Services\LeadService;
use HaloSec\Tests\Fixtures\ArrayLeadStorage;
use PHPUnit\Framework\TestCase;

final class LeadServiceTest extends TestCase
{
    private function service(ArrayLeadStorage $storage): LeadService
    {
        return new LeadService(new FormValidator([], [], [], [], [], []), $storage);
    }

    /**
     * @return array<string, string>
     */
    private function valid(): array
    {
        return ['name' => 'Ann', 'email' => 'ann@example.com', 'message' => 'Hi'];
    }

    public function testStoresValidLead(): void
    {
        $storage = new ArrayLeadStorage();
        $out = $this->service($storage)->submit(Lead::TYPE_CONTACT, $this->valid());

        $this->assertSame('ok', $out['status']);
        $this->assertCount(1, $storage->leads);
        $this->assertSame('contact', $storage->leads[0]->type);
    }

    public function testInvalidIsNotStored(): void
    {
        $storage = new ArrayLeadStorage();
        $out = $this->service($storage)->submit(Lead::TYPE_CONTACT, ['name' => '']);

        $this->assertSame('invalid', $out['status']);
        $this->assertSame([], $storage->leads);
    }

    public function testHoneypotSilentlyDiscards(): void
    {
        $storage = new ArrayLeadStorage();
        $out = $this->service($storage)->submit(Lead::TYPE_CONTACT, $this->valid() + [LeadService::HONEYPOT => 'bot']);

        $this->assertSame('ok', $out['status']);
        $this->assertSame([], $storage->leads);
    }

    public function testStorageFailureIsReported(): void
    {
        $out = $this->service(new ArrayLeadStorage(false))->submit(Lead::TYPE_CONTACT, $this->valid());

        $this->assertSame('failed', $out['status']);
    }
}
