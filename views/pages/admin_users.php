<?php
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$roles = ['super_admin', 'admin', 'editor', 'viewer'];
$withoutTwoFactor = count(array_filter($users, static fn (array $u): bool => (int) $u['is_active'] === 1 && empty($u['has_two_factor'])));
?>
<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
  <div>
    <h1 class="text-[28px] font-bold tracking-tight text-slate-900">Team &amp; roles</h1>
    <p class="mt-1 text-[15px] text-slate-600">Who can sign in to the admin area, and what they are allowed to do.</p>
  </div>

  <?php if ($message !== null) : ?>
  <p role="<?= $messageIsError ? 'alert' : 'status' ?>" class="rounded-xl border px-4 py-3 text-sm font-semibold <?= $messageIsError ? 'border-red-200 bg-red-50 text-red-900' : 'border-emerald-200 bg-emerald-50 text-emerald-900' ?>"><?= $e($message) ?></p>
  <?php endif; ?>

  <?php if (!($isDatabase ?? true)) : ?>
  <p role="status" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Team accounts need the database. Check the DB settings in <code>.env</code>.<?php if (!empty($dbError)) : ?> <span class="font-mono text-xs"><?= $e($dbError) ?></span><?php endif; ?></p>
  <?php endif; ?>

  <?php if ($withoutTwoFactor > 0) : ?>
  <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
    <strong><?= $e($withoutTwoFactor) ?> active <?= $withoutTwoFactor === 1 ? 'person has' : 'people have' ?> no two-factor sign-in yet.</strong>
    <?= $requireTwoFactor ? 'They will be asked to set it up when they next sign in.' : 'Ask them to set it up under My account, then turn on ADMIN_REQUIRE_2FA on the server.' ?>
  </p>
  <?php endif; ?>

  <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="people">
      <h2 id="people" class="border-b border-slate-100 px-5 py-3 text-sm font-bold text-slate-900">People (<?= $e(count($users)) ?>)</h2>
      <?php if ($users === []) : ?>
      <p class="p-6 text-sm text-slate-500">No team accounts yet.</p>
      <?php else : ?>
      <ul class="divide-y divide-slate-100">
        <?php foreach ($users as $user) : ?>
        <?php $isMe = (int) $user['id'] === (int) $me; $active = (int) $user['is_active'] === 1; ?>
        <li class="px-5 py-4">
          <details class="group">
            <summary class="flex cursor-pointer list-none flex-wrap items-center gap-3">
              <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-extrabold uppercase <?= $active ? 'bg-sky-100 text-sky-900' : 'bg-slate-100 text-slate-400' ?>" aria-hidden="true"><?= $e(mb_substr((string) $user['username'], 0, 1)) ?></span>
              <span class="min-w-0 flex-1">
                <span class="block font-semibold <?= $active ? 'text-slate-900' : 'text-slate-400 line-through' ?>"><?= $e($user['username']) ?><?= $isMe ? ' <span class="text-xs font-medium text-slate-500 no-underline">(you)</span>' : '' ?></span>
                <span class="block text-xs text-slate-500"><?= $e(Ui::roleLabel((string) ($user['role_key'] ?? ''))) ?> · <?= $e(Ui::roleHelp((string) ($user['role_key'] ?? ''))) ?></span>
              </span>
              <span class="flex items-center gap-2">
                <?php if (!$active) : ?><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Deactivated</span><?php endif; ?>
                <span class="rounded-full px-2 py-0.5 text-xs font-semibold <?= !empty($user['has_two_factor']) ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' ?>"><?= !empty($user['has_two_factor']) ? '✓ 2FA on' : '2FA off' ?></span>
                <span class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 group-open:bg-slate-100">Edit</span>
              </span>
            </summary>
            <form method="post" action="/admin/users/<?= $e($user['id']) ?>" class="mt-4 grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
              <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
              <div>
                <label for="role_<?= $e($user['id']) ?>" class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                <select id="role_<?= $e($user['id']) ?>" name="role" class="h-9 w-full rounded-lg border-slate-200 text-sm" <?= $isMe ? 'disabled' : '' ?>>
                  <?php foreach ($roles as $role) : ?><option value="<?= $e($role) ?>" <?= ($user['role_key'] ?? '') === $role ? 'selected' : '' ?>><?= $e(Ui::roleLabel($role)) ?></option><?php endforeach; ?>
                </select>
                <?php if ($isMe) : ?><input type="hidden" name="role" value="<?= $e($user['role_key'] ?? '') ?>"><p class="mt-1 text-xs text-slate-500">You can’t change your own role.</p><?php endif; ?>
              </div>
              <div>
                <label for="pw_<?= $e($user['id']) ?>" class="mb-1 block text-xs font-semibold text-slate-600">New password <span class="font-normal text-slate-400">(optional, 12+ characters)</span></label>
                <input id="pw_<?= $e($user['id']) ?>" type="password" name="password" minlength="12" maxlength="1024" autocomplete="new-password" class="h-9 w-full rounded-lg border-slate-200 text-sm">
              </div>
              <label class="flex items-center gap-2 text-sm text-slate-700 sm:col-span-2">
                <input type="checkbox" name="active" value="1" <?= $active ? 'checked' : '' ?> <?= $isMe ? 'disabled' : '' ?> class="rounded border-slate-300">
                Can sign in
                <?php if ($isMe) : ?><input type="hidden" name="active" value="1"><?php endif; ?>
              </label>
              <div class="sm:col-span-2"><button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Save changes</button></div>
            </form>
          </details>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </section>

    <div class="flex flex-col gap-6">
      <form method="post" action="/admin/users" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5">
        <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
        <h2 class="text-sm font-bold text-slate-900">Add a team member</h2>
        <div>
          <label for="admin_username" class="mb-1 block text-xs font-semibold text-slate-600">Username</label>
          <input id="admin_username" name="username" required minlength="3" maxlength="190" pattern="[A-Za-z0-9_.@-]+" autocomplete="off" class="h-9 w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
          <label for="admin_password" class="mb-1 block text-xs font-semibold text-slate-600">First password <span class="font-normal text-slate-400">(12+ characters)</span></label>
          <input id="admin_password" name="password" type="password" required minlength="12" maxlength="1024" autocomplete="new-password" class="h-9 w-full rounded-lg border-slate-200 text-sm">
          <p class="mt-1 text-xs text-slate-500">Share it privately. They can change it under My account.</p>
        </div>
        <fieldset>
          <legend class="mb-1 text-xs font-semibold text-slate-600">Role</legend>
          <div class="flex flex-col gap-1.5">
            <?php foreach (['viewer', 'editor', 'admin', 'super_admin'] as $role) : ?>
            <label class="flex cursor-pointer items-start gap-2 rounded-lg border border-slate-200 p-2.5 text-sm has-[:checked]:border-slate-900 has-[:checked]:bg-slate-50">
              <input type="radio" name="role" value="<?= $e($role) ?>" <?= $role === 'editor' ? 'checked' : '' ?> class="mt-0.5">
              <span><span class="block font-semibold text-slate-900"><?= $e(Ui::roleLabel($role)) ?></span><span class="block text-xs text-slate-500"><?= $e(Ui::roleHelp($role)) ?></span></span>
            </label>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Add person</button>
      </form>
    </div>
  </div>
</div>
