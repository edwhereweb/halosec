<?php
/** @var HaloSec\View $this */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$nav = [
    '/services' => 'SERVICES',
    '/free-audit' => 'FREE AUDIT',
    '/consultation' => 'CONSULTATION',
    '/about' => 'ABOUT & FOUNDERS',
    '/contact' => 'CONTACT',
];
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $e($title) ?> | HaloSec – The Aura of Security for Your Business</title>
<meta name="description" content="HaloSec – We are Securious. Zoho Partners and Sophos Silver Partners delivering cybersecurity services for your business.">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<script>
tailwind.config = {theme: {extend: {fontFamily: {sans: ['"Plus Jakarta Sans"', 'sans-serif'], display: ['"Space Grotesk"', 'sans-serif']}}}};
</script>
<style>
.sky-hero-backdrop{background:linear-gradient(180deg,#1884e8 0%,#3ca0fc 45%,#76bdff 70%,#d8edff 100%);position:relative;overflow:hidden}
.cloud-layer{position:absolute;bottom:0;left:0;right:0;height:300px;pointer-events:none;background:radial-gradient(ellipse 65% 55% at 50% 100%,rgba(255,255,255,.98) 30%,rgba(255,255,255,.85) 60%,rgba(255,255,255,0) 100%)}
.hp{position:absolute!important;left:-9999px;width:1px;height:1px;overflow:hidden}
</style>
</head>
<body class="bg-[#fcfdfd] text-slate-800 antialiased selection:bg-[#ccff00] selection:text-slate-900 font-sans">
<a class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:p-2" href="#main">Skip to content</a>
<header class="absolute top-0 left-0 right-0 z-50 pt-5 px-4 md:px-8">
<div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
<a class="flex items-center gap-3 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-full shadow-sm border border-white/60" href="/">
<span class="font-display text-lg font-bold text-sky-700">Halo<span class="text-slate-900">Sec</span></span>
<span class="hidden sm:inline-block text-[11px] font-bold uppercase tracking-wider bg-sky-100 text-sky-800 px-2 py-0.5 rounded-full">Sophos Silver Partner</span>
</a>
<nav aria-label="Main" class="hidden lg:flex items-center gap-7 bg-black/15 backdrop-blur-md px-7 py-2.5 rounded-full border border-white/20 text-white text-[13px] font-semibold tracking-wide">
<?php foreach ($nav as $href => $label) : ?>
<a class="hover:text-lime-300 transition-colors <?= $path === $href ? 'text-lime-300 underline underline-offset-8' : '' ?>" <?= $path === $href ? 'aria-current="page"' : '' ?> href="<?= $e($href) ?>"><?= $e($label) ?></a>
<?php endforeach; ?>
</nav>
<div class="flex items-center gap-2">
<a class="hidden sm:flex bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase px-4 py-2.5 rounded-full shadow-md" href="/under-attack">Under Attack?</a>
<a class="bg-[#e4fc68] hover:bg-[#d4f844] text-slate-900 font-bold text-xs uppercase px-5 py-2.5 rounded-full shadow-md" href="/free-audit">Free Audit ↗</a>
<details class="lg:hidden relative">
<summary class="list-none cursor-pointer bg-white/95 rounded-full px-4 py-2.5 text-xs font-bold">Menu</summary>
<div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl p-3 flex flex-col text-sm font-semibold text-slate-800">
<a class="px-3 py-2" href="/">HOME</a>
<?php foreach ($nav as $href => $label) : ?>
<a class="px-3 py-2 rounded-lg <?= $path === $href ? 'bg-sky-100 text-sky-800' : '' ?>" href="<?= $e($href) ?>"><?= $e($label) ?></a>
<?php endforeach; ?>
<a class="px-3 py-2 text-red-600" href="/under-attack">UNDER ATTACK?</a>
</div>
</details>
</div>
</div>
</header>
<main id="main">
<?= $content ?>
</main>
<footer class="bg-slate-900 text-slate-300 mt-20">
<div class="max-w-7xl mx-auto px-4 py-14 grid gap-10 md:grid-cols-4">
<div class="md:col-span-2">
<p class="font-display text-2xl font-bold text-white">Halo<span class="text-lime-300">Sec</span></p>
<p class="mt-3 text-sm max-w-md"><?= $e($config['tagline']) ?>. We are Securious — we are curious about security.</p>
<p class="mt-4 text-xs font-bold uppercase tracking-widest text-slate-400">Zoho Partners · Sophos Silver Partners</p>
</div>
<div>
<p class="text-white font-bold text-sm mb-3">Services</p>
<ul class="space-y-2 text-sm">
<?php foreach ($services as $slug => $s) : ?>
<li><a class="hover:text-lime-300" href="/services/<?= $e($slug) ?>"><?= $e($s['title']) ?></a></li>
<?php endforeach; ?>
</ul>
</div>
<div>
<p class="text-white font-bold text-sm mb-3">Get in touch</p>
<ul class="space-y-2 text-sm">
<li><a class="hover:text-lime-300" href="/free-audit">Free Cybersecurity Audit</a></li>
<li><a class="hover:text-lime-300" href="/consultation">Need a Consultation</a></li>
<li><a class="text-red-400 hover:text-red-300" href="/under-attack">Under a Cyber Attack</a></li>
<li><a class="hover:text-lime-300" href="/about">About & Founders Note</a></li>
<li><a class="hover:text-lime-300" href="/contact">Contact</a></li>
<li><a class="hover:text-lime-300" href="tel:<?= $e($config['emergency_phone']) ?>"><?= $e($config['emergency_phone']) ?></a></li>
</ul>
</div>
</div>
<p class="border-t border-slate-800 text-center text-xs py-5">© <?= $e(date('Y')) ?> HaloSec. All rights reserved.</p>
</footer>
</body>
</html>
