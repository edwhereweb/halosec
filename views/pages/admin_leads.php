<?php
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$qs = static fn (int $p): string => '?' . http_build_query(array_filter(['type' => $type, 'page' => $p > 1 ? $p : null]));
?>
<div class="mx-auto flex w-full max-w-5xl flex-col gap-5">
  <div>
    <h1 class="text-[28px] font-bold tracking-tight text-slate-900">Inbox <span class="text-base font-semibold text-slate-500">(<?= $e($total) ?>)</span></h1>
    <p class="mt-1 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">The database isn’t connected, so this is a read-only list from file storage. Statuses, owners and notes need the database.</p>
  </div>
  <form method="get" action="/admin/leads" data-autosubmit class="flex items-end gap-3">
    <div>
      <label for="f_type" class="mb-1 block text-xs font-semibold text-slate-500">Form</label>
      <select id="f_type" name="type" class="h-10 rounded-lg border-slate-200 pr-8 text-sm">
        <option value="">All forms</option>
        <?php foreach (Ui::formLabels() as $key => $label) : ?><option value="<?= $e($key) ?>" <?= $type === $key ? 'selected' : '' ?>><?= $e($label) ?></option><?php endforeach; ?>
      </select>
    </div>
    <button data-apply type="submit" class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white">Apply</button>
  </form>
  <?php if ($rows === []) : ?>
  <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-600">No submissions yet.</p>
  <?php else : ?>
  <ul class="rounded-2xl border border-slate-200 bg-white p-2"><?php foreach ($rows as $row) : ?><?= $this->include('partials/admin/item', ['row' => $row]) ?><?php endforeach; ?></ul>
  <?php endif; ?>
  <?php if ($pages > 1) : ?>
  <nav class="flex items-center justify-between text-sm" aria-label="Pagination">
    <?php if ($page > 1) : ?><a class="font-semibold text-sky-800" href="/admin/leads<?= $e($qs($page - 1)) ?>">← Newer</a><?php else : ?><span></span><?php endif; ?>
    <span class="text-slate-500">Page <?= $e($page) ?> of <?= $e($pages) ?></span>
    <?php if ($page < $pages) : ?><a class="font-semibold text-sky-800" href="/admin/leads<?= $e($qs($page + 1)) ?>">Older →</a><?php else : ?><span></span><?php endif; ?>
  </nav>
  <?php endif; ?>
</div>
