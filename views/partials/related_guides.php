<?php
/**
 * "Guides on this topic" for a service page. Expects $slug (the service) and $guides (config/guides.php).
 * Shows guides whose main service or related services include this slug.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$items = [];
foreach ($guides ?? [] as $guideSlug => $g) {
    if (($g['service'] ?? '') === $slug || in_array($slug, (array) ($g['related_services'] ?? []), true)) {
        $items[$guideSlug] = $g;
    }
}
if ($items === []) {
    return;
}
?>
<section class="wrap section" aria-labelledby="guides-heading">
  <div class="flex flex-col gap-10">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <h2 id="guides-heading" class="h-section">Guides on this topic.</h2>
      <a class="link text-[15px]" href="/guides">All security guides →</a>
    </div>
    <ul class="grid gap-4 md:grid-cols-2">
      <?php foreach ($items as $guideSlug => $g) : ?>
      <li>
        <a class="tile tile-hover tile-white h-full border border-slate-200" href="/guides/<?= $e($guideSlug) ?>">
          <span class="text-[13px] font-semibold text-brand-muted"><?= $e($g['category'] ?? 'Guide') ?> · <?= $e($g['read_minutes'] ?? '') ?> min read</span>
          <span class="tile-title text-xl"><?= $e($g['title']) ?></span>
          <span class="tile-text"><?= $e($g['lead'] ?? '') ?></span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
