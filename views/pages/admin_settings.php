<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="max-w-4xl mx-auto px-4 pt-28 pb-16">
  <?= $this->include('pages/admin_nav') ?>
  <h1 class="mb-2 text-3xl font-extrabold text-slate-900">Site settings</h1>
  <p class="mb-6 text-sm text-slate-600">Store non-secret operational settings in the database. Never enter credentials here.</p>
  <?php if ($message !== null) : ?><p role="status" class="mb-4 rounded-xl bg-sky-50 p-3 text-sm text-sky-900"><?= $e($message) ?></p><?php endif; ?>
  <div class="grid gap-6 md:grid-cols-[minmax(0,1fr)_minmax(280px,1fr)]">
    <form method="post" action="/admin/settings" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
      <h2 class="mb-4 font-bold text-slate-900">Add or update a setting</h2>
      <label for="setting_key" class="mb-1 block text-sm font-semibold text-slate-700">Key</label>
      <input id="setting_key" name="key" required maxlength="100" pattern="[A-Za-z0-9_.-]+" class="mb-4 w-full rounded-xl border-slate-300 text-sm" placeholder="site.tagline">
      <label for="setting_value" class="mb-1 block text-sm font-semibold text-slate-700">Value</label>
      <textarea id="setting_value" name="value" required maxlength="4000" rows="4" class="mb-4 w-full rounded-xl border-slate-300 text-sm"></textarea>
      <button class="rounded-full bg-[#e4fc68] px-5 py-3 text-xs font-bold uppercase text-slate-900" type="submit">Save setting</button>
    </form>
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 font-bold text-slate-900">Current settings</h2>
      <?php if ($settings === []) : ?><p class="text-sm text-slate-500">No DB-backed settings have been added.</p><?php else : ?>
      <dl class="space-y-4"><?php foreach ($settings as $key => $value) : ?><div class="border-b border-slate-100 pb-3"><dt class="text-xs font-bold uppercase tracking-wider text-slate-500"><?= $e($key) ?></dt><dd class="mt-1 break-words text-sm text-slate-900"><?= $e($value) ?></dd></div><?php endforeach; ?></dl>
      <?php endif; ?>
    </section>
  </div>
  <section class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="mb-1 text-xl font-extrabold text-slate-900">Content blocks</h2>
    <p class="mb-5 text-sm text-slate-600">Use plain text only. HTML is escaped when displayed; never paste executable markup.</p>
    <form method="post" action="/admin/content-blocks" class="mb-6 grid gap-3 md:grid-cols-[1fr_2fr_auto]">
      <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
      <label class="sr-only" for="block_key">Block key</label><input id="block_key" name="key" required maxlength="100" pattern="[A-Za-z0-9_.-]+" placeholder="home.hero_title" class="rounded-xl border-slate-300 text-sm">
      <label class="sr-only" for="block_content">Block text</label><input id="block_content" name="content" required maxlength="4000" placeholder="Plain text content" class="rounded-xl border-slate-300 text-sm">
      <input type="hidden" name="active" value="1"><button class="rounded-full bg-slate-900 px-4 py-2 text-xs font-bold uppercase text-white" type="submit">Save block</button>
    </form>
    <?php if ($blocks === []) : ?><p class="text-sm text-slate-500">No content blocks have been added.</p><?php else : ?>
    <div class="space-y-4"><?php foreach ($blocks as $block) : ?>
      <form method="post" action="/admin/content-blocks" class="grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-[1fr_2fr_auto_auto]">
        <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
        <label class="sr-only" for="block_<?= $e($block['block_key']) ?>_key">Block key</label><input id="block_<?= $e($block['block_key']) ?>_key" name="key" value="<?= $e($block['block_key']) ?>" readonly class="rounded-xl border-slate-300 bg-slate-50 text-sm">
        <label class="sr-only" for="block_<?= $e($block['block_key']) ?>_content">Block text</label><input id="block_<?= $e($block['block_key']) ?>_content" name="content" value="<?= $e($block['content']) ?>" required maxlength="4000" class="rounded-xl border-slate-300 text-sm">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="active" value="1" <?= (int) $block['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
        <button class="rounded-full border border-slate-300 px-4 py-2 text-xs font-bold uppercase text-slate-800" type="submit">Update</button>
      </form>
    <?php endforeach; ?></div>
    <?php endif; ?>
  </section>
</section>
