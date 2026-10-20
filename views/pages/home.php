<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$contentBlocks = $contentBlocks ?? [];
?>
<!-- Hero Section -->
<section class="relative pt-44 pb-20 md:pt-52 md:pb-24 rounded-b-[48px] overflow-hidden shadow-2xl bg-gradient-to-b from-slate-950 via-[#071739] to-[#040d21] border-b border-white/10">
  <!-- Ambient Cyber Aura Background Elements -->
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <!-- Radial core glow -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[450px] bg-sky-500/15 rounded-full blur-3xl"></div>
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[420px] h-[300px] bg-lime-400/10 rounded-full blur-2xl"></div>
    <!-- Concentric Halo/Aura security rings -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[620px] h-[620px] rounded-full border border-sky-400/15 animate-pulse"></div>
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[880px] h-[880px] rounded-full border border-sky-400/10"></div>
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1140px] h-[1140px] rounded-full border border-sky-400/5"></div>
    <!-- Subtle Grid mesh overlay -->
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:24px_24px]"></div>
  </div>

  <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
    <!-- Clean pre-heading badge -->
    <div class="inline-flex items-center gap-2 bg-slate-900/80 backdrop-blur-xl border border-white/15 px-4 py-1.5 rounded-full mb-6 text-white text-xs font-semibold shadow-lg shadow-black/40">
      <span class="relative flex h-2 w-2 mr-1">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
      </span>
      <?= $e($config['tagline'] ?? 'The Aura of Security for Modern Enterprises') ?>
    </div>

    <!-- Hero Title & Tracked Securious Moniker -->
    <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold tracking-tight leading-[1.05] text-white font-sans drop-shadow-sm">
      <span class="sr-only">HaloSec Cybersecurity — </span>
      <?= $e($contentBlocks['home.hero_prefix'] ?? 'We are') ?> <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-sky-100 to-sky-300"><?= $e($contentBlocks['home.hero_title'] ?? 'Securious') ?></span>
      <span class="block text-xl md:text-2xl font-bold tracking-normal text-sky-200/95 mt-3 font-sans">Enterprise Cybersecurity &amp; 24/7 SOC Surveillance</span>
    </h1>
    <p class="mt-4 font-display text-lg md:text-xl tracking-[0.35em] text-lime-300 font-bold uppercase" aria-label="Securious">S E C U R I O U S</p>

    <!-- Elevated supporting copy -->
    <p class="mt-6 text-base md:text-lg text-slate-300/95 font-medium max-w-2xl mx-auto leading-relaxed">
      We are curious about security — so you can fearlessly scale your business. HaloSec delivers impenetrable endpoint defense, network resilience, proactive SOC surveillance, and elite offensive testing.
    </p>

    <!-- High-clarity, elevated CTA grouping -->
    <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
      <a class="bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-7 py-3.5 rounded-xl shadow-xl shadow-lime-950/30 transition-all hover:scale-105 active:scale-95 flex items-center gap-2 border border-lime-200/50" href="/free-audit">
        <span><?= $e($contentBlocks['home.audit_cta_label'] ?? 'Get a Free Cybersecurity Audit') ?></span>
        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="7" x2="17" y1="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
      </a>
      <a class="bg-slate-900/70 hover:bg-slate-900 backdrop-blur-xl text-white border border-white/20 text-xs font-bold uppercase tracking-wider px-6 py-3.5 rounded-xl transition-all hover:border-sky-400/50 shadow-lg flex items-center gap-2" href="/consultation">
        <span>Book Consultation</span>
        <svg class="w-3.5 h-3.5 text-sky-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
      </a>
      <a class="inline-flex items-center gap-2 bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white border border-red-500/40 text-xs font-bold uppercase px-5 py-3.5 rounded-xl transition-all backdrop-blur-md" href="/under-attack">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-90"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
        </span>
        Incident Response Hotline
      </a>
    </div>
  </div>
</section>

<!-- Accredited Partnerships -->
<section aria-label="Accreditations" class="border-b border-slate-100 py-12 bg-white">
  <div class="max-w-5xl mx-auto px-4">
    <p class="text-center text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-6">Accredited partnerships</p>
    <div class="grid sm:grid-cols-2 gap-5">
      <div class="flex items-center justify-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:border-sky-300 hover:shadow-md transition-all duration-200">
        <span class="font-display text-2xl font-bold text-red-600">Zoho</span>
        <span class="font-bold text-slate-900">Zoho Partners</span>
      </div>
      <div class="flex items-center justify-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:border-sky-300 hover:shadow-md transition-all duration-200">
        <span class="font-display text-2xl font-bold text-sky-700">Sophos</span>
        <span class="font-bold text-slate-900">Sophos Silver Partners</span>
      </div>
    </div>
  </div>
