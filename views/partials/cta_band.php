<?php
/** Closing call to action band. Optional: $heading, $lead, $btnLabel, $btnHref. */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
?>
<section class="px-4 py-24">
  <div class="relative mx-auto max-w-6xl overflow-hidden rounded-[40px] bg-brand-blue px-6 py-20 text-center text-white md:py-28">
    <?= $this->include('partials/halo', ['tone' => 'blue', 'size' => 900, 'class' => 'top-1/2 -translate-y-1/2']) ?>
    <div class="relative mx-auto flex max-w-3xl flex-col items-center gap-6">
      <h2 class="display text-5xl md:text-7xl"><?= $e($heading ?? 'Not sure where to start?') ?></h2>
      <p class="text-lg text-sky-100 md:text-xl"><?= $e($lead ?? 'Start with a free audit. A plain-language report from certified Zoho & Sophos architects.') ?></p>
      <a class="btn btn-lg btn-cta" href="<?= $e($btnHref ?? '/free-audit') ?>"><?= $e($btnLabel ?? 'Get a free audit') ?></a>
    </div>
  </div>
</section>
