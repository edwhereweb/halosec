<?php
declare(strict_types=1);

/** @var HaloSec\View $this */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
?>
<?= $this->include('partials/service_subnav', ['name' => 'Endpoint Security', 'links' => ['features' => 'Features', 'stories' => 'Client stories', 'faq' => 'FAQ']]) ?>

<section class="relative overflow-hidden px-4 pb-16 pt-20 text-center md:pt-28">
  <div class="relative mx-auto flex max-w-4xl animate-rise flex-col items-center gap-6">
    <span class="eyebrow">For teams of 5 to 500+ devices</span>
    <h1 class="display text-[48px] md:text-7xl lg:text-[88px]">
      <span class="sr-only"><?= $e($service['title'] ?? 'Endpoint Security Solutions for Your Business') ?>: </span>
      Every device. <br><span class="marker">One dashboard.</span>
    </h1>
    <p class="lead max-w-2xl">Secure your company’s devices and data without managing each system by hand. Total control, strong protection, real peace of mind.</p>
    <div class="flex flex-wrap justify-center gap-3">
      <a class="btn btn-lg btn-cta" href="#enquiry">Get a quote</a>
      <a class="btn btn-lg btn-outline" href="/consultation">Book a consultation</a>
    </div>
  </div>
</section>

<!-- Dashboard preview -->
<section aria-label="Product preview" class="px-4 pb-28">
  <div class="mx-auto max-w-5xl rounded-[32px] bg-brand-ink p-4 sm:p-8 lg:p-12">
    <div aria-hidden="true" class="flex flex-col gap-5 rounded-[20px] border border-slate-700/60 bg-[#141a26] p-5 text-white sm:p-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <span class="text-[15px] font-bold">All devices</span>
        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1.5 text-xs font-bold text-emerald-300">
          <span class="dot-live"><span class="ping bg-emerald-500"></span><span class="relative h-2 w-2 rounded-full bg-emerald-500"></span></span>Protected
        </span>
      </div>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        <?php foreach ([['Management', 'Central'], ['Ransomware', 'Rollback'], ['USB policy', 'Blocked'], ['Zero-day', 'Guarded']] as $i => [$k, $v]) : ?>
        <div class="flex flex-col gap-1.5 rounded-2xl bg-[#1a2130] p-4">
          <span class="text-xs text-slate-400"><?= $e($k) ?></span>
          <span class="font-display text-2xl font-bold <?= $i === 2 ? 'text-brand-neon' : '' ?>"><?= $e($v) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="flex flex-wrap gap-3">
        <div class="flex h-40 min-w-0 flex-[999_1_380px] items-end gap-1.5 rounded-2xl bg-[#1a2130] p-4">
          <?php foreach ([30, 45, 38, 62, 50, 88, 44, 36, 58, 40, 33, 47] as $i => $h) : ?>
          <span class="flex-1 origin-bottom animate-grow rounded <?= $h === 88 ? 'bg-brand-neon' : ($i % 6 === 2 ? 'bg-sky-400' : 'bg-slate-700') ?>" style="height: <?= $h ?>%; animation-delay: <?= $i * 50 ?>ms"></span>
          <?php endforeach; ?>
        </div>
        <div class="flex flex-[1_1_260px] flex-col gap-3 rounded-2xl bg-[#1a2130] p-4 text-[13px]">
          <span class="text-xs font-bold text-slate-400">Recent events</span>
          <?php foreach ([['bg-brand-neon', 'Encrypted files rolled back', 'now'], ['bg-red-500', 'USB drive blocked', '2m'], ['bg-sky-400', 'Policy synced to Sales', '9m'], ['bg-emerald-500', 'Exploit attempt stopped', '1h']] as $i => [$dot, $text, $when]) : ?>
          <span class="flex animate-slide items-center gap-2.5" style="animation-delay: <?= 600 + $i * 200 ?>ms"><span class="h-3.5 w-3.5 shrink-0 rounded-full <?= $dot ?>"></span><span class="flex-1"><?= $e($text) ?></span><span class="text-slate-400"><?= $e($when) ?></span></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features -->
