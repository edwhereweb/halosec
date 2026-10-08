<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'About HaloSec', 'heading' => 'We are Securious', 'lead' => 'S E C U R I O U S – We are curious about security so you can fearlessly scale your business.']) ?>
<section class="max-w-4xl mx-auto px-4 py-16" id="founders-note">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-xl p-8 md:p-12">
    <div class="flex items-center gap-2 mb-3">
      <span class="w-1.5 h-1.5 rounded-full bg-lime-400"></span>
      <p class="text-xs font-bold uppercase tracking-widest text-sky-700">Founders note</p>
    </div>
    <h2 class="text-3xl font-extrabold text-slate-900">Our foundation: the aura of security</h2>
    <div class="mt-6 space-y-5 text-slate-700 leading-relaxed text-base">
      <p>HaloSec was founded on a simple belief: every business, large or small, deserves security that surrounds it quietly and constantly — like a halo — without slowing people down.</p>
      <p><strong>Our vision</strong> is a world where growing organisations can innovate confidently because their people, data and infrastructure are protected by honest, expert and accessible security.</p>
      <p><strong>Our values</strong> are what we hold ourselves to:</p>
      <ul class="list-disc pl-6 space-y-2">
        <li><strong>Curiosity</strong> — we are Securious. We keep asking how things can break so yours do not.</li>
        <li><strong>Integrity</strong> — plain-language findings, honest advice, no fear-selling.</li>
        <li><strong>Partnership</strong> — we work as an extension of your team, from audit to incident response.</li>
        <li><strong>Accessibility</strong> — enterprise-grade protection that fits SMB budgets, including part-time security team members.</li>
      </ul>
      <p class="font-semibold text-slate-900 pt-2">— The HaloSec Founders</p>
    </div>

    <!-- Accredited Partnerships cards -->
    <div class="mt-10 grid sm:grid-cols-2 gap-5">
      <div class="flex items-center justify-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:border-sky-300 hover:shadow-md transition-all duration-200">
        <span class="font-display text-2xl font-bold text-red-600">Zoho</span>
        <span class="font-bold text-slate-900">Zoho Partners</span>
      </div>
      <div class="flex items-center justify-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:border-sky-300 hover:shadow-md transition-all duration-200">
        <span class="font-display text-2xl font-bold text-sky-700">Sophos</span>
        <span class="font-bold text-slate-900">Sophos Silver Partners</span>
      </div>
    </div>

    <div class="mt-10 flex flex-wrap gap-4">
      <a class="bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-7 py-3.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 border border-lime-200/50 inline-flex items-center gap-2" href="/free-audit">
        <span>Get a Free Audit</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><line x1="7" x2="17" y1="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
      </a>
      <a class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase px-7 py-3.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-2" href="/contact">
        <span>Contact Us</span>
        <span>→</span>
      </a>
    </div>
  </div>
</section>
