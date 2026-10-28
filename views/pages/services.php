<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Defensive & offensive capabilities', 'heading' => 'Cybersecurity services', 'lead' => 'Eight disciplines, each backed by certified practitioners and proven deployment playbooks.']) ?>
<?php $colors = ['tile-ink', 'tile-white', 'tile-white', 'tile-sky', 'tile-blue', 'tile-lime', 'tile-white', 'tile-cta']; ?>
<section class="section bg-brand-mist">
  <div class="wrap grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php $i = 0; foreach ($services as $slug => $s) : ?>
    <?php $tile = $colors[$i++ % count($colors)]; $dark = in_array($tile, ['tile-ink', 'tile-blue'], true); ?>
    <a class="tile tile-hover min-h-[280px] <?= $tile ?>" href="/services/<?= $e($slug) ?>">
      <span class="font-display text-sm font-bold <?= $dark ? 'text-brand-neon' : 'text-brand-muted' ?>">0<?= $i ?></span>
      <h2 class="font-display text-[28px] font-bold leading-tight tracking-[-0.03em]"><?= $e($s['nav_label'] ?? $s['title']) ?></h2>
      <p class="text-[15px] leading-relaxed <?= $dark ? 'text-slate-200' : 'text-brand-body' ?>"><?= $e($s['short']) ?></p>
      <span class="mt-auto pt-4 text-sm font-bold <?= $dark ? 'text-white' : 'text-brand-blue' ?>">Learn more &amp; enquire →</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?= $this->include('partials/cta_band') ?>
