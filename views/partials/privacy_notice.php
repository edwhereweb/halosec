<?php
/**
 * Short privacy notice shown at the point of collection (GDPR Art. 13, DPDP Act s.5).
 * $kind: 'enquiry' (default), 'audit', 'emergency' or 'newsletter'. Optional $class.
 * Bump config privacy.notice_version when this wording changes.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$privacy = is_array($config['privacy'] ?? null) ? $config['privacy'] : [];
$email = (string) ($privacy['email'] ?? $config['contact_email'] ?? 'hello@halosec.in');
$months = max(1, (int) round(((int) ($privacy['retention_days'] ?? 730)) / 30.4));
$purpose = match ($kind ?? 'enquiry') {
    'audit' => 'to prepare your audit report and talk to you about it',
    'emergency' => 'to respond to this incident and contact you about it',
    'newsletter' => 'only to send you HaloSec security updates. Every email has an unsubscribe link',
    default => 'only to reply to this request',
};
?>
<p class="text-[13px] leading-relaxed text-brand-muted <?= $e($class ?? '') ?>">
  We use these details <?= $e($purpose) ?>. We keep them for up to <?= $e($months) ?> months, never sell them and never use them for advertising.
  You can ask to see, correct or delete them at any time: <a class="link" href="mailto:<?= $e($email) ?>"><?= $e($email) ?></a><?php if (!empty($config['privacy_ready'])) : ?> · <a class="link" href="/privacy">Privacy policy</a><?php endif; ?>.
</p>
