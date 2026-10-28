<?php
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$severityStyles = [
    'info' => 'bg-sky-100 text-sky-900',
    'low' => 'bg-slate-100 text-slate-700',
    'medium' => 'bg-amber-100 text-amber-900',
    'high' => 'bg-red-100 text-red-900',
    'critical' => 'bg-red-600 text-white',
];
$eventLabels = [
    'admin_login_failed' => 'Failed admin sign-in',
    'security_report_received' => 'Security report received',
    'suspicious_submission' => 'Suspicious form input',
];
$tabs = ['security_events' => 'Alerts', 'admin_login_failed' => 'Failed sign-ins'];
?>
<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
  <div>
    <h1 class="text-[28px] font-bold tracking-tight text-slate-900">Security log</h1>
    <p class="mt-1 text-[15px] text-slate-600">Failed admin sign-ins and alerts raised by the website, newest first.</p>
  </div>

  <?php if (!($isDatabase ?? true)) : ?>
  <p role="status" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Security logging needs the database. Check the DB settings in <code>.env</code>.<?php if (!empty($dbError)) : ?> <span class="font-mono text-xs"><?= $e($dbError) ?></span><?php endif; ?></p>
  <?php endif; ?>

  <div class="grid gap-4 sm:grid-cols-3">
    <?= $this->include('partials/admin/stat', ['label' => 'Failed sign-ins, 24 hours', 'value' => (string) $failed24h, 'hint' => $failed24h >= 10 ? 'Unusually high: check below' : 'A few typos a day is normal', 'tone' => $failed24h >= 10 ? 'alert' : 'default']) ?>
    <?= $this->include('partials/admin/stat', ['label' => 'Failed sign-ins, 7 days', 'value' => (string) $failed7d, 'hint' => 'Repeated tries are slowed down automatically']) ?>
    <?= $this->include('partials/admin/stat', ['label' => 'Failed sign-ins, 30 days', 'value' => (string) $failed30d, 'hint' => 'Locked-out admin? Run php bin/admin.php unblock']) ?>
  </div>

  <nav aria-label="Log type" class="flex gap-1 border-b border-slate-200">
    <?php foreach ($tabs as $key => $label) : ?>
    <a href="/admin/security?event=<?= $e($key) ?>" class="-mb-px border-b-2 px-3 py-2.5 text-sm font-semibold no-underline <?= $eventType === $key ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-800' ?>" <?= $eventType === $key ? 'aria-current="page"' : '' ?>><?= $e($label) ?></a>
    <?php endforeach; ?>
    <?php if ($eventType === 'security_events') : ?>
    <form method="get" action="/admin/security" data-autosubmit class="ml-auto flex items-center gap-2 pb-1.5">
      <input type="hidden" name="event" value="security_events">
      <label for="severity" class="text-xs font-semibold text-slate-500">Severity</label>
      <select id="severity" name="severity" class="h-8 rounded-lg border-slate-200 py-0 text-sm"><option value="">All</option>
        <?php foreach (['critical', 'high', 'medium', 'low', 'info'] as $level) : ?><option value="<?= $e($level) ?>" <?= $severity === $level ? 'selected' : '' ?>><?= $e(ucfirst($level)) ?></option><?php endforeach; ?>
      </select>
      <button data-apply class="h-8 rounded-lg bg-slate-900 px-3 text-sm font-semibold text-white" type="submit">Apply</button>
    </form>
    <?php endif; ?>
  </nav>

  <?php if ($events === []) : ?>
  <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-600">Nothing logged here. That’s good.</p>
  <?php else : ?>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
    <table class="w-full text-left text-sm">
      <thead class="border-b border-slate-100 text-xs font-semibold text-slate-500"><tr><th class="px-4 py-2.5">When (<?= $e(Ui::zoneAbbr()) ?>)</th><th class="px-4 py-2.5">Severity</th><th class="px-4 py-2.5">Event</th><th class="hidden px-4 py-2.5 md:table-cell">Details</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($events as $event) : ?>
        <tr>
          <td class="whitespace-nowrap px-4 py-3 text-slate-600"><span title="<?= $e(Ui::dateTime((string) $event['created_at'])) ?>"><?= $e(Ui::shortTime((string) $event['created_at'])) ?></span> <span class="text-xs text-slate-400"><?= $e(Ui::ago((string) $event['created_at'])) ?></span></td>
          <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize <?= $e($severityStyles[$event['severity']] ?? 'bg-slate-100 text-slate-700') ?>"><?= $e($event['severity']) ?></span></td>
          <td class="px-4 py-3 font-medium text-slate-900"><?= $e($eventLabels[$event['event_type']] ?? str_replace('_', ' ', (string) $event['event_type'])) ?></td>
          <td class="hidden px-4 py-3 text-slate-600 md:table-cell"><?= $e($event['description']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="text-xs text-slate-500">Showing the latest <?= $e(count($events)) ?>. IP addresses and usernames are stored only as one-way hashes.</p>
  <?php endif; ?>
</div>
