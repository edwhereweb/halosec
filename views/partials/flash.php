<?php if (!empty($flash)) : ?>
<div id="form-status" role="<?= $flash['type'] === 'error' ? 'alert' : 'status' ?>" class="mb-6 rounded-2xl border px-5 py-4 text-sm font-semibold <?= $flash['type'] === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800' ?>">
<?= HaloSec\View::e($flash['message']) ?>
</div>
<?php endif; ?>
