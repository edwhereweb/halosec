<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="relative pt-44 pb-16 md:pt-52 md:pb-20 rounded-b-[48px] overflow-hidden shadow-2xl bg-gradient-to-b from-slate-950 via-[#071739] to-[#040d21] border-b border-white/10 text-center">
  <!-- Ambient Cyber Aura Background Elements -->
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-sky-500/15 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[350px] h-[200px] bg-lime-400/10 rounded-full blur-2xl"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] rounded-full border border-sky-400/10"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[750px] h-[750px] rounded-full border border-sky-400/5"></div>
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:24px_24px]"></div>
  </div>

  <div class="max-w-4xl mx-auto px-4 relative z-10">
    <?php if (!empty($eyebrow)) : ?>
    <div class="inline-flex items-center gap-2 bg-slate-900/80 backdrop-blur-xl border border-white/15 px-4 py-1.5 rounded-full mb-5 text-white text-xs font-semibold shadow-lg shadow-black/40">
      <span class="relative flex h-2 w-2 mr-1">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
      </span>
      <?= $e($eyebrow) ?>
    </div>
    <?php endif; ?>
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight font-sans drop-shadow-sm">
      <?= $e($heading) ?>
    </h1>
    <?php if (!empty($lead)) : ?>
    <p class="mt-4 text-base md:text-lg text-slate-300/95 font-medium max-w-2xl mx-auto leading-relaxed">
      <?= $e($lead) ?>
    </p>
    <?php endif; ?>
  </div>
</section>
