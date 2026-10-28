<?= $this->include('partials/page_header', ['eyebrow' => 'Staff only', 'heading' => 'Admin login', 'lead' => 'Sign in to review incoming leads.']) ?>
<section class="px-4 pb-24">
  <div class="card-form mx-auto max-w-md">
    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
      <?= $this->include('partials/field', ['name' => 'username', 'label' => 'Username', 'required' => true, 'autocomplete' => 'username', 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true, 'autocomplete' => 'current-password', 'old' => [], 'errors' => $errors]) ?>
    <?= $this->include('partials/submit', ['btnLabel' => 'Sign in', 'btnClass' => 'btn-dark']) ?>
  </div>
</section>
