<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Service', 'heading' => $service['title'], 'lead' => $service['short']]) ?>
<section class="max-w-6xl mx-auto px-4 py-16 grid gap-10 lg:grid-cols-5">
<div class="lg:col-span-3">
<p class="text-slate-700 leading-relaxed text-lg"><?= $e($service['intro']) ?></p>
<h2 class="mt-8 text-xl font-bold text-slate-900">What's included</h2>
<ul class="mt-4 space-y-3">
<?php foreach ($service['points'] as $point) : ?>
<li class="flex gap-3"><span class="text-emerald-600 font-bold" aria-hidden="true">✓</span><span><?= $e($point) ?></span></li>
<?php endforeach; ?>
</ul>
<p class="mt-8 text-sm text-slate-500">Prefer to talk first? <a class="text-sky-700 font-semibold underline" href="/consultation">Book a consultation</a> or <a class="text-sky-700 font-semibold underline" href="/services">browse all services</a>.</p>
</div>
<div class="lg:col-span-2 rounded-3xl bg-white border border-slate-200 shadow-lg p-7" id="enquiry">
<h2 class="text-xl font-bold text-slate-900">Enquire about this service</h2>
<div class="mt-5">
<?= $this->include('partials/flash', ['flash' => $flash]) ?>
<?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
<?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'message', 'label' => 'How can we help?', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/submit', ['btnLabel' => 'Send enquiry']) ?>
</div>
</div>
</section>
