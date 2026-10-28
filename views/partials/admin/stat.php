<?php
/**
 * Stat tile: label, value, optional change vs the previous period.
 * Expects $label, $value (pre-formatted string); optional $delta (float|null, %), $goodWhenUp (bool|null; null = neutral),
 * $compare (e.g. "vs previous 30 days"), $previous (shown when there is no meaningful % change), $href, $hint,
 * $tone ('default' | 'alert').
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$delta = $delta ?? null;
$goodWhenUp = $goodWhenUp ?? true;
$tone = $tone ?? 'default';
$tag = !empty($href) ? 'a' : 'div';
$deltaClass = 'text-slate-500';
$arrow = '';
if (is_float($delta) || is_int($delta)) {
    if ($delta > 0) {
        $arrow = '↑';
    } elseif ($delta < 0) {
        $arrow = '↓';
    }
    if ($delta != 0 && $goodWhenUp !== null) {
        $good = $goodWhenUp ? $delta > 0 : $delta < 0;
        $deltaClass = $good ? 'text-emerald-700' : 'text-red-700';
    }
}
?>
<<?= $tag ?> <?= $tag === 'a' ? 'href="' . $e($href) . '"' : '' ?> class="flex flex-col gap-1 rounded-2xl border bg-white p-5 no-underline shadow-sm transition <?= $tone === 'alert' ? 'border-red-200 ring-1 ring-red-100' : 'border-slate-200' ?> <?= $tag === 'a' ? 'hover:border-slate-300 hover:shadow' : '' ?>">
  <span class="text-[13px] font-semibold text-slate-500"><?= $e($label) ?></span>
  <span class="text-[32px] font-bold leading-tight tracking-tight <?= $tone === 'alert' ? 'text-red-700' : 'text-slate-900' ?>"><?= $e($value) ?></span>
  <?php if (is_float($delta) || is_int($delta)) : ?>
  <span class="text-xs font-semibold <?= $deltaClass ?>"><?= $arrow ?> <?= $e(rtrim(rtrim(number_format(abs((float) $delta), 1), '0'), '.')) ?>% <span class="font-medium text-slate-500"><?= $e($compare ?? 'vs previous period') ?></span></span>
  <?php elseif (isset($previous) && $previous !== null && $previous !== '') : ?>
  <span class="text-xs font-medium text-slate-500"><?= $e($previous) ?> in the previous period</span>
  <?php elseif (!empty($hint)) : ?>
  <span class="text-xs font-medium text-slate-500"><?= $e($hint) ?></span>
  <?php else : ?>
  <span class="text-xs font-medium text-slate-400">No earlier data to compare</span>
  <?php endif; ?>
</<?= $tag ?>>
