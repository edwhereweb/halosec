<form method="post" action="<?= HaloSec\View::e($formAction) ?>" class="space-y-6" novalidate<?= !empty($formAttrs) ? ' ' . $formAttrs : '' ?>>
<input type="hidden" name="_csrf" value="<?= HaloSec\View::e($csrfToken) ?>">
<div class="hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="<?= HaloSec\View::e($honeypot) ?>" tabindex="-1" autocomplete="off"></label></div>
