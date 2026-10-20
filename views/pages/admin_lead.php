<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="mx-auto w-full max-w-7xl space-y-5">
  <a class="text-sm font-bold text-sky-700 hover:underline" href="/admin/submissions">← All Inbox</a>
  <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
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
