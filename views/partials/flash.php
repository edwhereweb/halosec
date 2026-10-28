<?php if (!empty($flash)) : ?>
<div id="form-status" role="<?= $flash['type'] === 'error' ? 'alert' : 'status' ?>" class="mb-6 flex items-start gap-3 rounded-2xl px-5 py-4 text-[15px] font-semibold <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-800' : 'bg-emerald-50 text-emerald-800' ?>">
<span aria-hidden="true" class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs text-white <?= $flash['type'] === 'error' ? 'bg-brand-red' : 'bg-emerald-600' ?>"><?= $flash['type'] === 'error' ? '!' : '✓' ?></span>
<span><?= HaloSec\View::e($flash['message']) ?></span>
</div>
<?php endif; ?>
