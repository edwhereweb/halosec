<?php

declare(strict_types=1);

namespace HaloSec\Services;

use HaloSec\Models\Lead;
use HaloSec\Models\ValidationResult;

final class FormValidator
{
    /**
     * @param list<string> $employeeScales
     * @param list<string> $attackTypes
     * @param list<string> $consultationTopics
     * @param list<string> $serviceSlugs
     * @param array<string, string> $auditQuestions
     * @param array<string, string> $auditOptions
     */
    public function __construct(
        private array $employeeScales,
        private array $attackTypes,
        private array $consultationTopics,
        private array $serviceSlugs,
        private array $auditQuestions,
        private array $auditOptions,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function validate(string $type, array $input): ValidationResult
    {
        $data = [];
        $errors = [];

        $text = function (string $key, int $max, bool $required, string $label) use ($input, &$data, &$errors): void {
            $value = self::clean($input[$key] ?? '', $max, $max > 200);
            $data[$key] = $value;
            if ($required && $value === '') {
                $errors[$key] = $label . ' is required.';
            }
        };
        $email = function () use ($input, &$data, &$errors): void {
            $value = self::clean($input['email'] ?? '', 254, false);
            $data['email'] = $value;
            if ($value === '' || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                $errors['email'] = 'A valid email address is required.';
            }
        };
        $phone = function (bool $required) use ($input, &$data, &$errors): void {
            $value = self::clean($input['phone'] ?? '', 30, false);
            $data['phone'] = $value;
            if ($value === '' && $required) {
                $errors['phone'] = 'A phone number is required.';
            } elseif ($value !== '' && preg_match('/^\+?[0-9][0-9 ()\-]{5,28}$/', $value) !== 1) {
                $errors['phone'] = 'Enter a valid phone number.';
            }
        };
        $choice = function (string $key, array $allowed, string $label, bool $required = true) use ($input, &$data, &$errors): void {
            $value = self::clean($input[$key] ?? '', 100, false);
            $data[$key] = $value;
            if ($value === '' && !$required) {
                return;
            }
            if (!in_array($value, $allowed, true)) {
                $errors[$key] = 'Please choose a valid ' . $label . '.';
            }
        };

        switch ($type) {
            case Lead::TYPE_AUDIT:
                $text('name', 100, true, 'Your name');
                $email();
                $phone(false);
                $text('company', 150, true, 'Company name');
                $text('company_website', 200, false, 'Website');
                $text('industry', 100, false, 'Industry');
                $choice('employee_scale', $this->employeeScales, 'employee range');
                $answers = $this->answers($input['answers'] ?? null, $errors);
                $data['answers'] = $answers;
                $data['score'] = $this->score($answers);
                $text('notes', 1000, false, 'Notes');
                break;
            case Lead::TYPE_ENQUIRY:
                $choice('service', $this->serviceSlugs, 'service');
                $text('name', 100, true, 'Your name');
                $email();
                $phone(false);
                $text('company', 150, false, 'Company');
                $text('message', 2000, true, 'Message');
                break;
            case Lead::TYPE_CONSULTATION:
                $text('name', 100, true, 'Your name');
                $email();
                $phone(false);
                $text('company', 150, false, 'Company');
                $choice('topic', $this->consultationTopics, 'topic');
                $text('preferred_time', 100, false, 'Preferred time');
                $text('message', 2000, false, 'Message');
                break;
            case Lead::TYPE_EMERGENCY:
                $text('name', 100, true, 'Your name');
                $email();
                $phone(true);
                $text('company', 150, false, 'Company');
                $choice('attack_type', $this->attackTypes, 'attack type');
                $text('message', 2000, true, 'Description of the incident');
                break;
            case Lead::TYPE_CONTACT:
                $text('name', 100, true, 'Your name');
                $email();
                $phone(false);
                $text('message', 2000, true, 'Message');
                break;
            default:
                $errors['form'] = 'Unknown form.';
        }

        return new ValidationResult($data, $errors);
    }

    /**
     * Score 0-100: yes = 2, partial = 1, no / unsure = 0 points per question.
     *
     * @param array<string, string> $answers
     */
    public function score(array $answers): int
    {
        if ($this->auditQuestions === []) {
            return 0;
        }
        $points = 0;
        foreach ($answers as $answer) {
            $points += match ($answer) {
                'yes' => 2,
                'partial' => 1,
                default => 0,
            };
        }

        return (int) round($points / (count($this->auditQuestions) * 2) * 100);
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function answers(mixed $raw, array &$errors): array
    {
        if ($this->auditQuestions === [] || $raw === null || $raw === []) {
            return [];
        }
        $raw = is_array($raw) ? $raw : [];
        $answers = [];
        foreach (array_keys($this->auditQuestions) as $id) {
            $value = $raw[$id] ?? '';
            if (!is_string($value) || !array_key_exists($value, $this->auditOptions)) {
                $errors['answers'] = 'Please answer every question.';
                continue;
            }
            $answers[$id] = $value;
        }

        return $answers;
    }

    /**
     * Normalises untrusted input: non-scalar becomes empty, control characters stripped, trimmed, length limited.
     */
    public static function clean(mixed $value, int $max, bool $multiline): string
    {
        if (!is_string($value)) {
            return '';
        }
        $pattern = $multiline ? '/[^\P{C}\n]+/u' : '/\p{C}+/u';
        $value = preg_replace($pattern, '', str_replace("\r\n", "\n", $value)) ?? '';
        $trimmed = trim($value);

        return function_exists('mb_substr') ? mb_substr($trimmed, 0, $max, 'UTF-8') : substr($trimmed, 0, $max);
    }
}
