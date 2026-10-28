<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$contentBlocks = $contentBlocks ?? [];
$phone = (string) ($config['emergency_phone'] ?? '+917356543520');

// Per-service tile styling and icon for the services grid.
$tileStyles = [
    'endpoint-security' => ['tile' => 'tile-ink sm:col-span-2', 'featured' => true],
    'soc-setup' => ['tile' => 'tile-blue'],
    'security-testing' => ['tile' => 'tile-sky'],
    'digital-vulnerability-assessments' => ['tile' => 'tile-lime'],
    'security-team-outsourcing' => ['tile' => 'tile-cta sm:col-span-2 lg:col-span-4', 'banner' => true],
];
$icons = [
    'endpoint-security' => '<rect x="5" y="7" width="22" height="15" rx="2"/><path d="M11 27h10M16 22v5"/>',
    'dlp-implementation' => '<path d="M7 4h13l5 5v19H7V4Z"/><path d="M12 16h8M12 21h6"/>',
    'network-security' => '<rect x="4" y="6" width="24" height="8" rx="2"/><rect x="4" y="18" width="24" height="8" rx="2"/><path d="M9 10h.1M9 22h.1"/>',
    'security-testing' => '<circle cx="14" cy="14" r="8"/><path d="m20 20 7 7"/>',
    'soc-setup' => '<circle cx="16" cy="16" r="12"/><circle cx="16" cy="16" r="5"/><path d="M16 4v6M16 22v6M4 16h6M22 16h6"/>',
    'digital-vulnerability-assessments' => '<path d="M5 26V16M12 26V10M19 26V14M26 26V6"/>',
    'security-audits' => '<circle cx="16" cy="16" r="12"/><path d="M4 16h24M16 4c4 4 4 20 0 24M16 4c-4 4-4 20 0 24"/>',
    'security-team-outsourcing' => '<circle cx="12" cy="11" r="4"/><path d="M4 26c0-4.4 3.6-8 8-8s8 3.6 8 8M21 7a4 4 0 0 1 0 8M24 18c2.4 1.2 4 3.6 4 8"/>',
];

