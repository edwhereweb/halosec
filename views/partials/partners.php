<?php
/**
 * Accredited partner logos: Zoho Partner, Sophos Authorized Partner, Sophos Endpoint & XDR Partner.
 * Logo files live in public/images/partners/<slug>.webp|svg|png (sized: wide logos 120px tall,
 * the square Sophos badge 320px). A text badge is shown for any logo file that is missing.
 * Optional: $variant ('inline' | 'cards'), $tone ('light' | 'dark').
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$variant = $variant ?? 'inline';
$tone = $tone ?? 'light';
$imgDir = dirname(__DIR__, 2) . '/public/images/partners/';
// 'w'/'h' are the logo file's pixel size (for width/height attributes); 'inline'/'card' are display heights.
$partners = [
    ['slug' => 'zoho-partner', 'name' => 'Zoho', 'label' => 'Zoho Partner', 'w' => 404, 'h' => 120, 'inline' => 'h-9', 'card' => 'h-14'],
    ['slug' => 'sophos-authorized-partner', 'name' => 'Sophos', 'label' => 'Sophos Authorized Partner', 'w' => 548, 'h' => 120, 'inline' => 'h-9', 'card' => 'h-14'],
    ['slug' => 'sophos-endpoint-xdr-partner', 'name' => 'Sophos', 'label' => 'Sophos Endpoint & XDR Partner', 'w' => 320, 'h' => 320, 'inline' => 'h-14', 'card' => 'h-24'],
];
foreach ($partners as $i => $p) {
    $partners[$i]['src'] = null;
    foreach (['webp', 'svg', 'png'] as $ext) {
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
<ul class="mx-auto grid w-full max-w-4xl gap-4 sm:grid-cols-3" aria-label="Accredited partnerships">
  <?php foreach ($partners as $p) : ?>
  <li class="flex flex-col items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-white px-6 py-6 text-center transition hover:border-sky-300">
    <span class="flex h-24 items-center justify-center">
    <?php if ($p['src'] !== null) : ?>
    <img src="<?= $e($p['src']) ?>" alt="<?= $e($p['label']) ?>" width="<?= $p['w'] ?>" height="<?= $p['h'] ?>" class="<?= $p['card'] ?> w-auto max-w-full object-contain" loading="lazy" decoding="async">
    <?php else : ?>
    <span class="font-display text-2xl font-bold text-brand-ink"><?= $e($p['name']) ?></span>
    <?php endif; ?>
    </span>
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
    <img src="<?= $e($p['src']) ?>" alt="<?= $e($p['label']) ?>" width="<?= $p['w'] ?>" height="<?= $p['h'] ?>" class="<?= $p['inline'] ?> w-auto object-contain" decoding="async">
    <?php else : ?>
    <span class="font-display text-lg font-bold <?= $nameClass ?>"><?= $e($p['name']) ?></span>
    <span class="text-[13px] font-semibold <?= $labelClass ?>"><?= $e(str_replace($p['name'] . ' ', '', $p['label'])) ?></span>
    <?php endif; ?>
  </span>
  <?php endforeach; ?>
</div>
<?php endif; ?>