</section>

<!-- Get Started Cards -->
<section class="max-w-7xl mx-auto px-4 py-16" aria-label="Get started">
  <div class="grid gap-6 lg:grid-cols-3">
    <article class="rounded-3xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <p class="text-xs font-bold uppercase tracking-widest text-lime-700">Free</p>
      <h2 class="mt-2 text-2xl font-extrabold text-slate-900">Get a Free Cybersecurity Audit</h2>
      <p class="mt-3 text-slate-600 text-sm flex-1 leading-relaxed">Answer a short questionnaire on your internal network and infrastructure and get a surface-level view of your security posture.</p>
      <a class="mt-6 self-start bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-6 py-3 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 border border-lime-200/50" href="/free-audit">Start the audit ↗</a>
    </article>

    <article class="rounded-3xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <p class="text-xs font-bold uppercase tracking-widest text-sky-700">Talk to an expert</p>
      <h2 class="mt-2 text-2xl font-extrabold text-slate-900">Need a Consultation</h2>
      <p class="mt-3 text-slate-600 text-sm flex-1 leading-relaxed">Book time with HaloSec specialists to shape your security roadmap, tooling and compliance plan.</p>
      <a class="mt-6 self-start bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase px-6 py-3 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95" href="/consultation">Book a consultation ↗</a>
    </article>

    <article class="rounded-3xl bg-red-600 text-white p-8 shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col ring-4 ring-red-200">
      <p class="text-xs font-bold uppercase tracking-widest text-red-100">Emergency · 24/7</p>
      <h2 class="mt-2 text-2xl font-extrabold">Under a Cyber Attack</h2>
      <p class="mt-3 text-red-50 text-sm flex-1 leading-relaxed">Ransomware, breach or compromised accounts? Call now or report the incident — we respond immediately.</p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a class="bg-white hover:bg-red-50 text-red-700 font-bold text-xs uppercase px-6 py-3 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95" href="tel:<?= $e($config['emergency_phone'] ?? '+917356543520') ?>">Call <?= $e($config['emergency_phone'] ?? '+917356543520') ?></a>
        <a class="border border-white/60 hover:bg-white/10 font-bold text-xs uppercase px-6 py-3 rounded-xl transition-all hover:scale-105 active:scale-95" href="/under-attack">Report an attack</a>
      </div>
    </article>
  </div>
</section>

<!-- Core Services -->
<section id="services" class="max-w-7xl mx-auto px-4 py-12">
  <h2 class="text-3xl font-extrabold text-slate-900">Core services</h2>
  <p class="mt-2 text-slate-600 max-w-2xl">Every service has its own page and an enquiry form so you can talk directly to the right team.</p>
  <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($services as $slug => $s) : ?>
    <a class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col" href="/services/<?= $e($slug) ?>">
      <span class="inline-flex w-10 h-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 text-[10px] font-bold"><?= $e($s['icon'] ?? 'SEC') ?></span>
      <h3 class="mt-4 font-bold text-slate-900 leading-snug"><?= $e($s['title']) ?></h3>
      <p class="mt-2 text-sm text-slate-600 flex-1"><?= $e($s['short']) ?></p>
      <span class="mt-4 inline-block text-xs font-bold text-sky-700 group-hover:underline">Learn more &amp; enquire →</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<?php
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
?>

