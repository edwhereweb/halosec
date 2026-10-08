<?php

declare(strict_types=1);

namespace HaloSec\Tests\Fixtures;

use HaloSec\Models\Lead;
use HaloSec\Services\LeadStorageInterface;

final class ArrayLeadStorage implements LeadStorageInterface
{
    /** @var list<Lead> */
    public array $leads = [];

    public function __construct(private bool $succeed = true)
    {
    }

    public function save(Lead $lead): bool
    {
        if ($this->succeed) {
            $this->leads[] = $lead;
        }

        return $this->succeed;
    }
}
