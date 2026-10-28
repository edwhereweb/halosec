<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$current = [];
foreach ($blocks as $block) {
    $current[(string) $block['block_key']] = $block;
}
// Every piece of website text that can be changed here, with the text the site uses when nothing is set.
$sections = [
    'Home page headline' => [
        'help' => 'The big words at the top of the home page. The middle part gets the lime highlight.',
        'fields' => [
            ['home.hero_headline_1', 'First part', 'Security that'],
            ['home.hero_headline_2', 'Highlighted word', 'surrounds'],
            ['home.hero_headline_3', 'Last part', 'you.'],
            ['home.audit_cta_label', 'Main button text', 'Get a free audit'],
        ],
    ],
    'Track record numbers' => [
        'help' => 'The four numbers shown under the home page headline. Keep labels short.',
        'fields' => [
            ['home.stat_1_value', 'Number 1', '100+'], ['home.stat_1_label', 'Label 1', 'websites secured'],
            ['home.stat_2_value', 'Number 2', '100+'], ['home.stat_2_label', 'Label 2', 'organisations protected from cyber attacks'],
            ['home.stat_3_value', 'Number 3', '100s'], ['home.stat_3_label', 'Label 3', 'of organisations advised, with IT infrastructure upgraded and managed'],
            ['home.stat_4_value', 'Number 4', '50+'], ['home.stat_4_label', 'Label 4', 'codebases we actively manage across websites and applications'],
        ],
    ],
    'Tagline' => [
        'help' => 'The large “We are Securious” line further down the home page.',
        'fields' => [
            ['home.hero_prefix', 'Opening words', 'We are'],
            ['home.hero_title', 'Highlighted word', 'Securious'],
        ],
    ],
];
$known = ['home.emergency_banner_enabled'];
foreach ($sections as $section) {
    foreach ($section['fields'] as $field) {
        $known[] = $field[0];
    }
}
$custom = array_filter($blocks, static fn (array $b): bool => !in_array((string) $b['block_key'], $known, true));
$bannerOn = !isset($current['home.emergency_banner_enabled']) || (int) $current['home.emergency_banner_enabled']['is_active'] !== 1
    || (string) $current['home.emergency_banner_enabled']['content'] !== '0';
