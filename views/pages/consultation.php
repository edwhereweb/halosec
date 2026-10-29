<?= $this->include('partials/page_header', ['eyebrow' => 'Talk to an expert', 'heading' => 'Book a cybersecurity consultation', 'lead' => 'Tell us what you’re trying to protect and a HaloSec specialist will connect with you.']) ?>
<section class="section bg-brand-mist">
  <div class="wrap flex flex-wrap gap-14">
    <div class="flex flex-[1_1_320px] flex-col gap-8">
      <h2 class="h-section">What you’ll get.</h2>
      <ol class="flex flex-col gap-6">
        <?php foreach ([
            ['A senior architect', 'Talk directly with someone who designs and runs security, not a salesperson.'],
            ['An honest read of your setup', 'Infrastructure, compliance posture and the gaps that matter most.'],
            ['A roadmap you can act on', 'Plain-language next steps, ranked by risk and budget.'],
        ] as $i => [$t, $d]) : ?>
        <li class="flex gap-5">
          <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-ink font-display font-bold text-white"><?= $i + 1 ?></span>
          <span class="flex flex-col gap-1"><span class="text-lg font-extrabold"><?= HaloSec\View::e($t) ?></span><span class="tile-text"><?= HaloSec\View::e($d) ?></span></span>
        </li>
        <?php endforeach; ?>
      </ol>
      <p class="text-[15px] text-brand-body">Prefer to look around first? Take the <a class="link" href="/free-audit">free cybersecurity audit</a>, browse our <a class="link" href="/services">cybersecurity services</a> or read our <a class="link" href="/guides">security guides</a>.</p>
    </div>
    <div class="card-form min-w-0 flex-[1_1_480px]">
      <div class="mb-6 flex flex-col gap-2">
        <h2 class="text-[28px] font-extrabold tracking-[-0.02em]">Request a consultation</h2>
        <p class="text-[15px] text-brand-body">We’ll confirm a time within one business day.</p>
      </div>
      <?= $this->include('partials/flash', ['flash' => $flash]) ?>
      <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
        <?= $this->include('partials/field', ['name' => 'topic', 'label' => 'What would you like to discuss?', 'type' => 'chips', 'required' => true, 'options' => $config['consultation_topics'], 'old' => $old, 'errors' => $errors]) ?>
        <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>
        <?= $this->include('partials/field', ['name' => 'preferred_time', 'label' => 'Preferred date / time', 'placeholder' => 'e.g. Tuesday afternoon', 'old' => $old, 'errors' => $errors]) ?>
        <?= $this->include('partials/field', ['name' => 'message', 'label' => 'Anything else we should know?', 'type' => 'textarea', 'old' => $old, 'errors' => $errors]) ?>
        <?= $this->include('partials/privacy_notice') ?>
      <?= $this->include('partials/submit', ['btnLabel' => 'Request consultation']) ?>
    </div>
  </div>
</section>
