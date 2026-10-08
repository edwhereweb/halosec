<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="relative pt-44 pb-20 md:pt-52 md:pb-24 rounded-b-[48px] overflow-hidden shadow-2xl bg-gradient-to-b from-red-950 via-[#3a060d] to-[#1c0205] border-b border-red-500/20 text-center">
  <!-- Ambient Emergency Radar Background Elements -->
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[450px] bg-red-600/15 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[620px] h-[620px] rounded-full border border-red-500/20 animate-pulse"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[880px] h-[880px] rounded-full border border-red-500/10"></div>
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#f87171_1px,transparent_1px)] [background-size:24px_24px]"></div>
  </div>

  <div class="max-w-4xl mx-auto px-4 relative z-10">
    <div class="inline-flex items-center gap-2 bg-red-950/80 backdrop-blur-xl border border-red-400/30 px-4 py-1.5 rounded-full mb-6 text-red-200 text-xs font-semibold shadow-lg shadow-black/40">
      <span class="relative flex h-2 w-2 mr-1">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-90"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
      </span>
      Emergency Incident Response · 24/7 Priority Hotline
    </div>

    <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-white leading-tight font-sans drop-shadow-sm">
      Under a Cyber Attack?
    </h1>
    <p class="mt-4 text-base md:text-lg text-red-100/90 font-medium max-w-2xl mx-auto leading-relaxed">
      Disconnect affected machines immediately. Do not pay ransoms or reply to attackers. Contact our rapid triage and isolation dispatch unit now.
    </p>

    <div class="mt-8">
      <a class="inline-flex items-center gap-3 bg-red-600 hover:bg-red-700 text-white font-extrabold text-base md:text-lg px-8 py-4 rounded-xl shadow-xl shadow-red-950/50 transition-all hover:scale-105 active:scale-95 border border-red-400/40" href="tel:<?= $e($config['emergency_phone'] ?? '+917356543520') ?>">
        <span class="relative flex h-3 w-3">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-90"></span>
          <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
        </span>
        <span>Call Emergency Line: <?= $e($config['emergency_phone'] ?? '+917356543520') ?></span>
      </a>
    </div>
  </div>
</section>

<section class="max-w-3xl mx-auto px-4 py-16">
  <div class="rounded-3xl bg-white border border-red-200 shadow-2xl p-8 md:p-10 ring-4 ring-red-50">
    <div class="mb-6 pb-4 border-b border-slate-100">
      <span class="text-[11px] font-bold uppercase tracking-widest text-red-600">Immediate Triage</span>
      <h2 class="text-2xl font-extrabold text-slate-900 mt-1">Report the incident online</h2>
      <p class="text-sm text-slate-600 mt-1">If you are unable to call immediately, provide the incident details below. Our on-duty security incident command will be alerted immediately.</p>
    </div>

    <div class="mt-5">
      <?= $this->include('partials/flash', ['flash' => $flash]) ?>
      <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'phoneRequired' => true]) ?>
      <?= $this->include('partials/field', ['name' => 'attack_type', 'label' => 'Type of attack', 'type' => 'select', 'required' => true, 'options' => $config['attack_types'], 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', ['name' => 'message', 'label' => 'What is happening?', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/submit', ['btnLabel' => 'Dispatch Incident Report Now', 'btnClass' => 'bg-red-600 hover:bg-red-700 text-white']) ?>
    </div>
  </div>
</section>
