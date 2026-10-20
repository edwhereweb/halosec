<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$query = ['type' => $type, 'status' => $status, 'from' => $fromDate, 'to' => $toDate];
$pageUrl = static fn (int $p): string => '/admin/submissions?' . http_build_query(array_filter($query + ['page' => $p]));
?>
<section class="max-w-6xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <div class="flex flex-wrap items-end justify-between gap-4 mb-5">
    <div><h1 class="text-3xl font-extrabold text-slate-900">Frontend data</h1><p class="mt-1 text-sm text-slate-600">All validated frontend requests in one operations inbox.</p></div>
    <a class="rounded-full bg-[#e4fc68] px-5 py-3 text-xs font-bold uppercase text-slate-900 hover:bg-[#c9ef30]" href="/admin/submissions/export<?= $query === [] ? '' : '?' . http_build_query(array_filter($query)) ?>">Export CSV</a>
  </div>
  <form method="get" action="/admin/submissions" class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4">
    <label for="f_type" class="text-sm font-semibold text-slate-700">Form</label>
    <select id="f_type" name="type" class="rounded-xl border-slate-300 text-sm">
      <option value="">All forms</option>
      <?php foreach (HaloSec\Models\Lead::TYPES as $form) : ?>
      <option value="<?= $e($form) ?>" <?= $type === $form ? 'selected' : '' ?>><?= $e(ucfirst($form)) ?></option>
      <?php endforeach; ?>
    </select>
    <label for="f_status" class="text-sm font-semibold text-slate-700">Status</label>
    <select id="f_status" name="status" class="rounded-xl border-slate-300 text-sm">
      <option value="">All statuses</option>
      <?php foreach (['new', 'in_review', 'resolved', 'spam', 'archived'] as $item) : ?>
      <option value="<?= $e($item) ?>" <?= $status === $item ? 'selected' : '' ?>><?= $e(str_replace('_', ' ', ucfirst($item))) ?></option>
      <?php endforeach; ?>
    </select>
    <label for="f_from" class="text-sm font-semibold text-slate-700">From</label>
    <input id="f_from" name="from" type="date" value="<?= $e($fromDate ?? '') ?>" class="rounded-xl border-slate-300 text-sm">
    <label for="f_to" class="text-sm font-semibold text-slate-700">To</label>
    <input id="f_to" name="to" type="date" value="<?= $e($toDate ?? '') ?>" class="rounded-xl border-slate-300 text-sm">
    <button class="rounded-full bg-slate-900 px-4 py-2 text-xs font-bold uppercase text-white" type="submit">Filter</button>
    <span class="ml-auto text-sm text-slate-500"><?= $e($total) ?> submissions</span>
  </form>
  <?php if ($rows === []) : ?>
  <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center"><h2 class="font-bold text-slate-900">No submissions found</h2><p class="mt-2 text-sm text-slate-600">Try another form or status filter.</p></div>
  <?php else : ?>
  <?php if ($canManage && isset($rows[0]['submission_id'])) : ?><form method="post" action="/admin/submissions/bulk">
  <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
  <div class="mb-3 flex flex-wrap items-center gap-3">
    <label for="bulk_status" class="text-sm font-semibold text-slate-700">Selected submissions</label>
    <select id="bulk_status" name="status" required class="rounded-xl border-slate-300 text-sm">
      <option value="">Choose action</option>
      <option value="resolved">Mark resolved</option><option value="spam">Mark spam</option><option value="archived">Archive</option>
    </select>
    <button class="rounded-full border border-slate-300 px-4 py-2 text-xs font-bold uppercase text-slate-800" type="submit">Apply</button>
  </div>
  <?php endif; ?>
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3"><span class="sr-only">Select</span></th><th class="px-4 py-3">Received (UTC)</th><th class="px-4 py-3">Form</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Assigned</th><th class="px-4 py-3"></th></tr></thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $row) : ?>
      <tr>
        <td class="px-4 py-3"><?php if ($canManage && isset($row['submission_id'])) : ?><input type="checkbox" name="submission_ids[]" value="<?= $e($row['submission_id']) ?>" aria-label="Select submission <?= $e($row['submission_id']) ?>"><?php endif; ?></td>
        <td class="whitespace-nowrap px-4 py-3"><?= $e(str_replace('T', ' ', substr((string) $row['created_at'], 0, 19))) ?></td>
        <td class="px-4 py-3 capitalize"><?= $e($row['form_key'] ?? $row['type']) ?></td>
        <td class="px-4 py-3"><span class="block font-semibold text-slate-900"><?= $e($row['data']['name'] ?? '—') ?></span><span class="text-slate-600"><?= $e($row['data']['email'] ?? '—') ?></span></td>
        <td class="px-4 py-3"><span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold capitalize text-sky-800"><?= $e(str_replace('_', ' ', $row['status'] ?? 'new')) ?></span></td>
        <td class="px-4 py-3"><?= $e($row['assigned_admin_user_id'] ?? 'Unassigned') ?></td>
        <td class="whitespace-nowrap px-4 py-3">
          <?php if (isset($row['submission_id'])) : ?><a class="font-bold text-sky-700 hover:underline" href="/admin/submissions/<?= $e($row['submission_id']) ?>">Review</a>
          <?php else : ?><a class="font-bold text-sky-700 hover:underline" href="/admin/leads/<?= $e($row['type']) ?>/<?= $e(substr($row['id'], strlen($row['type']) + 1)) ?>">View</a><?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($canManage && isset($rows[0]['submission_id'])) : ?></form><?php endif; ?>
  <?php endif; ?>
  <?php if ($pages > 1) : ?>
  <nav class="mt-6 flex justify-between text-sm" aria-label="Pagination">
    <?php if ($page > 1) : ?><a class="font-bold text-sky-700 hover:underline" href="<?= $e($pageUrl($page - 1)) ?>">← Previous</a><?php else : ?><span></span><?php endif; ?>
    <span class="text-slate-600">Page <?= $e($page) ?> of <?= $e($pages) ?></span>
    <?php if ($page < $pages) : ?><a class="font-bold text-sky-700 hover:underline" href="<?= $e($pageUrl($page + 1)) ?>">Next →</a><?php else : ?><span></span><?php endif; ?>
  </nav>
  <?php endif; ?>
</section>
