<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Get in touch', 'heading' => 'Contact HaloSec', 'lead' => 'Questions, partnerships or general enquiries — we would love to hear from you.']) ?>
<section class="max-w-3xl mx-auto px-4 py-16">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-xl p-8 md:p-10">
    <div class="mb-6 pb-4 border-b border-slate-100">
      <span class="text-[11px] font-bold uppercase tracking-widest text-sky-700">Direct Inquiries</span>
      <h2 class="text-2xl font-extrabold text-slate-900 mt-1">Send us a message</h2>
      <p class="text-sm text-slate-600 mt-1">Our cybersecurity advisory team will review your message and reply promptly.</p>
    </div>

    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <div class="space-y-5">
      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'showCompany' => false]) ?>
      <?= $this->include('partials/field', ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
      <div class="pt-2">
        <?= $this->include('partials/submit', ['btnLabel' => 'Send message']) ?>
      </div>
    </div>
  </div>

  <div class="mt-8 grid sm:grid-cols-2 gap-4">
    <div class="flex items-center gap-3 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center text-sky-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
      </div>
      <div>
        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Email Us</p>
        <a class="text-sm font-bold text-slate-900 hover:text-sky-700 transition-colors" href="mailto:<?= $e($config['contact_email']) ?>"><?= $e($config['contact_email']) ?></a>
      </div>
    </div>

    <div class="flex items-center gap-3 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <div class="w-10 h-10 rounded-xl bg-lime-100 flex items-center justify-center text-lime-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
      </div>
      <div>
        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Emergency Hotline</p>
        <a class="text-sm font-bold text-slate-900 hover:text-sky-700 transition-colors" href="tel:<?= $e($config['emergency_phone'] ?? '+917356543520') ?>"><?= $e($config['emergency_phone'] ?? '+917356543520') ?></a>
      </div>
    </div>
  </div>
</section>
