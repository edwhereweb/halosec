<?php

declare(strict_types=1);

namespace HaloSec\Admin;

/**
 * Presentation helpers for the admin area: human labels, local times and status styles.
 * Stored times are UTC ("Y-m-d H:i:s"); everything shown to admins is in the admin time zone.
 */
final class Ui
{
    public const STATUSES = ['new', 'in_review', 'resolved', 'spam', 'archived'];

    /** Open = still needs someone's attention. */
    public const OPEN_STATUSES = ['new', 'in_review'];

    private const FORM_LABELS = [
        'enquiry' => 'Service enquiry',
        'audit' => 'Free audit',
        'contact' => 'Contact',
        'consultation' => 'Consultation',
        'emergency' => 'Emergency',
        'security_report' => 'Security report',
        'newsletter' => 'Newsletter',
    ];

    private const STATUS_LABELS = [
        'new' => 'New',
        'in_review' => 'In progress',
        'resolved' => 'Done',
        'spam' => 'Spam',
        'archived' => 'Archived',
    ];

    private const FIELD_LABELS = [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'company' => 'Company',
        'company_website' => 'Website',
        'industry' => 'Industry',
        'employee_scale' => 'Employees',
        'service' => 'Service',
        'topic' => 'Topic',
        'preferred_time' => 'Preferred time',
        'attack_type' => 'Attack type',
        'message' => 'Message',
        'notes' => 'Notes',
        'score' => 'Security score',
        'answers' => 'Audit answers',
        'privacy_notice' => 'Privacy notice shown',
    ];

    private const ACTION_LABELS = [
        'submission.status_changed' => 'changed status',
        'submission.assigned' => 'changed assignee',
        'submission.note_added' => 'added a note',
        'submission.erased' => 'permanently deleted a submission',
        'privacy.exported' => 'exported a person’s data',
        'site_setting.updated' => 'updated a site setting',
        'admin_user.created' => 'created a team member',
        'admin_user.updated' => 'updated a team member',
        'admin_user.password_changed' => 'reset a password',
        'content_block.updated' => 'updated website content',
    ];

    private static ?\DateTimeZone $zone = null;

    public static function setTimezone(string $zone): void
    {
        try {
            self::$zone = new \DateTimeZone($zone);
        } catch (\Exception) {
            self::$zone = new \DateTimeZone('UTC');
        }
    }

    public static function timezone(): \DateTimeZone
    {
        return self::$zone ??= new \DateTimeZone('Asia/Kolkata');
    }

    /** Short zone name for headings, e.g. "IST". */
    public static function zoneAbbr(): string
    {
        return (new \DateTimeImmutable('now', self::timezone()))->format('T');
    }

    public static function local(string $utc): ?\DateTimeImmutable
    {
        if ($utc === '') {
            return null;
        }
        try {
            return (new \DateTimeImmutable(str_replace('T', ' ', substr($utc, 0, 19)), new \DateTimeZone('UTC')))
                ->setTimezone(self::timezone());
        } catch (\Exception) {
            return null;
        }
    }

    /** "28 Oct 2026, 4:06 pm" */
    public static function dateTime(string $utc): string
    {
        $date = self::local($utc);

        return $date === null ? '—' : $date->format('j M Y, g:i a');
    }

    /** "Today 4:06 pm", "Yesterday 9:10 am", "26 Oct" (this year) or "26 Oct 2025". */
    public static function shortTime(string $utc, ?int $now = null): string
    {
        $date = self::local($utc);
        if ($date === null) {
            return '—';
        }
        $today = (new \DateTimeImmutable('@' . ($now ?? time())))->setTimezone(self::timezone());
        $days = (int) $today->setTime(0, 0)->diff($date->setTime(0, 0))->format('%r%a');

        return match (true) {
            $days === 0 => 'Today ' . $date->format('g:i a'),
            $days === -1 => 'Yesterday ' . $date->format('g:i a'),
            $date->format('Y') === $today->format('Y') => $date->format('j M'),
            default => $date->format('j M Y'),
        };
    }

