<?= $this->include('partials/page_header', ['eyebrow' => '500 Error', 'heading' => 'System Temporarily Unavailable', 'lead' => 'We are experiencing a temporary issue. Our security operations center has been alerted.']) ?>
<section class="max-w-xl mx-auto px-4 py-16 text-center">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-xl p-10 text-center">
    <p class="text-slate-600 text-sm mb-6">If you are currently experiencing an urgent cyber incident or breach, please contact our Emergency SOC hotline immediately.</p>
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
      <a class="bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-7 py-3.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-2 border border-lime-200/50" href="/">
        <span>Back to Homepage</span>
        <span>→</span>
      </a>
      <a class="bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs uppercase px-6 py-3.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-2" href="tel:<?= HaloSec\View::e($config['emergency_phone'] ?? '+917356543520') ?>">
        <span>Call Emergency SOC</span>
      </a>
    </div>
  </div>
</section>
