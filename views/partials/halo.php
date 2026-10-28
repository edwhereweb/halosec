<?php
/**
 * Decorative "halo" rings with two slowly orbiting arcs. Purely visual (aria-hidden).
 * Optional: $tone ('light' | 'blue' | 'dark'), $size (px), $class (extra positioning classes).
 */
$tone = $tone ?? 'light';
$size = (int) ($size ?? 1120);
$class = $class ?? 'top-0';
[$r1, $r2, $r3, $arcA, $arcB] = match ($tone) {
    'blue' => ['#0c7cc0', '#0c7cc0', '#0c7cc0', '#ccff00', '#7dd3fc'],
    'dark' => ['#1e2636', '#1e2636', '#161d2a', '#ccff00', '#38bdf8'],
    default => ['#e6eef7', '#e3eef8', '#eef3f8', '#ccff00', '#38bdf8'],
};
$box = 'absolute inset-0 h-full w-full';
?>
<div aria-hidden="true" class="pointer-events-none absolute left-1/2 -translate-x-1/2 <?= HaloSec\View::e($class) ?>" style="width: <?= $size ?>px; height: <?= $size ?>px">
  <svg class="<?= $box ?>" viewBox="0 0 1000 1000">
    <circle cx="500" cy="500" r="300" fill="none" stroke="<?= $r1 ?>" stroke-width="1.5"></circle>
    <circle class="animate-breathe" cx="500" cy="500" r="390" fill="none" stroke="<?= $r2 ?>" stroke-width="1.5"></circle>
    <circle cx="500" cy="500" r="480" fill="none" stroke="<?= $r3 ?>" stroke-width="1.5"></circle>
  </svg>
  <svg class="<?= $box ?> animate-halo" viewBox="0 0 1000 1000">
    <circle cx="500" cy="500" r="390" fill="none" stroke="<?= $arcA ?>" stroke-width="4" stroke-dasharray="190 2300" stroke-linecap="round"></circle>
    <circle cx="890" cy="500" r="7" fill="<?= $arcA ?>"></circle>
  </svg>
  <svg class="<?= $box ?> animate-halo-rev" viewBox="0 0 1000 1000">
    <circle cx="500" cy="500" r="480" fill="none" stroke="<?= $arcB ?>" stroke-width="2.5" stroke-dasharray="100 2950" stroke-linecap="round"></circle>
    <circle cx="20" cy="500" r="5" fill="<?= $arcB ?>"></circle>
  </svg>
</div>