$rawVideo = (string) ($config['founder_video_id'] ?? '');
$videoId = '';
if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $rawVideo, $matches)) {
    $videoId = $matches[1];
} elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $rawVideo)) {
    $videoId = $rawVideo;
}
$chapters = [
    ['time' => '00:07', 'sec' => 7, 'title' => 'Introduction & Real-Life Security Audit Story'],
    ['time' => '02:38', 'sec' => 158, 'title' => 'ബിസിനസുകളിലേക്ക് ഹാക്കർമാർ എങ്ങനെ കയറിക്കൂടുന്നു? (Top Vulnerabilities)'],
    ['time' => '03:07', 'sec' => 187, 'title' => 'Why password strength is important?'],
    ['time' => '04:43', 'sec' => 283, 'title' => 'Software updates and patching'],
    ['time' => '05:50', 'sec' => 350, 'title' => 'Segmentation of network'],
    ['time' => '07:49', 'sec' => 469, 'title' => 'എന്തുകൊണ്ട് ആന്റിവൈറസ് മാത്രം പോരാ? (Endpoint Security)'],
    ['time' => '10:16', 'sec' => 616, 'title' => 'Data Loss Prevention tools'],
    ['time' => '11:57', 'sec' => 717, 'title' => 'ബിസിനസുകൾക്കുള്ള ലളിതമായ പരിഹാരങ്ങൾ & Free Security Audit'],
];
$arrow = '<svg class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" viewBox="0 0 16 16"><path d="M4 12 12 4M6 4h6v6"/></svg>';
?>
<!-- Hero -->
<section class="relative overflow-hidden px-4 pb-20 pt-20 text-center md:pb-24 md:pt-28">
  <?= $this->include('partials/halo', ['size' => 1120, 'class' => '-top-[180px]']) ?>

  <!-- Floating live cards (wide screens only, kept clear of the text) -->
  <div aria-hidden="true" class="absolute left-[max(24px,calc(50%-660px))] top-[400px] hidden w-[230px] animate-float gap-3 rounded-[20px] border border-slate-100 bg-white p-4 text-left shadow-float xl:flex">
    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-50 text-brand-red">
      <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24"><path d="M20 12a8 8 0 1 1-2.4-5.7M20 4v5h-5"/></svg>
    </span>
    <span class="flex flex-col gap-0.5">
      <span class="text-[13px] font-extrabold">Ransomware stopped</span>
      <span class="text-xs leading-snug text-brand-muted">Files rolled back on a finance laptop</span>
      <span class="text-[11px] font-bold text-emerald-700">Just now</span>
    </span>
  </div>
  <div aria-hidden="true" class="absolute right-[max(24px,calc(50%-660px))] top-16 hidden w-[210px] animate-float flex-col gap-3 rounded-[20px] bg-brand-ink p-[18px] text-left text-white shadow-float [animation-delay:-2s] xl:flex">
    <span class="flex items-center gap-2 text-xs font-bold text-slate-400">
      <span class="dot-live"><span class="ping bg-emerald-500"></span><span class="relative h-2 w-2 rounded-full bg-emerald-500"></span></span>
      SOC · Live
    </span>
    <span class="text-base font-extrabold">All endpoints healthy</span>
    <span class="flex h-[34px] items-end gap-1">
      <span class="h-[40%] flex-1 rounded-sm bg-slate-700"></span><span class="h-[65%] flex-1 rounded-sm bg-slate-700"></span><span class="h-1/2 flex-1 rounded-sm bg-slate-700"></span><span class="h-[90%] flex-1 rounded-sm bg-brand-neon"></span><span class="h-[55%] flex-1 rounded-sm bg-slate-700"></span><span class="h-[70%] flex-1 rounded-sm bg-sky-400"></span><span class="h-[45%] flex-1 rounded-sm bg-slate-700"></span>
    </span>
  </div>
  <div aria-hidden="true" class="absolute right-[max(40px,calc(50%-620px))] top-[470px] hidden animate-float items-center gap-2 rounded-full bg-brand-cta px-4 py-3 text-[13px] font-extrabold shadow-float [animation-delay:-4s] xl:flex">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.6" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
    Phishing link blocked
  </div>

  <div class="relative mx-auto flex max-w-4xl animate-rise flex-col items-center gap-7">
    <?php if (($contentBlocks['home.emergency_banner_enabled'] ?? '1') !== '0') : ?>
    <span class="chip chip-mist">
      <span class="dot-live"><span class="ping bg-emerald-500"></span><span class="relative h-2 w-2 rounded-full bg-emerald-500"></span></span>
      SOC monitoring active · 24/7
    </span>
    <?php endif; ?>
    <h1 class="display text-[52px] sm:text-7xl lg:text-8xl">
      <span class="sr-only">HaloSec Cybersecurity: </span>
      <?= $e($contentBlocks['home.hero_headline_1'] ?? 'Security that') ?><br>
      <span class="marker"><?= $e($contentBlocks['home.hero_headline_2'] ?? 'surrounds') ?></span> <?= $e($contentBlocks['home.hero_headline_3'] ?? 'you.') ?>
    </h1>
    <p class="lead max-w-2xl">Endpoint defense, network security, 24/7 SOC and penetration testing for growing businesses. Quietly, constantly, like a halo.</p>
    <div class="flex flex-wrap justify-center gap-3">
      <a class="btn btn-lg btn-cta" href="/free-audit"><?= $e($contentBlocks['home.audit_cta_label'] ?? 'Get a free audit') ?> <?= $arrow ?></a>
      <a class="btn btn-lg btn-outline" href="/consultation">Book a consultation</a>
    </div>
    <div class="mt-2">
      <?= $this->include('partials/partners') ?>
    </div>
  </div>
</section>

<!-- Services marquee -->
<div aria-hidden="true" class="overflow-hidden bg-brand-ink py-6">
  <div class="flex w-max animate-marquee items-center gap-10 whitespace-nowrap font-display text-3xl font-bold tracking-[-0.02em] text-white md:text-4xl">
    <?php for ($loop = 0; $loop < 2; $loop++) : ?>
    <?php $n = 0; foreach ($services as $s) : ?>
    <span><?= $e($s['nav_label'] ?? $s['title']) ?></span>
    <span class="h-3 w-3 rounded-full <?= $n++ % 2 === 0 ? 'bg-brand-neon' : 'bg-sky-400' ?>"></span>
    <?php endforeach; ?>
    <?php endfor; ?>
  </div>
