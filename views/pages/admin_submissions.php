<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$query = ['type' => $type, 'status' => $status, 'from' => $fromDate, 'to' => $toDate, 'assignee' => $assigneeId];
$pageUrl = static fn (int $p): string => '/admin/submissions?' . http_build_query(array_filter($query + ['page' => $p]));
$assigneeNames = [];
foreach ($assignees as $assignee) {
    $assigneeNames[(int) $assignee['id']] = (string) $assignee['username']
        . ((int) $assignee['is_active'] === 1 ? '' : ' (inactive)');
}
$statusStyles = [
    'new' => 'bg-sky-50 text-sky-800',
    'in_review' => 'bg-amber-50 text-amber-800',
    'resolved' => 'bg-emerald-50 text-emerald-800',
    'spam' => 'bg-slate-100 text-slate-700',
    'archived' => 'bg-slate-100 text-slate-600',
];
$formLabels = [
    'audit' => 'Audit',
    'consultation' => 'Consultation',
    'contact' => 'Contact',
    'emergency' => 'Emergency',
    'enquiry' => 'Enquiry',
    'newsletter' => 'Newsletter',
    'security_report' => 'Security report',
];
?>
<section class="mx-auto w-full max-w-7xl space-y-5">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm font-semibold text-slate-500">Submissions</p><h1 class="mt-1 text-3xl font-bold text-slate-950">All Inbox</h1><p class="mt-2 text-sm text-slate-600">Triage incoming requests across every channel.</p></div>
    <a class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700" href="/admin/submissions/export<?= http_build_query(array_filter($query)) === '' ? '' : '?' . http_build_query(array_filter($query)) ?>">Export CSV</a>
  </div>

  <form method="get" action="/admin/submissions" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 xl:grid-cols-5">
    <div><label for="f_type" class="mb-1 block text-xs font-semibold text-slate-600">Submission type</label>
      <select id="f_type" name="type" class="w-full rounded-lg border-slate-300 text-sm">
        <option value="">All types</option>
        <?php foreach (HaloSec\Models\Lead::TYPES as $form) : ?>
        <option value="<?= $e($form) ?>" <?= $type === $form ? 'selected' : '' ?>><?= $e($formLabels[$form] ?? ucfirst($form)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label for="f_status" class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
      <select id="f_status" name="status" class="w-full rounded-lg border-slate-300 text-sm">
        <option value="">All statuses</option>
        <?php foreach (['new', 'in_review', 'resolved', 'spam', 'archived'] as $item) : ?>
        <option value="<?= $e($item) ?>" <?= $status === $item ? 'selected' : '' ?>><?= $e(str_replace('_', ' ', ucfirst($item))) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label for="f_assignee" class="mb-1 block text-xs font-semibold text-slate-600">Assignee</label>
      <select id="f_assignee" name="assignee" class="w-full rounded-lg border-slate-300 text-sm">
        <option value="">Anyone</option>
        <option value="unassigned" <?= $assigneeId === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
        <?php foreach ($assignees as $assignee) : ?>
        <option value="<?= $e($assignee['id']) ?>" <?= $assigneeId === (string) $assignee['id'] ? 'selected' : '' ?>><?= $e($assignee['username']) ?><?= (int) $assignee['is_active'] === 1 ? '' : ' (inactive)' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="grid grid-cols-2 gap-3">
      <div><label for="f_from" class="mb-1 block text-xs font-semibold text-slate-600">From</label><input id="f_from" name="from" type="date" value="<?= $e($fromDate ?? '') ?>" class="w-full rounded-lg border-slate-300 text-sm"></div>
      <div><label for="f_to" class="mb-1 block text-xs font-semibold text-slate-600">To</label><input id="f_to" name="to" type="date" value="<?= $e($toDate ?? '') ?>" class="w-full rounded-lg border-slate-300 text-sm"></div>
    </div>
    <div class="flex items-end gap-3">
      <button class="min-h-10 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700" type="submit">Apply filters</button>
      <a class="rounded px-2 py-2 text-sm font-semibold text-slate-600 hover:text-slate-950" href="/admin/submissions">Reset</a>
    </div>
    <p class="text-sm text-slate-500 sm:col-span-2 xl:col-span-5" aria-live="polite"><?= $e($total) ?> <?= $total === 1 ? 'submission' : 'submissions' ?></p>
  </form>
  <?php if ($rows === []) : ?>
  <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><h2 class="font-bold text-slate-900">No submissions found</h2><p class="mt-2 text-sm text-slate-600">Try another type or status filter.</p></div>
  <?php else : ?>
  <?php if ($canManage && isset($rows[0]['submission_id'])) : ?><form method="post" action="/admin/submissions/bulk">
  <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
  <div class="mb-3 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
    <label for="bulk_status" class="text-sm font-semibold text-slate-700">Bulk action</label>
    <select id="bulk_status" name="status" required class="rounded-xl border-slate-300 text-sm">
      <option value="">Choose action</option>
      <option value="resolved">Mark resolved</option><option value="spam">Mark spam</option><option value="archived">Archive</option>
    </select>
    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 transition hover:bg-slate-50" type="submit">Apply action</button>
  </div>
  <?php endif; ?>
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm" tabindex="0" aria-label="Scrollable submissions table">
    <table class="min-w-[820px] w-full text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3"><span class="sr-only">Select</span></th><th class="px-4 py-3">Received (UTC)</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Assignee</th><th class="px-4 py-3"><span class="sr-only">Actions</span></th></tr></thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $row) : ?>
      <tr class="transition-colors hover:bg-slate-50">
        <td class="px-4 py-3"><?php if ($canManage && isset($row['submission_id'])) : ?><input type="checkbox" name="submission_ids[]" value="<?= $e($row['submission_id']) ?>" aria-label="Select submission <?= $e($row['submission_id']) ?>"><?php endif; ?></td>
        <td class="whitespace-nowrap px-4 py-3"><?= $e(str_replace('T', ' ', substr((string) $row['created_at'], 0, 19))) ?></td>
        <td class="px-4 py-3"><?= $e($formLabels[$row['form_key'] ?? $row['type']] ?? ucfirst((string) ($row['form_key'] ?? $row['type']))) ?></td>
        <td class="px-4 py-3"><span class="block font-semibold text-slate-900"><?= $e($row['data']['name'] ?? '—') ?></span><span class="text-slate-600"><?= $e($row['data']['email'] ?? '—') ?></span></td>
        <?php $rowStatus = (string) ($row['status'] ?? 'new'); ?>
        <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold capitalize <?= $e($statusStyles[$rowStatus] ?? 'bg-slate-100 text-slate-700') ?>"><?= $e(str_replace('_', ' ', $rowStatus)) ?></span></td>
        <?php $assignee = $row['assigned_admin_user_id'] ?? null; ?>
        <td class="px-4 py-3"><?= $e($assignee === null ? 'Unassigned' : ($assigneeNames[(int) $assignee] ?? 'Admin #' . $assignee)) ?></td>
        <td class="whitespace-nowrap px-4 py-3">
          <?php if (isset($row['submission_id'])) : ?><a class="rounded font-semibold text-sky-800 hover:underline" href="/admin/submissions/<?= $e($row['submission_id']) ?>">Review</a>
          <?php else : ?><a class="rounded font-semibold text-sky-800 hover:underline" href="/admin/leads/<?= $e($row['type']) ?>/<?= $e(substr($row['id'], strlen($row['type']) + 1)) ?>">View</a><?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($canManage && isset($rows[0]['submission_id'])) : ?></form><?php endif; ?>
  <?php endif; ?>
  <?php if ($pages > 1) : ?>
  <nav class="flex items-center justify-between text-sm" aria-label="Pagination">
    <?php if ($page > 1) : ?><a class="font-bold text-sky-700 hover:underline" href="<?= $e($pageUrl($page - 1)) ?>">← Previous</a><?php else : ?><span></span><?php endif; ?>
    <span class="text-slate-600">Page <?= $e($page) ?> of <?= $e($pages) ?></span>
    <?php if ($page < $pages) : ?><a class="font-bold text-sky-700 hover:underline" href="<?= $e($pageUrl($page + 1)) ?>">Next →</a><?php else : ?><span></span><?php endif; ?>
  </nav>
  <?php endif; ?>
</section>
