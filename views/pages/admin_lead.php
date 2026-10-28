<?php
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$data = is_array($lead['data'] ?? null) ? $lead['data'] : [];
?>
<div class="mx-auto flex w-full max-w-3xl flex-col gap-5">
  <a class="text-sm font-semibold text-slate-600 no-underline hover:text-slate-900" href="/admin/leads">← Inbox</a>
  <section class="rounded-2xl border border-slate-200 bg-white p-6">
    <p class="flex items-center gap-2 text-sm text-slate-500"><span class="h-2 w-2 rounded-full <?= Ui::formDot((string) $lead['type']) ?>" aria-hidden="true"></span><span class="font-semibold text-slate-700"><?= $e(Ui::formLabel((string) $lead['type'])) ?></span> · <?= $e(Ui::dateTime((string) $lead['created_at'])) ?> <?= $e(Ui::zoneAbbr()) ?></p>
    <h1 class="mt-2 text-[26px] font-bold tracking-tight text-slate-900"><?= $e(Ui::contactName($data)) ?></h1>
    <dl class="mt-5 grid gap-x-6 gap-y-3 border-t border-slate-100 pt-5 text-sm sm:grid-cols-2">
      <?php foreach ($data as $key => $value) : ?>
      <div class="<?= $key === 'message' ? 'sm:col-span-2' : '' ?>"><dt class="text-xs font-semibold text-slate-500"><?= $e(Ui::fieldLabel((string) $key)) ?></dt><dd class="whitespace-pre-wrap break-words text-slate-900"><?= $e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></dd></div>
      <?php endforeach; ?>
    </dl>
  </section>
</div>
