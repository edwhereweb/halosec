<?php
/** /guides: every guide in config/guides.php, newest first, with the latest one featured. */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$guides = $guides ?? [];
uasort($guides, static fn (array $a, array $b): int => [$b['updated'] ?? '', $b['published'] ?? ''] <=> [$a['updated'] ?? '', $a['published'] ?? '']);
$categories = array_count_values(array_map(static fn (array $g): string => (string) ($g['category'] ?? 'Guides'), $guides));
$latest = DateTimeImmutable::createFromFormat('!Y-m-d', (string) (reset($guides)['updated'] ?? ''));
?>
<section class="relative overflow-hidden px-4 pb-16 pt-16 md:pb-24 md:pt-24">
  <?= $this->include('partials/halo', ['size' => 1000, 'class' => '-top-[320px]']) ?>
  <div class="wrap relative grid items-center gap-12 px-0 lg:grid-cols-[1.15fr_1fr]">
    <div class="flex animate-rise flex-col items-start gap-6">
      <span class="chip chip-mist"><span class="dot-live"><span class="ping bg-lime-400"></span><span class="relative h-2 w-2 rounded-full bg-lime-500"></span></span>Security guides</span>
      <h1 class="display text-[48px] md:text-7xl">Cybersecurity guides, <span class="marker">explained plainly.</span></h1>
      <p class="lead max-w-xl">Practical guides for Indian businesses with 5 to 500 people. No jargon, no fear-selling: what the rules say, what attackers do, and what to do about it.</p>
      <ul class="flex flex-wrap gap-3 text-[14px] font-semibold text-brand-ink">
        <li class="rounded-2xl bg-white px-4 py-3 shadow-card-glass"><span class="font-display text-2xl font-bold"><?= count($guides) ?></span> <?= count($guides) === 1 ? 'guide' : 'guides' ?></li>
        <li class="rounded-2xl bg-white px-4 py-3 shadow-card-glass"><span class="font-display text-2xl font-bold"><?= count($categories) ?></span> <?= count($categories) === 1 ? 'topic' : 'topics' ?></li>
        <?php if ($latest !== false) : ?>
        <li class="rounded-2xl bg-white px-4 py-3 shadow-card-glass">Updated <span class="font-display text-lg font-bold"><?= $e($latest->format('M Y')) ?></span></li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="relative mx-auto w-full max-w-md animate-rise [animation-delay:.15s] lg:max-w-none">
      <div class="rounded-[40px] bg-sky-100 p-6 shadow-floating-sky sm:p-10">
        <?= $this->include('partials/guide_art', ['name' => 'library', 'class' => 'h-64 w-full animate-float sm:h-80']) ?>
      </div>
      <span aria-hidden="true" class="absolute -left-3 top-8 hidden rounded-2xl bg-white px-4 py-3 text-[13px] font-bold shadow-float sm:block">✓ No jargon</span>
      <span aria-hidden="true" class="absolute -right-2 bottom-10 hidden rounded-2xl bg-brand-ink px-4 py-3 text-[13px] font-bold text-brand-neon shadow-float sm:block">Written by our engineers</span>
    </div>
  </div>
</section>

<section class="wrap pb-24" aria-labelledby="all-guides">
  <div class="flex flex-wrap items-end justify-between gap-4 border-t border-slate-200 pt-10">
    <h2 id="all-guides" class="font-display text-3xl font-bold tracking-[-0.03em] md:text-4xl">All guides</h2>
    <?php if (count($categories) > 1) : ?>
    <div class="hidden flex-wrap gap-2" role="group" aria-label="Filter by topic" data-guide-filter>
      <button type="button" class="opt" aria-pressed="true" data-filter="">All <span class="ml-1 text-brand-muted"><?= count($guides) ?></span></button>
      <?php foreach ($categories as $category => $count) : ?>
      <button type="button" class="opt" aria-pressed="false" data-filter="<?= $e($category) ?>"><?= $e($category) ?> <span class="ml-1 opacity-60"><?= $count ?></span></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if ($guides === []) : ?>
  <p class="lead pt-10 text-center">Our first guides are on their way.</p>
  <?php endif; ?>
  <ul class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
    <?php $first = true; foreach ($guides as $guideSlug => $g) : ?>
    <li class="flex <?= $first ? 'md:col-span-2 lg:col-span-3' : '' ?>" data-category="<?= $e($g['category'] ?? 'Guides') ?>" data-reveal>
      <?= $this->include('partials/guide_card', ['slug' => $guideSlug, 'g' => $g, 'featured' => $first]) ?>
    </li>
    <?php $first = false; endforeach; ?>
  </ul>
</section>

<?= $this->include('partials/cta_band', [
    'heading' => 'Rather talk it through?',
    'lead' => 'Start with a free audit. A plain-language report on where you stand, from certified Zoho & Sophos architects.',
]) ?>