<!-- Founder's Security Briefing & Audit Walkthrough -->
<section id="founder-briefing" class="max-w-7xl mx-auto px-4 py-16 scroll-mt-20">
  <div class="rounded-3xl bg-slate-900 border border-slate-800 text-white p-6 sm:p-10 lg:p-12 relative overflow-hidden shadow-2xl">
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-lime-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10">
      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 border-b border-slate-800/80 pb-8 mb-8">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-300 text-xs font-bold uppercase tracking-widest mb-4">
            <svg class="w-3.5 h-3.5 text-lime-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path></svg>
            Founder's Video Briefing
          </div>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
            How Hackers Breach Businesses &amp; Why Antivirus Isn't Enough
          </h2>
          <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed">
            Real-world security audit findings, critical corporate vulnerabilities, and why perimeter defenses fail without network segmentation and proactive endpoint monitoring.
          </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          <a class="bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-6 py-3.5 rounded-xl shadow-lg transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-2 border border-lime-200/50" href="/free-audit">
            <span>Get Free Audit</span>
            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="7" x2="17" y1="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
          </a>
        </div>
      </div>

      <!-- Player & Chapters Side-by-Side -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
        <!-- 16:9 Video Container -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col justify-between">
          <div class="relative w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-2xl aspect-video group">
            <?php if ($videoId !== '') : ?>
            <iframe
              id="founder-video-iframe"
              class="w-full h-full"
              src="https://www.youtube-nocookie.com/embed/<?= $e($videoId) ?>?enablejsapi=1&rel=0&modestbranding=1"
              title="Founder Security Audit Briefing"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen>
            </iframe>
            <?php else : ?>
            <!-- Fallback video frame if ID is pending in .env -->
            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
              <div class="w-16 h-16 rounded-2xl bg-sky-500/20 border border-sky-400/30 flex items-center justify-center mb-4 text-sky-400 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-sky-300 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              </div>
              <p class="font-bold text-white text-base">Founder's Video Guide</p>
              <p class="text-xs text-slate-400 max-w-sm mt-1">Real-life security audit walkthrough &amp; corporate attack vectors.</p>
              <span class="mt-4 text-[11px] font-mono px-3 py-1 rounded bg-slate-800 text-slate-400 border border-slate-700">Set FOUNDER_VIDEO_ID in .env</span>
            </div>
            <?php endif; ?>
          </div>
          <div class="mt-3 flex items-center justify-between text-xs text-slate-400 px-1">
            <span class="inline-flex items-center gap-1.5 text-slate-300">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              Click any chapter to jump directly to that timestamp
            </span>
            <span class="text-slate-400 font-mono text-[11px]">8 Key Chapters</span>
          </div>
        </div>

        <!-- Interactive Chapter Navigation -->
        <div class="lg:col-span-5 xl:col-span-4 bg-slate-950/70 backdrop-blur-md rounded-2xl border border-slate-800/80 p-4 sm:p-5 flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jump to Chapter</span>
              <span class="text-[11px] text-lime-400 font-semibold font-mono">12+ Mins Masterclass</span>
            </div>

            <div class="space-y-1.5 max-h-[360px] overflow-y-auto pr-1">
              <?php foreach ($chapters as $ch) : ?>
              <button
                type="button"
                onclick="seekFounderVideo(<?= $ch['sec'] ?>)"
                class="w-full text-left flex items-start gap-3 p-2 rounded-xl transition-all duration-150 hover:bg-slate-800/80 border border-transparent hover:border-slate-700 group focus:outline-none focus:ring-1 focus:ring-lime-400">
                <span class="px-2 py-0.5 rounded-md bg-slate-800/90 text-sky-300 font-mono text-[11px] font-bold group-hover:bg-lime-400 group-hover:text-slate-950 transition-colors shrink-0">
                  <?= $e($ch['time']) ?>
                </span>
                <span class="text-xs font-medium text-slate-300 group-hover:text-white leading-snug">
                  <?= $e($ch['title']) ?>
                </span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-slate-800/80">
            <a href="/free-audit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition-colors border border-slate-700/60 hover:text-lime-300">
              <span>Book Your Free Security Audit</span>
              <span>→</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function seekFounderVideo(seconds) {
  const iframe = document.getElementById('founder-video-iframe');
  if (iframe && iframe.contentWindow) {
    iframe.contentWindow.postMessage(JSON.stringify({
      event: 'command',
      func: 'seekTo',
      args: [seconds, true]
    }), '*');
    iframe.contentWindow.postMessage(JSON.stringify({
      event: 'command',
      func: 'playVideo',
      args: []
    }), '*');
  }
}
</script>

<!-- Founders Note -->
<section class="max-w-5xl mx-auto px-4 py-16">
  <div class="rounded-3xl bg-slate-900 text-white p-10 relative overflow-hidden">
    <div class="absolute right-0 top-0 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <p class="text-xs font-bold uppercase tracking-widest text-lime-300 relative z-10">Founders note</p>
    <h2 class="mt-2 text-2xl md:text-3xl font-extrabold relative z-10">Security should feel like an aura, not an obstacle.</h2>
    <p class="mt-4 text-slate-300 relative z-10">Read the vision and values that shape every HaloSec engagement.</p>
    <a class="mt-6 inline-block bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-6 py-3 rounded-xl relative z-10 transition-all hover:scale-105 active:scale-95 border border-lime-200/50" href="/about">Read the founders note →</a>
  </div>
</section>
