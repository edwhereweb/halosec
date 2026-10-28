<?php
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$describe = static function (array $entry): array {
    $details = json_decode((string) ($entry['details'] ?? ''), true);
    $details = is_array($details) ? $details : [];
    $target = match ((string) $entry['entity_type']) {
        'form_submission' => ['request #' . $entry['entity_id'], '/admin/submissions/' . $entry['entity_id']],
        'admin_user' => [isset($details['username']) ? (string) $details['username'] : 'team member #' . $entry['entity_id'], '/admin/users'],
        'content_block' => [isset($details['key']) ? (string) $details['key'] : 'website text', '/admin/settings'],
        'site_setting' => [isset($details['key']) ? (string) $details['key'] : 'a setting', '/admin/settings'],
        default => [(string) $entry['entity_type'], null],
    };
    $extra = match ((string) $entry['action']) {
        'submission.status_changed' => isset($details['status']) ? '→ ' . Ui::statusLabel((string) $details['status']) : '',
        'admin_user.created', 'admin_user.updated' => isset($details['role']) ? 'as ' . Ui::roleLabel((string) $details['role']) . (array_key_exists('active', $details) && !$details['active'] ? ', deactivated' : '') : '',
        default => '',
    };

    return [Ui::actionLabel((string) $entry['action']), $target[0], $target[1], $extra];
};
?>
<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
  <div>
    <h1 class="text-[28px] font-bold tracking-tight text-slate-900">Audit log</h1>
    <p class="mt-1 text-[15px] text-slate-600">Every change made in the admin area: who did what, and when.</p>
  </div>

  <?php if (!($isDatabase ?? true)) : ?>
  <p role="status" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">The audit log needs the database. Check the DB settings in <code>.env</code>.<?php if (!empty($dbError)) : ?> <span class="font-mono text-xs"><?= $e($dbError) ?></span><?php endif; ?></p>
  <?php endif; ?>

  <?php if ($entries === []) : ?>
  <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-600">No changes recorded yet.</p>
  <?php else : ?>
  <ol class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200 bg-white">
    <?php foreach ($entries as $entry) : ?>
    <?php [$action, $target, $href, $extra] = $describe($entry); $actor = (string) ($entry['actor_username'] ?? '') !== '' ? (string) $entry['actor_username'] : ($entry['actor_user_id'] === null ? 'System' : 'Former member'); ?>
    <li class="flex flex-wrap items-center gap-x-3 gap-y-1 px-5 py-3 text-sm">
      <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold uppercase text-slate-600" aria-hidden="true"><?= $e(mb_substr($actor, 0, 1)) ?></span>
      <span class="min-w-0 flex-1 text-slate-700">
        <strong class="font-semibold text-slate-900"><?= $e($actor) ?></strong> <?= $e($action) ?>
        <?php if ($href !== null) : ?><a class="font-semibold text-sky-800" href="<?= $e($href) ?>"><?= $e($target) ?></a><?php else : ?><span class="font-semibold"><?= $e($target) ?></span><?php endif; ?>
        <?php if ($extra !== '') : ?><span class="text-slate-500"><?= $e($extra) ?></span><?php endif; ?>
      </span>
      <time class="shrink-0 text-xs text-slate-500" datetime="<?= $e($entry['created_at']) ?>" title="<?= $e(Ui::dateTime((string) $entry['created_at'])) ?>"><?= $e(Ui::shortTime((string) $entry['created_at'])) ?> · <?= $e(Ui::ago((string) $entry['created_at'])) ?></time>
    </li>
    <?php endforeach; ?>
  </ol>
  <p class="text-xs text-slate-500">Showing the latest <?= $e(count($entries)) ?> changes.</p>
  <?php endif; ?>
</div>
