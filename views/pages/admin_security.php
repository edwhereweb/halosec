<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$severityStyles = [
    'info' => 'bg-sky-50 text-sky-800',
    'low' => 'bg-slate-100 text-slate-700',
    'medium' => 'bg-amber-50 text-amber-800',
    'high' => 'bg-rose-50 text-rose-800',
    'critical' => 'bg-red-100 text-red-800',
];
?>
<section class="mx-auto w-full max-w-7xl space-y-5">
  <div><p class="text-sm font-semibold text-slate-500">Security Center</p><h1 class="mt-1 text-3xl font-bold text-slate-950"><?= $eventType === 'admin_login_failed' ? 'Failed Logins' : 'Security Events' ?></h1></div>
  <p class="text-sm text-slate-600"><?= $eventType === 'admin_login_failed' ? 'Review failed administrator sign-in attempts.' : 'Review suspicious submission patterns and security alerts.' ?></p>
  <?php if (!($isDatabase ?? true)) : ?>
  <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
    <div class="flex items-center gap-2 font-bold text-amber-950">
      <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>
      Security Event &amp; Failed Sign-in Logging Inactive (File Storage Mode)
    </div>
    <p class="mt-1.5 leading-relaxed text-amber-800">
      Logging failed login attempts and real-time security events requires MySQL. Connect your MySQL database in <code>.env</code> to start recording events.
    </p>
    <?php if (!empty($dbError)) : ?>
    <div class="mt-2.5 rounded-lg bg-amber-100/80 p-2.5 font-mono text-[11px] text-amber-950 break-words">
      <strong>Diagnostic Error:</strong> <?= $e($dbError) ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  <form method="get" action="/admin/security" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-3">
    <input type="hidden" name="event" value="<?= $e($eventType) ?>">
    <div><label for="severity" class="mb-1 block text-xs font-semibold text-slate-600">Severity</label>
    <select id="severity" name="severity" class="w-full rounded-lg border-slate-300 text-sm"><option value="">All severities</option>
      <?php foreach (['info', 'low', 'medium', 'high', 'critical'] as $level) : ?><option value="<?= $e($level) ?>" <?= $severity === $level ? 'selected' : '' ?>><?= $e(ucfirst($level)) ?></option><?php endforeach; ?>
    </select></div>
    <div class="flex items-end gap-3"><button class="min-h-10 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Apply filter</button><a class="rounded px-2 py-2 text-sm font-semibold text-slate-600 hover:text-slate-950" href="/admin/security?event=<?= $e($eventType === 'admin_login_failed' ? 'admin_login_failed' : 'security_events') ?>">Reset</a></div>
  </form>
  <?php if ($events === []) : ?><p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No <?= $eventType === 'admin_login_failed' ? 'failed login attempts' : 'security events' ?> recorded.</p>
  <?php else : ?>
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm"><table class="min-w-[700px] w-full text-left text-sm">
    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">When (UTC)</th><th class="px-4 py-3">Severity</th><th class="px-4 py-3">Event</th><th class="px-4 py-3">Description</th></tr></thead>
    <tbody class="divide-y divide-slate-100"><?php foreach ($events as $event) : ?><tr><td class="px-4 py-3"><?= $e($event['created_at']) ?></td><td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-bold uppercase <?= $e($severityStyles[$event['severity']] ?? 'bg-slate-100 text-slate-700') ?>"><?= $e($event['severity']) ?></span></td><td class="px-4 py-3"><?= $e($event['event_type']) ?></td><td class="px-4 py-3"><?= $e($event['description']) ?></td></tr><?php endforeach; ?></tbody>
  </table></div>
  <?php endif; ?>
</section>
