<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Defensive & Offensive Capabilities', 'heading' => 'Cybersecurity Services', 'lead' => 'Eight comprehensive cybersecurity disciplines, each backed by certified practitioners and rapid deployment playbooks.']) ?>
<section class="max-w-7xl mx-auto px-4 py-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
  <?php foreach ($services as $slug => $s) : ?>
  <article class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
    <span class="inline-flex w-12 h-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700 text-xs font-bold shadow-sm"><?= $e($s['icon'] ?? 'SEC') ?></span>
    <h2 class="mt-4 text-lg font-bold text-slate-900 leading-snug"><?= $e($s['title']) ?></h2>
    <p class="mt-2 text-slate-600 text-sm flex-1 leading-relaxed"><?= $e($s['short']) ?></p>
    <a class="mt-6 self-start bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs uppercase px-5 py-2.5 rounded-xl shadow-sm transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-1.5" href="/services/<?= $e($slug) ?>">
      <span>View &amp; Enquire</span>
      <span>→</span>
    </a>
  </article>
  <?php endforeach; ?>
</section>
