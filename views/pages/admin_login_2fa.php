<?= $this->include('partials/page_header', ['eyebrow' => 'Staff only', 'heading' => 'Two-factor verification', 'lead' => 'Enter the 6-digit code from your authenticator app.']) ?>
<section class="max-w-md mx-auto px-4 py-16">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-xl p-8">
    <div class="flex flex-col items-center justify-center mb-6 text-center">
      <?= $this->include('partials/brand_logo', ['variant' => 'light', 'size' => 'lg', 'showTagline' => true]) ?>
    </div>
    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <form method="post" action="<?= HaloSec\View::e($formAction) ?>" class="space-y-5">
      <input type="hidden" name="_csrf" value="<?= HaloSec\View::e($csrfToken) ?>">
      <div>
        <label for="code" class="block text-sm font-semibold text-slate-700 mb-1.5">Authentication code</label>
        <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9 ]{6,7}" maxlength="7" autocomplete="one-time-code" required autofocus
          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-2xl tracking-[0.4em] font-display focus:border-sky-500 focus:ring-sky-500">
      </div>
      <div class="pt-2"><?= $this->include('partials/submit', ['btnLabel' => 'Verify']) ?></div>
      <p class="text-center text-xs text-slate-500">Lost your device? Ask a super administrator to run <code>php bin/admin.php 2fa-reset &lt;username&gt;</code>.</p>
    </form>
  </div>
</section>
