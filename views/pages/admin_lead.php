<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="max-w-3xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <a class="text-sm font-bold text-sky-700 hover:underline" href="/admin/leads?type=<?= $e($lead['type']) ?>">← Back to <?= $e($lead['type']) ?> leads</a>
  <div class="mt-4 rounded-3xl bg-white border border-slate-200 shadow-lg p-8">
    <span class="text-[11px] font-bold uppercase tracking-widest text-sky-700"><?= $e($lead['type']) ?></span>
    <p class="text-sm text-slate-500 mt-1">Received <?= $e(str_replace('T', ' ', substr($lead['created_at'], 0, 19))) ?> UTC</p>
    <dl class="mt-6 space-y-4">
      <?php foreach ($lead['data'] as $key => $value) : ?>
      <div>
        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500"><?= $e(str_replace('_', ' ', (string) $key)) ?></dt>
        <dd class="text-sm text-slate-900 whitespace-pre-wrap break-words"><?= $e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></dd>
      </div>
      <?php endforeach; ?>
    </dl>
  </div>
</section>
