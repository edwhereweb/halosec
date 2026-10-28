<?php
/**
 * Compact submission row for dashboard lists. Expects $row (from listSubmissions); optional $showStatus.
 */
use HaloSec\Admin\Ui;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$data = is_array($row['data'] ?? null) ? $row['data'] : [];
$formKey = (string) ($row['form_key'] ?? $row['type'] ?? '');
$status = (string) ($row['status'] ?? 'new');
$href = isset($row['submission_id']) ? '/admin/submissions/' . $row['submission_id'] : '/admin/leads/' . $formKey . '/' . substr((string) $row['id'], strlen($formKey) + 1);
$urgent = Ui::isUrgent($formKey, $status);
?>
<li>
  <a href="<?= $e($href) ?>" class="flex items-start gap-3 rounded-xl px-3 py-2.5 no-underline transition hover:bg-slate-50 <?= $urgent ? 'bg-red-50/60 hover:bg-red-50' : '' ?>">
    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full <?= Ui::formDot($formKey) ?>" aria-hidden="true"></span>
    <span class="min-w-0 flex-1">
      <span class="flex items-baseline gap-2">
        <span class="truncate text-sm font-semibold text-slate-900"><?= $e(Ui::contactName($data)) ?></span>
        <span class="shrink-0 text-xs text-slate-500"><?= $e(Ui::formLabel($formKey)) ?></span>
      </span>
      <?php $preview = Ui::preview($data, 90); ?>
      <?php if ($preview !== '') : ?><span class="block truncate text-[13px] text-slate-500"><?= $e($preview) ?></span><?php endif; ?>
    </span>
    <span class="flex shrink-0 flex-col items-end gap-1">
      <time class="text-xs text-slate-500" datetime="<?= $e($row['created_at']) ?>" title="<?= $e(Ui::dateTime((string) $row['created_at'])) ?>"><?= $e(Ui::ago((string) $row['created_at'])) ?></time>
      <?php if (!empty($showStatus)) : ?><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset <?= Ui::statusClass($status) ?>"><?= $e(Ui::statusLabel($status)) ?></span><?php endif; ?>
    </span>
  </a>
</li>