</div>

<!-- Three doors -->
<section aria-labelledby="doors" class="wrap section">
  <h2 id="doors" class="mb-7 text-[15px] font-bold text-brand-muted">Where are you today?</h2>
  <div class="grid gap-5 md:grid-cols-3">
    <a class="tile tile-mist tile-hover group min-h-[260px] gap-4" href="/free-audit">
      <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-cta transition group-hover:-rotate-6">
        <svg class="h-6 w-6" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/><path d="m8.5 12 2.5 2.5 4.5-5"/></svg>
      </span>
      <span class="text-[13px] font-bold text-lime-800">Free · 5 minutes</span>
      <span class="text-[26px] font-extrabold leading-tight tracking-[-0.02em]">I want to know how exposed we are.</span>
      <span class="tile-text">Answer a short questionnaire about your network and get a surface-level view of your security posture.</span>
      <span class="mt-auto text-[15px] font-bold text-brand-blue">Start the free audit →</span>
    </a>
    <a class="tile tile-mist tile-hover group min-h-[260px] gap-4" href="/consultation">
      <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-ink text-white transition group-hover:-rotate-6">
        <svg class="h-6 w-6" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 5h16v11H9l-5 4V5Z"/><path d="M8 10h8M8 13h5"/></svg>
      </span>
      <span class="text-[13px] font-bold text-brand-body">Talk to an expert</span>
      <span class="text-[26px] font-extrabold leading-tight tracking-[-0.02em]">I need a plan, tools or compliance help.</span>
      <span class="tile-text">Book time with HaloSec specialists to shape your security roadmap, tooling and compliance plan.</span>
      <span class="mt-auto text-[15px] font-bold text-brand-blue">Book a consultation →</span>
    </a>
    <a class="tile tile-hover group min-h-[260px] gap-4 bg-brand-red text-white" href="/under-attack">
      <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 transition group-hover:-rotate-6">
        <svg class="h-6 w-6" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 4 2.5 20h19L12 4Z"/><path d="M12 10v4.5M12 17.2v.1"/></svg>
      </span>
      <span class="text-[13px] font-bold text-red-100">Emergency · 24/7</span>
      <span class="text-[26px] font-extrabold leading-tight tracking-[-0.02em]">We’re under attack right now.</span>
      <span class="text-[15px] leading-relaxed text-red-50">Ransomware, breach or compromised accounts? Call <?= $e($phone) ?> or report the incident and we respond immediately.</span>
      <span class="mt-auto text-[15px] font-bold">Get help now →</span>
    </a>
  </div>
</section>

