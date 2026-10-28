<?= $this->include('partials/page_header', ['eyebrow' => 'Staff only', 'heading' => 'Two-factor verification', 'lead' => 'Enter the 6-digit code from your authenticator app.']) ?>
<section class="px-4 pb-24">
  <div class="card-form mx-auto max-w-md">
    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <form method="post" action="<?= HaloSec\View::e($formAction) ?>" class="space-y-6">
      <input type="hidden" name="_csrf" value="<?= HaloSec\View::e($csrfToken) ?>">
      <div>
        <label for="code" class="label">Authentication code</label>
        <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9 ]{6,7}" maxlength="7" autocomplete="one-time-code" required autofocus
          class="input text-center font-display text-2xl tracking-[0.4em]">
      </div>
      <button type="submit" class="btn btn-lg btn-dark w-full">Verify</button>
      <p class="text-center text-xs text-brand-muted">Lost your device? Ask a super administrator to run <code>php bin/admin.php 2fa-reset &lt;username&gt;</code>.</p>
    </form>
  </div>
</section>
