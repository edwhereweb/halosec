<?php
/**
 * Privacy policy. DRAFT: replace every [TO COMPLETE] item, have it reviewed, then set
 * 'privacy_ready' => true in config/app.php so the page is indexed and linked from the footer.
 * The data practices below describe what this codebase does today; update them if that changes.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$email = (string) ($config['contact_email'] ?? 'hello@halosec.in');
$todo = static fn (string $what): string => '<mark class="rounded bg-amber-100 px-1 font-semibold text-amber-900">[TO COMPLETE: ' . HaloSec\View::e($what) . ']</mark>';
?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Legal', 'heading' => 'Privacy policy', 'lead' => 'How we handle the information you share with us.']) ?>
<section class="section">
  <div class="wrap max-w-3xl">
    <div class="flex flex-col gap-10 text-[17px] leading-relaxed text-brand-body [&_h2]:font-display [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:tracking-[-0.02em] [&_h2]:text-brand-ink [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-1.5">
      <p class="text-[15px] text-brand-muted">Last updated: <?= $todo('date') ?></p>

      <div class="flex flex-col gap-4">
        <h2>Who we are</h2>
        <p>This website is operated by <?= $todo('registered legal name of the company') ?>, <?= $todo('registered address') ?> (“HaloSec”, “we”, “us”). We are the data fiduciary for the personal data described here.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>What we collect</h2>
        <p>We only collect what you choose to send us through the forms on this site:</p>
        <ul>
          <li><strong>Contact details:</strong> name, work email, phone number and company name.</li>
          <li><strong>Free audit:</strong> company website, industry, number of employees and your answers to the audit questions.</li>
          <li><strong>Consultation, contact and service enquiries:</strong> the topic, your preferred time and your message.</li>
          <li><strong>Incident reports:</strong> the type of attack and the details you describe.</li>
          <li><strong>Newsletter:</strong> your email address.</li>
        </ul>
        <p>To protect the forms from abuse, if a submission fails repeatedly we keep a short-lived record of the IP address it came from. We do not store IP addresses with your enquiry.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>How we use it</h2>
        <ul>
          <li>To reply to your request, prepare audit reports and proposals, and respond to security incidents.</li>
          <li>To send the newsletter, if you subscribed. Every email has an unsubscribe link.</li>
          <li>To keep the site secure and prevent spam.</li>
        </ul>
        <p>We process this data with your consent, which you give by submitting a form. We do not sell your data or use it for advertising.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Cookies and third parties</h2>
        <ul>
          <li>We set one strictly necessary cookie, <code>halosec_session</code>, which keeps forms secure. It is deleted when you close your browser.</li>
          <li>We do not use analytics or advertising cookies.</li>
          <li>Fonts are loaded from Google Fonts, which receives your IP address when the page loads.</li>
          <li>The founder video on the home page is embedded from YouTube in privacy-enhanced mode (youtube-nocookie.com). YouTube may set cookies if you play it.</li>
          <li>Our website is hosted by <?= $todo('hosting provider, e.g. Hostinger, and server location') ?>.</li>
        </ul>
      </div>

      <div class="flex flex-col gap-4">
        <h2>How long we keep it</h2>
        <p>We keep enquiries for <?= $todo('retention period, e.g. 24 months after our last contact') ?>, unless you become a client, in which case your contract terms apply. Database backups are deleted automatically after 14 days.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>How we protect it</h2>
        <p>The site uses HTTPS everywhere. Submissions are stored in a private database that is not reachable from the web, and only authorised HaloSec staff can see them, through an admin area protected by strong passwords and two-factor authentication.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Your rights</h2>
        <p>Under India’s Digital Personal Data Protection Act, 2023 you can ask us to access, correct or erase your personal data, withdraw your consent at any time, and nominate someone to exercise these rights for you. Email <a class="link" href="mailto:<?= $e($email) ?>"><?= $e($email) ?></a> and we will respond within <?= $todo('response time, e.g. 30 days') ?>.</p>
        <p>Grievance officer: <?= $todo('name, email and phone of the person handling complaints') ?>. If you are not satisfied with our response, you can complain to the Data Protection Board of India.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Changes</h2>
        <p>We will post any changes on this page and update the date at the top.</p>
      </div>
    </div>
  </div>
</section>
