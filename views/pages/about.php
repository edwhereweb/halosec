<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="relative overflow-hidden px-4 pb-20 pt-20 text-center md:pt-28">
  <?= $this->include('partials/halo', ['size' => 1000, 'class' => '-top-[300px]']) ?>
  <div class="relative mx-auto flex max-w-4xl animate-rise flex-col items-center gap-6">
    <span class="chip chip-mist">About HaloSec</span>
    <h1 class="display text-[56px] md:text-8xl"><span class="sr-only">About HaloSec: </span>We are <span class="text-brand-blue">Secur</span><span class="marker">ious</span>.</h1>
    <p class="lead max-w-2xl"><strong class="text-brand-ink">Curious about security</strong>, so you can fearlessly scale your business.</p>
  </div>
</section>

<section id="founders-note" class="section bg-brand-mist">
  <div class="wrap flex flex-wrap gap-14">
    <div class="flex flex-[1_1_320px] flex-col gap-4">
      <span class="eyebrow">Founders note</span>
      <h2 class="h-section">Our foundation: the aura of security.</h2>
    </div>
    <div class="flex flex-[2_1_480px] flex-col gap-6 text-lg leading-relaxed text-brand-body">
      <p class="text-2xl font-semibold leading-snug text-brand-ink">HaloSec was founded on a simple belief: every business, large or small, deserves security that surrounds it quietly and constantly, like a halo, without slowing people down.</p>
      <p><strong class="text-brand-ink">Our vision</strong> is a world where growing organisations can innovate confidently because their people, data and infrastructure are protected by honest, expert and accessible security.</p>
      <p>Today that means <a class="link" href="/services/endpoint-security">endpoint security</a>, <a class="link" href="/services/security-testing">penetration testing</a>, <a class="link" href="/services/soc-setup">SOC setup</a> and <a class="link" href="/services/security-team-outsourcing">part-time security experts</a> sized for growing businesses. <a class="link" href="/services">See all our cybersecurity services</a>.</p>
      <p class="font-semibold text-brand-ink">— The HaloSec Founders</p>
    </div>
  </div>
</section>

<section aria-labelledby="values" class="wrap section flex flex-col gap-12">
  <h2 id="values" class="h-section">What we hold ourselves to.</h2>
  <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ([
        ['Curiosity', 'We are Securious. We keep asking how things can break so yours don’t.', 'tile-ink'],
        ['Integrity', 'Plain-language findings, honest advice, no fear-selling.', 'tile-sky'],
        ['Partnership', 'An extension of your team, from audit to incident response.', 'tile-white border border-slate-200'],
        ['Accessibility', 'Enterprise-grade protection that fits SMB budgets, including part-time security team members.', 'tile-lime'],
    ] as [$t, $d, $tile]) : ?>
    <li class="tile tile-hover min-h-[220px] <?= $tile ?>">
      <h3 class="font-display text-[28px] font-bold tracking-[-0.03em] <?= $tile === 'tile-ink' ? 'text-brand-neon' : '' ?>"><?= $e($t) ?></h3>
      <p class="text-[15px] leading-relaxed <?= $tile === 'tile-ink' ? 'text-slate-300' : 'text-brand-body' ?>"><?= $e($d) ?></p>
    </li>
    <?php endforeach; ?>
  </ul>
</section>

<section aria-label="Accredited partnerships" class="section bg-brand-mist text-center">
  <div class="wrap flex flex-col items-center gap-8">
    <h2 class="font-display text-3xl font-bold tracking-[-0.03em] md:text-4xl">Accredited partnerships</h2>
    <?= $this->include('partials/partners', ['variant' => 'cards']) ?>
  </div>
</section>

<?= $this->include('partials/cta_band', ['heading' => 'Let’s talk about your security.', 'lead' => 'Start with a free audit, or send us a message.', 'btnLabel' => 'Get a free audit']) ?>
