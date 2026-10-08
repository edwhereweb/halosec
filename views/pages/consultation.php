<?= $this->include('partials/page_header', ['eyebrow' => 'Talk to an expert', 'heading' => 'Need a Consultation', 'lead' => 'Tell us what you are trying to protect and a HaloSec specialist will connect with you.']) ?>
<section class="max-w-3xl mx-auto px-4 py-16">
<div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-8">
<?= $this->include('partials/flash', ['flash' => $flash]) ?>
<?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
<?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'topic', 'label' => 'Topic', 'type' => 'select', 'required' => true, 'options' => $config['consultation_topics'], 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'preferred_time', 'label' => 'Preferred date / time', 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'message', 'label' => 'Anything else we should know?', 'type' => 'textarea', 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/submit', ['btnLabel' => 'Request consultation', 'btnClass' => 'bg-sky-600 hover:bg-sky-700 text-white']) ?>
</div>
</section>
