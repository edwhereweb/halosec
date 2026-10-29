<?php

declare(strict_types=1);

namespace HaloSec\Services;

use HaloSec\Models\Lead;
use HaloSec\Models\ValidationResult;

final class LeadService
{
    public const HONEYPOT = 'contact_fax';

    /** Field that records which privacy notice the person saw when they submitted. */
    public const NOTICE_FIELD = 'privacy_notice';

    public function __construct(
        private FormValidator $validator,
        private LeadStorageInterface $storage,
        private string $noticeVersion = '',
    ) {
    }

    /**
     * Validates and stores a submission.
     *
     * @param array<string, mixed> $input
     * @return array{status: string, result: ValidationResult}
     *         status is one of: ok, invalid, failed
     */
    public function submit(string $type, array $input): array
    {
        $result = $this->validator->validate($type, $input);

        $honeypot = $input[self::HONEYPOT] ?? '';
        if (!is_string($honeypot) || $honeypot !== '') {
            // Bots get a silent "success" and nothing is stored.
            return ['status' => 'ok', 'result' => $result];
        }

        if (!$result->isValid()) {
            return ['status' => 'invalid', 'result' => $result];
        }

        $data = $result->data;
        if ($this->noticeVersion !== '') {
            $data[self::NOTICE_FIELD] = $this->noticeVersion;
        }
        $lead = new Lead($type, $data, gmdate('c'));

        return ['status' => $this->storage->save($lead) ? 'ok' : 'failed', 'result' => $result];
    }
}
