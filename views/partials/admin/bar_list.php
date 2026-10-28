<?php
/**
 * Horizontal bar list: one colour, labels and values in text colours, built as a real table so it reads
 * correctly without the bars. Expects $items (label => count); optional $links (label => href), $unit,
 * $empty (message), $total (for percentages; defaults to the sum).
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$items = is_array($items ?? null) ? $items : [];
$links = is_array($links ?? null) ? $links : [];
$unit = $unit ?? 'submissions';
$sum = (int) ($total ?? array_sum($items));
$max = $items === [] ? 0 : max($items);
?>
<?php if ($items === [] || $sum === 0) : ?>
<p class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500"><?= $e($empty ?? 'Nothing in this period yet.') ?></p>
<?php else : ?>
<table class="w-full text-sm">
  <tbody>
    <?php foreach ($items as $label => $count) : ?>
    <?php $pct = $sum > 0 ? round(100 * $count / $sum) : 0; $width = $max > 0 ? max(1.5, 100 * $count / $max) : 0; ?>
    <tr class="group" data-tip="<?= $e($label . ': ' . $count . ' ' . ($count === 1 ? rtrim($unit, 's') : $unit) . ' (' . $pct . '%)') ?>">
      <th scope="row" class="w-[42%] py-1.5 pr-3 text-left align-middle text-[13px] font-medium leading-snug text-slate-700">
        <?php if (isset($links[$label])) : ?>
        <a class="block text-slate-700 no-underline hover:text-sky-800 hover:underline" href="<?= $e($links[$label]) ?>"><?= $e($label) ?></a>
        <?php else : ?>
        <span class="block"><?= $e($label) ?></span>
        <?php endif; ?>
      </th>
      <td class="py-1.5">
        <div class="flex items-center gap-2">
          <div class="h-2.5 flex-1 rounded-r-[4px] bg-slate-100">
            <div class="h-full rounded-r-[4px] bg-[#2a78d6] transition group-hover:bg-[#1c5cab]" style="width: <?= round($width, 2) ?>%"></div>
          </div>
          <span class="w-16 shrink-0 text-right tabular-nums text-slate-700"><strong class="font-semibold"><?= $e($count) ?></strong> <span class="text-xs text-slate-400"><?= $e($pct) ?>%</span></span>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
