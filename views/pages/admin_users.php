<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="max-w-6xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <h1 class="mb-2 text-3xl font-extrabold text-slate-900">Admin users</h1>
  <p class="mb-6 text-sm text-slate-600">Create named accounts with least-privilege roles. Passwords are hashed before storage.</p>
  <?php if ($message !== null) : ?><p role="status" class="mb-4 rounded-xl bg-sky-50 p-3 text-sm text-sky-900"><?= $e($message) ?></p><?php endif; ?>
  <div class="grid gap-6 md:grid-cols-[minmax(0,1fr)_minmax(300px,1.3fr)]">
    <form method="post" action="/admin/users" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
      <label for="admin_username" class="mb-1 block text-sm font-semibold text-slate-700">Username</label>
      <input id="admin_username" name="username" required minlength="3" maxlength="190" pattern="[A-Za-z0-9_.@-]+" autocomplete="username" class="mb-4 w-full rounded-xl border-slate-300 text-sm">
      <label for="admin_password" class="mb-1 block text-sm font-semibold text-slate-700">Initial password</label>
      <input id="admin_password" name="password" type="password" required minlength="12" maxlength="1024" autocomplete="new-password" class="mb-4 w-full rounded-xl border-slate-300 text-sm">
      <label for="admin_role" class="mb-1 block text-sm font-semibold text-slate-700">Role</label>
      <select id="admin_role" name="role" required class="mb-5 w-full rounded-xl border-slate-300 text-sm">
        <?php foreach (['admin', 'editor', 'viewer', 'super_admin'] as $role) : ?><option value="<?= $e($role) ?>"><?= $e(str_replace('_', ' ', ucfirst($role))) ?></option><?php endforeach; ?>
      </select>
      <button class="rounded-full bg-[#e4fc68] px-5 py-3 text-xs font-bold uppercase text-slate-900" type="submit">Create admin</button>
    </form>
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 font-bold text-slate-900">Existing accounts</h2>
      <?php if ($users === []) : ?><p class="text-sm text-slate-500">No database admin accounts are configured.</p><?php else : ?>
      <div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="text-xs uppercase tracking-wider text-slate-500"><tr><th class="py-2 pr-4">Username</th><th class="py-2 pr-4">Role / access</th><th class="py-2">Save</th></tr></thead>
        <tbody class="divide-y divide-slate-100"><?php foreach ($users as $user) : ?><tr>
          <td class="py-3 pr-4"><?= $e($user['username']) ?></td>
          <td class="py-3 pr-4"><form id="user_<?= $e($user['id']) ?>" method="post" action="/admin/users/<?= $e($user['id']) ?>"><input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
            <label class="sr-only" for="role_<?= $e($user['id']) ?>">Role for <?= $e($user['username']) ?></label><select id="role_<?= $e($user['id']) ?>" name="role" class="rounded-xl border-slate-300 text-sm">
              <?php foreach (['super_admin', 'admin', 'editor', 'viewer'] as $role) : ?><option value="<?= $e($role) ?>" <?= ($user['role_key'] ?? '') === $role ? 'selected' : '' ?>><?= $e(str_replace('_', ' ', ucfirst($role))) ?></option><?php endforeach; ?>
            </select>
            <label class="ml-2 inline-flex items-center gap-1 text-xs"><input type="checkbox" name="active" value="1" <?= (int) $user['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
          </form></td>
          <td class="py-3"><button form="user_<?= $e($user['id']) ?>" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-bold uppercase text-slate-800" type="submit">Update</button></td>
        </tr><?php endforeach; ?></tbody>
      </table></div>
      <?php endif; ?>
    </section>
  </div>
</section>
