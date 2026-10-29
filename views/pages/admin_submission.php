<?php
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$data = is_array($submission['data'] ?? null) ? $submission['data'] : [];
$id = (int) $submission['submission_id'];
$formKey = (string) $submission['form_key'];
$status = (string) $submission['status'];
$urgent = Ui::isUrgent($formKey, $status);
$str = static fn (string $key): string => is_scalar($data[$key] ?? null) ? trim((string) $data[$key]) : '';
$email = $str('email');
$phone = $str('phone');
$website = $str('company_website');
$name = Ui::contactName($data);
$owner = null;
foreach ($users as $user) {
    if ((int) $user['id'] === (int) ($submission['assigned_admin_user_id'] ?? 0)) {
        $owner = $user;
    }
}
$subject = 'Re: your ' . strtolower(Ui::formLabel($formKey)) . ' request to HaloSec';
$serviceName = static function (string $slug) use ($services): string {
    $service = $services[$slug] ?? null;

    return is_array($service) ? (string) ($service['nav_label'] ?? $service['title'] ?? $slug) : $slug;
};
$details = [];
foreach ($data as $key => $value) {
    if (in_array($key, ['name', 'email', 'phone', 'company', 'message', 'answers', 'score', 'company_website'], true) || !is_scalar($value) || trim((string) $value) === '') {
        continue;
    }
    $details[Ui::fieldLabel((string) $key)] = $key === 'service' ? $serviceName((string) $value) : (string) $value;
}
$answers = is_array($data['answers'] ?? null) ? $data['answers'] : [];
$score = is_numeric($data['score'] ?? null) ? (int) $data['score'] : null;
$answerStyle = static fn (string $a): string => match ($a) {
    'yes' => 'bg-emerald-100 text-emerald-900',
    'partial' => 'bg-amber-100 text-amber-900',
    'no' => 'bg-red-100 text-red-900',
    default => 'bg-slate-100 text-slate-700',
};
$statusButtons = ['new' => 'New', 'in_review' => 'In progress', 'resolved' => 'Done', 'archived' => 'Archived', 'spam' => 'Spam'];
?>
<div class="mx-auto flex w-full max-w-7xl flex-col gap-5">
  <div class="flex items-center justify-between gap-3">
    <a class="inline-flex items-center gap-1 text-sm font-semibold text-slate-600 no-underline hover:text-slate-900" href="/admin/submissions">← Inbox</a>
    <div class="flex items-center gap-1 text-sm">
      <span class="mr-2 text-slate-400">#<?= $e($id) ?></span>
      <?php if ($neighbours['newer'] !== null) : ?><a class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 font-semibold text-slate-600 no-underline hover:bg-slate-50" href="/admin/submissions/<?= $e($neighbours['newer']) ?>" title="Newer request">↑ Newer</a><?php endif; ?>
      <?php if ($neighbours['older'] !== null) : ?><a class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 font-semibold text-slate-600 no-underline hover:bg-slate-50" href="/admin/submissions/<?= $e($neighbours['older']) ?>" title="Older request">↓ Older</a><?php endif; ?>
    </div>
  </div>

  <?php if ($flashMessage !== null) : ?>
  <p role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">✓ <?= $e($flashMessage) ?></p>
  <?php endif; ?>

  <?php if ($urgent) : ?>
  <div class="flex flex-wrap items-center gap-3 rounded-2xl bg-red-600 px-5 py-4 text-white">
    <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 3 2 20h20L12 3Z"/><path d="M12 10v4M12 17.5v.01"/></svg>
    <p class="flex-1 text-sm font-semibold"><?= $status === 'new'
        ? 'Live incident. Call ' . $e($name) . ' back now, then set the status to In progress so the team knows it’s being handled.'
        : 'Incident in progress. Keep ' . $e($name) . ' updated and mark it Done once they are safe.' ?></p>
    <?php if ($phone !== '') : ?><a href="tel:<?= $e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-red-700 no-underline hover:bg-red-50">Call <?= $e($phone) ?></a><?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
    <div class="flex min-w-0 flex-col gap-5">
      <section class="rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex flex-wrap items-center gap-2 text-sm">
          <span class="inline-flex items-center gap-1.5 font-semibold text-slate-700"><span class="h-2 w-2 rounded-full <?= Ui::formDot($formKey) ?>" aria-hidden="true"></span><?= $e(Ui::formLabel($formKey)) ?></span>
          <span class="text-slate-300">·</span>
          <time class="text-slate-500" datetime="<?= $e($submission['created_at']) ?>"><?= $e(Ui::dateTime((string) $submission['created_at'])) ?> <?= $e(Ui::zoneAbbr()) ?> (<?= $e(Ui::ago((string) $submission['created_at'])) ?>)</time>
          <span class="ml-auto rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset <?= Ui::statusClass($status) ?>"><?= $e(Ui::statusLabel($status)) ?></span>
        </div>
        <h1 class="mt-3 text-[26px] font-bold tracking-tight text-slate-900"><?= $e($name) ?></h1>
        <?php if ($str('company') !== '' && $str('company') !== $name) : ?><p class="text-[15px] text-slate-600"><?= $e($str('company')) ?></p><?php endif; ?>

        <div class="mt-4 flex flex-wrap gap-2">
          <?php if ($email !== '') : ?>
          <a href="mailto:<?= $e($email) ?>?subject=<?= $e(rawurlencode($subject)) ?>" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3.5 py-2 text-sm font-semibold text-white no-underline hover:bg-slate-700">
            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>Reply by email
          </a>
          <?php endif; ?>
          <?php if ($phone !== '') : ?>
          <a href="tel:<?= $e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50">
            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg>Call <?= $e($phone) ?>
          </a>
          <?php endif; ?>
          <?php if ($email !== '') : ?><button type="button" data-copy="<?= $e($email) ?>" class="rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Copy email</button><?php endif; ?>
          <?php if ($website !== '' && preg_match('#^https?://#i', $website) === 1) : ?><a href="<?= $e($website) ?>" target="_blank" rel="noopener noreferrer nofollow" class="rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50">Visit website ↗</a><?php endif; ?>
        </div>

        <dl class="mt-6 grid gap-x-6 gap-y-3 border-t border-slate-100 pt-5 text-sm sm:grid-cols-2">
          <?php if ($email !== '') : ?><div><dt class="text-xs font-semibold text-slate-500">Email</dt><dd class="break-all text-slate-900"><?= $e($email) ?></dd></div><?php endif; ?>
          <?php if ($phone !== '') : ?><div><dt class="text-xs font-semibold text-slate-500">Phone</dt><dd class="text-slate-900"><?= $e($phone) ?></dd></div><?php endif; ?>
          <?php if ($website !== '') : ?><div><dt class="text-xs font-semibold text-slate-500">Website</dt><dd class="break-all text-slate-900"><?= $e($website) ?></dd></div><?php endif; ?>
          <?php foreach ($details as $label => $value) : ?><div><dt class="text-xs font-semibold text-slate-500"><?= $e($label) ?></dt><dd class="whitespace-pre-wrap break-words text-slate-900"><?= $e($value) ?></dd></div><?php endforeach; ?>
        </dl>
      </section>

      <?php if ($str('message') !== '') : ?>
      <section class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="msg">
        <h2 id="msg" class="text-sm font-bold text-slate-900">Message</h2>
        <p class="mt-3 whitespace-pre-wrap break-words text-[15px] leading-relaxed text-slate-800"><?= $e($str('message')) ?></p>
      </section>
      <?php endif; ?>

      <?php if ($score !== null || $answers !== []) : ?>
      <section class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="auditres">
        <div class="flex flex-wrap items-end justify-between gap-3">
          <h2 id="auditres" class="text-sm font-bold text-slate-900">Audit answers</h2>
          <?php if ($score !== null) : ?>
          <?php $band = $score < 40 ? ['Weak', 'bg-red-600', 'text-red-800'] : ($score < 70 ? ['Fair', 'bg-amber-500', 'text-amber-900'] : ['Strong', 'bg-emerald-600', 'text-emerald-800']); ?>
          <p class="text-sm"><span class="text-2xl font-bold text-slate-900"><?= $e($score) ?></span><span class="text-slate-500">/100</span> <span class="ml-1 font-semibold <?= $band[2] ?>"><?= $e($band[0]) ?></span></p>
          <?php endif; ?>
        </div>
        <?php if ($score !== null) : ?>
        <div class="mt-3 h-2 rounded-full bg-slate-100" role="img" aria-label="Security score <?= $e($score) ?> out of 100"><div class="h-2 rounded-full <?= $band[1] ?>" style="width: <?= max(2, $score) ?>%"></div></div>
        <?php endif; ?>
        <?php if ($answers !== []) : ?>
        <ul class="mt-5 divide-y divide-slate-100">
          <?php foreach ($answers as $key => $answer) : ?>
          <li class="flex items-start justify-between gap-4 py-2.5 text-sm">
            <span class="text-slate-700"><?= $e($auditQuestions[$key] ?? Ui::fieldLabel((string) $key)) ?></span>
            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $answerStyle((string) $answer) ?>"><?= $e($auditOptions[$answer] ?? ucfirst((string) $answer)) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </section>
      <?php endif; ?>
    </div>

    <aside class="flex flex-col gap-5">
      <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="st">
        <h2 id="st" class="text-sm font-bold text-slate-900">Status</h2>
        <?php if ($canManage) : ?>
        <form method="post" action="/admin/submissions/<?= $e($id) ?>/status" class="mt-3 flex flex-wrap gap-1.5">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <?php foreach ($statusButtons as $key => $label) : ?>
          <button type="submit" name="status" value="<?= $e($key) ?>" <?= $key === $status ? 'aria-pressed="true" disabled' : 'aria-pressed="false"' ?> <?= $key === 'spam' ? 'data-confirm="Mark this request as spam?"' : '' ?>
            class="rounded-lg px-3 py-1.5 text-sm font-semibold <?= $key === $status ? 'bg-slate-900 text-white' : ($key === 'spam' ? 'border border-slate-200 text-red-700 hover:bg-red-50' : 'border border-slate-200 text-slate-700 hover:bg-slate-50') ?>"><?= $e($label) ?></button>
          <?php endforeach; ?>
        </form>
        <?php else : ?>
        <p class="mt-2"><span class="rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset <?= Ui::statusClass($status) ?>"><?= $e(Ui::statusLabel($status)) ?></span></p>
        <?php endif; ?>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="own">
        <h2 id="own" class="text-sm font-bold text-slate-900">Owner</h2>
        <p class="mt-2 text-sm text-slate-700"><?= $owner === null ? '<span class="text-slate-500">Nobody yet</span>' : $e($owner['username']) ?></p>
        <?php if ($canAssign) : ?>
        <form method="post" action="/admin/submissions/<?= $e($id) ?>/assignment" class="mt-3 flex flex-col gap-2">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <?php if ((int) ($submission['assigned_admin_user_id'] ?? 0) !== (int) $me) : ?>
          <button type="submit" name="assigned_admin_user_id" value="me" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700">Assign to me</button>
          <?php endif; ?>
          <div class="flex gap-2">
            <label for="assignee" class="sr-only">Assign to</label>
            <select id="assignee" name="assigned_admin_user_id" class="h-9 min-w-0 flex-1 rounded-lg border-slate-200 text-sm">
              <option value="">Nobody</option>
              <?php foreach ($users as $user) : if ((int) $user['is_active'] !== 1) { continue; } ?>
              <option value="<?= $e($user['id']) ?>" <?= (int) ($submission['assigned_admin_user_id'] ?? 0) === (int) $user['id'] ? 'selected' : '' ?>><?= $e($user['username']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="rounded-lg border border-slate-200 px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Save</button>
          </div>
        </form>
        <?php endif; ?>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="act">
        <h2 id="act" class="text-sm font-bold text-slate-900">Activity</h2>
        <?php if ($canManage) : ?>
        <form method="post" action="/admin/submissions/<?= $e($id) ?>/notes" class="mt-3 flex flex-col gap-2">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <label for="internal_note" class="sr-only">Add an internal note</label>
          <textarea id="internal_note" name="note" rows="3" maxlength="4000" required placeholder="Add a note for the team (not sent to the customer)" class="w-full rounded-lg border-slate-200 text-sm placeholder:text-slate-400"></textarea>
          <button class="self-end rounded-lg bg-slate-900 px-3.5 py-1.5 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Add note</button>
        </form>
        <?php endif; ?>
        <ol class="mt-4 flex flex-col gap-4 border-l-2 border-slate-100 pl-4">
          <?php foreach ($timeline as $entry) : ?>
          <li class="relative">
            <span class="absolute -left-[23px] top-1 h-3 w-3 rounded-full border-2 border-white <?= $entry['kind'] === 'note' ? 'bg-sky-500' : 'bg-slate-300' ?>" aria-hidden="true"></span>
            <p class="text-xs text-slate-500"><strong class="font-semibold text-slate-700"><?= $e($entry['actor']) ?></strong> · <time datetime="<?= $e($entry['created_at']) ?>" title="<?= $e(Ui::dateTime($entry['created_at'])) ?>"><?= $e(Ui::ago($entry['created_at'])) ?></time></p>
            <?php if ($entry['kind'] === 'note') : ?>
            <p class="mt-1 whitespace-pre-wrap break-words rounded-lg bg-sky-50 px-3 py-2 text-sm text-slate-800"><?= $e($entry['text']) ?></p>
            <?php else : ?>
            <p class="mt-0.5 text-sm text-slate-700"><?= $e($entry['text']) ?></p>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
          <li class="relative">
            <span class="absolute -left-[23px] top-1 h-3 w-3 rounded-full border-2 border-white bg-lime-500" aria-hidden="true"></span>
            <p class="text-xs text-slate-500"><strong class="font-semibold text-slate-700">Website form</strong> · <?= $e(Ui::ago((string) $submission['created_at'])) ?></p>
            <p class="mt-0.5 text-sm text-slate-700">Request received</p>
          </li>
        </ol>
      </section>

      <?php if (!empty($canPrivacy)) : ?>
      <?php $others = max(0, (int) ($personSubmissions ?? 0)); ?>
      <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="priv">
        <h2 id="priv" class="text-sm font-bold text-slate-900">Privacy request</h2>
        <p class="mt-1 text-sm text-slate-600">If this person asks for a copy of their data or asks us to delete it (GDPR / DPDP), use these. <?= $others > 1 ? 'We hold ' . $e($others) . ' requests from this email.' : '' ?></p>
        <form method="post" action="/admin/submissions/<?= $e($id) ?>/privacy-export" class="mt-3">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <button type="submit" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Download their data (JSON)</button>
        </form>
        <details class="mt-3 rounded-lg border border-red-200">
          <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-red-700">Delete permanently…</summary>
          <form method="post" action="/admin/submissions/<?= $e($id) ?>/erase" class="flex flex-col gap-2 border-t border-red-100 p-3">
            <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
            <?php if ($others > 1) : ?>
            <label class="flex items-start gap-2 text-sm text-slate-700"><input type="radio" name="scope" value="person" checked class="mt-0.5"> All <?= $e($others) ?> requests from this email</label>
            <label class="flex items-start gap-2 text-sm text-slate-700"><input type="radio" name="scope" value="one" class="mt-0.5"> Only this request</label>
            <?php else : ?>
            <input type="hidden" name="scope" value="person">
            <?php endif; ?>
            <p class="text-xs text-slate-500">The request, its fields and team notes are removed for good. This cannot be undone.</p>
            <label for="erase_confirm" class="text-xs font-semibold text-slate-700">Type DELETE to confirm</label>
            <input id="erase_confirm" name="confirm" required pattern="DELETE" autocomplete="off" class="h-9 rounded-lg border-slate-200 text-sm">
            <button type="submit" data-confirm="Permanently delete this person’s data? This cannot be undone." class="rounded-lg bg-red-700 px-3 py-2 text-sm font-semibold text-white hover:bg-red-800">Delete permanently</button>
          </form>
        </details>
      </section>
      <?php endif; ?>
    </aside>
  </div>
</div>
