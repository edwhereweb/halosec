<?php
declare(strict_types=1);

/** @var HaloSec\View $this */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$tiers = [
    ['name' => 'Starter', 'hours' => '10–15 hrs / month', 'chip' => '10–15 hrs/mo', 'for' => 'Businesses with 10–50 systems that need recurring posture health checks without a full-time hire.', 'items' => ['Weekly SOC & alert log triage', 'Monthly policy & patch audits', 'Incident standby response'], 'featured' => false],
    ['name' => 'Growth', 'hours' => '20–40 hrs / month', 'chip' => '20–40 hrs/mo', 'for' => 'Software and SaaS teams, fintechs, or firms handling sensitive client financial or health data.', 'items' => ['DevSecOps & app security testing', 'Active endpoint & USB rule push', 'Compliance audit readiness (ISO / SOC 2)'], 'featured' => true],
    ['name' => 'Dedicated', 'hours' => '60+ hrs / month', 'chip' => '60+ hrs/mo', 'for' => 'Your embedded virtual CISO and senior hands-on engineer for high-growth operations.', 'items' => ['Direct executive & board reporting', 'Vendor security risk reviews', 'Full architecture & roadmap leadership'], 'featured' => false],
];
?>
<?= $this->include('partials/service_subnav', ['name' => 'Security Team Outsourcing', 'links' => ['why' => 'Why us', 'plans' => 'Plans', 'help' => 'What we do', 'stories' => 'Examples']]) ?>

<section class="relative overflow-hidden px-4 pb-16 pt-20 text-center md:pt-28">
  <?= $this->include('partials/halo', ['size' => 1000, 'class' => '-top-[300px]']) ?>
  <div class="relative mx-auto flex max-w-4xl animate-rise flex-col items-center gap-6">
    <span class="eyebrow">For small &amp; mid-sized businesses</span>
    <h1 class="display text-[44px] md:text-6xl lg:text-[80px]">
      <span class="sr-only"><?= $e($service['nav_label'] ?? 'Security Team Outsourcing') ?>: </span>
      A security team, <br><span class="marker">without the payroll.</span>
    </h1>
    <p class="lead max-w-2xl">A full-time in-house security team is too expensive for most growing businesses. Hire certified HaloSec experts by the hour or month, tailored to your scope and budget.</p>
    <div class="flex flex-wrap justify-center gap-3">
      <a class="btn btn-lg btn-cta" href="#plans">See plans</a>
      <a class="btn btn-lg btn-outline" href="#enquiry">Request talent</a>
    </div>
  </div>
</section>

<!-- Quick facts -->
<section aria-label="At a glance" class="wrap pb-20">
  <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-tile bg-slate-200 md:grid-cols-4">
    <?php foreach ([['Model', 'Flexible hours'], ['Certifications', 'CEH, CySA+, Cloud'], ['Engagement', 'Hourly / Monthly'], ['Onboarding', 'Under 48 hours']] as [$k, $v]) : ?>
    <div class="flex flex-col gap-1 bg-white p-6">
      <dt class="text-[13px] font-semibold text-brand-muted"><?= $e($k) ?></dt>
      <dd class="font-display text-xl font-bold md:text-2xl"><?= $e($v) ?></dd>
    </div>
    <?php endforeach; ?>
  </dl>
</section>

<!-- Why us -->
<section id="why" class="section bg-brand-mist">
  <div class="wrap flex flex-col gap-12">
    <div class="flex max-w-3xl flex-col gap-4">
      <h2 class="h-section">Enterprise talent, SMB budgets.</h2>
      <p class="lead">Certified security capability without building an expensive in-house division.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="tile tile-ink sm:col-span-2 lg:row-span-2">
        <span class="text-[13px] font-bold text-brand-neon">Certified engineers</span>
        <h3 class="font-display text-3xl font-bold leading-tight tracking-[-0.03em]">Experts certified by EC-Council, CompTIA, AWS, Azure and Sophos.</h3>
        <ul class="mt-auto flex flex-wrap gap-2 pt-4">
          <?php foreach (['CEH', 'Security+', 'CySA+', 'AWS / Azure / GCP', 'Sophos Silver Partner'] as $cert) : ?>
          <li class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold"><?= $e($cert) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="tile tile-sky tile-hover"><h3 class="tile-title">100% customised</h3><p class="tile-text">Choose the exact skills and support level you need. No paying for what you won’t use.</p></div>
      <div class="tile tile-white tile-hover"><h3 class="tile-title">Flexible plans</h3><p class="tile-text">Pay hourly, or set up a monthly engagement with fixed hours.</p></div>
      <div class="tile tile-lime tile-hover sm:col-span-2"><h3 class="tile-title">Scales with you</h3><p class="tile-text">Scale up for rollouts, audits or busy quarters, and back down as things settle.</p></div>
    </div>
  </div>
