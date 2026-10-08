<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="bg-gradient-to-b from-red-700 to-red-600 pt-32 pb-16 md:pt-40 text-white text-center rounded-b-[40px] shadow-xl">
<div class="max-w-3xl mx-auto px-4">
<p class="inline-block bg-white/20 border border-white/40 text-xs font-bold uppercase tracking-widest px-3.5 py-1.5 rounded-full mb-4">Emergency response · 24/7</p>
<h1 class="text-4xl md:text-5xl font-extrabold">Under a Cyber Attack?</h1>
<p class="mt-4 text-red-50 text-lg">Disconnect affected machines from the network, do not pay or reply to attackers, and call us now.</p>
<a class="mt-7 inline-block bg-white text-red-700 font-extrabold text-xl px-9 py-4 rounded-full shadow-lg" href="tel:<?= $e($config['emergency_phone']) ?>">Call <?= $e($config['emergency_phone']) ?></a>
</div>
</section>
<section class="max-w-3xl mx-auto px-4 py-16">
<div class="rounded-3xl bg-white border-2 border-red-200 shadow-lg p-8">
<h2 class="text-xl font-bold text-slate-900">Report the incident</h2>
<p class="text-sm text-slate-600 mt-1">Can't call? Send the details and our response team is alerted immediately.</p>
<div class="mt-5">
<?= $this->include('partials/flash', ['flash' => $flash]) ?>
<?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
<?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors, 'phoneRequired' => true]) ?>
<?= $this->include('partials/field', ['name' => 'attack_type', 'label' => 'Type of attack', 'type' => 'select', 'required' => true, 'options' => $config['attack_types'], 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'message', 'label' => 'What is happening?', 'type' => 'textarea', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/submit', ['btnLabel' => 'Report attack now', 'btnClass' => 'bg-red-600 hover:bg-red-700 text-white']) ?>
</div>
</div>
</section>
