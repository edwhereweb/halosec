<?= $this->include('partials/page_header', ['eyebrow' => 'HaloSec updates', 'heading' => 'Security insights, occasionally', 'lead' => 'Practical cybersecurity updates from our team. No spam.']) ?>
<section class="bg-brand-mist px-4 py-20">
  <div class="card-form mx-auto max-w-xl">
    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
      <?= $this->include('partials/field', ['name' => 'email', 'label' => 'Email address', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/privacy_notice', ['kind' => 'newsletter']) ?>
    <?= $this->include('partials/submit', ['btnLabel' => 'Subscribe']) ?>
  </div>
</section>