<!-- Services -->
<section id="services" aria-labelledby="svc" class="section bg-brand-mist">
  <div class="wrap flex flex-col gap-12">
    <div class="flex flex-wrap items-end justify-between gap-5">
      <h2 id="svc" class="h-section max-w-2xl">Everything it takes to stay protected.</h2>
      <a class="link text-base" href="/services">See all services →</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($services as $slug => $s) : ?>
      <?php
        $style = $tileStyles[$slug] ?? ['tile' => 'tile-white'];
        $dark = str_contains($style['tile'], 'tile-ink') || str_contains($style['tile'], 'tile-blue');
        $name = $s['nav_label'] ?? $s['title'];
      ?>
      <?php if (!empty($style['banner'])) : ?>
      <a class="tile tile-hover flex-row flex-wrap items-center gap-6 <?= $e($style['tile']) ?>" href="/services/<?= $e($slug) ?>">
        <span class="flex flex-[1_1_320px] flex-col gap-2">
          <span class="text-[13px] font-bold">For SMBs</span>
          <span class="font-display text-3xl font-bold leading-tight tracking-[-0.03em]">Hire a security team member.</span>
          <span class="text-[15px] leading-relaxed"><?= $e($s['short']) ?></span>
        </span>
        <span class="btn btn-dark">See plans →</span>
      </a>
      <?php else : ?>
      <a class="tile tile-hover group relative min-h-[260px] overflow-hidden <?= $e($style['tile']) ?>" href="/services/<?= $e($slug) ?>">
        <?php if ($slug === 'soc-setup') : ?>
        <svg aria-hidden="true" class="pointer-events-none absolute -right-14 -top-14 h-56 w-56 opacity-50" viewBox="0 0 200 200" fill="none" stroke="#7dd3fc" stroke-width="1.5"><circle cx="100" cy="100" r="40"/><circle class="animate-breathe" cx="100" cy="100" r="70"/><circle cx="100" cy="100" r="98"/><circle cx="100" cy="100" r="8" fill="#ccff00" stroke="none"/></svg>
        <?php endif; ?>
        <?php if (!empty($style['featured'])) : ?>
        <span class="text-[13px] font-bold text-brand-neon">Most requested</span>
        <span class="font-display text-4xl font-bold leading-tight tracking-[-0.03em]"><?= $e($name) ?></span>
        <span class="max-w-md text-base leading-relaxed text-slate-300"><?= $e($s['short']) ?></span>
        <span class="mt-auto flex flex-wrap gap-2 pt-4">
          <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold">Ransomware rollback</span>
          <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold">USB &amp; web control</span>
          <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold">Central policy</span>
        </span>
        <?php else : ?>
        <svg class="relative h-8 w-8 transition duration-300 group-hover:-rotate-6 group-hover:scale-110 <?= $dark ? 'text-brand-neon' : 'text-brand-blue' ?>" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 32 32"><?= $icons[$slug] ?? $icons['security-audits'] ?></svg>
        <span class="tile-title relative"><?= $e($name) ?></span>
        <span class="relative text-[15px] leading-relaxed <?= $dark ? 'text-sky-100' : 'text-brand-body' ?>"><?= $e($s['short']) ?></span>
        <span class="relative mt-auto text-sm font-bold <?= $dark ? 'text-white' : 'text-brand-blue' ?>">Learn more →</span>
        <?php endif; ?>
      </a>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- How we work -->
<section aria-labelledby="how" class="wrap section flex flex-col gap-14">
  <h2 id="how" class="h-section max-w-3xl">From first look to always-on, in four steps.</h2>
  <ol class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ([
        ['Audit', 'A free, surface-level look at your network, cloud and endpoints.'],
        ['Plan', 'A plain-language roadmap, ranked by risk. No fear-selling.'],
        ['Protect', 'We deploy endpoint, network and data controls that fit your budget.'],
        ['Watch & respond', '24/7 SOC monitoring, and a team on call the moment something happens.'],
    ] as $i => [$stepTitle, $stepText]) : ?>
    <li class="flex flex-col gap-3 border-t-2 pt-6 <?= $i === 0 ? 'border-brand-ink' : 'border-slate-300' ?>">
      <span class="font-display text-[15px] font-bold text-brand-muted">0<?= $i + 1 ?></span>
      <span class="text-[22px] font-extrabold"><?= $e($stepTitle) ?></span>
      <span class="tile-text"><?= $e($stepText) ?></span>
    </li>
    <?php endforeach; ?>
  </ol>
</section>

<!-- Tagline -->
<section aria-labelledby="securious" class="wrap pb-28 pt-4 text-center">
  <div class="flex flex-col items-center gap-6">
    <span class="chip chip-lime">Our tagline</span>
    <h2 id="securious" class="display text-[56px] leading-[0.92] tracking-[-0.055em] sm:text-8xl lg:text-[136px]">
      <?= $e($contentBlocks['home.hero_prefix'] ?? 'We are') ?>
      <?php if (isset($contentBlocks['home.hero_title']) && $contentBlocks['home.hero_title'] !== 'Securious') : ?>
      <span class="marker"><?= $e($contentBlocks['home.hero_title']) ?></span>.
      <?php else : ?>
      <span class="text-brand-blue">Secur</span><span class="marker">ious</span>.
      <?php endif; ?>
    </h2>
    <p class="lead max-w-2xl"><strong class="text-brand-ink">Curious about security</strong>, so you can fearlessly scale your business.</p>
  </div>
