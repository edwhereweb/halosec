<?php
/**
 * One guide. Expects $slug, $guide (config/guides.php entry) and $bodyTemplate (views/guides/...).
 * The "On this page" menu is built from the body's <h2 id="..."> headings.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$services = $services ?? [];
$allGuides = $guides ?? [];
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
$summary = (array) ($guide['summary'] ?? []);
$author = (array) ($guide['author'] ?? []);
$authorName = (string) ($author['name'] ?? 'HaloSec security team');
$initials = implode('', array_map(static fn (string $w): string => mb_strtoupper(mb_substr($w, 0, 1)), array_slice(preg_split('/\s+/', $authorName) ?: [], 0, 2)));
$category = (string) ($guide['category'] ?? 'Guide');
[$art, $tone] = match (strtolower($category)) {
    'compliance' => ['compliance', 'tone-lime'],
    'endpoint' => ['endpoint', 'tone-sky'],
    default => ['library', 'tone-mist'],
};

$body = $this->include($bodyTemplate, ['guide' => $guide, 'services' => $services]);
$toc = $summary !== [] ? ['takeaways' => 'Key takeaways'] : [];
preg_match_all('#<h2 id="([^"]+)"[^>]*>(.*?)</h2>#s', $body, $headings, PREG_SET_ORDER);
foreach ($headings as [, $id, $text]) {
    $toc[$id] = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
if ($faqs !== []) {
    $toc['faq'] = 'FAQs';
}
$more = array_slice(array_diff_key($allGuides, [$slug => true]), 0, 3, true);
$tocLinks = static function (array $toc) use ($e): string {
    $html = '';
    foreach ($toc as $id => $label) {
        $html .= '<li><a class="toc-link" href="#' . $e($id) . '">' . $e($label) . '</a></li>';
    }

    return $html;
};
?>
<div aria-hidden="true" class="fixed inset-x-0 top-0 z-[70] h-1 origin-left scale-x-0 bg-gradient-to-r from-brand-cta to-brand-sky" data-read-progress></div>

<article data-article>
  <header class="relative overflow-hidden px-4 pb-14 pt-8 md:pb-20">
    <?= $this->include('partials/halo', ['size' => 1000, 'class' => '-top-[360px]']) ?>
    <div class="wrap relative px-0">
      <nav aria-label="Breadcrumb" class="text-[13px] font-semibold text-brand-muted">
        <ol class="flex flex-wrap items-center gap-2">
          <li><a class="text-brand-muted no-underline hover:text-brand-ink" href="/">Home</a></li>
          <li aria-hidden="true">/</li>
          <li><a class="text-brand-muted no-underline hover:text-brand-ink" href="/guides">Guides</a></li>
          <li aria-hidden="true">/</li>
          <li class="text-brand-ink" aria-current="page"><?= $e($category) ?></li>
        </ol>
      </nav>

      <div class="mt-8 grid items-center gap-10 lg:grid-cols-[1.25fr_1fr] lg:gap-14">
        <div class="flex animate-rise flex-col items-start gap-5">
          <div class="flex flex-wrap items-center gap-2">
            <span class="chip <?= $tone === 'tone-lime' ? 'chip-lime' : 'bg-sky-100 text-sky-900' ?>"><?= $e($category) ?></span>
            <span class="chip chip-mist">
              <svg class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
              <?= $e($guide['read_minutes'] ?? '') ?> min read
            </span>
          </div>
          <h1 class="display text-[40px] md:text-6xl"><?= $e($guide['title']) ?></h1>
          <p class="lead"><?= $e($guide['lead'] ?? '') ?></p>
          <div class="flex items-center gap-3 pt-1">
            <span aria-hidden="true" class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-ink font-display text-base font-bold text-brand-neon ring-4 ring-lime-100"><?= $e($initials) ?></span>
            <p class="text-[14px] leading-snug text-brand-muted">
              By <strong class="text-brand-ink"><?= $e($author['name'] ?? 'the HaloSec security team') ?></strong><?= !empty($author['bio']) ? ', ' . $e($author['bio']) : '' ?>
              <?php if ($updated !== '') : ?><br>Updated <time datetime="<?= $e($updated) ?>"><?= $e($date($updated)) ?></time><?php endif; ?>
            </p>
          </div>
        </div>
        <div class="relative mx-auto hidden w-full max-w-md animate-rise [animation-delay:.15s] sm:block lg:max-w-none">
          <div class="rounded-[40px] p-8 shadow-floating-sky <?= $tone ?>">
            <?= $this->include('partials/guide_art', ['name' => $art, 'class' => 'h-64 w-full animate-float lg:h-72']) ?>
          </div>
          <?php if ($summary !== []) : ?>
          <a href="#takeaways" class="absolute -left-4 bottom-8 rounded-2xl bg-white px-4 py-3 text-[13px] font-bold text-brand-ink no-underline shadow-float transition hover:-translate-y-0.5">⚡ <?= count($summary) ?> key takeaways</a>
          <?php endif; ?>
          <?php if ($faqs !== []) : ?>
          <a href="#faq" class="absolute -right-3 top-8 rounded-2xl bg-brand-ink px-4 py-3 text-[13px] font-bold text-brand-neon no-underline shadow-float transition hover:-translate-y-0.5"><?= count($faqs) ?> questions answered</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <div class="wrap grid gap-12 pb-20 lg:grid-cols-[minmax(0,1fr)_300px] lg:gap-16">
    <div class="min-w-0 max-w-3xl">
      <?php if ($summary !== []) : ?>
      <section id="takeaways" aria-labelledby="takeaways-title" class="relative mb-10 overflow-hidden rounded-tile bg-brand-ink p-7 text-white sm:p-10">
        <span aria-hidden="true" class="absolute -right-16 -top-16 h-48 w-48 rounded-full border-[18px] border-brand-neon/15"></span>
        <h2 id="takeaways-title" class="relative flex items-center gap-3 font-display text-2xl font-bold tracking-[-0.02em]">
          <span aria-hidden="true" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-neon text-brand-ink">⚡</span>
          Key takeaways
        </h2>
        <ol class="relative mt-6 flex flex-col gap-4 text-[16px] leading-relaxed text-slate-200">
          <?php foreach ($summary as $i => $point) : ?>
          <li class="flex gap-4"><span aria-hidden="true" class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-white/20 font-display text-[13px] font-bold text-brand-neon"><?= $i + 1 ?></span><span><?= $e($point) ?></span></li>
          <?php endforeach; ?>
        </ol>
      </section>
      <?php endif; ?>

      <?php if (count($toc) > 2) : ?>
      <details class="mb-10 rounded-3xl border border-slate-200 bg-white p-5 lg:hidden">
        <summary class="flex items-center justify-between font-bold text-brand-ink">On this page <span aria-hidden="true" class="text-brand-blue">▾</span></summary>
        <ol class="toc mt-4"><?= $tocLinks($toc) ?></ol>
      </details>
      <?php endif; ?>

      <div class="prose-guide">
        <?= $body ?>
      </div>

      <?php if ($faqs !== []) : ?>
      <section aria-labelledby="faq" class="mt-16">
        <h2 id="faq" class="h-section text-3xl md:text-[40px]">Frequently asked questions</h2>
        <div class="mt-8 flex flex-col gap-3">
          <?php foreach ($faqs as [$question, $answer]) : ?>
          <details class="group rounded-3xl bg-brand-mist px-6 py-5 transition open:bg-white open:shadow-lift open:ring-1 open:ring-slate-200" data-reveal>
            <summary class="flex items-start justify-between gap-6 text-lg font-extrabold text-brand-ink">
              <span><?= $e($question) ?></span>
              <span aria-hidden="true" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-xl leading-none text-brand-blue transition group-open:rotate-45 group-open:bg-brand-cta group-open:text-brand-ink">+</span>
            </summary>
            <p class="mt-3 text-[16px] leading-relaxed text-brand-body"><?= $e($answer) ?></p>
          </details>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <div class="mt-14 flex flex-col gap-5 rounded-tile border border-slate-200 p-7 sm:flex-row sm:items-center sm:p-8" data-reveal>
        <span aria-hidden="true" class="inline-flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-brand-ink font-display text-xl font-bold text-brand-neon ring-[6px] ring-lime-100"><?= $e($initials) ?></span>
        <div class="flex flex-1 flex-col gap-1">
          <span class="text-[13px] font-bold text-brand-blue">Written by</span>
          <span class="text-xl font-extrabold text-brand-ink"><?= $e($authorName) ?></span>
          <span class="text-[15px] text-brand-body"><?= $e($author['bio'] ?? 'Certified security engineers at HaloSec, Kochi.') ?></span>
        </div>
        <a class="btn btn-dark btn-sm shrink-0" href="/consultation">Ask a question</a>
      </div>

      <p class="mt-8 text-[14px] leading-relaxed text-brand-muted">
        <?= $e($guide['disclaimer'] ?? 'This guide is general information, not legal advice. Laws and deadlines change; check the current official text before acting.') ?>
        <?php if ($published !== '') : ?>First published <time datetime="<?= $e($published) ?>"><?= $e($date($published)) ?></time>.<?php endif; ?>
      </p>
    </div>

    <aside class="flex w-full flex-col gap-4 lg:sticky lg:top-28 lg:self-start" aria-label="On this page and help">
      <?php if (count($toc) > 2) : ?>
      <nav aria-label="On this page" class="hidden rounded-3xl bg-brand-mist p-6 lg:block" data-toc>
        <p class="mb-3 text-[13px] font-bold text-brand-muted">On this page</p>
        <ol class="toc"><?= $tocLinks($toc) ?></ol>
      </nav>
      <?php endif; ?>
      <div class="tile tile-ink relative overflow-hidden p-7">
        <span aria-hidden="true" class="absolute -right-10 -top-10 h-32 w-32 rounded-full border-[14px] border-brand-neon/15"></span>
        <span class="text-[13px] font-bold text-brand-neon">Free, no obligation</span>
        <span class="tile-title text-white">Find out where you stand.</span>
        <span class="text-[15px] leading-relaxed text-slate-300">A 3-minute check-up, then a plain-language report from certified Zoho &amp; Sophos architects.</span>
        <a class="btn btn-cta mt-2" href="/free-audit">Get a free audit</a>
      </div>
      <?php if ($main !== null) : ?>
      <a class="tile tile-hover p-7 <?= $tone ?>" href="/services/<?= $e($mainSlug) ?>">
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

<?php if ($more !== []) : ?>
<section class="bg-brand-mist py-20" aria-labelledby="more-guides">
  <div class="wrap flex flex-col gap-10">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <h2 id="more-guides" class="h-section">Keep reading.</h2>
      <a class="link text-[15px]" href="/guides">All security guides →</a>
    </div>
    <ul class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($more as $moreSlug => $g) : ?>
      <li class="flex" data-reveal><?= $this->include('partials/guide_card', ['slug' => $moreSlug, 'g' => $g]) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<?= $this->include('partials/related_services', [
    'services' => $services,
    'related' => array_values(array_unique(array_merge([$mainSlug], (array) ($guide['related_services'] ?? [])))),
]) ?>
<?= $this->include('partials/cta_band') ?>
