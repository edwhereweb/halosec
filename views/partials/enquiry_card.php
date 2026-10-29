<?php
/**
 * Service enquiry form card (id="enquiry").
 * Optional: $heading, $sub, $chips (list of labels that prefix the message), $chipsLegend, $chipsPrefix,
 * $messageLabel, $messageHint, $btnLabel, $notes (list of short reassurance lines).
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$chips = $chips ?? [];
$notes = $notes ?? [];
?>
<div id="enquiry" class="card-form">
  <div class="mb-6 flex flex-col gap-2">
    <h2 class="text-[28px] font-extrabold tracking-[-0.02em]"><?= $e($heading ?? 'Get a quote') ?></h2>
    <p class="text-[15px] text-brand-body"><?= $e($sub ?? 'A specialist replies within 24 hours.') ?></p>
  </div>
  <?= $this->include('partials/flash', ['flash' => $flash]) ?>
  <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <?php if ($chips !== []) : ?>
    <div data-prefix-group data-prefix="<?= $e($chipsPrefix ?? 'Scope') ?>">
      <p class="label mb-3" id="enquiry-chips-legend"><?= $e($chipsLegend ?? 'Scope') ?> <span class="font-medium text-brand-muted">Optional</span></p>
      <div class="flex flex-wrap gap-2" role="group" aria-labelledby="enquiry-chips-legend">
        <?php foreach ($chips as $chip) : ?>
        <button type="button" class="opt" aria-pressed="false" data-chip="<?= $e($chip) ?>"><?= $e($chip) ?></button>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>
    <?= $this->include('partials/field', [
        'name' => 'message',
        'label' => $messageLabel ?? 'How can we help?',
        'hint' => $messageHint ?? '',
        'type' => 'textarea',
        'required' => true,
        'old' => $old,
        'errors' => $errors,
    ]) ?>
    <?= $this->include('partials/privacy_notice') ?>
  <?= $this->include('partials/submit', ['btnLabel' => $btnLabel ?? 'Send enquiry']) ?>
  <?php if ($notes !== []) : ?>
  <ul class="mt-6 flex flex-col gap-2 border-t border-slate-100 pt-5 text-[13px] font-medium text-brand-muted">
    <?php foreach ($notes as $note) : ?>
    <li class="flex items-center gap-2"><span aria-hidden="true" class="font-bold text-emerald-700">✓</span><?= $e($note) ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>
