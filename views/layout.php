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
// Nav highlighting: /services/foo still highlights "Services", /guides/foo highlights "Guides".
$navPath = str_starts_with($path, '/services') ? '/services' : (str_starts_with($path, '/guides') ? '/guides' : $path);
$isGuide = str_starts_with($path, '/guides/') && isset($guide) && is_array($guide);
$noindex = ($noindex ?? false) || $path === '' || ($isAdmin ?? false) || str_starts_with($path, '/admin');

$siteName = $config['name'] ?? 'HaloSec';
$defaultTitle = 'Cybersecurity Services, 24/7 SOC & Penetration Testing | ' . $siteName;
$pageTitle = isset($title) && $title !== '' && $title !== $siteName
    ? $title . ' | ' . $siteName
    : $defaultTitle;

$defaultDesc = 'HaloSec delivers enterprise cybersecurity services, 24/7 proactive SOC surveillance, endpoint defense, and offensive penetration testing. Zoho Partner and Sophos Authorized Partner.';
$metaDesc = isset($description) && $description !== '' ? $description : $defaultDesc;
$cssFile = dirname(__DIR__) . '/public/css/app.css';
$cssVersion = is_file($cssFile) ? (string) filemtime($cssFile) : '1';
$jsFile = dirname(__DIR__) . '/public/js/site.js';
$jsVersion = is_file($jsFile) ? (string) filemtime($jsFile) : '1';
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
<meta property="og:type" content="<?= $isGuide ? 'article' : 'website' ?>">
<?php if ($isGuide && !empty($guide['published'])) : ?>
<meta property="article:published_time" content="<?= $e($guide['published']) ?>">
<meta property="article:modified_time" content="<?= $e($guide['updated'] ?? $guide['published']) ?>">
<?php endif; ?>
<meta property="og:site_name" content="<?= $e($siteName) ?>">
<meta property="og:title" content="<?= $e($pageTitle) ?>">
<meta property="og:description" content="<?= $e($metaDesc) ?>">
<meta property="og:url" content="<?= $e($canonicalUrl !== '' ? $canonicalUrl : $baseUrl . '/') ?>">
<meta property="og:image" content="<?= $e($baseUrl) ?>/images/og-default.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="HaloSec: the aura of security for your business">
<meta property="og:locale" content="en_IN">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $e($pageTitle) ?>">
<meta name="twitter:description" content="<?= $e($metaDesc) ?>">
<meta name="twitter:image" content="<?= $e($baseUrl) ?>/images/og-default.png">

<!-- Icons: white HaloSec shield mark on a navy tile, readable on any browser tab colour. -->
<link rel="icon" href="/favicon.ico?v=2" sizes="16x16 32x32 48x48">
<link rel="icon" href="/images/favicon.svg?v=2" type="image/svg+xml">
<link rel="icon" href="/images/favicon-32.png?v=2" type="image/png" sizes="32x32">
<link rel="icon" href="/images/favicon-48.png?v=2" type="image/png" sizes="48x48">
<link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#ffffff">

<?php if (!($isAdmin ?? false)) : ?>
<script type="application/ld+json">
<?= HaloSec\Seo\StructuredData::toJson(HaloSec\Seo\StructuredData::build(
    $baseUrl,
    $path === '' ? '/' : $path,
    isset($title) && $title !== '' ? (string) $title : $pageTitle,
    $metaDesc,
    is_array($config ?? null) ? $config : [],
    $services,
    isset($service) && is_array($service) && str_starts_with($path, '/services/') ? $service : null,
    $isGuide ? $guide : null,
)) ?>

</script>
<?php endif; ?>

<link rel="preload" href="/fonts/plus-jakarta-sans-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="/css/app.css?v=<?= $e($cssVersion) ?>">

</head>
<body class="<?= ($isAdmin ?? false) ? 'bg-slate-50 text-slate-800 antialiased font-sans' : 'bg-white text-brand-ink antialiased font-sans' ?>">
<a class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:p-2" href="#main">Skip to content</a>

