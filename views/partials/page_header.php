<?php
/**
 * Hero for inner pages. Expects $heading; optional $eyebrow, $lead, $tone ('default' | 'danger').
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$tone = $tone ?? 'default';
?>
<section class="relative overflow-hidden px-4 pb-16 pt-20 text-center md:pb-20 md:pt-28 <?= $tone === 'danger' ? 'bg-red-50' : 'bg-white' ?>">
  <?php if ($tone !== 'danger') : ?>
  <?= $this->include('partials/halo', ['size' => 1000, 'class' => '-top-[300px]']) ?>
  <?php endif; ?>
  <div class="relative mx-auto flex max-w-4xl animate-rise flex-col items-center gap-5">
    <?php if (!empty($eyebrow)) : ?>
    <span class="chip <?= $tone === 'danger' ? 'chip-red' : 'chip-mist' ?>"><?= $e($eyebrow) ?></span>
    <?php endif; ?>
    <h1 class="display text-[44px] md:text-6xl lg:text-7xl"><?= $e($heading) ?></h1>
    <?php if (!empty($lead)) : ?>
    <p class="lead max-w-2xl"><?= $e($lead) ?></p>
    <?php endif; ?>
  </div>
</section>
