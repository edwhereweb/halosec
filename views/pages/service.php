<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Specialized Practice', 'heading' => $service['title'], 'lead' => $service['short']]) ?>
<section class="max-w-6xl mx-auto px-4 py-16 grid gap-10 lg:grid-cols-5 items-start">
  <div class="lg:col-span-3 space-y-8">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 md:p-10">
      <h2 class="text-xs font-bold uppercase tracking-widest text-sky-700 mb-2">Practice Overview</h2>
      <p class="text-slate-700 leading-relaxed text-lg font-medium"><?= $e($service['intro']) ?></p>

      <h3 class="mt-8 text-xl font-extrabold text-slate-900">What's included in this engagement</h3>
      <ul class="mt-5 space-y-3">
        <?php foreach ($service['points'] as $point) : ?>
        <li class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/80">
          <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5" aria-hidden="true">✓</span>
          <span class="text-slate-800 text-sm font-medium leading-normal"><?= $e($point) ?></span>
        </li>
        <?php endforeach; ?>
      </ul>

      <div class="mt-8 pt-6 border-t border-slate-100 text-sm text-slate-500 flex flex-wrap gap-4 items-center">
        <span>Need custom scope?</span>
        <a class="text-sky-700 font-bold hover:underline" href="/consultation">Book a Consultation →</a>
        <span>·</span>
        <a class="text-slate-700 font-bold hover:underline" href="/services">Browse All Services →</a>
      </div>
    </div>
  </div>

  <div class="lg:col-span-2 rounded-3xl bg-white border border-slate-200 shadow-xl p-8" id="enquiry">
    <div class="mb-5 pb-3 border-b border-slate-100">
      <span class="text-[11px] font-bold uppercase tracking-widest text-sky-700">Direct Enquiry</span>
      <h2 class="text-xl font-extrabold text-slate-900 mt-0.5">Enquire about this service</h2>
      <p class="text-xs text-slate-600 mt-1">Our <?= $e($service['title']) ?> specialists will respond within 24 hours.</p>
    </div>

    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <div class="space-y-5">
      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', ['name' => 'message', 'label' => 'How can we help?', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
      <div class="pt-2">
        <?= $this->include('partials/submit', ['btnLabel' => 'Send Service Enquiry']) ?>
      </div>
    </div>
  </div>
</section>
