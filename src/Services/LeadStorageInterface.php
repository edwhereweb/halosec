<?php

declare(strict_types=1);

namespace HaloSec\Services;

use HaloSec\Models\Lead;

interface LeadStorageInterface
{
    /**
     * Persist a lead. Returns false when it could not be stored.
     */
    public function save(Lead $lead): bool;
}
