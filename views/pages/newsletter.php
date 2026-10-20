<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?= $this->include('partials/page_header', ['eyebrow' => 'HaloSec updates', 'heading' => 'Security insights, occasionally', 'lead' => 'Subscribe to receive practical cybersecurity updates from our team.']) ?>
<section class="max-w-3xl mx-auto px-4 py-16">
  <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <?= $this->include('partials/field', ['name' => 'email', 'label' => 'Email address', 'type' => 'email', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
    <p class="text-xs text-slate-500">You can unsubscribe at any time.</p>
    <?= $this->include('partials/submit', ['btnLabel' => 'Subscribe']) ?>
    </form>
  </div>
</section>
