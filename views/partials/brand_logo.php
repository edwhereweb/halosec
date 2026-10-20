<?php

declare(strict_types=1);

/**
 * HaloSec Official Brand Logo Partial
 *
 * Uses the exact supplied logo image asset.
 *
 * @var string $variant     'light' (default) or 'dark'
 * @var string $size        'sm' (h-6), 'md' (h-7 default), 'lg' (h-9)
 * @var bool   $showTagline Whether to show "We Are Securious" tagline
 * @var string $class       Additional wrapper classes
 */

$variant = $variant ?? 'light';
$size = $size ?? 'md';
$showTagline = $showTagline ?? false;
$class = $class ?? '';

$heightClasses = match ($size) {
    'sm' => 'h-6',
    'lg' => 'h-10',
    default => 'h-8',
};
?>
<div class="inline-flex flex-col <?= HaloSec\View::e($class) ?>">
  <?php if ($variant === 'dark') : ?>
    <div class="inline-flex items-center bg-white/95 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-md w-fit">
      <img
        src="/images/halosec-logo.png"
        alt="HaloSec – The Aura of Security for Your Business"
        class="<?= $heightClasses ?> w-auto object-contain select-none transition-transform duration-200 group-hover:scale-105"
        width="300"
        height="86"
        style="aspect-ratio: 300 / 86;"
        loading="eager"
      />
    </div>
  <?php else : ?>
    <img
      src="/images/halosec-logo.png"
      alt="HaloSec – The Aura of Security for Your Business"
      class="<?= $heightClasses ?> w-auto object-contain select-none transition-transform duration-200 group-hover:scale-105"
      width="300"
      height="86"
      style="aspect-ratio: 300 / 86;"
      loading="eager"
    />
  <?php endif; ?>

  <?php if ($showTagline) : ?>
    <span class="text-[9px] tracking-[0.2em] uppercase font-bold pl-0.5 mt-0.5 select-none <?= $variant === 'dark' ? 'text-slate-400' : 'text-slate-500' ?>">
      We Are Securious
    </span>
  <?php endif; ?>
</div>
