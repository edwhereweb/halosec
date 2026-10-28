<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);

$customTemplate = 'pages/services/' . str_replace('-', '_', (string) ($slug ?? ''));
if (!empty($slug) && is_file(dirname(__DIR__) . '/' . $customTemplate . '.php')) {
    echo $this->include($customTemplate, get_defined_vars());
    return;
}
$name = $service['nav_label'] ?? $service['title'];
$tileColors = ['tile-white', 'tile-sky', 'tile-white', 'tile-lime', 'tile-white', 'tile-white'];
?>
<?= $this->include('partials/service_subnav', ['name' => $name, 'links' => ['included' => 'What’s included', 'next' => 'How it works']]) ?>

<section class="relative overflow-hidden px-4 pb-20 pt-20 text-center md:pt-28">
  <?= $this->include('partials/halo', ['size' => 1000, 'class' => '-top-[300px]']) ?>
  <div class="relative mx-auto flex max-w-4xl animate-rise flex-col items-center gap-6">
    <span class="eyebrow">HaloSec service</span>
    <h1 class="display text-[44px] md:text-6xl lg:text-7xl"><?= $e($service['title']) ?></h1>
    <p class="lead max-w-2xl"><?= $e($service['short']) ?></p>
    <div class="flex flex-wrap justify-center gap-3">
      <a class="btn btn-lg btn-cta" href="#enquiry">Get a quote</a>
      <a class="btn btn-lg btn-outline" href="/consultation">Book a consultation</a>
    </div>
  </div>
</section>

<section id="included" class="section bg-brand-mist">
  <div class="wrap flex flex-col gap-12">
    <div class="flex max-w-3xl flex-col gap-4">
      <h2 class="h-section">What’s included.</h2>
      <p class="lead"><?= $e($service['intro']) ?></p>
    </div>
    <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($service['points'] as $i => $point) : ?>
      <li class="tile tile-hover <?= $tileColors[$i % count($tileColors)] ?>">
        <span aria-hidden="true" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-brand-ink font-display text-sm font-bold text-brand-neon">0<?= $i + 1 ?></span>
        <span class="tile-title text-xl"><?= $e($point) ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="wrap section flex flex-wrap gap-14">
  <div id="next" class="flex flex-[1_1_380px] flex-col gap-8">
    <h2 class="h-section">How it works.</h2>
    <ol class="flex flex-col gap-6">
      <?php foreach ([
          ['Tell us what you need', 'Share your setup, goals and any deadlines in the form.'],
          ['Get a clear proposal', 'A specialist replies within 24 hours with scope, timeline and cost.'],
          ['We get to work', 'Certified engineers deliver, document and hand over in plain language.'],
      ] as $i => [$stepTitle, $stepText]) : ?>
      <li class="flex gap-5">
        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-ink font-display font-bold text-white"><?= $i + 1 ?></span>
        <span class="flex flex-col gap-1"><span class="text-lg font-extrabold"><?= $e($stepTitle) ?></span><span class="tile-text"><?= $e($stepText) ?></span></span>
      </li>
      <?php endforeach; ?>
    </ol>
    <p class="text-[15px] text-brand-body">Not sure this is the right service? <a class="link" href="/services">Browse all services</a></p>
  </div>
  <div class="min-w-0 flex-[1_1_440px]">
    <?= $this->include('partials/enquiry_card', [
        'heading' => 'Enquire about ' . $name,
        'sub' => 'Our ' . $name . ' specialists reply within 24 hours.',
        'btnLabel' => 'Send enquiry',
        'notes' => ['Certified Sophos Silver & Zoho Partner', 'Plain-language advice, no fear-selling'],
        'flash' => $flash,
        'formAction' => $formAction,
        'old' => $old,
        'errors' => $errors,
    ]) ?>
  </div>
</section>
