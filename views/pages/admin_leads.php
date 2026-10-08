<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$qs = static fn (int $p): string => '?' . http_build_query(array_filter(['type' => $type, 'page' => $p > 1 ? $p : null]));
?>
<section class="max-w-6xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <h1 class="text-3xl font-extrabold text-slate-900 mb-4">Leads <span class="text-base font-semibold text-slate-500">(<?= $e($total) ?>)</span></h1>
  <form method="get" action="/admin/leads" class="mb-6 flex items-center gap-3">
    <label for="f_type" class="text-sm font-semibold text-slate-700">Type</label>
    <select id="f_type" name="type" class="rounded-xl border-slate-300 text-sm" onchange="this.form.submit()">
      <option value="">All</option>
      <?php foreach (HaloSec\Models\Lead::TYPES as $t) : ?>
      <option value="<?= $e($t) ?>" <?= $type === $t ? 'selected' : '' ?>><?= $e(ucfirst($t)) ?></option>
      <?php endforeach; ?>
    </select>
    <noscript><button type="submit" class="text-sm font-bold">Filter</button></noscript>
  </form>
  <?= $this->include('pages/admin_leads_table', ['rows' => $rows]) ?>
  <?php if ($pages > 1) : ?>
  <nav class="mt-6 flex items-center justify-between text-sm" aria-label="Pagination">
    <?php if ($page > 1) : ?><a class="font-bold text-sky-700 hover:underline" href="/admin/leads<?= $e($qs($page - 1)) ?>">← Previous</a><?php else : ?><span></span><?php endif; ?>
    <span class="text-slate-600">Page <?= $e($page) ?> of <?= $e($pages) ?></span>
    <?php if ($page < $pages) : ?><a class="font-bold text-sky-700 hover:underline" href="/admin/leads<?= $e($qs($page + 1)) ?>">Next →</a><?php else : ?><span></span><?php endif; ?>
  </nav>
  <?php endif; ?>
</section>
