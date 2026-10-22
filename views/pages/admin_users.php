<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="mx-auto w-full max-w-7xl space-y-5">
  <div><p class="text-sm font-semibold text-slate-500">Governance</p><h1 class="mt-1 text-3xl font-bold text-slate-950">Admin Users / Roles</h1></div>
  <p class="mb-6 text-sm text-slate-600">Create named accounts with least-privilege roles. Passwords are hashed before storage.</p>
  <?php if ($message !== null) : ?><p role="status" class="mb-4 rounded-xl bg-sky-50 p-3 text-sm text-sky-900"><?= $e($message) ?></p><?php endif; ?>
  <div class="grid gap-6 md:grid-cols-[minmax(0,1fr)_minmax(300px,1.3fr)]">
    <form method="post" action="/admin/users" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
      <label for="admin_username" class="mb-1 block text-sm font-semibold text-slate-700">Username</label>
      <input id="admin_username" name="username" required minlength="3" maxlength="190" pattern="[A-Za-z0-9_.@-]+" autocomplete="username" class="mb-4 w-full rounded-xl border-slate-300 text-sm">
      <label for="admin_password" class="mb-1 block text-sm font-semibold text-slate-700">Initial password</label>
      <input id="admin_password" name="password" type="password" required minlength="12" maxlength="1024" autocomplete="new-password" class="mb-4 w-full rounded-xl border-slate-300 text-sm">
      <label for="admin_role" class="mb-1 block text-sm font-semibold text-slate-700">Role</label>
      <select id="admin_role" name="role" required class="mb-5 w-full rounded-xl border-slate-300 text-sm">
        <?php foreach (['admin', 'editor', 'viewer', 'super_admin'] as $role) : ?><option value="<?= $e($role) ?>"><?= $e(str_replace('_', ' ', ucfirst($role))) ?></option><?php endforeach; ?>
      </select>
      <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700" type="submit">Create admin</button>
    </form>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 font-bold text-slate-900">Existing accounts</h2>
      <?php if (!($isDatabase ?? true)) : ?>
      <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
        <div class="flex items-center gap-2 font-bold text-amber-950">
          <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>
          Database Inactive (Running in Fallback Mode)
        </div>
        <p class="mt-1.5 leading-relaxed text-amber-800">
          Managing database admin accounts and granular roles requires an active MySQL connection. You are currently logged in via environment fallback mode.
          To activate database accounts and role permissions, verify your MySQL connection settings in <code>.env</code> (e.g. <code>DB_HOST=localhost</code>, <code>DB_NAME=...</code>, <code>DB_USER=...</code>, <code>DB_PASSWORD=...</code> or <code>DB_DSN=...</code>).
        </p>
      </div>
      <?php endif; ?>
      <?php if ($users === []) : ?><p class="text-sm text-slate-500">No database admin accounts are configured.</p><?php else : ?>
      <div class="overflow-x-auto" tabindex="0" aria-label="Scrollable administrator accounts"><table class="min-w-[680px] w-full text-left text-sm"><thead class="text-xs uppercase tracking-wider text-slate-500"><tr><th class="py-2 pr-4">Username</th><th class="py-2 pr-4">Role / access</th><th class="py-2">Save</th></tr></thead>
        <tbody class="divide-y divide-slate-100"><?php foreach ($users as $user) : ?><tr>
          <td class="py-3 pr-4 font-semibold text-slate-800"><?= $e($user['username']) ?></td>
          <td class="py-3 pr-4"><form id="user_<?= $e($user['id']) ?>" method="post" action="/admin/users/<?= $e($user['id']) ?>" class="space-y-1.5"><input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
            <div class="flex items-center gap-2">
              <label class="sr-only" for="role_<?= $e($user['id']) ?>">Role for <?= $e($user['username']) ?></label><select id="role_<?= $e($user['id']) ?>" name="role" class="rounded-xl border-slate-300 text-xs py-1.5 px-2">
                <?php foreach (['super_admin', 'admin', 'editor', 'viewer'] as $role) : ?><option value="<?= $e($role) ?>" <?= ($user['role_key'] ?? '') === $role ? 'selected' : '' ?>><?= $e(str_replace('_', ' ', ucfirst($role))) ?></option><?php endforeach; ?>
              </select>
              <label class="ml-1 inline-flex items-center gap-1 text-xs text-slate-600"><input type="checkbox" name="active" value="1" <?= (int) $user['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
            </div>
            <div>
              <input type="password" name="password" minlength="12" maxlength="1024" autocomplete="new-password" placeholder="New password (leave blank to keep current)" class="w-full max-w-xs rounded-lg border-slate-200 text-xs py-1 px-2.5 placeholder:text-slate-400 focus:border-sky-500">
            </div>
          </form></td>
          <td class="py-3 align-top"><button form="user_<?= $e($user['id']) ?>" class="rounded-full border border-slate-300 px-3 py-1.5 text-xs font-bold uppercase text-slate-800 hover:bg-slate-100 transition" type="submit">Update</button></td>
        </tr><?php endforeach; ?></tbody>
      </table></div>
      <?php endif; ?>
    </section>
  </div>
</section>
