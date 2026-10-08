<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$answers = is_array($old['answers'] ?? null) ? $old['answers'] : [];
?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Free · No obligation', 'heading' => 'Get a Free Cybersecurity Audit', 'lead' => 'A quick surface-level assessment of your internal network and infrastructure security posture.']) ?>
<section class="max-w-3xl mx-auto px-4 py-16">
<div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-8">
<?= $this->include('partials/flash', ['flash' => $flash]) ?>
<?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
<h2 class="text-lg font-bold text-slate-900">1. About your company</h2>
<?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'companyRequired' => true]) ?>
<div class="grid gap-5 sm:grid-cols-2">
<?= $this->include('partials/field', ['name' => 'company_website', 'label' => 'Company website', 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'industry', 'label' => 'Industry', 'old' => $old, 'errors' => $errors]) ?>
</div>
<?= $this->include('partials/field', ['name' => 'employee_scale', 'label' => 'Number of employees', 'type' => 'select', 'required' => true, 'options' => $config['employee_scales'], 'old' => $old, 'errors' => $errors]) ?>

<h2 class="text-lg font-bold text-slate-900 pt-4">2. Internal network &amp; infrastructure</h2>
<?php if (isset($errors['answers'])) : ?><p class="text-sm text-red-600" role="alert"><?= $e($errors['answers']) ?></p><?php endif; ?>
<?php $n = 0; foreach ($audit['questions'] as $id => $question) : $n++; ?>
<fieldset class="rounded-2xl border border-slate-200 p-4">
<legend class="px-2 text-sm font-semibold text-slate-800"><?= $e($n) ?>. <?= $e($question) ?></legend>
<div class="mt-2 flex flex-wrap gap-4 text-sm">
<?php foreach ($audit['options'] as $value => $label) : ?>
<label class="inline-flex items-center gap-2"><input type="radio" class="text-sky-600" name="answers[<?= $e($id) ?>]" value="<?= $e($value) ?>" <?= ($answers[$id] ?? '') === $value ? 'checked' : '' ?>> <?= $e($label) ?></label>
<?php endforeach; ?>
</div>
</fieldset>
<?php endforeach; ?>
<?= $this->include('partials/field', ['name' => 'notes', 'label' => 'Anything else (optional)', 'type' => 'textarea', 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/submit', ['btnLabel' => 'Submit for my free audit', 'btnClass' => 'bg-[#e4fc68] hover:bg-[#c9ef30] text-slate-900']) ?>
</div>
</section>
