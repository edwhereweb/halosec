<?= $this->include('partials/page_header', ['eyebrow' => 'Talk to an expert', 'heading' => 'Need a Consultation', 'lead' => 'Tell us what you are trying to protect and a HaloSec specialist will connect with you.']) ?>
<section class="max-w-3xl mx-auto px-4 py-16">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-xl p-8 md:p-10">
    <div class="mb-6 pb-4 border-b border-slate-100">
      <span class="text-[11px] font-bold uppercase tracking-widest text-sky-700">Expert Guidance</span>
      <h2 class="text-2xl font-extrabold text-slate-900 mt-1">Book your security consultation</h2>
      <p class="text-sm text-slate-600 mt-1">Connect directly with our senior security architects to review your infrastructure, compliance posture and defense roadmap.</p>
    </div>

    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <div class="space-y-5">
      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', ['name' => 'topic', 'label' => 'Topic', 'type' => 'select', 'required' => true, 'options' => $config['consultation_topics'], 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', ['name' => 'preferred_time', 'label' => 'Preferred date / time', 'old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', ['name' => 'message', 'label' => 'Anything else we should know?', 'type' => 'textarea', 'old' => $old, 'errors' => $errors]) ?>
      <div class="pt-2">
        <?= $this->include('partials/submit', ['btnLabel' => 'Request consultation']) ?>
      </div>
    </div>
  </div>
</section>
