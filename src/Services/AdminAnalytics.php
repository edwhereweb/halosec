<?php

declare(strict_types=1);

namespace HaloSec\Services;

use HaloSec\Admin\Ui;

/**
 * Turns raw submission rows into dashboard and analytics numbers. Pure PHP (no database), so it is easy to
 * test and works the same on MySQL and file storage. Rows carry UTC "Y-m-d H:i:s" times; days and hours are
 * bucketed in the admin time zone so "today" matches the admin's clock.
 *
 * @phpstan-type Row array{form_key: string, status: string, submitted_at: string, assigned_admin_user_id: int|null}
 */
final class AdminAnalytics
{
    public const RANGES = [7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days', 365 => 'Last 12 months'];

    private \DateTimeZone $zone;

    private \DateTimeZone $utc;

    public function __construct(?\DateTimeZone $zone = null, private ?int $now = null)
    {
        $this->zone = $zone ?? Ui::timezone();
        $this->utc = new \DateTimeZone('UTC');
    }

    public static function range(mixed $value): int
    {
        $days = is_string($value) && ctype_digit($value) ? (int) $value : 30;

        return array_key_exists($days, self::RANGES) ? $days : 30;
    }

    /**
     * The current period (last N local days including today) and the N days before it, as UTC bounds.
     *
     * @return array{from: string, to: string, prev_from: string, start_local: \DateTimeImmutable}
     */
    public function period(int $days): array
    {
        $today = $this->today();
        $start = $today->modify('-' . ($days - 1) . ' days');
        $prev = $start->modify('-' . $days . ' days');

        return [
            'from' => $this->toUtc($start),
            'to' => $this->toUtc($today->modify('+1 day')),
            'prev_from' => $this->toUtc($prev),
            'start_local' => $start,
        ];
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return list<array<string, mixed>>
     */
    public function between(array $rows, string $fromUtc, string $toUtc): array
    {
        return array_values(array_filter(
            $rows,
            static fn (array $row): bool => (string) $row['submitted_at'] >= $fromUtc && (string) $row['submitted_at'] < $toUtc,
        ));
    }

    /**
     * Headline numbers for a period, compared with the previous period of the same length.
     *
     * @param list<array<string, mixed>> $current
     * @param list<array<string, mixed>> $previous
     *
     * @return array<string, array{value: int|float|null, previous: int|float|null, delta: float|null}>
     */
    public function summary(array $current, array $previous): array
    {
        $leads = static fn (array $rows): array => array_values(array_filter(
            $rows,
            static fn (array $r): bool => $r['form_key'] !== 'newsletter' && $r['status'] !== 'spam',
        ));
        $count = static fn (array $rows, callable $test): int => count(array_filter($rows, $test));
        $spamRate = static function (array $rows): ?float {
            return $rows === [] ? null : round(100 * count(array_filter($rows, static fn (array $r): bool => $r['status'] === 'spam')) / count($rows), 1);
        };

        $metrics = [
            'leads' => [count($leads($current)), count($leads($previous))],
            'urgent' => [
                $count($current, static fn (array $r): bool => in_array($r['form_key'], ['emergency', 'security_report'], true)),
                $count($previous, static fn (array $r): bool => in_array($r['form_key'], ['emergency', 'security_report'], true)),
            ],
            'audits' => [
                $count($current, static fn (array $r): bool => $r['form_key'] === 'audit'),
                $count($previous, static fn (array $r): bool => $r['form_key'] === 'audit'),
            ],
            'subscribers' => [
                $count($current, static fn (array $r): bool => $r['form_key'] === 'newsletter'),
                $count($previous, static fn (array $r): bool => $r['form_key'] === 'newsletter'),
            ],
            'spam_rate' => [$spamRate($current), $spamRate($previous)],
        ];

        $out = [];
        foreach ($metrics as $key => [$value, $prev]) {
            $out[$key] = ['value' => $value, 'previous' => $prev, 'delta' => self::delta($value, $prev, $key === 'spam_rate' ? 0.0 : 5.0)];
        }

        return $out;
    }

    /**
     * Percentage change, or null when there is nothing meaningful to compare against: no earlier value, or an
     * earlier count below $minimum (going from 1 to 40 is "+3,900%", which says nothing useful).
     */
    public static function delta(int|float|null $value, int|float|null $previous, float $minimum = 0.0): ?float
    {
        if ($value === null || $previous === null || (float) $previous === 0.0 || (float) $previous < $minimum) {
            return null;
        }

        return round(100 * ($value - $previous) / $previous, 1);
    }

    /**
     * Submissions per local day (or per week for ranges over 60 days), oldest first, with empty buckets kept.
     *
     * @param list<array<string, mixed>> $rows
     *
     * @return list<array{key: string, label: string, long: string, count: int}>
     */
    public function series(array $rows, int $days): array
    {
        $weekly = $days > 60;
        $start = $this->period($days)['start_local'];
        if ($weekly) {
            $start = $start->modify('monday this week');
        }
        $buckets = [];
        $cursor = $start;
        $end = $this->today();
        while ($cursor <= $end) {
            $key = $cursor->format('Y-m-d');
            $buckets[$key] = [
                'key' => $key,
                'label' => $cursor->format($weekly ? 'j M' : 'j M'),
                'long' => $weekly ? 'Week of ' . $cursor->format('j M Y') : $cursor->format('D j M Y'),
                'count' => 0,
            ];
            $cursor = $cursor->modify($weekly ? '+1 week' : '+1 day');
        }
        foreach ($rows as $row) {
            $local = $this->local((string) $row['submitted_at']);
            if ($local === null) {
                continue;
            }
            $key = ($weekly ? $local->modify('monday this week') : $local)->format('Y-m-d');
            if (isset($buckets[$key])) {
                $buckets[$key]['count']++;
            }
        }

        return array_values($buckets);
    }

    /**
     * Counts by a row column (form_key, status, assigned_admin_user_id), biggest first.
     *
     * @param list<array<string, mixed>> $rows
     *
     * @return array<string, int>
     */
    public function countBy(array $rows, string $column): array
    {
        $counts = [];
        foreach ($rows as $row) {
            $key = (string) ($row[$column] ?? '');
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        arsort($counts);

        return $counts;
    }

    /**
     * Counts of free values (industries, services…), biggest first, the tail folded into "Other".
     *
     * @param list<string> $values
     *
     * @return array<string, int>
     */
    public function distribution(array $values, int $top = 6): array
    {
        $counts = [];
        foreach ($values as $value) {
            $value = trim($value);
            if ($value !== '') {
                $counts[$value] = ($counts[$value] ?? 0) + 1;
            }
        }
        arsort($counts);
        if (count($counts) <= $top + 1) {
            return $counts;
        }
        $head = array_slice($counts, 0, $top, true);
        $head['Other'] = array_sum(array_slice($counts, $top));

        return $head;
    }

    /**
     * When people get in touch: 7 weekdays (Mon first) × 12 two-hour blocks, in local time.
     *
     * @param list<array<string, mixed>> $rows
     *
     * @return array{cells: list<list<int>>, max: int, days: list<string>, blocks: list<string>}
     */
    public function heatmap(array $rows): array
    {
        $cells = array_fill(0, 7, array_fill(0, 12, 0));
        foreach ($rows as $row) {
            $local = $this->local((string) $row['submitted_at']);
            if ($local !== null) {
                $cells[(int) $local->format('N') - 1][intdiv((int) $local->format('G'), 2)]++;
            }
        }
        $blocks = [];
        for ($h = 0; $h < 24; $h += 2) {
            $blocks[] = (new \DateTimeImmutable('2000-01-01 ' . $h . ':00'))->format('ga');
        }

        return [
            'cells' => $cells,
            'max' => max(1, ...array_map('max', $cells)),
            'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'blocks' => $blocks,
        ];
    }

    /**
     * Audit security scores grouped into the bands shown to prospects.
     *
     * @param list<int> $scores
     *
     * @return array{bands: array<string, int>, average: int|null}
     */
    public function scoreBands(array $scores): array
    {
        $bands = ['Weak (0–39)' => 0, 'Fair (40–69)' => 0, 'Strong (70–100)' => 0];
        foreach ($scores as $score) {
            $key = $score < 40 ? 'Weak (0–39)' : ($score < 70 ? 'Fair (40–69)' : 'Strong (70–100)');
            $bands[$key]++;
        }

        return ['bands' => $bands, 'average' => $scores === [] ? null : (int) round(array_sum($scores) / count($scores))];
    }

    /**
     * Median hours from submission to the first team action (status change, assignment or note).
     *
     * @param list<array{submitted_at: string, first_action_at: string}> $pairs
     */
    public function medianResponseHours(array $pairs): ?float
    {
        $hours = [];
        foreach ($pairs as $pair) {
            $diff = strtotime($pair['first_action_at'] . ' UTC') - strtotime($pair['submitted_at'] . ' UTC');
            if ($diff >= 0) {
                $hours[] = $diff / 3600;
            }
        }
        if ($hours === []) {
            return null;
        }
        sort($hours);
        $mid = intdiv(count($hours), 2);
        $median = count($hours) % 2 === 1 ? $hours[$mid] : ($hours[$mid - 1] + $hours[$mid]) / 2;

        return round($median, 1);
    }

    /** "45 min", "3.5 h", "2.1 days" */
    public static function duration(?float $hours): string
    {
        return match (true) {
            $hours === null => '—',
            $hours < 1 => max(1, (int) round($hours * 60)) . ' min',
            $hours < 48 => rtrim(rtrim(number_format($hours, 1), '0'), '.') . ' h',
            default => rtrim(rtrim(number_format($hours / 24, 1), '0'), '.') . ' days',
        };
    }

    private function today(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable('@' . ($this->now ?? time())))->setTimezone($this->zone)->setTime(0, 0);
    }

    private function toUtc(\DateTimeImmutable $local): string
    {
        return $local->setTimezone($this->utc)->format('Y-m-d H:i:s');
    }

    private function local(string $utc): ?\DateTimeImmutable
    {
        try {
            return (new \DateTimeImmutable(str_replace('T', ' ', substr($utc, 0, 19)), $this->utc))->setTimezone($this->zone);
        } catch (\Exception) {
            return null;
        }
    }
}
