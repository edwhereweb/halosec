<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$phone = (string) ($config['emergency_phone'] ?? '+917356543520');
?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Get in touch', 'heading' => 'Contact HaloSec', 'lead' => 'Questions, partnerships or general enquiries. We’d love to hear from you.']) ?>
<section class="section bg-brand-mist">
  <div class="wrap flex flex-wrap gap-10">
    <div class="flex flex-[1_1_300px] flex-col gap-4">
      <a class="tile tile-white tile-hover gap-2" href="mailto:<?= $e($config['contact_email'] ?? 'hello@halosec.in') ?>">
        <span class="text-[13px] font-bold text-brand-muted">Email us</span>
        <span class="text-xl font-extrabold break-all"><?= $e($config['contact_email'] ?? 'hello@halosec.in') ?></span>
      </a>
      <a class="tile tile-hover gap-2 bg-brand-red text-white" href="tel:<?= $e($phone) ?>">
        <span class="text-[13px] font-bold text-red-100">Emergency Hotline · 24/7</span>
        <span class="font-display text-2xl font-bold"><?= $e($phone) ?></span>
      </a>
      <a class="tile tile-lime tile-hover gap-2" href="/free-audit">
        <span class="text-[13px] font-bold text-lime-800">Free</span>
        <span class="text-xl font-extrabold">Check how exposed you are →</span>
      </a>
    </div>
    <div class="card-form min-w-0 flex-[2_1_480px]">
      <div class="mb-6 flex flex-col gap-2">
        <h2 class="text-[28px] font-extrabold tracking-[-0.02em]">Send us a message</h2>
        <p class="text-[15px] text-brand-body">Our advisory team reads every message and replies promptly.</p>
      </div>
      <?= $this->include('partials/flash', ['flash' => $flash]) ?>
      <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
        <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'showCompany' => false]) ?>
        <?= $this->include('partials/field', ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
        <?= $this->include('partials/privacy_notice') ?>
      <?= $this->include('partials/submit', ['btnLabel' => 'Send message']) ?>
    </div>
  </div>
</section>