$isEdited = static fn (string $key): bool => isset($current[$key]) && (int) $current[$key]['is_active'] === 1;
?>
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
  <div>
    <h1 class="text-[28px] font-bold tracking-tight text-slate-900">Website content</h1>
    <p class="mt-1 text-[15px] text-slate-600">Change text on the public website. Saved changes go live straight away. Plain text only.</p>
  </div>

  <?php if ($message !== null) : ?>
  <p role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">✓ <?= $e($message) ?></p>
  <?php endif; ?>
  <?php if (!($isDatabase ?? true)) : ?>
  <p role="status" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Editing website text needs the database. Check the DB settings in <code>.env</code>.</p>
  <?php endif; ?>

  <section class="flex flex-wrap items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="banner">
    <div class="min-w-0 flex-1">
      <h2 id="banner" class="text-sm font-bold text-slate-900">“SOC monitoring active” status line</h2>
      <p class="text-sm text-slate-500">The small live-status line at the top of every public page.</p>
    </div>
    <form method="post" action="/admin/content-blocks">
      <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
      <input type="hidden" name="key" value="home.emergency_banner_enabled">
      <input type="hidden" name="content" value="<?= $bannerOn ? '0' : '1' ?>">
      <input type="hidden" name="active" value="1">
      <button type="submit" class="inline-flex items-center gap-2 rounded-full border px-1 py-1 pr-3 text-sm font-semibold <?= $bannerOn ? 'border-emerald-300 bg-emerald-50 text-emerald-900' : 'border-slate-200 bg-slate-50 text-slate-600' ?>" aria-pressed="<?= $bannerOn ? 'true' : 'false' ?>">
        <span class="inline-flex h-6 w-10 items-center rounded-full p-0.5 <?= $bannerOn ? 'justify-end bg-emerald-600' : 'justify-start bg-slate-300' ?>"><span class="h-5 w-5 rounded-full bg-white shadow"></span></span>
        <?= $bannerOn ? 'Shown' : 'Hidden' ?>
      </button>
    </form>
  </section>

  <?php foreach ($sections as $heading => $section) : ?>
  <section class="rounded-2xl border border-slate-200 bg-white" aria-label="<?= $e($heading) ?>">
    <div class="border-b border-slate-100 px-5 py-4">
      <h2 class="text-sm font-bold text-slate-900"><?= $e($heading) ?></h2>
      <p class="text-sm text-slate-500"><?= $e($section['help']) ?></p>
    </div>
    <div class="divide-y divide-slate-100">
      <?php foreach ($section['fields'] as [$key, $label, $default]) : ?>
      <?php $value = $isEdited($key) ? (string) $current[$key]['content'] : $default; $fid = 'cb_' . str_replace('.', '_', $key); ?>
      <div class="grid items-center gap-2 px-5 py-3 sm:grid-cols-[180px_minmax(0,1fr)_auto]">
        <label for="<?= $e($fid) ?>" class="text-sm font-semibold text-slate-700"><?= $e($label) ?>
          <?php if ($isEdited($key)) : ?><span class="ml-1 rounded bg-sky-100 px-1.5 py-0.5 text-[11px] font-semibold text-sky-900">Edited</span><?php endif; ?>
        </label>
        <form method="post" action="/admin/content-blocks" class="flex min-w-0 gap-2" id="f_<?= $e($fid) ?>">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <input type="hidden" name="key" value="<?= $e($key) ?>">
          <input type="hidden" name="active" value="1">
          <input id="<?= $e($fid) ?>" name="content" value="<?= $e($value) ?>" required maxlength="300" class="h-9 min-w-0 flex-1 rounded-lg border-slate-200 text-sm">
          <button type="submit" class="rounded-lg bg-slate-900 px-3 text-sm font-semibold text-white hover:bg-slate-700">Save</button>
        </form>
        <?php if ($isEdited($key)) : ?>
        <form method="post" action="/admin/content-blocks">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <input type="hidden" name="key" value="<?= $e($key) ?>">
          <input type="hidden" name="content" value="<?= $e($current[$key]['content']) ?>">
          <button type="submit" title="Go back to: <?= $e($default) ?>" class="text-xs font-semibold text-slate-500 hover:text-slate-900">Reset</button>
        </form>
        <?php else : ?><span class="text-xs text-slate-400">Default</span><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endforeach; ?>

  <details class="rounded-2xl border border-slate-200 bg-white">
    <summary class="cursor-pointer px-5 py-4 text-sm font-bold text-slate-900">Advanced: custom content keys and site settings</summary>
    <div class="flex flex-col gap-6 border-t border-slate-100 p-5">
      <div>
        <h3 class="text-sm font-semibold text-slate-900">Custom content keys</h3>
        <p class="mb-3 text-sm text-slate-500">For developers: any key a template reads from content blocks.</p>
        <form method="post" action="/admin/content-blocks" class="mb-4 grid gap-2 sm:grid-cols-[1fr_2fr_auto]">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>"><input type="hidden" name="active" value="1">
          <label class="sr-only" for="block_key">Key</label><input id="block_key" name="key" required maxlength="100" pattern="[A-Za-z0-9_.-]+" placeholder="page.section_key" class="h-9 rounded-lg border-slate-200 text-sm">
          <label class="sr-only" for="block_content">Text</label><input id="block_content" name="content" required maxlength="4000" placeholder="Text" class="h-9 rounded-lg border-slate-200 text-sm">
          <button class="rounded-lg bg-slate-900 px-3 text-sm font-semibold text-white" type="submit">Save</button>
        </form>
        <?php foreach ($custom as $block) : ?>
        <form method="post" action="/admin/content-blocks" class="grid items-center gap-2 border-t border-slate-100 py-2 sm:grid-cols-[1fr_2fr_auto_auto]">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <input name="key" value="<?= $e($block['block_key']) ?>" readonly aria-label="Key" class="h-9 rounded-lg border-slate-200 bg-slate-50 text-sm">
          <input name="content" value="<?= $e($block['content']) ?>" required maxlength="4000" aria-label="Text for <?= $e($block['block_key']) ?>" class="h-9 rounded-lg border-slate-200 text-sm">
          <label class="flex items-center gap-1.5 text-sm"><input type="checkbox" name="active" value="1" <?= (int) $block['is_active'] === 1 ? 'checked' : '' ?> class="rounded border-slate-300"> Live</label>
          <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700" type="submit">Save</button>
        </form>
        <?php endforeach; ?>
      </div>
      <div>
        <h3 class="text-sm font-semibold text-slate-900">Site settings</h3>
        <p class="mb-3 text-sm text-slate-500">Non-secret settings stored in the database. Never put passwords or keys here.</p>
        <form method="post" action="/admin/settings" class="mb-4 grid gap-2 sm:grid-cols-[1fr_2fr_auto]">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">
          <label class="sr-only" for="setting_key">Key</label><input id="setting_key" name="key" required maxlength="100" pattern="[A-Za-z0-9_.-]+" placeholder="site.setting" class="h-9 rounded-lg border-slate-200 text-sm">
          <label class="sr-only" for="setting_value">Value</label><input id="setting_value" name="value" required maxlength="4000" class="h-9 rounded-lg border-slate-200 text-sm">
          <button class="rounded-lg bg-slate-900 px-3 text-sm font-semibold text-white" type="submit">Save</button>
        </form>
        <?php if ($settings !== []) : ?>
        <dl class="divide-y divide-slate-100 text-sm"><?php foreach ($settings as $key => $value) : ?><div class="grid gap-2 py-2 sm:grid-cols-[1fr_2fr]"><dt class="font-mono text-xs text-slate-500"><?= $e($key) ?></dt><dd class="break-words text-slate-900"><?= $e($value) ?></dd></div><?php endforeach; ?></dl>
        <?php endif; ?>
      </div>
    </div>
  </details>
</div>
