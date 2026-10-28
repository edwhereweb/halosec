<?php
/**
 * "Related services" links for a service page. Expects $services (all) and $related (list of slugs).
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$items = [];
foreach ($related ?? [] as $relSlug) {
    if (isset($services[$relSlug])) {
        $items[$relSlug] = $services[$relSlug];
    }
}
if ($items === []) {
    return;
}
?>
<section class="section bg-brand-mist" aria-labelledby="related-heading">
  <div class="wrap flex flex-col gap-10">
    <h2 id="related-heading" class="h-section">Related services.</h2>
    <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($items as $relSlug => $rel) : ?>
      <li>
        <a class="tile tile-hover tile-white h-full no-underline" href="/services/<?= $e($relSlug) ?>">
          <span class="tile-title text-xl"><?= $e($rel['nav_label'] ?? $rel['title']) ?></span>
          <span class="tile-text"><?= $e($rel['short'] ?? '') ?></span>
          <span class="link mt-auto text-[15px] font-bold">Learn more about <?= $e($rel['nav_label'] ?? $rel['title']) ?> →</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
