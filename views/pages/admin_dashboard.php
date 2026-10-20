<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="mx-auto w-full max-w-7xl space-y-6">
  <h1 class="text-3xl font-extrabold text-slate-900 mb-6">Dashboard</h1>
  <?php if (is_array($stats)) : ?>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <?php foreach (['today' => 'New today', 'open' => 'Open', 'spam' => 'Spam', 'high_security' => 'High severity', 'aging' => 'Unresolved >24h'] as $key => $label) : ?>
    <a href="<?= $key === 'high_security' ? '/admin/security' : '/admin/submissions' ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-sky-300">
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-500"><?= $e($label) ?></p>
      <p class="font-display text-3xl font-bold <?= in_array($key, ['high_security', 'aging'], true) && ($stats[$key] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-900' ?>"><?= $e($stats[$key] ?? 0) ?></p>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total</p>
      <p class="font-display text-3xl font-bold text-slate-900"><?= $e(array_sum($counts)) ?></p>
    </div>
    <?php foreach ($counts as $t => $n) : ?>
    <a href="/admin/submissions?type=<?= $e($t) ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-sky-300 hover:shadow-md transition-all">
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-500"><?= $e($t) ?></p>
      <p class="font-display text-3xl font-bold <?= $t === 'emergency' && $n > 0 ? 'text-red-600' : 'text-slate-900' ?>"><?= $e($n) ?></p>
    </a>
    <?php endforeach; ?>
  </div>
  <h2 class="text-xl font-bold text-slate-900">Recent submissions</h2>
  <?= $this->include('pages/admin_leads_table', ['rows' => $recent]) ?>
</section>
