<?php
/** Submit button that also closes the form opened by partials/form_open. Optional: $btnLabel, $btnClass (e.g. 'btn-danger'). */
$class = !empty($btnClass) ? $btnClass : 'btn-cta';
?>
<button type="submit" class="btn btn-lg w-full <?= HaloSec\View::e($class) ?>">
<span><?= HaloSec\View::e($btnLabel ?? 'Submit') ?></span>
<svg class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</button>
</form>
