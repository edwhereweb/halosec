<?php
/**
 * One guide. Expects $slug, $guide (config/guides.php entry) and $bodyTemplate (views/guides/...).
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$services = $services ?? [];
$date = static function (string $ymd): string {
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $ymd);

    return $d === false ? $ymd : $d->format('j M Y');
};
$mainSlug = (string) ($guide['service'] ?? '');
$main = $services[$mainSlug] ?? null;
$mainName = is_array($main) ? (string) ($main['nav_label'] ?? $main['title']) : '';
$published = (string) ($guide['published'] ?? '');
$updated = (string) ($guide['updated'] ?? $published);
$faqs = (array) ($guide['faqs'] ?? []);
?>
<nav aria-label="Breadcrumb" class="wrap pt-6 text-[13px] font-semibold text-brand-muted">
  <ol class="flex flex-wrap items-center gap-2">
    <li><a class="text-brand-muted no-underline hover:text-brand-ink" href="/">Home</a></li>
    <li aria-hidden="true">/</li>
    <li><a class="text-brand-muted no-underline hover:text-brand-ink" href="/guides">Guides</a></li>
    <li aria-hidden="true">/</li>
    <li class="text-brand-ink" aria-current="page"><?= $e($guide['category'] ?? '') ?></li>
  </ol>
</nav>

<article>
  <header class="relative overflow-hidden px-4 pb-14 pt-14 md:pb-16 md:pt-20">
    <?= $this->include('partials/halo', ['size' => 900, 'class' => '-top-[340px]']) ?>
    <div class="relative mx-auto flex max-w-3xl animate-rise flex-col items-start gap-5">
      <span class="chip chip-mist"><?= $e($guide['category'] ?? 'Guide') ?></span>
      <h1 class="display text-[40px] md:text-6xl"><?= $e($guide['title']) ?></h1>
      <p class="lead"><?= $e($guide['lead'] ?? '') ?></p>
      <p class="text-[14px] font-semibold text-brand-muted">
        By the HaloSec security team ·
        <?php if ($updated !== '') : ?>Updated <time datetime="<?= $e($updated) ?>"><?= $e($date($updated)) ?></time> · <?php endif; ?>
        <?= $e($guide['read_minutes'] ?? '') ?> min read
      </p>
    </div>
  </header>

  <div class="wrap flex flex-col gap-12 pb-20 lg:flex-row lg:items-start lg:gap-16">
    <div class="min-w-0 max-w-3xl flex-1">
      <?php if (!empty($guide['summary'])) : ?>
      <section aria-labelledby="takeaways" class="mb-12 rounded-tile bg-lime-100 p-7 sm:p-9">
        <h2 id="takeaways" class="font-display text-2xl font-bold tracking-[-0.02em]">Key takeaways</h2>
        <ul class="mt-4 flex flex-col gap-3 text-[16px] leading-relaxed text-brand-ink">
          <?php foreach ((array) $guide['summary'] as $point) : ?>
          <li class="flex gap-3"><span aria-hidden="true" class="mt-[9px] h-2 w-2 shrink-0 rounded-full bg-brand-ink"></span><span><?= $e($point) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endif; ?>

      <div class="prose-guide">
        <?= $this->include($bodyTemplate, ['guide' => $guide, 'services' => $services]) ?>
      </div>

      <?php if ($faqs !== []) : ?>
      <section aria-labelledby="faq" class="mt-16">
        <h2 id="faq" class="h-section text-3xl md:text-[40px]">Frequently asked questions</h2>
        <div class="mt-6 flex flex-col divide-y divide-slate-200 border-y border-slate-200">
          <?php foreach ($faqs as [$question, $answer]) : ?>
          <details class="group py-5">
            <summary class="flex items-start justify-between gap-6 text-lg font-extrabold text-brand-ink">
              <span><?= $e($question) ?></span>
              <span aria-hidden="true" class="mt-1 text-2xl leading-none text-brand-blue transition group-open:rotate-45">+</span>
            </summary>
            <p class="mt-3 text-[16px] leading-relaxed text-brand-body"><?= $e($answer) ?></p>
          </details>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <p class="mt-10 text-[14px] leading-relaxed text-brand-muted">
        This guide is general information, not legal advice. Laws and deadlines change; check the current official text before acting.
        <?php if ($published !== '') : ?>First published <time datetime="<?= $e($published) ?>"><?= $e($date($published)) ?></time>.<?php endif; ?>
      </p>
    </div>

    <aside class="flex w-full flex-col gap-4 lg:sticky lg:top-28 lg:w-[340px] lg:shrink-0" aria-label="Get help">
      <div class="tile tile-ink">
        <span class="text-[13px] font-bold text-brand-neon">Free, no obligation</span>
        <span class="tile-title text-white">Find out where you stand.</span>
        <span class="text-[15px] leading-relaxed text-slate-300">A 3-minute check-up, then a plain-language report from certified Zoho &amp; Sophos architects.</span>
        <a class="btn btn-cta mt-2" href="/free-audit">Get a free audit</a>
      </div>
      <?php if ($main !== null) : ?>
      <a class="tile tile-hover tile-mist" href="/services/<?= $e($mainSlug) ?>">
        <span class="text-[13px] font-bold text-brand-blue">HaloSec service</span>
        <span class="tile-title text-xl"><?= $e($mainName) ?></span>
        <span class="tile-text"><?= $e($main['short'] ?? '') ?></span>
        <span class="link text-[15px]">See how we help →</span>
      </a>
      <?php endif; ?>
      <a class="btn btn-danger-soft" href="/under-attack">Breach happening now? Get help</a>
    </aside>
  </div>
</article>

<?= $this->include('partials/related_services', [
    'services' => $services,
    'related' => array_values(array_unique(array_merge([$mainSlug], (array) ($guide['related_services'] ?? [])))),
]) ?>
<?= $this->include('partials/cta_band') ?>
