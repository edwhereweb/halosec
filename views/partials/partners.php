<?php
/**
 * Accredited partner badges (Zoho Partner, Sophos Silver Partner).
 * Drop the official logo files into public/images/partners/ as zoho-partner.svg|png|webp and
 * sophos-silver-partner.svg|png|webp; until then a text badge is shown.
 * Optional: $variant ('inline' | 'cards'), $tone ('light' | 'dark').
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$variant = $variant ?? 'inline';
$tone = $tone ?? 'light';
$imgDir = dirname(__DIR__, 2) . '/public/images/partners/';
$partners = [
    ['slug' => 'zoho-partner', 'name' => 'Zoho', 'label' => 'Zoho Partner'],
    ['slug' => 'sophos-silver-partner', 'name' => 'Sophos', 'label' => 'Sophos Silver Partner'],
];
foreach ($partners as $i => $p) {
    $partners[$i]['src'] = null;
    foreach (['svg', 'png', 'webp'] as $ext) {
        if (is_file($imgDir . $p['slug'] . '.' . $ext)) {
            $partners[$i]['src'] = '/images/partners/' . $p['slug'] . '.' . $ext;
            break;
        }
    }
}
$nameClass = $tone === 'dark' ? 'text-white' : 'text-brand-ink';
$labelClass = $tone === 'dark' ? 'text-slate-300' : 'text-brand-muted';
?>
<?php if ($variant === 'cards') : ?>
<ul class="flex flex-wrap justify-center gap-4" aria-label="Accredited partnerships">
  <?php foreach ($partners as $p) : ?>
  <li class="flex min-w-[220px] flex-col items-center gap-3 rounded-3xl border border-slate-200 bg-white px-9 py-6 transition hover:border-sky-300">
    <?php if ($p['src'] !== null) : ?>
    <img src="<?= $e($p['src']) ?>" alt="<?= $e($p['label']) ?>" class="h-14 w-auto object-contain" loading="lazy">
    <?php else : ?>
    <span class="font-display text-2xl font-bold text-brand-ink"><?= $e($p['name']) ?></span>
    <?php endif; ?>
    <span class="text-[13px] font-semibold text-brand-muted"><?= $e($p['label']) ?></span>
  </li>
  <?php endforeach; ?>
</ul>
<?php else : ?>
<div class="flex flex-wrap items-center justify-center gap-x-7 gap-y-3">
  <span class="text-[13px] font-semibold <?= $labelClass ?>">Accredited partner of</span>
  <?php foreach ($partners as $i => $p) : ?>
  <?php if ($i > 0) : ?><span class="hidden h-7 w-px bg-slate-300 sm:block" aria-hidden="true"></span><?php endif; ?>
  <span class="inline-flex items-center gap-2.5">
    <?php if ($p['src'] !== null) : ?>
    <img src="<?= $e($p['src']) ?>" alt="<?= $e($p['label']) ?>" class="h-9 w-auto object-contain" loading="lazy">
    <?php else : ?>
    <span class="font-display text-lg font-bold <?= $nameClass ?>"><?= $e($p['name']) ?></span>
    <span class="text-[13px] font-semibold <?= $labelClass ?>"><?= $e(str_replace($p['name'] . ' ', '', $p['label'])) ?></span>
    <?php endif; ?>
  </span>
  <?php endforeach; ?>
</div>
<?php endif; ?>
