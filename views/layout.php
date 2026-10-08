<?php
/** @var HaloSec\View $this */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$path = $path ?? '';
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $e($title ?? 'HaloSec') ?> | HaloSec – The Aura of Security for Your Business</title>
<meta name="description" content="HaloSec – We are Securious. Zoho Partners and Sophos Silver Partners delivering cybersecurity services for your business.">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        display: ['"Space Grotesk"', 'sans-serif'],
      },
      colors: {
        brand: {
          blue: '#0369a1',
          sky: '#0ea5e9',
          dark: '#090e17',
          lime: '#d9f99d',
          neon: '#ccff00',
          accent: '#84cc16'
        }
      },
      boxShadow: {
        'card-glass': '0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 1px 1px rgba(255, 255, 255, 0.8)',
        'floating-sky': '0 25px 50px -12px rgba(14, 116, 144, 0.25)',
      }
    }
  }
};
</script>
<style>
/* Daytime sky gradient with rich upper saturation for maximum text contrast */
.sky-hero-backdrop{background:radial-gradient(circle at 50% 10%, #1e88e5 0%, #0d5cb6 45%, #083b7e 100%);position:relative;overflow:hidden}
.cloud-layer{position:absolute;bottom:0;left:0;right:0;height:220px;pointer-events:none;background:radial-gradient(ellipse 70% 50% at 50% 100%,rgba(255,255,255,.98) 20%,rgba(255,255,255,.75) 60%,rgba(255,255,255,0) 100%)}
.cloud-puff-left{position:absolute;bottom:-40px;left:-140px;width:550px;height:260px;background:radial-gradient(circle,rgba(255,255,255,.75) 0%,rgba(255,255,255,0) 70%);filter:blur(24px);pointer-events:none}
.cloud-puff-right{position:absolute;bottom:-20px;right:-120px;width:650px;height:280px;background:radial-gradient(circle,rgba(255,255,255,.8) 0%,rgba(255,255,255,0) 70%);filter:blur(28px);pointer-events:none}
.hero-title{text-shadow:0 3px 20px rgba(1,18,48,.5)}
.hero-lead{text-shadow:0 1px 8px rgba(1,18,48,.4)}
/* 3D Arc and angled perspective cards */
.perspective-container{perspective:1200px}
.tilt-left-far{transform:rotateY(20deg) rotateZ(-3deg) scale(0.9);transition:all .3s cubic-bezier(.16,1,.3,1)}
.tilt-left{transform:rotateY(12deg) rotateZ(-1.5deg) scale(0.96);transition:all .3s cubic-bezier(.16,1,.3,1)}
.tilt-center{transform:translateY(-8px) scale(1.03);transition:all .3s cubic-bezier(.16,1,.3,1)}
.tilt-right{transform:rotateY(-12deg) rotateZ(1.5deg) scale(0.96);transition:all .3s cubic-bezier(.16,1,.3,1)}
.tilt-right-far{transform:rotateY(-20deg) rotateZ(3deg) scale(0.9);transition:all .3s cubic-bezier(.16,1,.3,1)}
.tilt-card:hover{transform:translateY(-14px) scale(1.05) rotateY(0deg) rotateZ(0deg)!important;z-index:30}
@keyframes pulseSoft{0%,100%{opacity:.95;transform:scale(1)}50%{opacity:1;transform:scale(1.03)}}
.status-pulse{animation:pulseSoft 3s ease-in-out infinite}
.hp{position:absolute!important;left:-9999px;width:1px;height:1px;overflow:hidden}
</style>
</head>
<body class="bg-[#fcfdfd] text-slate-800 antialiased selection:bg-[#ccff00] selection:text-slate-900 font-sans">
<a class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:p-2" href="#main">Skip to content</a>

<!-- Redesigned High-Fidelity Header -->
<header class="absolute top-0 left-0 right-0 z-50 pt-4 px-4 sm:px-6 lg:px-8">
  <!-- Top Utility Bar: Emergency Alert & Live Status -->
  <div class="max-w-7xl mx-auto mb-3 flex items-center justify-between text-xs text-white/80 border-b border-white/10 pb-2.5 px-2">
    <div class="flex items-center gap-4">
      <span class="inline-flex items-center gap-1.5 font-medium text-lime-300">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-lime-400"></span>
        </span>
        SOC Monitoring 24/7 Active
      </span>
      <span class="hidden md:inline-block text-white/40">|</span>
      <span class="hidden md:flex items-center gap-2 text-white/70">
        <span>Certified Partners:</span>
        <span class="bg-white/10 px-2 py-0.5 rounded text-[11px] font-semibold text-white">Zoho Certified</span>
        <span class="bg-white/10 px-2 py-0.5 rounded text-[11px] font-semibold text-sky-200">Sophos Silver</span>
      </span>
    </div>
    <div class="flex items-center gap-4 text-xs font-medium">
      <a href="tel:<?= $e($config['emergency_phone'] ?? '+917356543520') ?>" class="hover:text-white transition-colors flex items-center gap-1.5 text-white/90">
        <svg class="w-3.5 h-3.5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
        <span class="hidden sm:inline">Emergency Hotline:</span> <?= $e($config['emergency_phone'] ?? '+91 73565 43520') ?>
      </a>
    </div>
  </div>

  <!-- Primary Floating Island Navigation Header -->
  <div class="max-w-7xl mx-auto bg-white/90 backdrop-blur-xl border border-white/80 rounded-2xl shadow-xl shadow-black/20 p-2.5 sm:px-4 flex items-center justify-between gap-4">
    <!-- Brand Logo & Aura Shield Mark -->
    <a class="flex items-center gap-3 group" href="/" aria-label="HaloSec Home">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 via-sky-400 to-lime-300 flex items-center justify-center shadow-md shadow-sky-900/40 group-hover:scale-105 transition-transform duration-200">
        <svg class="w-5 h-5 text-slate-950" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12v4.7c0 4.46-3.08 8.63-7 9.8-3.92-1.17-7-5.34-7-9.8V6.3l7-3.12zm-1 5.82v5h2v-5h-2zm0 6v2h2v-2h-2z"></path>
        </svg>
      </div>
      <div class="flex flex-col">
        <div class="flex items-center gap-2">
          <span class="font-display text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-sky-700 transition-colors">Halo<span class="text-sky-600">Sec</span></span>
          <span class="inline-block w-1.5 h-1.5 rounded-full bg-lime-400"></span>
        </div>
        <span class="text-[10px] tracking-widest uppercase font-semibold text-slate-500 -mt-0.5">We Are Securious</span>
      </div>
    </a>

    <!-- Main Navigation Links with Light Pill Styling -->
    <nav aria-label="Main Navigation" class="hidden lg:flex items-center gap-1 bg-slate-100/70 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 text-[13px] font-semibold tracking-wide">
      <a class="px-3.5 py-1.5 rounded-lg hover:text-slate-900 hover:bg-white transition-all duration-150 <?= $path === '/services' ? 'bg-white text-slate-900 shadow-sm' : '' ?>" href="/services" <?= $path === '/services' ? 'aria-current="page"' : '' ?>>Services</a>
      <a class="px-3.5 py-1.5 rounded-lg hover:text-slate-900 hover:bg-white transition-all duration-150 flex items-center gap-1.5 <?= $path === '/free-audit' ? 'bg-white text-slate-900 shadow-sm' : '' ?>" href="/free-audit" <?= $path === '/free-audit' ? 'aria-current="page"' : '' ?>>
        Free Audit
        <span class="px-1.5 py-0.2 text-[10px] bg-lime-400/20 text-lime-700 rounded font-bold uppercase tracking-wider">Free</span>
      </a>
      <a class="px-3.5 py-1.5 rounded-lg hover:text-slate-900 hover:bg-white transition-all duration-150 <?= $path === '/consultation' ? 'bg-white text-slate-900 shadow-sm' : '' ?>" href="/consultation" <?= $path === '/consultation' ? 'aria-current="page"' : '' ?>>Consultation</a>
      <a class="px-3.5 py-1.5 rounded-lg hover:text-slate-900 hover:bg-white transition-all duration-150 <?= $path === '/about' ? 'bg-white text-slate-900 shadow-sm' : '' ?>" href="/about" <?= $path === '/about' ? 'aria-current="page"' : '' ?>>About &amp; Founders</a>
      <a class="px-3.5 py-1.5 rounded-lg hover:text-slate-900 hover:bg-white transition-all duration-150 <?= $path === '/contact' ? 'bg-white text-slate-900 shadow-sm' : '' ?>" href="/contact" <?= $path === '/contact' ? 'aria-current="page"' : '' ?>>Contact</a>
    </nav>

    <!-- Header Actions / CTAs -->
    <div class="flex items-center gap-2.5">
      <a class="hidden sm:inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase px-4 py-2.5 rounded-xl shadow-md border border-red-500 transition-all hover:scale-105 active:scale-95" href="/under-attack">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-90"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
        </span>
        Under Attack?
      </a>
      <a class="bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-5 py-2.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 flex items-center gap-1.5 border border-lime-200/50" href="/free-audit">
        <span>Get Free Audit</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><line x1="7" x2="17" y1="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
      </a>

      <!-- Mobile Dropdown Navigation -->
      <details class="lg:hidden relative">
        <summary class="list-none cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-bold transition-colors">Menu</summary>
        <div class="absolute right-0 mt-3 w-64 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-2xl shadow-2xl p-3 flex flex-col text-sm font-semibold text-slate-800 divide-y divide-slate-100">
          <div class="space-y-1 pb-2">
            <a class="block px-3 py-2 rounded-lg hover:bg-slate-100 <?= $path === '/' ? 'bg-sky-50 text-sky-800 font-bold' : '' ?>" href="/">Home</a>
            <a class="block px-3 py-2 rounded-lg hover:bg-slate-100 <?= $path === '/services' ? 'bg-sky-50 text-sky-800 font-bold' : '' ?>" href="/services">Services</a>
            <a class="block px-3 py-2 rounded-lg hover:bg-slate-100 text-sky-700 font-bold <?= $path === '/free-audit' ? 'bg-sky-50' : '' ?>" href="/free-audit">Free Audit</a>
            <a class="block px-3 py-2 rounded-lg hover:bg-slate-100 <?= $path === '/consultation' ? 'bg-sky-50 text-sky-800 font-bold' : '' ?>" href="/consultation">Consultation</a>
            <a class="block px-3 py-2 rounded-lg hover:bg-slate-100 <?= $path === '/about' ? 'bg-sky-50 text-sky-800 font-bold' : '' ?>" href="/about">About &amp; Founders</a>
            <a class="block px-3 py-2 rounded-lg hover:bg-slate-100 <?= $path === '/contact' ? 'bg-sky-50 text-sky-800 font-bold' : '' ?>" href="/contact">Contact</a>
          </div>
          <div class="pt-2">
            <a class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-red-600 text-white font-bold text-xs uppercase shadow-sm" href="/under-attack">
              Emergency: Under Attack?
            </a>
          </div>
        </div>
      </details>
    </div>
  </div>
</header>

<main id="main">
<?= $content ?>
</main>

<footer class="bg-slate-950 text-slate-400 text-xs mt-20 border-t border-slate-800">
  <div class="max-w-7xl mx-auto px-4 py-14 grid gap-10 md:grid-cols-4">
    <div class="md:col-span-2">
      <p class="font-display text-2xl font-bold text-white">Halo<span class="text-lime-300">Sec</span></p>
      <p class="mt-3 text-sm max-w-md leading-relaxed text-slate-300"><?= $e($config['tagline'] ?? 'The Aura of Security for Your Business') ?>. We are Securious — we are curious about security.</p>
      <p class="mt-4 text-xs font-bold uppercase tracking-widest text-slate-400">Zoho Partners · Sophos Silver Partners</p>
    </div>
    <div>
      <p class="text-white font-bold text-sm mb-3">Services</p>
      <ul class="space-y-2 text-sm">
        <?php foreach ($services as $slug => $s) : ?>
        <li><a class="hover:text-lime-300 transition-colors" href="/services/<?= $e($slug) ?>"><?= $e($s['title']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="text-white font-bold text-sm mb-3">Get in touch</p>
      <ul class="space-y-2 text-sm">
        <li><a class="hover:text-lime-300 transition-colors" href="/free-audit">Free Cybersecurity Audit</a></li>
        <li><a class="hover:text-lime-300 transition-colors" href="/consultation">Need a Consultation</a></li>
        <li><a class="text-red-400 hover:text-red-300 transition-colors" href="/under-attack">Under a Cyber Attack</a></li>
        <li><a class="hover:text-lime-300 transition-colors" href="/about">About &amp; Founders Note</a></li>
        <li><a class="hover:text-lime-300 transition-colors" href="/contact">Contact</a></li>
        <li><a class="hover:text-lime-300 transition-colors" href="tel:<?= $e($config['emergency_phone'] ?? '+917356543520') ?>"><?= $e($config['emergency_phone'] ?? '+917356543520') ?></a></li>
      </ul>
    </div>
  </div>
  <p class="border-t border-slate-800/80 text-center text-xs py-6 text-slate-500">© <?= $e(date('Y')) ?> HaloSec. All rights reserved.</p>
</footer>
</body>
</html>
