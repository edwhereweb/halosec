<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Get in touch', 'heading' => 'Contact HaloSec', 'lead' => 'Questions, partnerships or general enquiries — we would love to hear from you.']) ?>
<section class="max-w-3xl mx-auto px-4 py-16">
<div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-8">
<?= $this->include('partials/flash', ['flash' => $flash]) ?>
<?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
<?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'showCompany' => false]) ?>
<?= $this->include('partials/field', ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/submit', ['btnLabel' => 'Send message']) ?>
</div>
<p class="mt-6 text-sm text-slate-600 text-center">Email: <a class="text-sky-700 underline" href="mailto:<?= $e($config['contact_email']) ?>"><?= $e($config['contact_email']) ?></a></p>
</section>