</section>

<!-- How it works -->
<section class="wrap section flex flex-col gap-12">
  <h2 class="h-section">Onboarded in three steps.</h2>
  <ol class="grid gap-8 md:grid-cols-3">
    <?php foreach ([
        ['Tell us what you need', 'Share your security goals, tooling, compliance deadlines or current setup issues.'],
        ['Pick your plan', 'Choose the certification level and an hourly or monthly plan that matches your workload.'],
        ['Get started', 'Your expert joins your team, plugged into your Slack, Jira or email.'],
    ] as $i => [$stepTitle, $stepText]) : ?>
    <li class="flex flex-col gap-3 border-t-2 pt-6 <?= $i === 0 ? 'border-brand-ink' : 'border-slate-300' ?>">
      <span class="font-display text-[15px] font-bold text-brand-muted">0<?= $i + 1 ?></span>
      <span class="text-[22px] font-extrabold"><?= $e($stepTitle) ?></span>
      <span class="tile-text"><?= $e($stepText) ?></span>
    </li>
    <?php endforeach; ?>
  </ol>
</section>

<!-- Plans -->
<section id="plans" class="section bg-brand-mist">
  <div class="wrap flex flex-col gap-12">
    <div class="flex max-w-3xl flex-col gap-4">
      <h2 class="h-section">Pick the rhythm that fits.</h2>
      <p class="lead">Recommended engagement tiers, or talk to us for custom hours.</p>
    </div>
    <div class="grid gap-5 lg:grid-cols-3">
      <?php foreach ($tiers as $tier) : ?>
      <article class="tile relative gap-5 <?= $tier['featured'] ? 'tile-ink shadow-lift lg:-translate-y-3' : 'tile-white' ?>">
        <?php if ($tier['featured']) : ?><span class="chip w-fit bg-brand-cta text-brand-ink">Most popular</span><?php endif; ?>
        <div class="flex flex-col gap-1">
          <h3 class="text-[15px] font-bold <?= $tier['featured'] ? 'text-brand-neon' : 'text-brand-blue' ?>"><?= $e($tier['name']) ?></h3>
          <p class="font-display text-3xl font-bold tracking-[-0.03em]"><?= $e($tier['hours']) ?></p>
        </div>
        <p class="text-[15px] leading-relaxed <?= $tier['featured'] ? 'text-slate-300' : 'text-brand-body' ?>"><?= $e($tier['for']) ?></p>
        <ul class="flex flex-col gap-2.5 text-[15px]">
          <?php foreach ($tier['items'] as $item) : ?>
          <li class="flex gap-2.5"><span aria-hidden="true" class="font-bold <?= $tier['featured'] ? 'text-brand-neon' : 'text-emerald-700' ?>">✓</span><?= $e($item) ?></li>
          <?php endforeach; ?>
        </ul>
        <button type="button" data-pick-chip="<?= $e($tier['chip']) ?>" class="btn mt-auto <?= $tier['featured'] ? 'btn-cta' : 'btn-outline' ?>">Choose <?= $e($tier['name']) ?></button>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- What we help with -->