</section>

<!-- Founder video -->
<section id="founder-briefing" aria-labelledby="video" class="wrap pb-28">
  <div class="flex flex-wrap gap-12 rounded-[36px] bg-brand-ink p-7 text-white sm:p-12 lg:p-16">
    <div class="flex min-w-0 flex-[999_1_520px] flex-col gap-6">
      <span class="text-[13px] font-bold text-brand-neon">Founder’s briefing · 12 min</span>
      <h2 id="video" class="font-display text-3xl font-bold leading-[1.05] tracking-[-0.03em] md:text-[46px]">How hackers get into businesses, and why antivirus isn’t enough.</h2>
      <div class="relative aspect-video overflow-hidden rounded-[20px] bg-slate-800">
        <?php if ($videoId !== '') : ?>
        <iframe
          id="founder-video-iframe"
          class="absolute inset-0 h-full w-full"
          src="https://www.youtube-nocookie.com/embed/<?= $e($videoId) ?>?enablejsapi=1&rel=0&modestbranding=1"
          title="Founder security audit briefing"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen>
        </iframe>
        <?php else : ?>
        <div class="flex h-full flex-col items-center justify-center gap-3 p-6 text-center">
          <span class="inline-flex h-[76px] w-[76px] items-center justify-center rounded-full bg-white text-brand-ink">
            <svg class="ml-1 h-7 w-7" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </span>
          <span class="font-mono text-xs text-slate-400">Set FOUNDER_VIDEO_ID in .env</span>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="flex flex-[1_1_300px] flex-col">
      <h3 class="mb-3 text-[13px] font-bold text-slate-400">Chapters</h3>
      <div class="flex flex-col">
        <?php foreach ($chapters as $ch) : ?>
        <button type="button" data-seek="<?= (int) $ch['sec'] ?>" class="flex gap-4 border-b border-white/10 py-3.5 text-left text-[15px] text-white transition hover:text-brand-neon">
          <span class="w-12 shrink-0 font-display text-brand-neon"><?= $e($ch['time']) ?></span>
          <span><?= $e($ch['title']) ?></span>
        </button>
        <?php endforeach; ?>
      </div>
      <a class="btn btn-lg btn-cta mt-6" href="/free-audit">Book your free audit</a>
    </div>
  </div>
</section>

<!-- Founders note + partners -->
<section aria-labelledby="note" class="section bg-brand-mist text-center">
  <div class="wrap flex max-w-4xl flex-col items-center gap-10">
    <svg class="h-10 w-10" aria-hidden="true" viewBox="0 0 40 40"><circle cx="20" cy="20" r="17" fill="none" stroke="#0b0f17" stroke-width="2"/><circle cx="20" cy="20" r="8" fill="#ccff00" stroke="#0b0f17" stroke-width="2"/></svg>
    <h2 id="note" class="font-display text-4xl font-bold leading-[1.08] tracking-[-0.035em] md:text-[56px]">“Security should feel like an aura, not an obstacle.”</h2>
    <p class="text-[17px] text-brand-body">The HaloSec Founders · <a class="link" href="/about">Read the founders note</a></p>
    <div class="mt-2">
      <?= $this->include('partials/partners', ['variant' => 'cards']) ?>
    </div>
  </div>
</section>

<!-- Final CTA -->
<section class="px-4 py-24">
  <div class="relative mx-auto max-w-6xl overflow-hidden rounded-[40px] bg-brand-blue px-6 py-24 text-center text-white md:py-32">
    <?= $this->include('partials/halo', ['tone' => 'blue', 'size' => 900, 'class' => 'top-1/2 -translate-y-1/2']) ?>
    <div class="relative mx-auto flex max-w-3xl flex-col items-center gap-6">
      <h2 class="display text-5xl md:text-7xl">Find out where you stand.</h2>
      <p class="text-lg text-sky-100 md:text-xl">Free. No obligation. A plain-language report from certified Zoho &amp; Sophos architects.</p>
      <a class="btn btn-lg btn-cta" href="/free-audit">Start my free audit</a>
    </div>
  </div>
</section>
