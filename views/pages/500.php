<?= $this->include('partials/page_header', ['eyebrow' => '500', 'heading' => 'Something went wrong on our side.', 'lead' => 'We’re having a temporary issue and our team has been alerted. If you’re dealing with a cyber incident right now, call us.']) ?>
<section class="px-4 pb-28 text-center">
  <div class="flex flex-wrap justify-center gap-3">
    <a class="btn btn-lg btn-danger" href="tel:<?= HaloSec\View::e($config['emergency_phone'] ?? '+917356543520') ?>">Call the emergency line</a>
    <a class="btn btn-lg btn-outline" href="/">Back to home</a>
  </div>
</section>
