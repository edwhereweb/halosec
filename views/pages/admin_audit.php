<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="mx-auto w-full max-w-7xl space-y-5">
  <div><p class="text-sm font-semibold text-slate-500">Governance</p><h1 class="mt-1 text-3xl font-bold text-slate-950">Audit Logs</h1><p class="mt-2 text-sm text-slate-600">A traceable record of administrative changes.</p></div>
  <?php if ($entries === []) : ?><p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No admin changes recorded.</p>
  <?php else : ?>
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm"><table class="min-w-full text-left text-sm">
    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">When (UTC)</th><th class="px-4 py-3">Actor ID</th><th class="px-4 py-3">Action</th><th class="px-4 py-3">Entity</th><th class="px-4 py-3">Details</th></tr></thead>
    <tbody class="divide-y divide-slate-100"><?php foreach ($entries as $entry) : ?><tr><td class="px-4 py-3"><?= $e($entry['created_at']) ?></td><td class="px-4 py-3"><?= $e($entry['actor_user_id'] ?? 'System/configured admin') ?></td><td class="px-4 py-3"><?= $e($entry['action']) ?></td><td class="px-4 py-3"><?= $e($entry['entity_type']) ?> <?= $e($entry['entity_id'] ?? '') ?></td><td class="max-w-lg break-words px-4 py-3"><?= $e($entry['details'] ?? '') ?></td></tr><?php endforeach; ?></tbody>
  </table></div>
  <?php endif; ?>
</section>
