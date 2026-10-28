<?php
/**
 * Weekday × time-of-day heatmap on a single-hue (blue) sequential ramp. Expects $heatmap from
 * AdminAnalytics::heatmap(). Each cell has a tooltip; a scale legend explains the shades.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$ramp = ['#cde2fb', '#9ec5f4', '#6da7ec', '#3987e5', '#256abf', '#184f95'];
$max = max(1, (int) $heatmap['max']);
$shade = static function (int $value) use ($ramp, $max): string {
    if ($value === 0) {
        return '#f1f5f9';
    }

    return $ramp[min(count($ramp) - 1, (int) floor(($value / $max) * (count($ramp) - 0.001)))];
};
$blockLabel = static function (int $i) use ($heatmap): string {
    $next = $heatmap['blocks'][$i + 1] ?? '12am';

    return $heatmap['blocks'][$i] . '–' . $next;
};
$zone = HaloSec\Admin\Ui::zoneAbbr();
?>
<div class="overflow-x-auto">
  <table class="w-full min-w-[520px] table-fixed border-separate border-spacing-[3px] text-[11px]">
    <caption class="sr-only">Submissions by weekday and time of day (<?= $e($zone) ?>)</caption>
    <thead>
      <tr><th class="w-10"></th><?php foreach ($heatmap['blocks'] as $i => $block) : ?><th scope="col" class="pb-1 text-left font-medium text-slate-400"><?= $i % 2 === 0 ? $e($block) : '' ?><span class="sr-only"><?= $i % 2 === 0 ? '' : $e($block) ?></span></th><?php endforeach; ?></tr>
    </thead>
    <tbody>
      <?php foreach ($heatmap['cells'] as $d => $row) : ?>
      <tr>
        <th scope="row" class="w-10 pr-1 text-left font-semibold text-slate-500"><?= $e($heatmap['days'][$d]) ?></th>
        <?php foreach ($row as $i => $value) : ?>
        <td tabindex="0" class="h-7 rounded-[4px] outline-none ring-slate-900 focus-visible:ring-2" style="background: <?= $shade($value) ?>"
          data-tip="<?= $e($heatmap['days'][$d] . ' ' . $blockLabel($i) . ': ' . $value) ?>"><span class="sr-only"><?= $e($value) ?></span></td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<div class="mt-2 flex items-center gap-2 text-[11px] text-slate-500" aria-hidden="true">
  <span>Fewer</span>
  <span class="h-2.5 w-4 rounded-sm" style="background:#f1f5f9"></span>
  <?php foreach ($ramp as $step) : ?><span class="h-2.5 w-4 rounded-sm" style="background: <?= $step ?>"></span><?php endforeach; ?>
  <span>More</span>
  <span class="ml-auto">Times in <?= $e($zone) ?></span>
</div>
