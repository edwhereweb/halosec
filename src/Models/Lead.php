<?php

declare(strict_types=1);

namespace HaloSec\Models;

final class Lead
{
    public const TYPE_AUDIT = 'audit';
    public const TYPE_ENQUIRY = 'enquiry';
    public const TYPE_CONSULTATION = 'consultation';
    public const TYPE_EMERGENCY = 'emergency';
    public const TYPE_CONTACT = 'contact';

    public const TYPES = [
        self::TYPE_AUDIT,
        self::TYPE_ENQUIRY,
        self::TYPE_CONSULTATION,
        self::TYPE_EMERGENCY,
        self::TYPE_CONTACT,
    ];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly string $type,
        public readonly array $data,
        public readonly string $createdAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'created_at' => $this->createdAt,
            'data' => $this->data,
        ];
    }
}
