<?php
/**
 * Single-series column chart over time (one colour, no legend needed: the title names the series).
 * Expects $series (list of [label, long, count]), $title; optional $unit ("submissions"), $height (px).
 * Every column has a hover/focus tooltip, and the same numbers are available as a table.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$unit = $unit ?? 'submissions';
$height = (int) ($height ?? 200);
$max = 0;
foreach ($series as $point) {
    $max = max($max, (int) $point['count']);
}
// Clean axis maximum: 1, 2, 5 × 10^n.
$niceMax = 1;
if ($max > 0) {
    $magnitude = 10 ** (int) floor(log10($max));
    foreach ([1, 2, 5, 10] as $step) {
        if ($step * $magnitude >= $max) {
            $niceMax = $step * $magnitude;
            break;
        }
    }
    if ($niceMax < 4) {
        $niceMax = 4;
    }
}
$ticks = [$niceMax, $niceMax / 2, 0];
$count = count($series);
$labelEvery = max(1, (int) ceil($count / 7));
$total = array_sum(array_map(static fn (array $p): int => (int) $p['count'], $series));
$id = 'chart-' . substr(md5($title . $count), 0, 8);
?>
<figure class="flex flex-col gap-3" aria-labelledby="<?= $id ?>-title">
  <figcaption id="<?= $id ?>-title" class="sr-only"><?= $e($title) ?>: <?= $e($total) ?> <?= $e($unit) ?> in total</figcaption>
  <div class="grid grid-cols-[32px_minmax(0,1fr)] gap-2">
    <div class="relative" style="height: <?= $height ?>px" aria-hidden="true">
      <?php foreach ($ticks as $i => $tick) : ?>
      <span class="absolute right-0 -translate-y-1/2 text-[11px] tabular-nums text-slate-400" style="top: <?= $i * 50 ?>%"><?= $e((int) $tick) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="relative" style="height: <?= $height ?>px">
      <?php foreach ($ticks as $i => $tick) : ?>
      <div aria-hidden="true" class="absolute inset-x-0 border-t <?= $tick === 0 ? 'border-slate-300' : 'border-slate-100' ?>" style="top: <?= $i * 50 ?>%"></div>
      <?php endforeach; ?>
      <div class="absolute inset-0 flex items-end gap-[2px]">
        <?php foreach ($series as $point) : ?>
        <?php $pct = $niceMax > 0 ? 100 * (int) $point['count'] / $niceMax : 0; ?>
        <div tabindex="0" class="group flex h-full min-w-0 flex-1 cursor-default items-end justify-center rounded-sm outline-none hover:bg-slate-100/70 focus-visible:bg-slate-100"
          data-tip="<?= $e($point['long'] . ': ' . $point['count'] . ' ' . ((int) $point['count'] === 1 ? rtrim($unit, 's') : $unit)) ?>"
          aria-label="<?= $e($point['long'] . ': ' . $point['count']) ?>">
          <div class="w-full max-w-[24px] rounded-t-[4px] bg-[#2a78d6] transition group-hover:bg-[#1c5cab]" style="height: <?= $point['count'] > 0 ? max(2, round($pct, 2)) : 0 ?>%"></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div></div>
    <div class="flex gap-[2px]" aria-hidden="true">
      <?php foreach ($series as $i => $point) : ?>
      <span class="min-w-0 flex-1 overflow-visible whitespace-nowrap text-center text-[11px] text-slate-400"><?= ($i % $labelEvery === 0 || $i === $count - 1) && ($count - 1 - $i >= $labelEvery / 2 || $i === $count - 1) ? $e($point['label']) : '' ?></span>
      <?php endforeach; ?>
    </div>
  </div>
  <details class="text-sm">
    <summary class="cursor-pointer text-xs font-semibold text-slate-500 hover:text-slate-800">Show as table</summary>
    <div class="mt-2 max-h-64 overflow-y-auto rounded-lg border border-slate-200">
      <table class="w-full text-left text-sm">
        <thead class="sticky top-0 bg-slate-50 text-xs text-slate-500"><tr><th class="px-3 py-2 font-semibold">Date</th><th class="px-3 py-2 text-right font-semibold"><?= $e(ucfirst($unit)) ?></th></tr></thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach (array_reverse($series) as $point) : ?>
          <tr><td class="px-3 py-1.5"><?= $e($point['long']) ?></td><td class="px-3 py-1.5 text-right tabular-nums"><?= $e($point['count']) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </details>
</figure>
