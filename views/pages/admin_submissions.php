<?php
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$query = array_filter([
    'q' => $filters['q'],
    'type' => $filters['type'],
    'assignee' => $filters['assignee'],
    'from' => $filters['from'],
    'to' => $filters['to'],
    'status' => $filters['status'] === 'open' ? null : $filters['status'],
], static fn (mixed $v): bool => $v !== null && $v !== '');
$url = static function (array $changes) use ($query): string {
    $params = array_filter(array_merge($query, $changes), static fn (mixed $v): bool => $v !== null && $v !== '');

    return '/admin/submissions' . ($params === [] ? '' : '?' . http_build_query($params));
};
$names = [];
foreach ($assignees as $user) {
    $names[(int) $user['id']] = (string) $user['username'];
}
$tabs = ['open' => 'Open', 'new' => 'New', 'in_review' => 'In progress', 'resolved' => 'Done', 'spam' => 'Spam', 'archived' => 'Archived', 'all' => 'All'];
$hasFilters = $filters['q'] !== null || $filters['type'] !== null || $filters['from'] !== null || $filters['to'] !== null
    || ($filters['assignee'] !== null && !in_array($title, ['Assigned to me', 'Unassigned'], true));
$first = $total === 0 ? 0 : ($page - 1) * HaloSec\Controllers\AdminController::PER_PAGE + 1;
$last = min($total, $page * HaloSec\Controllers\AdminController::PER_PAGE);
$intro = match ($title) {
    'Emergencies' => 'Live incidents and security reports. Call the person back before anything else.',
    'Assigned to me' => 'Requests you own. Close them when they are handled.',
    'Unassigned' => 'Nobody owns these yet. Pick one up.',
    default => 'Every request from the website forms.',
};
$returnUrl = $url([]);
?>
<div class="mx-auto flex w-full max-w-7xl flex-col gap-5">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <h1 class="text-[28px] font-bold tracking-tight text-slate-900"><?= $e($title) ?></h1>
      <p class="mt-1 text-[15px] text-slate-600"><?= $e($intro) ?></p>
    </div>
    <a class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50" href="/admin/submissions/export<?= $query === [] ? '' : '?' . $e(http_build_query($query)) ?>">
      <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v11m0 0-4-4m4 4 4-4M5 20h14"/></svg>
      Export CSV
    </a>
  </div>

  <?php if (is_string($saved) && ctype_digit($saved)) : ?>
  <p role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900"><?= $e($saved) ?> <?= $saved === '1' ? 'request' : 'requests' ?> updated.</p>
  <?php endif; ?>

  <nav aria-label="Filter by status" class="-mx-1 flex gap-1 overflow-x-auto overflow-y-hidden border-b border-slate-200 px-1">
    <?php foreach ($tabs as $key => $label) : ?>
    <?php $isActive = $filters['status'] === $key; ?>
    <a href="<?= $e($url(['status' => $key === 'open' ? null : $key, 'page' => null])) ?>" class="-mb-px flex shrink-0 items-center gap-2 border-b-2 px-3 py-2.5 text-sm font-semibold no-underline <?= $isActive ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-800' ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
      <?= $e($label) ?>
      <span class="rounded-full px-1.5 text-xs tabular-nums <?= $isActive ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' ?>"><?= $e($statusCounts[$key] ?? 0) ?></span>
    </a>
    <?php endforeach; ?>
  </nav>

  <form method="get" action="/admin/submissions" data-autosubmit class="flex flex-wrap items-end gap-3">
    <?php if ($filters['status'] !== 'open') : ?><input type="hidden" name="status" value="<?= $e($filters['status']) ?>"><?php endif; ?>
    <div class="w-full sm:w-auto sm:min-w-[220px] sm:flex-1">
      <label for="f_q" class="mb-1 block text-xs font-semibold text-slate-500">Search</label>
      <input id="f_q" name="q" type="search" value="<?= $e($filters['q'] ?? '') ?>" placeholder="Name, email, company, message or #id" class="h-10 w-full rounded-lg border-slate-200 text-sm">
    </div>
    <div class="min-w-0 flex-1 sm:flex-none">
      <label for="f_type" class="mb-1 block text-xs font-semibold text-slate-500">Form</label>
      <select id="f_type" name="type" class="h-10 w-full rounded-lg border-slate-200 pr-8 text-sm sm:w-auto">
        <option value="">All forms</option>
        <option value="urgent" <?= $filters['type'] === 'urgent' ? 'selected' : '' ?>>Emergencies &amp; security reports</option>
        <?php foreach (Ui::formLabels() as $key => $label) : ?>
        <option value="<?= $e($key) ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="min-w-0 flex-1 sm:flex-none">
      <label for="f_assignee" class="mb-1 block text-xs font-semibold text-slate-500">Owner</label>
      <select id="f_assignee" name="assignee" class="h-10 w-full rounded-lg border-slate-200 pr-8 text-sm sm:w-auto">
        <option value="">Anyone</option>
        <option value="me" <?= $filters['assignee'] === 'me' ? 'selected' : '' ?>>Me</option>
        <option value="unassigned" <?= $filters['assignee'] === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
        <?php foreach ($assignees as $user) : ?>
        <option value="<?= $e($user['id']) ?>" <?= $filters['assignee'] === (string) $user['id'] ? 'selected' : '' ?>><?= $e($user['username']) ?><?= (int) $user['is_active'] === 1 ? '' : ' (inactive)' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <input type="checkbox" id="more-filters" class="peer sr-only" <?= $filters['from'] !== null || $filters['to'] !== null ? 'checked' : '' ?>>
    <label for="more-filters" class="w-full cursor-pointer text-sm font-semibold text-slate-600 sm:hidden">More filters</label>
    <div class="hidden w-full grid-cols-2 gap-3 peer-checked:grid sm:flex sm:w-auto">
      <div>
        <label for="f_from" class="mb-1 block text-xs font-semibold text-slate-500">From</label>
        <input id="f_from" name="from" type="date" value="<?= $e($filters['from'] ?? '') ?>" class="h-10 w-full rounded-lg border-slate-200 text-sm">
      </div>
      <div>
        <label for="f_to" class="mb-1 block text-xs font-semibold text-slate-500">To</label>
        <input id="f_to" name="to" type="date" value="<?= $e($filters['to'] ?? '') ?>" class="h-10 w-full rounded-lg border-slate-200 text-sm">
      </div>
    </div>
    <button data-apply class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Apply</button>
    <?php if ($hasFilters) : ?><a class="flex h-10 items-center px-2 text-sm font-semibold text-slate-600 hover:text-slate-900" href="/admin/submissions<?= $filters['status'] === 'open' ? '' : '?status=' . $e($filters['status']) ?>">Clear filters</a><?php endif; ?>
  </form>

  <?php if ($rows === []) : ?>
  <div class="flex flex-col items-center gap-2 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
    <p class="text-base font-bold text-slate-900"><?= $hasFilters ? 'No requests match these filters' : ($filters['status'] === 'open' ? 'All caught up' : 'Nothing here') ?></p>
    <p class="text-sm text-slate-600"><?= $hasFilters ? 'Try a different search or clear the filters.' : ($filters['status'] === 'open' ? 'There are no open requests in this view.' : 'No requests have this status.') ?></p>
    <?php if ($hasFilters) : ?><a class="mt-2 text-sm font-semibold text-sky-800" href="/admin/submissions">Clear filters</a><?php endif; ?>
  </div>
  <?php else : ?>
  <?php if ($canManage) : ?><form method="post" action="/admin/submissions/bulk" id="bulk-form"><input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>"><input type="hidden" name="return" value="<?= $e($returnUrl) ?>"></form><?php endif; ?>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <?php if ($canManage) : ?>
    <div data-bulk class="flex flex-wrap items-center gap-2 border-b border-slate-100 bg-slate-50/80 px-4 py-2.5 ring-inset ring-sky-200">
      <span data-selected-count class="mr-auto text-sm font-medium text-slate-600">Tick requests to update several at once</span>
      <span class="text-xs font-semibold text-slate-500">Mark as</span>
      <button form="bulk-form" name="status" value="in_review" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-40">In progress</button>
      <button form="bulk-form" name="status" value="resolved" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-40">Done</button>
      <button form="bulk-form" name="status" value="archived" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-40">Archived</button>
      <button form="bulk-form" name="status" value="spam" data-confirm="Mark the selected requests as spam?" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50 disabled:opacity-40">Spam</button>
    </div>
    <?php endif; ?>
    <table class="w-full table-fixed text-left text-sm">
      <thead class="border-b border-slate-100 text-xs font-semibold text-slate-500">
        <tr>
          <?php if ($canManage) : ?><th class="w-10 py-2.5 pl-4"><input type="checkbox" data-check-all aria-label="Select all on this page" class="rounded border-slate-300"></th><?php endif; ?>
          <th class="px-4 py-2.5 md:w-[26%]">From</th>
          <th class="hidden px-4 py-2.5 md:table-cell">Request</th>
          <th class="hidden w-[150px] whitespace-nowrap px-4 py-2.5 sm:table-cell">Received <span class="font-normal text-slate-400">(<?= $e(Ui::zoneAbbr()) ?>)</span></th>
          <th class="w-[104px] px-4 py-2.5">Status</th>
          <th class="hidden w-[150px] px-4 py-2.5 lg:table-cell">Owner</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $row) : ?>
        <?php
          $data = is_array($row['data'] ?? null) ? $row['data'] : [];
          $status = (string) $row['status'];
          $urgent = Ui::isUrgent((string) $row['form_key'], $status);
          $owner = $row['assigned_admin_user_id'] === null ? null : ($names[(int) $row['assigned_admin_user_id']] ?? 'Former member');
          $preview = Ui::preview($data, 120);
          $href = '/admin/submissions/' . $row['submission_id'];
        ?>
        <tr class="relative transition-colors hover:bg-slate-50 <?= $urgent ? 'bg-red-50/50' : '' ?> <?= $status === 'new' ? 'font-medium' : '' ?>">
          <?php if ($canManage) : ?><td class="relative z-10 py-3 pl-4 align-top <?= $urgent ? 'border-l-[3px] border-red-600' : '' ?>"><input form="bulk-form" type="checkbox" name="submission_ids[]" value="<?= $e($row['submission_id']) ?>" data-row-check aria-label="Select request from <?= $e(Ui::contactName($data)) ?>" class="mt-0.5 rounded border-slate-300"></td><?php endif; ?>
          <td class="max-w-0 px-4 py-3 align-top">
            <a href="<?= $e($href) ?>" class="block truncate font-semibold text-slate-900 no-underline after:absolute after:inset-0 hover:text-sky-800"><?= $e(Ui::contactName($data)) ?></a>
            <span class="block truncate text-[13px] text-slate-500"><?= $e(is_string($data['company'] ?? null) && $data['company'] !== '' && Ui::contactName($data) !== $data['company'] ? $data['company'] : ($data['email'] ?? '')) ?></span>
            <span class="mt-1 flex items-center gap-1.5 text-[13px] font-normal text-slate-500 md:hidden"><span class="h-1.5 w-1.5 rounded-full <?= Ui::formDot((string) $row['form_key']) ?>" aria-hidden="true"></span><?= $e(Ui::formLabel((string) $row['form_key'])) ?><span class="sm:hidden"> · <?= $e(Ui::ago((string) $row['created_at'])) ?></span></span>
          </td>
          <td class="hidden max-w-0 px-4 py-3 align-top md:table-cell">
            <span class="flex items-center gap-1.5 truncate text-[13px] font-semibold text-slate-700"><span class="h-2 w-2 shrink-0 rounded-full <?= Ui::formDot((string) $row['form_key']) ?>" aria-hidden="true"></span><?= $e(Ui::formLabel((string) $row['form_key'])) ?><?= $row['form_key'] === 'enquiry' && is_string($data['service'] ?? null) ? ' · ' . $e(str_replace('-', ' ', $data['service'])) : '' ?></span>
            <?php if ($preview !== '') : ?><span class="mt-0.5 block truncate font-normal text-[13px] text-slate-500"><?= $e($preview) ?></span><?php endif; ?>
          </td>
          <td class="hidden whitespace-nowrap px-4 py-3 align-top text-[13px] text-slate-600 sm:table-cell">
            <time datetime="<?= $e($row['created_at']) ?>" title="<?= $e(Ui::dateTime((string) $row['created_at'])) ?>"><?= $e(Ui::shortTime((string) $row['created_at'])) ?></time>
            <span class="block text-xs font-normal text-slate-400"><?= $e(Ui::ago((string) $row['created_at'])) ?></span>
          </td>
          <td class="px-4 py-3 align-top"><span class="inline-flex whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset <?= Ui::statusClass($status) ?>"><?= $e(Ui::statusLabel($status)) ?></span></td>
          <td class="hidden px-4 py-3 align-top font-normal text-slate-600 lg:table-cell">
            <?php if ($owner === null) : ?><span class="text-slate-400">—</span>
            <?php else : ?><span class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-100 text-[11px] font-bold uppercase text-sky-900" aria-hidden="true"><?= $e(mb_substr($owner, 0, 1)) ?></span><?= $e($owner) ?></span><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <nav class="flex flex-wrap items-center justify-between gap-3 text-sm" aria-label="Pagination">
    <span class="text-slate-500">Showing <?= $e($first) ?>–<?= $e($last) ?> of <?= $e($total) ?></span>
    <?php if ($pages > 1) : ?>
    <span class="flex gap-2">
      <?php if ($page > 1) : ?><a class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-700 no-underline hover:bg-slate-50" href="<?= $e($url(['page' => $page - 1 > 1 ? $page - 1 : null])) ?>">← Newer</a><?php endif; ?>
      <span class="px-2 py-1.5 text-slate-500">Page <?= $e($page) ?> of <?= $e($pages) ?></span>
      <?php if ($page < $pages) : ?><a class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-700 no-underline hover:bg-slate-50" href="<?= $e($url(['page' => $page + 1])) ?>">Older →</a><?php endif; ?>
    </span>
    <?php endif; ?>
  </nav>
  <?php endif; ?>
</div>
