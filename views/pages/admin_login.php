<?= $this->include('partials/page_header', ['eyebrow' => 'Staff only', 'heading' => 'Admin login', 'lead' => 'Sign in to review incoming leads.']) ?>
<section class="max-w-md mx-auto px-4 py-16">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-xl p-8">
    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <div class="space-y-5">
      <?= $this->include('partials/field', ['name' => 'username', 'label' => 'Username', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true, 'old' => [], 'errors' => $errors]) ?>
      <div class="pt-2"><?= $this->include('partials/submit', ['btnLabel' => 'Sign in']) ?></div>
    </div>
  </div>
</section>
