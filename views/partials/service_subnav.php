<?php
/** Sticky product-style sub navigation for a service page. Expects $name; optional $links (anchor => label). */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$links = $links ?? [];
?>
<div class="sticky top-16 z-30 border-b border-slate-200 bg-white/90 backdrop-blur-xl backdrop-saturate-150">
  <nav aria-label="<?= $e($name) ?>" class="wrap flex min-h-[56px] flex-wrap items-center gap-x-6 gap-y-2 py-2">
    <span class="flex-1 text-lg font-extrabold tracking-[-0.01em]"><?= $e($name) ?></span>
    <?php if ($links !== []) : ?>
    <div class="hidden flex-wrap gap-5 text-[13px] font-medium md:flex">
      <?php foreach ($links as $anchor => $label) : ?>
      <a class="text-brand-body no-underline hover:text-brand-ink" href="#<?= $e($anchor) ?>"><?= $e($label) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <a class="btn btn-sm btn-blue" href="#enquiry">Enquire</a>
  </nav>
</div>
