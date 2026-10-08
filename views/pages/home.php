<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="sky-hero-backdrop pt-32 pb-40 md:pt-44 md:pb-48 rounded-b-[48px] shadow-2xl">
<div class="cloud-layer"></div>
<div class="max-w-5xl mx-auto px-4 text-center relative z-10">
<p class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md border border-white/40 px-3.5 py-1.5 rounded-full mb-6 text-white text-xs font-medium">
<span class="w-2 h-2 rounded-full bg-emerald-400"></span> <?= $e($config['tagline']) ?>
</p>
<h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold text-white tracking-tight leading-[1.05] drop-shadow-sm">We are Securious</h1>
<p class="mt-5 font-display text-xl md:text-2xl tracking-[0.35em] text-lime-200" aria-label="Securious">S E C U R I O U S</p>
<p class="mt-5 text-base md:text-lg text-sky-50 max-w-2xl mx-auto leading-relaxed">We are curious about security — so you can focus on your business. HaloSec protects growing organisations with endpoint, network, SOC and testing services.</p>
<div class="mt-9 flex flex-wrap items-center justify-center gap-3.5">
<a class="bg-[#e4fc68] hover:bg-[#c9ef30] text-slate-900 font-bold text-xs uppercase px-7 py-3.5 rounded-full" href="/free-audit">Get a Free Cybersecurity Audit ↗</a>
<a class="bg-black/25 hover:bg-black/35 text-white border border-white/30 text-xs font-semibold px-6 py-3.5 rounded-full uppercase tracking-wider" href="/consultation">Need a Consultation</a>
<a class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase px-6 py-3.5 rounded-full" href="/under-attack">Under a Cyber Attack</a>
</div>
</div>
</section>

<section aria-label="Accreditations" class="border-b border-slate-100 py-10 bg-white">
<div class="max-w-5xl mx-auto px-4">
<p class="text-center text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-6">Accredited partnerships</p>
<div class="grid sm:grid-cols-2 gap-5">
<div class="flex items-center justify-center gap-4 rounded-2xl border border-slate-200 p-6"><span class="font-display text-2xl font-bold text-red-600">Zoho</span><span class="font-bold text-slate-900">Zoho Partners</span></div>
<div class="flex items-center justify-center gap-4 rounded-2xl border border-slate-200 p-6"><span class="font-display text-2xl font-bold text-sky-700">Sophos</span><span class="font-bold text-slate-900">Sophos Silver Partners</span></div>
</div>
</div>
</section>

<section class="max-w-7xl mx-auto px-4 -mt-0 py-16" aria-label="Get started">
<div class="grid gap-6 lg:grid-cols-3">
<article class="rounded-3xl bg-gradient-to-br from-lime-100 to-white border border-lime-200 p-8 shadow-lg flex flex-col">
<p class="text-xs font-bold uppercase tracking-widest text-lime-700">Free</p>
<h2 class="mt-2 text-2xl font-extrabold text-slate-900">Get a Free Cybersecurity Audit</h2>
<p class="mt-3 text-slate-600 text-sm flex-1">Answer a short questionnaire on your internal network and infrastructure and get a surface-level view of your security posture.</p>
<a class="mt-6 self-start bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase px-6 py-3 rounded-full" href="/free-audit">Start the audit ↗</a>
</article>
<article class="rounded-3xl bg-sky-50 border border-sky-200 p-8 shadow-lg flex flex-col">
<p class="text-xs font-bold uppercase tracking-widest text-sky-700">Talk to an expert</p>
<h2 class="mt-2 text-2xl font-extrabold text-slate-900">Need a Consultation</h2>
<p class="mt-3 text-slate-600 text-sm flex-1">Book time with HaloSec specialists to shape your security roadmap, tooling and compliance plan.</p>
<a class="mt-6 self-start bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs uppercase px-6 py-3 rounded-full" href="/consultation">Book a consultation ↗</a>
</article>
<article class="rounded-3xl bg-red-600 text-white p-8 shadow-lg flex flex-col ring-4 ring-red-200">
<p class="text-xs font-bold uppercase tracking-widest text-red-100">Emergency · 24/7</p>
<h2 class="mt-2 text-2xl font-extrabold">Under a Cyber Attack</h2>
<p class="mt-3 text-red-50 text-sm flex-1">Ransomware, breach or compromised accounts? Call now or report the incident — we respond immediately.</p>
<div class="mt-6 flex flex-wrap gap-3">
<a class="bg-white text-red-700 font-bold text-xs uppercase px-6 py-3 rounded-full" href="tel:<?= $e($config['emergency_phone']) ?>">Call <?= $e($config['emergency_phone']) ?></a>
<a class="border border-white/60 font-bold text-xs uppercase px-6 py-3 rounded-full" href="/under-attack">Report an attack</a>
</div>
</article>
</div>
</section>

<section id="services" class="max-w-7xl mx-auto px-4 py-10">
<h2 class="text-3xl font-extrabold text-slate-900">Core services</h2>
<p class="mt-2 text-slate-600 max-w-2xl">Every service has its own page and an enquiry form so you can talk directly to the right team.</p>
<div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
<?php foreach ($services as $slug => $s) : ?>
<a class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition" href="/services/<?= $e($slug) ?>">
<span class="inline-flex w-10 h-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 text-[10px] font-bold"><?= $e($s['icon']) ?></span>
<h3 class="mt-4 font-bold text-slate-900 leading-snug"><?= $e($s['title']) ?></h3>
<p class="mt-2 text-sm text-slate-600"><?= $e($s['short']) ?></p>
<span class="mt-4 inline-block text-xs font-bold text-sky-700 group-hover:underline">Learn more &amp; enquire →</span>
</a>
<?php endforeach; ?>
</div>
</section>

<section class="max-w-5xl mx-auto px-4 py-16">
<div class="rounded-3xl bg-slate-900 text-white p-10">
<p class="text-xs font-bold uppercase tracking-widest text-lime-300">Founders note</p>
<h2 class="mt-2 text-2xl md:text-3xl font-extrabold">Security should feel like an aura, not an obstacle.</h2>
<p class="mt-4 text-slate-300">Read the vision and values that shape every HaloSec engagement.</p>
<a class="mt-6 inline-block bg-[#e4fc68] text-slate-900 font-bold text-xs uppercase px-6 py-3 rounded-full" href="/about">Read the founders note →</a>
</div>
</section>
