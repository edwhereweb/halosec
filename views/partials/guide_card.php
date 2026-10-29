<?php
/**
 * Illustrated card linking to one guide. Expects $slug and $g (config/guides.php entry);
 * optional $featured (wide layout with large art). Topic art and tint come from the guide's category.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$featured = $featured ?? false;
$category = (string) ($g['category'] ?? 'Guides');
[$art, $tone] = match (strtolower($category)) {
    'compliance' => ['compliance', 'tone-lime'],
    'endpoint' => ['endpoint', 'tone-sky'],
    default => ['library', 'tone-mist'],
};
$updated = DateTimeImmutable::createFromFormat('!Y-m-d', (string) ($g['updated'] ?? ''));
?>
<a class="guide-card group <?= $featured ? 'md:flex-row' : '' ?>" href="/guides/<?= $e($slug) ?>">
  <span class="guide-card-art <?= $tone ?> <?= $featured ? 'md:w-[46%] md:shrink-0' : '' ?>">
    <?= $this->include('partials/guide_art', ['name' => $art, 'class' => ($featured ? 'h-52 md:h-72' : 'h-40') . ' w-full transition duration-500 group-hover:-translate-y-1 group-hover:scale-[1.04]']) ?>
  </span>
  <span class="flex flex-1 flex-col gap-3 p-7 <?= $featured ? 'md:justify-center md:p-12' : '' ?>">
    <span class="flex flex-wrap items-center gap-2">
      <?php if ($featured) : ?><span class="chip chip-lime py-1.5">New</span><?php endif; ?>
      <span class="chip chip-mist py-1.5"><?= $e($category) ?></span>
      <span class="text-[13px] font-semibold text-brand-muted"><?= $e($g['read_minutes'] ?? '') ?> min read<?= $updated !== false ? ' · ' . $e($updated->format('M Y')) : '' ?></span>
    </span>
    <span class="<?= $featured ? 'font-display text-3xl font-bold leading-tight tracking-[-0.03em] md:text-[40px]' : 'tile-title' ?>"><?= $e($g['title']) ?></span>
    <span class="tile-text <?= $featured ? 'md:text-[17px]' : 'line-clamp-3' ?>"><?= $e($g['lead'] ?? '') ?></span>
    <span class="mt-auto inline-flex items-center gap-2 pt-2 text-[15px] font-bold text-brand-blue">
      Read the guide
      <svg class="h-4 w-4 transition group-hover:translate-x-1" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </span>
  </span>
</a>
