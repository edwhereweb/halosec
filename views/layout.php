<?php
/** @var HaloSec\View $this */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$path = $path ?? '';
$services = $services ?? [];
$contentBlocks = $contentBlocks ?? [];
$showEmergencyBanner = ($contentBlocks['home.emergency_banner_enabled'] ?? '1') !== '0';

$baseUrl = rtrim((string) (getenv('APP_URL') ?: (isset($_SERVER['HTTP_HOST']) ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']) : 'https://halosec.in')), '/');
// Pages without a path (404, 500) get no canonical tag.
$canonicalUrl = $path === '' ? '' : $baseUrl . $path;
// Nav highlighting: /services/foo still highlights "Services".
$navPath = str_starts_with($path, '/services') ? '/services' : $path;
$noindex = ($noindex ?? false) || $path === '' || ($isAdmin ?? false) || str_starts_with($path, '/admin');

$siteName = $config['name'] ?? 'HaloSec';
$defaultTitle = 'Cybersecurity Services, 24/7 SOC & Penetration Testing | ' . $siteName;
$pageTitle = isset($title) && $title !== '' && $title !== $siteName
    ? $title . ' | ' . $siteName
    : $defaultTitle;

$defaultDesc = 'HaloSec delivers enterprise cybersecurity services, 24/7 proactive SOC surveillance, endpoint defense, and offensive penetration testing. Certified Zoho & Sophos Silver Partners.';
$metaDesc = isset($description) && $description !== '' ? $description : $defaultDesc;
$cssFile = dirname(__DIR__) . '/public/css/app.css';
$cssVersion = is_file($cssFile) ? (string) filemtime($cssFile) : '1';
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $e($pageTitle) ?></title>
<meta name="description" content="<?= $e($metaDesc) ?>">
<?php if ($canonicalUrl !== '') : ?>
<link rel="canonical" href="<?= $e($canonicalUrl) ?>">
<?php endif; ?>
<?php if ($noindex) : ?>
<meta name="robots" content="noindex, nofollow">
<?php else : ?>
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<?php endif; ?>

<!-- Open Graph / Facebook / LinkedIn -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= $e($siteName) ?>">
<meta property="og:title" content="<?= $e($pageTitle) ?>">
<meta property="og:description" content="<?= $e($metaDesc) ?>">
<meta property="og:url" content="<?= $e($canonicalUrl !== '' ? $canonicalUrl : $baseUrl . '/') ?>">
<meta property="og:image" content="<?= $e($baseUrl) ?>/images/halosec-logo.png">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $e($pageTitle) ?>">
<meta name="twitter:description" content="<?= $e($metaDesc) ?>">
<meta name="twitter:image" content="<?= $e($baseUrl) ?>/images/halosec-logo.png">

<link rel="icon" type="image/png" href="/images/favicon.png">

<?php if (!($isAdmin ?? false)) : ?>
<!-- Structured Data: Organization & ProfessionalService Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "<?= $e($baseUrl) ?>/#organization",
      "name": "HaloSec",
      "url": "<?= $e($baseUrl) ?>",
      "logo": "<?= $e($baseUrl) ?>/images/halosec-logo.png",
      "slogan": "The Aura of Security for Your Business",
      "description": "Enterprise cybersecurity services, 24/7 SOC surveillance, endpoint defense, and offensive penetration testing.",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= $e($config['emergency_phone'] ?? '+917356543520') ?>",
        "contactType": "Emergency Cyber Incident Response",
        "availableLanguage": "English"
      }
    },
    {
      "@type": "ProfessionalService",
      "@id": "<?= $e($baseUrl) ?>/#service",
      "name": "HaloSec Cybersecurity",
      "image": "<?= $e($baseUrl) ?>/images/halosec-logo.png",
      "url": "<?= $e($baseUrl) ?>",
      "telephone": "<?= $e($config['emergency_phone'] ?? '+917356543520') ?>",
      "priceRange": "$$",
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "HaloSec Core Cybersecurity Services",
        "itemListElement": [
          <?php
          $catalog = [];
          foreach ($services as $sSlug => $sData) {
              $catalog[] = json_encode([
                  '@type' => 'Offer',
                  'itemOffered' => [
                      '@type' => 'Service',
                      'name' => $sData['title'] ?? '',
                      'description' => $sData['short'] ?? '',
                      'url' => $baseUrl . '/services/' . $sSlug,
                  ],
              ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
          }
          echo implode(",\n          ", $catalog);
          ?>
        ]
      }
    },
    {
      "@type": "WebSite",
      "@id": "<?= $e($baseUrl) ?>/#website",
      "url": "<?= $e($baseUrl) ?>",
      "name": "HaloSec",
      "publisher": {
        "@id": "<?= $e($baseUrl) ?>/#organization"
      }
    }
  ]
}
</script>
<?php endif; ?>