<section id="features" class="section bg-brand-mist">
  <div class="wrap flex flex-col gap-12">
    <div class="flex max-w-3xl flex-col gap-4">
      <h2 class="h-section">Antivirus guards one PC. This guards the company.</h2>
      <p class="lead">The days of standalone antivirus are over. Today’s threats need a central, smart system that covers every endpoint in your network.</p>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
      <div class="tile tile-ink md:row-span-2">
        <svg class="h-9 w-9 text-brand-neon" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" viewBox="0 0 36 36"><path d="M28 18a10 10 0 1 1-3-7.1M28 6v6h-6"/></svg>
        <h3 class="font-display text-[34px] font-bold leading-tight tracking-[-0.03em]">Ransomware protection &amp; rollback</h3>
        <p class="text-base leading-relaxed text-slate-300">If ransomware tries to encrypt your files, we stop the process, block its communication and roll your files back to safety, automatically.</p>
      </div>
      <div class="tile tile-sky tile-hover"><h3 class="tile-title">Centrally managed</h3><p class="tile-text">Set policies and control every system from one dashboard, not one PC at a time.</p></div>
      <div class="tile tile-white tile-hover"><h3 class="tile-title">Device &amp; web control</h3><p class="tile-text">Block USB drives, cameras, web access or PowerShell whenever you need to, to stop data leaving.</p></div>
      <div class="tile tile-lime tile-hover"><h3 class="tile-title">Zero-day defense</h3><p class="tile-text">Behavioural intelligence and exploit prevention against new, unpatched threats.</p></div>
      <div class="tile tile-white tile-hover"><h3 class="tile-title">Custom group policies</h3><p class="tile-text">Different access levels and confidentiality rules per team or department.</p></div>
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
      <?php foreach ([['5+', 'Works from 5 systems', 'From 5 computers up to hundreds of devices.'], ['1', 'One console for every device', 'No configuring each employee’s computer separately.'], ['0', 'Zero slowdown for your team', 'Protection without getting in the way of daily work.']] as [$num, $t, $d]) : ?>
      <div class="flex items-start gap-4 rounded-3xl border border-slate-200 bg-white p-6">
        <span class="font-display text-3xl font-bold text-brand-blue"><?= $e($num) ?></span>
        <span class="flex flex-col gap-1"><span class="font-extrabold"><?= $e($t) ?></span><span class="text-sm text-brand-body"><?= $e($d) ?></span></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Stories -->
<section id="stories" class="wrap section flex flex-col gap-12">
  <h2 class="h-section">Stories from our clients.</h2>
  <div class="grid gap-5 md:grid-cols-2">
    <?php foreach ([
        ['GCC tourism firm', 'Zero attacks since.', 'Hackers got in and booked fraudulent tickets, costing lakhs of rupees.', 'Installed Endpoint Security with strict access policies across their systems.', 'Full visibility over every device, and zero cyber attacks since.'],
        ['Accounting & tax practice', 'Client data, sealed.', 'A firm handling confidential client financial data needed total data protection.', 'Endpoint Security plus Data Loss Prevention, with USB and device access blocked.', 'Client data stays protected, with no risk of unauthorised transfers.'],
    ] as [$who, $headline, $problem, $did, $result]) : ?>
    <article class="flex flex-col gap-5 rounded-tile border border-slate-200 p-8 sm:p-9">
      <span class="text-[13px] font-bold text-brand-muted"><?= $e($who) ?></span>
      <h3 class="font-display text-4xl font-bold leading-none tracking-[-0.03em]"><?= $e($headline) ?></h3>
      <dl class="grid grid-cols-[110px_1fr] gap-x-4 gap-y-3 text-[15px] leading-relaxed">
        <dt class="font-bold">Problem</dt><dd class="text-brand-body"><?= $e($problem) ?></dd>
        <dt class="font-bold">What we did</dt><dd class="text-brand-body"><?= $e($did) ?></dd>
        <dt class="font-bold">Result</dt><dd class="text-brand-body"><?= $e($result) ?></dd>
      </dl>
    </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- FAQ + enquiry -->
<section class="section bg-brand-mist">
  <div class="wrap flex flex-wrap gap-14">
    <div id="faq" class="flex flex-[1_1_380px] flex-col">
      <h2 class="mb-6 font-display text-4xl font-bold tracking-[-0.03em]">Questions</h2>
      <?php foreach ($service['faqs'] ?? [] as $i => [$q, $a]) : ?>
      <details class="group border-b border-slate-300 py-5"<?= $i === 0 ? ' open' : '' ?>>
        <summary class="flex items-center justify-between gap-4 text-lg font-bold">
          <?= $e($q) ?>
          <span aria-hidden="true" class="text-2xl font-normal text-brand-muted transition group-open:rotate-45">+</span>
        </summary>
        <p class="mt-3 tile-text"><?= $e($a) ?></p>
      </details>
      <?php endforeach; ?>
    </div>
    <div class="min-w-0 flex-[1_1_440px]">
      <?= $this->include('partials/enquiry_card', [
          'heading' => 'Get a quote',
          'sub' => 'Our Endpoint Security specialists reply within 24 hours.',
          'chips' => ['5–20', '20–50', '50–150', '150+'],
          'chipsLegend' => 'How many devices?',
          'chipsPrefix' => 'Approx. systems',
          'messageLabel' => 'How can we help?',
          'messageHint' => 'Operating systems, current antivirus, anything we should know.',
          'btnLabel' => 'Send enquiry',
          'notes' => ['Minimum 5 systems to deploy', 'Certified Sophos Silver & Zoho Partner', 'No disruption to employee productivity'],
          'flash' => $flash,
          'formAction' => $formAction,
          'old' => $old,
          'errors' => $errors,
      ]) ?>
    </div>
  </div>
</section>

<?= $this->include('partials/related_services', ['services' => $services, 'related' => $service['related'] ?? []]) ?>
