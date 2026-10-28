<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="mx-auto w-full max-w-5xl space-y-6">
  <div><h1 class="text-[28px] font-bold tracking-tight text-slate-900">My account</h1><p class="mt-1 text-[15px] text-slate-600">Signed in as <strong class="font-semibold text-slate-900"><?= $e($username) ?></strong>. Manage your password and two-factor sign-in.</p></div>
  <?php if ($requireTwoFactor && !$hasTwoFactor) : ?>
  <p role="alert" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900">Two-factor authentication is required for admin accounts. Set it up below to continue to the dashboard.</p>
  <?php endif; ?>
  <?php if ($message !== null) : ?>
  <p role="<?= $messageIsError ? 'alert' : 'status' ?>" class="rounded-xl p-3 text-sm <?= $messageIsError ? 'bg-red-50 text-red-800' : 'bg-sky-50 text-sky-900' ?>"><?= $e($message) ?></p>
  <?php endif; ?>

  <div class="grid gap-6 lg:grid-cols-2">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-1 font-bold text-slate-900">Two-factor authentication</h2>
      <?php if ($hasTwoFactor) : ?>
        <p class="mb-4 text-sm text-slate-600"><span class="font-semibold text-emerald-700">On.</span> You'll be asked for a code from your authenticator app each time you sign in.</p>
        <?php if (!$requireTwoFactor) : ?>
        <form method="post" action="/admin/account" class="space-y-3 border-t border-slate-100 pt-4">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>"><input type="hidden" name="action" value="2fa_disable">
          <label for="disable_password" class="block text-sm font-semibold text-slate-700">Current password</label>
          <input id="disable_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-xl border-slate-300 text-sm">
          <label for="disable_code" class="block text-sm font-semibold text-slate-700">Current code</label>
          <input id="disable_code" name="code" inputmode="numeric" maxlength="7" required autocomplete="one-time-code" class="w-full rounded-xl border-slate-300 text-sm">
          <button class="rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50" type="submit">Turn off 2FA</button>
        </form>
        <?php endif; ?>
      <?php elseif ($enrollSecret !== null) : ?>
        <ol class="mb-4 list-decimal space-y-3 pl-5 text-sm text-slate-700">
          <li>Open an authenticator app (Google Authenticator, Microsoft Authenticator, 1Password…) and tap <em>+</em> / <em>Add account</em>.</li>
          <li>
            <span class="font-semibold">Scan this QR code</span> with the app.
            <div data-otpauth="<?= $e($enrollUri) ?>" hidden class="my-3 h-48 w-48 rounded-xl border border-slate-200 bg-white p-2"></div>
            <details data-otpauth-fallback open class="mt-2 rounded-lg border border-slate-200 p-3">
              <summary class="cursor-pointer font-semibold text-slate-800">Can’t scan? Enter the key instead</summary>
              <p class="mt-2">Choose <em>Enter a setup key</em>. Account name: <strong><?= $e($username) ?></strong>. Key (time-based, 6 digits):</p>
              <code class="mt-1 block select-all break-all rounded-lg bg-slate-100 p-3 font-mono text-base tracking-wider text-slate-900"><?= $e(trim(chunk_split($enrollSecret, 4, ' '))) ?></code>
              <p class="mt-2 text-xs text-slate-500">On this phone? <a class="text-sky-700 underline" href="<?= $e($enrollUri) ?>">Open the setup link</a> instead.</p>
            </details>
          </li>
          <li>Enter the 6-digit code the app shows to finish.</li>
        </ol>
        <script src="/js/vendor/qrcode.js?v=1.4.4" defer></script>
        <script src="/js/admin-2fa.js?v=<?= $e((string) @filemtime(dirname(__DIR__, 2) . '/public/js/admin-2fa.js')) ?>" defer></script>
        <form method="post" action="/admin/account" class="flex flex-wrap items-end gap-3">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>"><input type="hidden" name="action" value="2fa_confirm">
          <div><label for="confirm_code" class="mb-1 block text-sm font-semibold text-slate-700">Code</label>
          <input id="confirm_code" name="code" inputmode="numeric" maxlength="7" required autocomplete="one-time-code" class="w-40 rounded-xl border-slate-300 text-center text-lg tracking-widest"></div>
          <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Confirm</button>
        </form>
      <?php else : ?>
        <p class="mb-4 text-sm text-slate-600"><span class="font-semibold text-amber-700">Off.</span> Add a second step to your sign-in so a stolen password isn't enough.</p>
        <form method="post" action="/admin/account">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>"><input type="hidden" name="action" value="2fa_start">
          <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Set up 2FA</button>
        </form>
      <?php endif; ?>
    </section>

    <form method="post" action="/admin/account" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
      <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>"><input type="hidden" name="action" value="password">
      <h2 class="mb-1 font-bold text-slate-900">Change password</h2>
      <label for="current_password" class="block text-sm font-semibold text-slate-700">Current password</label>
      <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-xl border-slate-300 text-sm">
      <label for="new_password" class="block text-sm font-semibold text-slate-700">New password (12+ characters)</label>
      <input id="new_password" name="new_password" type="password" required minlength="12" maxlength="1024" autocomplete="new-password" class="w-full rounded-xl border-slate-300 text-sm">
      <label for="confirm_password" class="block text-sm font-semibold text-slate-700">Repeat new password</label>
      <input id="confirm_password" name="confirm_password" type="password" required minlength="12" maxlength="1024" autocomplete="new-password" class="w-full rounded-xl border-slate-300 text-sm">
      <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Change password</button>
    </form>
  </div>
</section>
