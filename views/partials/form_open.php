<?php
// WebMCP: public lead forms are offered to in-browser agents as tools; the visitor still presses submit.
$tool = match (true) {
    $formAction === '/free-audit' => ['request_free_security_audit', 'Request a free cybersecurity audit from HaloSec: answer questions about the business setup and leave contact details for a written report.'],
    $formAction === '/consultation' => ['book_security_consultation', 'Book a consultation with a HaloSec security architect about infrastructure, compliance or a security roadmap.'],
    $formAction === '/under-attack' => ['report_cyber_attack', 'Report an active cyber attack (ransomware, phishing, data breach) to HaloSec’s 24/7 incident response team.'],
    $formAction === '/contact' => ['contact_halosec', 'Send a general enquiry or partnership message to HaloSec.'],
    str_starts_with($formAction, '/services/') => ['enquire_about_security_service', 'Ask HaloSec for a quote or details about the cybersecurity service on this page.'],
    default => null,
};
?>
<form method="post" action="<?= HaloSec\View::e($formAction) ?>" class="space-y-6" novalidate<?= $tool !== null ? ' toolname="' . HaloSec\View::e($tool[0]) . '" tooldescription="' . HaloSec\View::e($tool[1]) . '"' : '' ?><?= !empty($formAttrs) ? ' ' . $formAttrs : '' ?>>
<input type="hidden" name="_csrf" value="<?= HaloSec\View::e($csrfToken) ?>">
<div class="hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="<?= HaloSec\View::e($honeypot) ?>" tabindex="-1" autocomplete="off"></label></div>