<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/app.css?v=<?= $e($cssVersion) ?>">
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
:focus-visible{outline:3px solid #0369a1;outline-offset:3px}
.hp{position:absolute!important;left:-9999px;width:1px;height:1px;overflow:hidden}
</style>
</head>
<body class="<?= ($isAdmin ?? false) ? 'bg-slate-100 text-slate-800 antialiased font-sans' : 'bg-[#fcfdfd] text-slate-800 antialiased selection:bg-[#ccff00] selection:text-slate-900 font-sans' ?>">
<a class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:p-2" href="#main">Skip to content</a>

<?php if ($isAdmin ?? false) : ?>
<div class="min-h-screen">
  <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4 px-4 py-3 sm:px-6">
      <a class="flex min-w-0 items-center gap-3 rounded-lg text-slate-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-700 group" href="/admin" aria-label="HaloSec admin dashboard">
        <img src="/images/halosec-logo.png" alt="HaloSec Admin Operations" class="h-7 w-auto object-contain" width="300" height="86" style="aspect-ratio: 300 / 86;" />
        <span class="rounded-md bg-slate-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">Operations</span>
      </a>
      <div class="flex min-w-0 items-center gap-3 sm:gap-5">
        <span class="hidden truncate text-sm font-semibold text-slate-600 sm:block"><?= $e($title ?? 'Admin') ?></span>
        <form method="post" action="/admin/logout">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken ?? '') ?>">
          <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-700">Log out</button>
        </form>
      </div>
    </div>
  </header>
  <div class="mx-auto flex min-h-[calc(100vh-61px)] max-w-[1600px] flex-col lg:flex-row">
    <aside class="border-b border-slate-200 bg-white lg:w-64 lg:shrink-0 lg:border-b-0 lg:border-r">
      <nav aria-label="Admin navigation">
        <?= $this->include('pages/admin_nav', [
            'activePage' => $adminPage ?? '',
            'currentType' => $type ?? null,
            'eventType' => $eventType ?? '',
        ]) ?>
      </nav>
    </aside>
    <div class="min-w-0 flex-1">
      <main id="main" class="min-w-0 px-4 py-6 sm:px-6 lg:px-8">
        <?= $content ?>
      </main>
      <footer class="border-t border-slate-200 px-4 py-4 text-xs text-slate-500 sm:px-6 lg:px-8">HaloSec administrative workspace</footer>
    </div>
  </div>
</div>
<?php else : ?>
<!-- Redesigned High-Fidelity Header -->
<header class="absolute top-0 left-0 right-0 z-50 pt-4 px-4 sm:px-6 lg:px-8">
  <!-- Top Utility Bar: Emergency Alert & Live Status -->
  <div class="max-w-7xl mx-auto mb-3 flex items-center justify-between text-xs text-white/80 border-b border-white/10 pb-2.5 px-2">
    <div class="flex items-center gap-4">
      <?php if ($showEmergencyBanner) : ?>
      <span class="inline-flex items-center gap-1.5 font-medium text-lime-300">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-lime-400"></span>
        </span>
        SOC Monitoring 24/7 Active
      </span>
      <?php endif; ?>
      <?php if ($showEmergencyBanner) : ?>
      <span class="hidden md:inline-block text-white/40">|</span>
      <?php endif; ?>
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
    <!-- Brand Logo -->
    <a class="flex items-center group py-0.5" href="/" aria-label="HaloSec Home">
      <?= $this->include('partials/brand_logo', ['variant' => 'light', 'size' => 'md', 'showTagline' => true]) ?>
    </a>

    <!-- Main Navigation Links with Light Pill Styling -->
    <nav aria-label="Main Navigation" class="hidden lg:flex items-center gap-1 bg-slate-100/70 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 text-[13px] font-semibold tracking-wide">
      <a class="px-3.5 py-1.5 rounded-lg hover:text-slate-900 hover:bg-white transition-all duration-150 <?= $navPath === '/services' ? 'bg-white text-slate-900 shadow-sm' : '' ?>" href="/services" <?= $path === '/services' ? 'aria-current="page"' : '' ?>>Services</a>
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
            <a class="block px-3 py-2 rounded-lg hover:bg-slate-100 <?= $navPath === '/services' ? 'bg-sky-50 text-sky-800 font-bold' : '' ?>" href="/services">Services</a>
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
      <a href="/" class="inline-block group mb-3" aria-label="HaloSec Home">
        <?= $this->include('partials/brand_logo', ['variant' => 'dark', 'size' => 'lg', 'showTagline' => true]) ?>
      </a>
      <p class="mt-2 text-sm max-w-md leading-relaxed text-slate-300"><?= $e($config['tagline'] ?? 'The Aura of Security for Your Business') ?>. We are Securious — we are curious about security.</p>
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
<?php endif; ?>
</body>
</html>
