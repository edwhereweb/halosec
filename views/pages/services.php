<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'What we do', 'heading' => 'Cybersecurity services', 'lead' => 'Eight focused services, each with a dedicated team and enquiry form.']) ?>
<section class="max-w-7xl mx-auto px-4 py-16 grid gap-6 md:grid-cols-2">
<?php foreach ($services as $slug => $s) : ?>
<article class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
<span class="inline-flex w-11 h-11 items-center justify-center rounded-xl bg-sky-100 text-sky-700 text-[10px] font-bold"><?= $e($s['icon']) ?></span>
<h2 class="mt-4 text-xl font-bold text-slate-900"><?= $e($s['title']) ?></h2>
<p class="mt-2 text-slate-600 text-sm"><?= $e($s['short']) ?></p>
<a class="mt-5 inline-block bg-slate-900 text-white font-bold text-xs uppercase px-5 py-2.5 rounded-full" href="/services/<?= $e($slug) ?>">View service &amp; enquire →</a>
</article>
<?php endforeach; ?>
</section>
