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
    <?php /* Dark backgrounds (footer): light shield mark + light wordmark, no white box. */ ?>
    <span class="inline-flex items-center gap-3">
      <img
        src="/images/halosec-mark-light.png"
        alt=""
        aria-hidden="true"
        class="h-10 w-10 select-none object-contain transition-transform duration-200 group-hover:-rotate-6"
        width="128"
        height="128"
        loading="lazy"
        decoding="async"
      />
      <img
        src="/images/halosec-logo-light.png"
        alt="HaloSec – The Aura of Security for Your Business"
        class="h-7 w-auto select-none object-contain"
        width="230"
        height="66"
        style="aspect-ratio: 230 / 66;"
        loading="lazy"
        decoding="async"
      />
    </span>
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
