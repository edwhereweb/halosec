<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$phone = (string) ($config['emergency_phone'] ?? '+917356543520');
?>
<section class="bg-red-50 px-4 pb-16 pt-16 md:pb-20 md:pt-24">
  <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-12">
    <div class="flex min-w-0 flex-[999_1_480px] animate-rise flex-col gap-5">
      <span class="chip chip-red w-fit bg-white">
        <span class="dot-live"><span class="ping bg-red-500"></span><span class="relative h-2 w-2 rounded-full bg-red-600"></span></span>
        Emergency · Responders on duty 24/7
      </span>
      <h1 class="display text-[52px] md:text-7xl lg:text-[80px]"><span class="sr-only">Cyber attack incident response: </span>Under attack? <br>We’re here.</h1>
      <p class="lead max-w-xl">Call first. A responder picks up, triages with you and starts isolating the threat.</p>
    </div>
    <div class="flex flex-[1_1_340px] flex-col gap-3">
      <a class="flex items-center gap-4 rounded-3xl bg-brand-red px-7 py-6 text-white no-underline shadow-[0_20px_40px_-20px_rgba(180,35,24,.6)] transition hover:bg-red-800" href="tel:<?= $e($phone) ?>">
        <span class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white/20">
          <svg class="h-6 w-6" aria-hidden="true" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg>
        </span>
        <span class="flex flex-col">
          <span class="text-sm font-semibold text-red-100">Call the emergency line</span>
          <span class="font-display text-[28px] font-bold tracking-[-0.02em]"><?= $e($phone) ?></span>
        </span>
      </a>
      <a class="btn btn-lg border-[1.5px] border-brand-red bg-white text-brand-red hover:bg-red-50" href="#report">Can’t call? Report online</a>
    </div>
  </div>
</section>

<section aria-labelledby="now" class="wrap py-16 md:py-20">
  <h2 id="now" class="mb-8 text-[28px] font-extrabold tracking-[-0.02em]">While you wait, do these three things.</h2>
  <ol class="grid gap-4 md:grid-cols-3">
    <?php foreach ([
        ['Disconnect', 'Unplug affected machines from the network and Wi-Fi. Don’t switch them off.'],
        ['Don’t pay', 'Don’t pay a ransom or reply to the attackers.'],
        ['Write it down', 'Note what you saw and when. Photograph any ransom screen.'],
    ] as $i => [$t, $d]) : ?>
    <li class="flex gap-4 rounded-3xl bg-brand-mist p-7">
      <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-ink font-display font-bold text-white"><?= $i + 1 ?></span>
      <span class="flex flex-col gap-1.5"><span class="text-lg font-extrabold"><?= $e($t) ?></span><span class="tile-text"><?= $e($d) ?></span></span>
    </li>
    <?php endforeach; ?>
  </ol>
</section>

<section id="report" class="px-4 pb-24">
  <div class="card-form mx-auto max-w-3xl">
    <div class="mb-6 flex flex-col gap-2">
      <h2 class="text-[28px] font-extrabold tracking-[-0.02em]">Report the incident</h2>
      <p class="text-[15px] text-brand-body">This alerts our on-duty incident command right away. Only the essentials.</p>
    </div>
    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
      <?= $this->include('partials/field', ['name' => 'attack_type', 'label' => 'What’s happening?', 'type' => 'chips', 'chipClass' => 'opt-danger', 'required' => true, 'options' => $config['attack_types'], 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'phoneRequired' => true]) ?>
      <?= $this->include('partials/field', ['name' => 'message', 'label' => 'What do you see?', 'hint' => 'A sentence or two is enough.', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
    <?= $this->include('partials/submit', ['btnLabel' => 'Send to incident command', 'btnClass' => 'btn-danger']) ?>
  </div>
</section>