<?php if ($isAdmin ?? false) : ?>
<?= $this->include('partials/admin/shell', ['content' => $content, 'nav' => $nav ?? null, 'title' => $title ?? '', 'csrfToken' => $csrfToken ?? '']) ?>
<?php else : ?>
<?php
$phone = (string) ($config['emergency_phone'] ?? '+917356543520');
$navItems = [
    '/services' => 'Services',
    '/guides' => 'Guides',
    '/free-audit' => 'Free audit',
    '/consultation' => 'Consultation',
    '/about' => 'About',
    '/contact' => 'Contact',
];
?>
<!-- Utility bar -->
<div class="border-b border-slate-200 bg-brand-mist text-[12px] font-semibold text-brand-body">
  <div class="wrap flex min-h-[34px] items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <?php if ($showEmergencyBanner) : ?>
      <span class="hidden items-center gap-2 sm:inline-flex">
        <span class="dot-live"><span class="ping bg-emerald-500"></span><span class="relative h-2 w-2 rounded-full bg-emerald-500"></span></span>
        SOC Monitoring 24/7 Active
      </span>
      <?php endif; ?>
      <span class="hidden md:inline">Zoho Partner · Sophos Authorized Partner</span>
    </div>
    <a class="inline-flex items-center gap-1.5 text-brand-red no-underline hover:underline" href="tel:<?= $e($phone) ?>">
      <svg class="h-3.5 w-3.5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linejoin="round" d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"></path></svg>
      <span>Emergency Hotline: <?= $e($phone) ?></span>
    </a>
  </div>
</div>

<!-- Global navigation -->
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/85 backdrop-blur-xl backdrop-saturate-150">
  <div class="wrap flex min-h-[64px] items-center gap-6">
    <a class="flex shrink-0 items-center no-underline group" href="/" aria-label="HaloSec home">
      <?= $this->include('partials/brand_logo', ['variant' => 'light', 'size' => 'md']) ?>
    </a>

    <nav aria-label="Main navigation" class="hidden flex-1 items-center gap-1 text-sm font-medium lg:flex">
      <?php foreach ($navItems as $href => $label) : ?>
      <?php $active = $navPath === $href; ?>
      <a class="rounded-full px-3.5 py-2 no-underline transition hover:bg-slate-100 <?= $active ? 'font-bold text-brand-ink' : 'text-brand-body hover:text-brand-ink' ?>" href="<?= $e($href) ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= $e($label) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="ml-auto flex items-center gap-2">
      <a class="btn btn-sm btn-danger-soft hidden sm:inline-flex" href="/under-attack">
        <span class="dot-live"><span class="ping bg-red-500"></span><span class="relative h-2 w-2 rounded-full bg-red-600"></span></span>
        Under attack?
      </a>
      <a class="btn btn-sm btn-dark hidden sm:inline-flex" href="/free-audit">Free audit</a>

      <details class="relative lg:hidden" data-mobile-menu>
        <summary class="inline-flex h-11 w-11 items-center justify-center rounded-full hover:bg-slate-100" aria-label="Open menu">
          <svg class="h-6 w-6" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" viewBox="0 0 24 24"><path d="M4 8h16M4 16h16"></path></svg>
        </summary>
        <div class="absolute right-0 mt-3 flex w-72 flex-col gap-1 rounded-3xl border border-slate-200 bg-white p-3 text-[15px] font-semibold shadow-float">
          <a class="rounded-2xl px-4 py-3 no-underline hover:bg-slate-100 <?= $path === '/' ? 'bg-brand-mist' : '' ?>" href="/"<?= $path === '/' ? ' aria-current="page"' : '' ?>>Home</a>
          <?php foreach ($navItems as $href => $label) : ?>
          <?php $active = $navPath === $href; ?>
          <a class="rounded-2xl px-4 py-3 no-underline hover:bg-slate-100 <?= $active ? 'bg-brand-mist' : '' ?>" href="<?= $e($href) ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= $e($label) ?></a>
          <?php endforeach; ?>
          <a class="btn btn-danger mt-2" href="/under-attack">Under attack? Get help now</a>
        </div>
      </details>
    </div>
  </div>
