<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="max-w-5xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <a class="text-sm font-bold text-sky-700 hover:underline" href="/admin/submissions">← All frontend data</a>
  <div class="mt-4 grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(260px,1fr)]">
    <article class="rounded-3xl border border-slate-200 bg-white p-7 shadow-lg">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div><span class="text-xs font-bold uppercase tracking-widest text-sky-700"><?= $e($submission['form_key']) ?></span><h1 class="mt-1 text-2xl font-extrabold text-slate-900">Submission #<?= $e($submission['submission_id']) ?></h1></div>
        <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold capitalize text-sky-800"><?= $e(str_replace('_', ' ', $submission['status'])) ?></span>
      </div>
      <p class="mt-2 text-sm text-slate-500">Received <?= $e($submission['created_at']) ?> UTC</p>
      <dl class="mt-7 space-y-5">
        <?php foreach ($submission['data'] as $key => $value) : ?>
        <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-500"><?= $e(str_replace('_', ' ', (string) $key)) ?></dt><dd class="mt-1 break-words whitespace-pre-wrap text-sm text-slate-900"><?= $e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></dd></div>
        <?php endforeach; ?>
      </dl>
    </article>
    <aside class="space-y-5">
      <?php if ($canManage) : ?><form method="post" action="/admin/submissions/<?= $e($submission['submission_id']) ?>/status" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
        <label for="submission_status" class="mb-2 block text-sm font-bold text-slate-900">Update status</label>
        <select id="submission_status" name="status" class="mb-3 w-full rounded-xl border-slate-300 text-sm">
          <?php foreach (['new', 'in_review', 'resolved', 'spam', 'archived'] as $status) : ?>
          <option value="<?= $e($status) ?>" <?= $submission['status'] === $status ? 'selected' : '' ?>><?= $e(str_replace('_', ' ', ucfirst($status))) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="w-full rounded-full bg-slate-900 px-4 py-2.5 text-xs font-bold uppercase text-white" type="submit">Save status</button>
      </form>
      <?php endif; ?>
      <?php if ($canAssign) : ?><form method="post" action="/admin/submissions/<?= $e($submission['submission_id']) ?>/assignment" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
        <label for="assignee" class="mb-2 block text-sm font-bold text-slate-900">Assigned admin</label>
        <select id="assignee" name="assigned_admin_user_id" class="mb-3 w-full rounded-xl border-slate-300 text-sm">
          <option value="">Unassigned</option><?php foreach ($users as $user) : if ((int) $user['is_active'] === 1) : ?>
          <option value="<?= $e($user['id']) ?>" <?= (int) $submission['assigned_admin_user_id'] === (int) $user['id'] ? 'selected' : '' ?>><?= $e($user['username']) ?></option>
          <?php endif; endforeach; ?>
        </select>
        <button class="w-full rounded-full border border-slate-300 px-4 py-2.5 text-xs font-bold uppercase text-slate-800" type="submit">Update assignment</button>
      </form>
      <?php endif; ?>
      <?php if ($canManage) : ?><form method="post" action="/admin/submissions/<?= $e($submission['submission_id']) ?>/notes" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
        <label for="internal_note" class="mb-2 block text-sm font-bold text-slate-900">Internal note</label>
        <textarea id="internal_note" name="note" rows="3" maxlength="4000" required class="mb-3 w-full rounded-xl border-slate-300 text-sm"></textarea>
        <button class="w-full rounded-full bg-[#e4fc68] px-4 py-2.5 text-xs font-bold uppercase text-slate-900" type="submit">Add note</button>
      </form>
      <?php endif; ?>
      <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900">Internal history</h2>
        <?php if ($notes === []) : ?><p class="mt-3 text-sm text-slate-500">No notes yet.</p><?php else : ?>
        <ol class="mt-3 space-y-3"><?php foreach ($notes as $note) : ?><li class="border-t border-slate-100 pt-3"><p class="whitespace-pre-wrap break-words text-sm text-slate-800"><?= $e($note['note']) ?></p><time class="mt-1 block text-xs text-slate-500"><?= $e($note['created_at']) ?></time></li><?php endforeach; ?></ol>
        <?php endif; ?>
      </section>
    </aside>
  </div>
</section>