<section id="help" class="wrap section flex flex-col gap-12">
  <div class="flex max-w-3xl flex-col gap-4">
    <h2 class="h-section">What we can help you with.</h2>
    <p class="lead">Specialists for one discipline, or a multi-skilled generalist who covers several.</p>
  </div>
  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    <?php foreach ([
        ['SOC monitoring & reports', ['We monitor your Security Operations Center around the clock.', 'Clear reports that tell you exactly what steps to take.']],
        ['DevSecOps & app security', ['Security support while you build your software or apps.', 'Testing, documentation and patch management.']],
        ['Endpoint security management', ['We monitor and manage laptops, desktops and servers.', 'We build, test and push custom policies across your network.']],
        ['Vulnerability checks & policy', ['We find gaps in your websites, apps and network.', 'Custom security policies to keep infrastructure safe.']],
        ['Generalist, multi-role support', ['One engineer for triage, policy tuning and patch oversight.', 'Your go-to security contact in meetings and with management.']],
    ] as $i => [$title, $points]) : ?>
    <div class="tile tile-hover <?= $i === 4 ? 'tile-blue lg:col-span-1' : 'border border-slate-200 bg-white' ?>">
      <span class="font-display text-sm font-bold <?= $i === 4 ? 'text-brand-neon' : 'text-brand-muted' ?>">0<?= $i + 1 ?></span>
      <h3 class="tile-title"><?= $e($title) ?></h3>
      <ul class="flex flex-col gap-2 text-[15px] leading-relaxed <?= $i === 4 ? 'text-sky-100' : 'text-brand-body' ?>">
        <?php foreach ($points as $point) : ?><li><?= $e($point) ?></li><?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Examples -->
<section id="stories" class="section bg-brand-mist">
  <div class="wrap flex flex-col gap-12">
    <h2 class="h-section">Real examples.</h2>
    <div class="grid gap-5 md:grid-cols-2">
      <?php foreach ([
          ['Audit & compliance firm · Monthly hourly plan', 'Reports, finally acted on.', 'They had Endpoint Security, but no time or expertise to read the technical reports, investigate threats or change settings.', 'A HaloSec engineer on a monthly hourly plan analyses reports, investigates threats, updates settings and pushes new policies regularly.'],
          ['Enterprise office · Baseline hardening', 'Windows, locked down.', 'The company needed specific, strict security policies pushed across every Windows system in the office.', 'We built custom baseline security checklists and set up the whole endpoint configuration from scratch.'],
      ] as [$who, $headline, $problem, $solution]) : ?>
      <article class="flex flex-col gap-5 rounded-tile bg-white p-8 sm:p-9">
        <span class="text-[13px] font-bold text-brand-muted"><?= $e($who) ?></span>
        <h3 class="font-display text-4xl font-bold leading-none tracking-[-0.03em]"><?= $e($headline) ?></h3>
        <dl class="grid grid-cols-[110px_1fr] gap-x-4 gap-y-3 text-[15px] leading-relaxed">
          <dt class="font-bold">Problem</dt><dd class="text-brand-body"><?= $e($problem) ?></dd>
          <dt class="font-bold">Solution</dt><dd class="text-brand-body"><?= $e($solution) ?></dd>
        </dl>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Enquiry -->
<section class="wrap section flex flex-wrap gap-14">
  <div class="flex flex-[1_1_380px] flex-col gap-6">
    <h2 class="h-section">Add a certified expert to your team.</h2>
    <p class="lead">Skip the long recruitment cycle and payroll burden. Tell us what you need and we’ll tailor a profile and plan within 24 hours.</p>
    <p class="text-[15px] text-brand-body">Not sure how many hours you need? <a class="link" href="/consultation">Book a consultation</a></p>
  </div>
  <div class="min-w-0 flex-[1_1_440px]">
    <?= $this->include('partials/enquiry_card', [
        'heading' => 'Request security talent',
        'sub' => 'We reply with a tailored profile and plan within 24 hours.',
        'chips' => ['10–15 hrs/mo', '20–40 hrs/mo', '60+ hrs/mo', 'Ad-hoc project'],
        'chipsLegend' => 'Estimated scope',
        'chipsPrefix' => 'Engagement',
        'messageLabel' => 'Describe what you need',
        'messageHint' => 'For example: SOC monitoring, endpoint tuning, weekly hours.',
        'btnLabel' => 'Request security talent',
        'notes' => ['Hourly or fixed monthly hours', 'Certified engineers (CEH, CompTIA, Cloud, Sophos)', 'Works inside your existing tools'],
        'flash' => $flash,
        'formAction' => $formAction,
        'old' => $old,
        'errors' => $errors,
    ]) ?>
  </div>
</section>

<?= $this->include('partials/related_services', ['services' => $services, 'related' => $service['related'] ?? []]) ?>
