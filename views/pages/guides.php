<?php
/** /guides: every guide in config/guides.php, grouped by category. */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$guides = $guides ?? [];
$byCategory = [];
foreach ($guides as $guideSlug => $g) {
    $byCategory[(string) ($g['category'] ?? 'Guides')][$guideSlug] = $g;
}
$tiles = ['tile-white border border-slate-200', 'tile-sky', 'tile-lime', 'tile-mist'];
?>
<?= $this->include('partials/page_header', [
    'eyebrow' => 'Security guides',
    'heading' => 'Security, explained plainly.',
    'lead' => 'Practical guides for Indian businesses with 5 to 500 people. No jargon, no fear-selling: what the rules say, what attackers do, and what to do about it.',
]) ?>

<section class="wrap pb-24">
  <?php if ($byCategory === []) : ?>
  <p class="lead text-center">Our first guides are on their way.</p>
  <?php endif; ?>
  <?php foreach ($byCategory as $category => $items) : ?>
  <div class="flex flex-col gap-6 pt-10">
    <h2 class="font-display text-2xl font-bold tracking-[-0.02em]"><?= $e($category) ?></h2>
    <ul class="grid gap-4 md:grid-cols-2">
      <?php $i = 0; foreach ($items as $guideSlug => $g) : ?>
      <li>
        <a class="tile tile-hover h-full <?= $tiles[$i++ % count($tiles)] ?>" href="/guides/<?= $e($guideSlug) ?>">
          <span class="text-[13px] font-semibold text-brand-muted"><?= $e($g['read_minutes'] ?? '') ?> min read</span>
          <span class="tile-title"><?= $e($g['title']) ?></span>
          <span class="tile-text"><?= $e($g['lead'] ?? '') ?></span>
          <span class="link mt-auto pt-2 text-[15px]">Read the guide →</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endforeach; ?>
</section>

<?= $this->include('partials/cta_band', [
    'heading' => 'Rather talk it through?',
    'lead' => 'Start with a free audit. A plain-language report on where you stand, from certified Zoho & Sophos architects.',
]) ?>
