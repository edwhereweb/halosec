<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="max-w-6xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <h1 class="mb-2 text-3xl font-extrabold text-slate-900">Security events</h1>
  <p class="mb-6 text-sm text-slate-600">Review suspicious submission patterns and security alerts.</p>
  <form method="get" action="/admin/security" class="mb-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4">
    <label for="severity" class="text-sm font-semibold text-slate-700">Severity</label>
    <select id="severity" name="severity" class="rounded-xl border-slate-300 text-sm"><option value="">All</option>
      <?php foreach (['info', 'low', 'medium', 'high', 'critical'] as $level) : ?><option value="<?= $e($level) ?>" <?= $severity === $level ? 'selected' : '' ?>><?= $e(ucfirst($level)) ?></option><?php endforeach; ?>
    </select>
    <button class="rounded-full bg-slate-900 px-4 py-2 text-xs font-bold uppercase text-white" type="submit">Filter</button>
  </form>
  <?php if ($events === []) : ?><p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No security events recorded.</p>
  <?php else : ?>
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm"><table class="min-w-full text-left text-sm">
    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">When (UTC)</th><th class="px-4 py-3">Severity</th><th class="px-4 py-3">Event</th><th class="px-4 py-3">Description</th></tr></thead>
    <tbody class="divide-y divide-slate-100"><?php foreach ($events as $event) : ?><tr><td class="px-4 py-3"><?= $e($event['created_at']) ?></td><td class="px-4 py-3"><span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold uppercase text-rose-700"><?= $e($event['severity']) ?></span></td><td class="px-4 py-3"><?= $e($event['event_type']) ?></td><td class="px-4 py-3"><?= $e($event['description']) ?></td></tr><?php endforeach; ?></tbody>
  </table></div>
  <?php endif; ?>
</section>