    /** "just now", "5m ago", "3h ago", "2d ago", "6w ago". */
    public static function ago(string $utc, ?int $now = null): string
    {
        $date = self::local($utc);
        if ($date === null) {
            return '';
        }
        $seconds = max(0, ($now ?? time()) - $date->getTimestamp());

        return match (true) {
            $seconds < 60 => 'just now',
            $seconds < 3600 => intdiv($seconds, 60) . 'm ago',
            $seconds < 86400 => intdiv($seconds, 3600) . 'h ago',
            $seconds < 86400 * 14 => intdiv($seconds, 86400) . 'd ago',
            default => intdiv($seconds, 86400 * 7) . 'w ago',
        };
    }

    public static function formLabel(string $key): string
    {
        return self::FORM_LABELS[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /** @return array<string, string> form key => label, in display order */
    public static function formLabels(): array
    {
        return self::FORM_LABELS;
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    /** Tailwind classes for a status pill. Colour is always paired with the label text. */
    public static function statusClass(string $status): string
    {
        return match ($status) {
            'new' => 'bg-sky-100 text-sky-900 ring-sky-200',
            'in_review' => 'bg-amber-100 text-amber-900 ring-amber-200',
            'resolved' => 'bg-emerald-100 text-emerald-900 ring-emerald-200',
            default => 'bg-slate-100 text-slate-700 ring-slate-200',
        };
    }

    /** Small dot colour for a form type; emergencies are the only red. */
    public static function formDot(string $key): string
    {
        return match ($key) {
            'emergency', 'security_report' => 'bg-red-600',
            'audit' => 'bg-lime-500',
            'enquiry' => 'bg-sky-600',
            'consultation' => 'bg-violet-500',
            'newsletter' => 'bg-slate-400',
            default => 'bg-slate-600',
        };
    }

    public static function isUrgent(string $formKey, string $status): bool
    {
        return in_array($formKey, ['emergency', 'security_report'], true) && in_array($status, self::OPEN_STATUSES, true);
    }

    public static function fieldLabel(string $key): string
    {
        return self::FIELD_LABELS[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    public static function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? str_replace(['.', '_'], ' ', $action);
    }

    public static function roleLabel(string $role): string
    {
        return match ($role) {
            'super_admin' => 'Owner',
            'admin' => 'Admin',
            'editor' => 'Editor',
            'viewer' => 'Viewer',
            default => ucfirst($role),
        };
    }

    public static function roleHelp(string $role): string
    {
        return match ($role) {
            'super_admin' => 'Everything, including managing the team',
            'admin' => 'Inbox, assignments, website content, security and audit logs',
            'editor' => 'Work the inbox: update status and add notes',
            'viewer' => 'Read-only access to the inbox and reports',
            default => '',
        };
    }

    /**
     * Best display name for a submission: person, then company, then email.
     *
     * @param array<string, mixed> $data
     */
    public static function contactName(array $data): string
    {
        foreach (['name', 'company', 'email'] as $key) {
            if (isset($data[$key]) && is_string($data[$key]) && trim($data[$key]) !== '') {
                return trim($data[$key]);
            }
        }

        return 'Unknown sender';
    }

    /**
     * One-line preview of what the person asked for.
     *
     * @param array<string, mixed> $data
     */
    public static function preview(array $data, int $length = 110): string
    {
        $text = '';
        foreach (['message', 'notes', 'topic', 'service', 'attack_type'] as $key) {
            if (isset($data[$key]) && is_string($data[$key]) && trim($data[$key]) !== '') {
                $text = trim($data[$key]);
                break;
            }
        }
        if ($text === '' && isset($data['score']) && is_numeric($data['score'])) {
            $text = 'Audit score ' . (int) $data['score'] . '/100';
        }
        $text = (string) preg_replace('/\s+/', ' ', $text);

        return mb_strlen($text) > $length ? rtrim(mb_substr($text, 0, $length - 1)) . '…' : $text;
    }

    /** Compact numbers for stat tiles: 1,284 / 12.9K. */
    public static function number(int|float $value): string
    {
        if (abs($value) >= 10000) {
            return rtrim(rtrim(number_format($value / 1000, 1), '0'), '.') . 'K';
        }

        return number_format((float) $value, is_float($value) && floor($value) !== $value ? 1 : 0);
    }
}
