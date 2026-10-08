<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Free · No obligation', 'heading' => 'Get a Free Cybersecurity Audit', 'lead' => 'A surface-level diagnostic of your internal network, cloud, and endpoint security posture.']) ?>
<section class="max-w-3xl mx-auto px-4 py-16">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-xl p-8 md:p-10">
    <div class="mb-6 pb-4 border-b border-slate-100">
      <div class="flex items-center gap-2">
        <span class="w-1.5 h-1.5 rounded-full bg-lime-400"></span>
        <span class="text-[11px] font-bold uppercase tracking-widest text-lime-700">100% Free · Zero Obligation</span>
      </div>
      <h2 class="text-2xl font-extrabold text-slate-900 mt-1">Cybersecurity Posture Diagnostic</h2>
      <p class="text-sm text-slate-600 mt-1">Provide your organization profile below to receive an actionable diagnostic report from certified Zoho &amp; Sophos security architects.</p>
    </div>

    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>

    <div class="space-y-6">
      <div class="flex items-center gap-2 pb-1">
        <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 font-bold text-xs flex items-center justify-center">✓</span>
        <h3 class="text-base font-bold text-slate-900">Organization Profile</h3>
      </div>

      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'companyRequired' => true]) ?>

      <div class="grid gap-5 sm:grid-cols-2">
        <?= $this->include('partials/field', ['name' => 'company_website', 'label' => 'Company website', 'old' => $old, 'errors' => $errors]) ?>
        <?= $this->include('partials/field', ['name' => 'industry', 'label' => 'Industry', 'old' => $old, 'errors' => $errors]) ?>
      </div>
      <?= $this->include('partials/field', ['name' => 'employee_scale', 'label' => 'Number of employees', 'type' => 'select', 'required' => true, 'options' => $config['employee_scales'], 'old' => $old, 'errors' => $errors]) ?>

      <?= $this->include('partials/field', ['name' => 'notes', 'label' => 'Anything else we should know? (optional)', 'type' => 'textarea', 'old' => $old, 'errors' => $errors]) ?>

      <div class="pt-2">
        <?= $this->include('partials/submit', ['btnLabel' => 'Submit for My Free Audit']) ?>
      </div>
    </div>
  </div>
</section>
