<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="max-w-6xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <h1 class="mb-6 text-3xl font-extrabold text-slate-900">Audit log</h1>
  <?php if ($entries === []) : ?><p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No admin changes recorded.</p>
  <?php else : ?>
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm"><table class="min-w-full text-left text-sm">
    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">When (UTC)</th><th class="px-4 py-3">Actor ID</th><th class="px-4 py-3">Action</th><th class="px-4 py-3">Entity</th><th class="px-4 py-3">Details</th></tr></thead>
    <tbody class="divide-y divide-slate-100"><?php foreach ($entries as $entry) : ?><tr><td class="px-4 py-3"><?= $e($entry['created_at']) ?></td><td class="px-4 py-3"><?= $e($entry['actor_user_id'] ?? 'System/configured admin') ?></td><td class="px-4 py-3"><?= $e($entry['action']) ?></td><td class="px-4 py-3"><?= $e($entry['entity_type']) ?> <?= $e($entry['entity_id'] ?? '') ?></td><td class="max-w-lg break-words px-4 py-3"><?= $e($entry['details'] ?? '') ?></td></tr><?php endforeach; ?></tbody>
  </table></div>
  <?php endif; ?>
</section>
