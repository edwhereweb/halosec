<?php
/**
 * Privacy policy covering India's DPDP Act 2023 / DPDP Rules 2025 and the EU/UK GDPR.
 * DRAFT: fill the [TO COMPLETE] items (most come from .env: BUSINESS_LEGAL_NAME, BUSINESS_*,
 * PRIVACY_EMAIL, GRIEVANCE_OFFICER_*, HOSTING_PROVIDER), have it reviewed, then set
 * 'privacy_ready' => true in config/app.php so the page is indexed and linked from the footer and forms.
 * The data practices below describe what this codebase does today; update them if that changes,
 * and bump config privacy.notice_version (it is also the "Last updated" date).
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$todo = static fn (string $what): string => '<mark class="rounded bg-amber-100 px-1 font-semibold text-amber-900">[TO COMPLETE: ' . HaloSec\View::e($what) . ']</mark>';
$privacy = is_array($config['privacy'] ?? null) ? $config['privacy'] : [];
$value = static fn (string $key, string $what): string => trim((string) ($privacy[$key] ?? '')) !== ''
    ? HaloSec\View::e($privacy[$key])
    : $todo($what);
$email = (string) ($privacy['email'] ?? $config['contact_email'] ?? 'hello@halosec.in');
$mail = '<a class="link" href="mailto:' . $e($email) . '">' . $e($email) . '</a>';
$address = implode(', ', array_filter(array_map('trim', array_map('strval', (array) ($config['address'] ?? [])))));
$retentionMonths = max(1, (int) round(((int) ($privacy['retention_days'] ?? 730)) / 30.4));
$backupDays = max(1, (int) (getenv('BACKUP_KEEP_DAYS') ?: 14));
$updated = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) ($privacy['notice_version'] ?? ''));
?>
<?= $this->include('partials/page_header', ['eyebrow' => 'Legal', 'heading' => 'Privacy policy', 'lead' => 'What we collect, why, how long we keep it, and how to get it corrected or deleted.']) ?>
<section class="section">
  <div class="wrap max-w-3xl">
    <div class="flex flex-col gap-10 text-[17px] leading-relaxed text-brand-body [&_h2]:font-display [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:tracking-[-0.02em] [&_h2]:text-brand-ink [&_h3]:font-bold [&_h3]:text-brand-ink [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-1.5">
      <p class="text-[15px] text-brand-muted">Last updated: <?= $updated !== false ? $e($updated->format('j F Y')) : $todo('date') ?></p>

      <div class="flex flex-col gap-4">
        <h2>In short</h2>
        <ul>
          <li>We only collect what you type into our forms, and only use it to answer you.</li>
          <li>No analytics, no advertising cookies, no selling or sharing for marketing.</li>
          <li>Enquiries are deleted automatically after <?= $e($retentionMonths) ?> months.</li>
          <li>Email <?= $mail ?> at any time to see, correct or delete your data.</li>
        </ul>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Who we are</h2>
        <p>This website is operated by <?= $value('legal_name', 'registered legal name of the company') ?>, <?= $address !== '' ? $e($address) : $todo('registered address') ?> (“HaloSec”, “we”, “us”). We decide how and why your personal data is used, which makes us the <strong>data fiduciary</strong> under India’s Digital Personal Data Protection Act, 2023 (DPDP Act) and the <strong>controller</strong> under the EU and UK General Data Protection Regulation (GDPR).</p>
        <p>For anything about your data, contact <?= $mail ?>.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>What we collect</h2>
        <p>Only what you choose to send through the forms on this site:</p>
        <ul>
          <li><strong>Contact details:</strong> name, work email, phone number and company name.</li>
          <li><strong>Free audit:</strong> company website, industry, number of employees and your answers to the audit questions.</li>
          <li><strong>Consultation, contact and service enquiries:</strong> the topic, your preferred time and your message.</li>
          <li><strong>Incident and security reports:</strong> the type of attack and the details you describe.</li>
          <li><strong>Newsletter:</strong> your email address.</li>
        </ul>
        <p>With each submission we also record when it arrived and which version of our privacy notice was shown to you, so we can show what you agreed to.</p>
        <p>To protect the forms from abuse, if a submission fails repeatedly we keep a short-lived record (under 24 hours) of the IP address it came from. Failed admin sign-ins are logged with a one-way hash of the IP address. We do not store IP addresses with your enquiry.</p>
        <p>Please don’t send us sensitive details we haven’t asked for, such as passwords, health or financial information.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Why we use it, and on what basis</h2>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[520px] border-collapse text-left text-[15px]">
            <thead>
              <tr class="border-b border-slate-200 text-brand-ink">
                <th class="py-2 pr-4 font-bold">Purpose</th>
                <th class="py-2 pr-4 font-bold">DPDP Act (India)</th>
                <th class="py-2 font-bold">GDPR (EU/UK)</th>
              </tr>
            </thead>
            <tbody class="[&_td]:border-b [&_td]:border-slate-100 [&_td]:py-2.5 [&_td]:pr-4 [&_td]:align-top">
              <tr><td>Replying to your enquiry, preparing your audit report or proposal</td><td>Your consent, given when you send the form, for this purpose only</td><td>Steps you asked for before a contract (Art. 6(1)(b))</td></tr>
              <tr><td>Responding to a security incident you report</td><td>Your consent, given when you send the form</td><td>Steps you asked for before a contract (Art. 6(1)(b)) and our legitimate interest in helping you contain the attack (Art. 6(1)(f))</td></tr>
              <tr><td>Sending the newsletter</td><td>Your consent</td><td>Your consent (Art. 6(1)(a))</td></tr>
              <tr><td>Keeping the site secure and stopping spam</td><td>Legitimate use and our security obligations under the DPDP Rules</td><td>Our legitimate interest in a secure service (Art. 6(1)(f))</td></tr>
            </tbody>
          </table>
        </div>
        <p>We do not sell your data, use it for advertising, or make automated decisions about you. Your audit score is calculated from your answers only to show you your results.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Who sees it</h2>
        <ul>
          <li>Authorised HaloSec staff who handle your request.</li>
          <li>Our hosting provider, <?= $value('hosting', 'hosting provider and server location, e.g. Hostinger, servers in India') ?>, which stores the website and its database for us under a data processing agreement.</li>
          <li><?= $todo('email provider used to reply to enquiries, e.g. Zoho Mail, and where it stores data') ?></li>
          <li>Authorities, if the law requires it.</li>
        </ul>
        <p>If you are in the EU or UK, your data is processed in India, which does not have an EU or UK adequacy decision. We protect it with the measures described on this page wherever it is stored.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Cookies and third parties</h2>
        <ul>
          <li>We set one strictly necessary cookie, <code>halosec_session</code>, which keeps forms secure. It is deleted when you close your browser. Because it is essential, we do not ask for consent for it.</li>
          <li>We do not use analytics, tracking or advertising cookies.</li>
          <li>Our fonts are served from our own server, so loading a page sends nothing to Google or other third parties.</li>
          <li>The founder video on the home page only loads from YouTube (in privacy-enhanced mode, youtube-nocookie.com) after you press play. From then on, YouTube may set cookies and receives your IP address, under <a class="link" href="https://policies.google.com/privacy" rel="noopener">Google’s privacy policy</a>.</li>
        </ul>
      </div>

      <div class="flex flex-col gap-4">
        <h2>How long we keep it</h2>
        <ul>
          <li><strong>Enquiries, audits and reports:</strong> deleted automatically <?= $e($retentionMonths) ?> months after they arrive, or earlier if you ask. If you become a client, your contract terms apply instead.</li>
          <li><strong>Newsletter:</strong> until you unsubscribe or ask us to delete it.</li>
          <li><strong>Backups:</strong> private database backups are deleted automatically after <?= $e($backupDays) ?> days, so deleted data disappears from them within that time.</li>
          <li><strong>Security logs:</strong> kept for at least one year, as the DPDP Rules require. They do not contain the contents of your enquiry.</li>
        </ul>
      </div>

      <div class="flex flex-col gap-4">
        <h2>How we protect it</h2>
        <p>The site uses HTTPS everywhere. Submissions are stored in a private database that is not reachable from the web. Only authorised HaloSec staff can see them, through an admin area protected by strong passwords, two-factor authentication and an audit log of every action.</p>
        <p>If a personal data breach affects you, we will tell you without delay, along with what happened and what you can do. We will also report it to the Data Protection Board of India and, where GDPR applies, to the relevant supervisory authority within 72 hours.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Your rights</h2>
        <div class="flex flex-col gap-2">
          <h3>Everyone</h3>
          <p>You can ask us to:</p>
          <ul>
            <li><strong>see</strong> a summary of the data we hold about you and who we shared it with, or get a copy in a machine-readable file;</li>
            <li><strong>correct, complete or update</strong> it;</li>
            <li><strong>delete</strong> it;</li>
            <li><strong>withdraw your consent</strong> at any time. It is as easy as giving it: use the unsubscribe link in any newsletter, or email us. Withdrawing does not affect what we did before.</li>
          </ul>
        </div>
        <div class="flex flex-col gap-2">
          <h3>In India (DPDP Act)</h3>
          <p>You can also nominate another person to exercise these rights if you die or become unable to, and raise a grievance with us. If you are not satisfied with our answer, you can complain to the Data Protection Board of India.</p>
        </div>
        <div class="flex flex-col gap-2">
          <h3>In the EU or UK (GDPR)</h3>
          <p>You can also ask us to restrict how we use your data, and object to uses based on our legitimate interests. You can complain to the data protection authority where you live or work (in the UK, the <a class="link" href="https://ico.org.uk/make-a-complaint/" rel="noopener">ICO</a>).</p>
        </div>
        <p>To use any of these rights, email <?= $mail ?> from the address you used with us. We may ask you to confirm your identity before acting. We will reply within one month.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Grievance officer</h2>
        <p>Name: <?= $value('officer_name', 'name of the person handling privacy complaints') ?><br>
          Email: <?= $mail ?><br>
          Phone: <?= $value('officer_phone', 'phone number') ?></p>
        <p>We will acknowledge a grievance promptly and resolve it within 90 days at the latest.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Children</h2>
        <p>Our services are for businesses and this site is not meant for anyone under 18. We do not knowingly collect data from children. If you believe a child has sent us their details, email us and we will delete them.</p>
      </div>

      <div class="flex flex-col gap-4">
        <h2>Changes</h2>
        <p>We will post any changes on this page and update the date at the top. If a change affects how we use data you have already given us, we will ask for your consent again where the law requires it.</p>
      </div>
    </div>
  </div>
</section>
