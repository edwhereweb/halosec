<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="max-w-6xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <h1 class="text-3xl font-extrabold text-slate-900 mb-6">Dashboard</h1>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total</p>
      <p class="font-display text-3xl font-bold text-slate-900"><?= $e(array_sum($counts)) ?></p>
    </div>
    <?php foreach ($counts as $t => $n) : ?>
    <a href="/admin/leads?type=<?= $e($t) ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-sky-300 hover:shadow-md transition-all">
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-500"><?= $e($t) ?></p>
      <p class="font-display text-3xl font-bold <?= $t === 'emergency' && $n > 0 ? 'text-red-600' : 'text-slate-900' ?>"><?= $e($n) ?></p>
    </a>
    <?php endforeach; ?>
  </div>
  <h2 class="text-xl font-extrabold text-slate-900 mb-3">Recent leads</h2>
  <?= $this->include('pages/admin_leads_table', ['rows' => $recent]) ?>
</section>
