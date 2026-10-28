<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Admin\Ui;
use HaloSec\Services\AdminAnalytics;
use PHPUnit\Framework\TestCase;

final class AdminAnalyticsTest extends TestCase
{
    /** 2026-10-28 18:30:00 UTC = 29 Oct 00:00 in India. */
    private const NOW = 1793212200;

    private function analytics(): AdminAnalytics
    {
        return new AdminAnalytics(new \DateTimeZone('Asia/Kolkata'), self::NOW + 3600);
    }

    /** @return array{form_key: string, status: string, submitted_at: string, assigned_admin_user_id: int|null} */
    private function row(string $utc, string $form = 'contact', string $status = 'new', ?int $owner = null): array
    {
        return ['form_key' => $form, 'status' => $status, 'submitted_at' => $utc, 'assigned_admin_user_id' => $owner];
    }

    public function testPeriodStartsAtLocalMidnight(): void
    {
        $period = $this->analytics()->period(7);

        // 7 local days ending today (29 Oct IST) start on 23 Oct 00:00 IST = 22 Oct 18:30 UTC.
        $this->assertSame('2026-10-22 18:30:00', $period['from']);
        $this->assertSame('2026-10-29 18:30:00', $period['to']);
        $this->assertSame('2026-10-15 18:30:00', $period['prev_from']);
    }

    public function testSeriesBucketsByLocalDayAndKeepsEmptyDays(): void
    {
        $series = $this->analytics()->series([
            $this->row('2026-10-28 19:00:00'), // 29 Oct 00:30 IST: today, not yesterday
            $this->row('2026-10-28 10:00:00'), // 28 Oct IST
            $this->row('2026-10-28 11:00:00'),
        ], 7);

        $this->assertCount(7, $series);
        $this->assertSame('2026-10-23', $series[0]['key']);
        $this->assertSame(0, $series[0]['count']);
        $this->assertSame(2, $series[5]['count']);
        $this->assertSame(1, $series[6]['count']);
        $this->assertSame('Thu 29 Oct 2026', $series[6]['long']);
    }

    public function testLongRangesUseWeeklyBuckets(): void
    {
        $series = $this->analytics()->series([], 90);

        $this->assertLessThanOrEqual(15, count($series));
        $this->assertStringStartsWith('Week of', $series[0]['long']);
    }

    public function testSummaryExcludesNewsletterAndSpamFromLeadsAndGuardsTinyBaselines(): void
    {
        $current = [
            $this->row('2026-10-28 10:00:00', 'enquiry'),
            $this->row('2026-10-28 10:00:00', 'audit'),
            $this->row('2026-10-28 10:00:00', 'newsletter'),
            $this->row('2026-10-28 10:00:00', 'contact', 'spam'),
            $this->row('2026-10-28 10:00:00', 'emergency'),
        ];
        $previous = [$this->row('2026-10-10 10:00:00', 'enquiry')];
        $summary = $this->analytics()->summary($current, $previous);

        $this->assertSame(3, $summary['leads']['value']);
        $this->assertSame(1, $summary['leads']['previous']);
        // 1 → 3 is "+200%", which is noise; no percentage below a baseline of 5.
        $this->assertNull($summary['leads']['delta']);
        $this->assertSame(1, $summary['urgent']['value']);
        $this->assertSame(20.0, $summary['spam_rate']['value']);
        $this->assertSame(50.0, AdminAnalytics::delta(15, 10, 5.0));
    }

    public function testHeatmapUsesLocalWeekdayAndTwoHourBlocks(): void
    {
        // Wed 28 Oct 2026 04:45 UTC = 10:15 IST → Wednesday, 10am–12pm block.
        $heatmap = $this->analytics()->heatmap([$this->row('2026-10-28 04:45:00'), $this->row('2026-10-28 05:10:00')]);

        $this->assertSame(2, $heatmap['cells'][2][5]);
        $this->assertSame(2, $heatmap['max']);
        $this->assertSame('10am', $heatmap['blocks'][5]);
    }

    public function testDistributionFoldsTheTailIntoOther(): void
    {
        $values = array_merge(array_fill(0, 5, 'Finance'), array_fill(0, 3, 'Retail'), ['A', 'B', 'C', 'D', 'E', 'F', ' ']);
        $result = $this->analytics()->distribution($values, 3);

        $this->assertSame(['Finance' => 5, 'Retail' => 3, 'A' => 1, 'Other' => 5], $result);
    }

    public function testScoreBandsAndMedianResponse(): void
    {
        $a = $this->analytics();

        $this->assertSame(['bands' => ['Weak (0–39)' => 1, 'Fair (40–69)' => 1, 'Strong (70–100)' => 1], 'average' => 50], $a->scoreBands([10, 50, 90]));
        $this->assertNull($a->scoreBands([])['average']);
        $this->assertSame(2.0, $a->medianResponseHours([
            ['submitted_at' => '2026-10-28 10:00:00', 'first_action_at' => '2026-10-28 11:00:00'],
            ['submitted_at' => '2026-10-28 10:00:00', 'first_action_at' => '2026-10-28 13:00:00'],
            ['submitted_at' => '2026-10-28 10:00:00', 'first_action_at' => '2026-10-28 09:00:00'], // clock skew: ignored
        ]));
        $this->assertSame('45 min', AdminAnalytics::duration(0.75));
        $this->assertSame('3.5 h', AdminAnalytics::duration(3.5));
        $this->assertSame('2.5 days', AdminAnalytics::duration(60));
        $this->assertSame('—', AdminAnalytics::duration(null));
    }

    public function testRangeAcceptsOnlyKnownValues(): void
    {
        $this->assertSame(90, AdminAnalytics::range('90'));
        $this->assertSame(30, AdminAnalytics::range('31'));
        $this->assertSame(30, AdminAnalytics::range(['x']));
    }

    public function testUiHelpersShowLocalTimesAndFriendlyLabels(): void
    {
        Ui::setTimezone('Asia/Kolkata');

        $this->assertSame('28 Oct 2026, 4:06 pm', Ui::dateTime('2026-10-28 10:36:00'));
        $this->assertSame('Today 12:15 am', Ui::shortTime('2026-10-28 18:45:00', self::NOW + 3600));
        $this->assertSame('Yesterday 4:06 pm', Ui::shortTime('2026-10-28 10:36:00', self::NOW + 3600));
        $this->assertSame('3h ago', Ui::ago('2026-10-28 16:30:00', self::NOW + 3600));
        $this->assertSame('Security report', Ui::formLabel('security_report'));
        $this->assertSame('Done', Ui::statusLabel('resolved'));
        $this->assertSame('Nila Hospitals', Ui::contactName(['name' => ' ', 'company' => 'Nila Hospitals']));
        $this->assertSame('Audit score 45/100', Ui::preview(['score' => 45]));
        $this->assertSame('ab…', Ui::preview(['message' => "ab\n\ncdef"], 3));
        $this->assertTrue(Ui::isUrgent('emergency', 'in_review'));
        $this->assertFalse(Ui::isUrgent('emergency', 'resolved'));
    }
}