</header>

<main id="main">
<?= $content ?>
</main>

<!-- Thumb-reach actions on phones -->
<?php if (!in_array($path, ['/under-attack', '/free-audit'], true)) : ?>
<div class="fixed inset-x-3 bottom-4 z-40 flex gap-2 rounded-full border border-slate-200 bg-white/95 p-2 shadow-float backdrop-blur-xl sm:hidden">
  <a class="btn btn-danger shrink-0 px-4" href="tel:<?= $e($phone) ?>" aria-label="Call the 24/7 emergency line">
    <svg class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linejoin="round" d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"></path></svg>
    24/7
  </a>
  <a class="btn btn-cta flex-1" href="/free-audit">Get a free audit</a>
</div>
<?php endif; ?>

<footer class="bg-brand-ink pb-28 pt-16 text-slate-300 sm:pb-8">
  <div class="wrap flex flex-col gap-12">
    <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
      <div class="flex flex-col gap-4">
        <a href="/" class="w-fit no-underline group" aria-label="HaloSec home">
          <?= $this->include('partials/brand_logo', ['variant' => 'dark', 'size' => 'md']) ?>
        </a>
        <p class="text-sm leading-relaxed">We are Securious: curious about security, so you can fearlessly scale your business.</p>
        <p class="text-[13px] font-semibold text-slate-400">Zoho Partner · Sophos Authorized Partner · Sophos Endpoint &amp; XDR Partner · Hostinger Partner</p>
      </div>
      <div class="flex flex-col gap-2.5 text-sm">
        <p class="mb-1 font-bold text-white">Services</p>
        <?php foreach ($services as $slug => $s) : ?>
        <a class="text-slate-300 no-underline hover:text-white" href="/services/<?= $e($slug) ?>"><?= $e($s['nav_label'] ?? $s['title']) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="flex flex-col gap-2.5 text-sm">
        <p class="mb-1 font-bold text-white">Company</p>
        <a class="text-slate-300 no-underline hover:text-white" href="/about">About &amp; Founders</a>
        <a class="text-slate-300 no-underline hover:text-white" href="/guides">Security guides</a>
        <a class="text-slate-300 no-underline hover:text-white" href="/free-audit">Free cybersecurity audit</a>
        <a class="text-slate-300 no-underline hover:text-white" href="/consultation">Book a consultation</a>
        <a class="text-slate-300 no-underline hover:text-white" href="/contact">Contact</a>
      </div>
      <div class="flex flex-col gap-2.5 text-sm">
        <p class="mb-1 font-bold text-white">Get in touch</p>
        <a class="text-slate-300 no-underline hover:text-white" href="mailto:<?= $e($config['contact_email'] ?? 'hello@halosec.in') ?>"><?= $e($config['contact_email'] ?? 'hello@halosec.in') ?></a>
        <a class="font-bold text-red-300 no-underline hover:text-red-200" href="/under-attack">Under a cyber attack?</a>
        <a class="font-bold text-red-300 no-underline hover:text-red-200" href="tel:<?= $e($phone) ?>">24/7: <?= $e($phone) ?></a>
      </div>
    </div>
    <div class="flex flex-wrap justify-between gap-3 border-t border-white/10 pt-6 text-[13px] text-slate-400">
      <p>© <?= $e(date('Y')) ?> HaloSec. All rights reserved.<?php if (!empty($config['privacy_ready'])) : ?> · <a class="text-slate-400 hover:text-white" href="/privacy">Privacy policy</a><?php endif; ?></p>
      <p>The aura of security for your business.</p>
    </div>
  </div>
</footer>
<script src="/js/site.js?v=<?= $e($jsVersion) ?>" defer></script>
<?php endif; ?>
</body>
</html>
