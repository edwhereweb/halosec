<?php
$defaultClass = 'bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 border border-lime-200/50';
$class = !empty($btnClass) ? $btnClass : $defaultClass;
?>
<button type="submit" class="<?= HaloSec\View::e($class) ?> font-extrabold text-xs uppercase tracking-wider px-8 py-3.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 inline-flex items-center justify-center gap-2">
<span><?= HaloSec\View::e($btnLabel ?? 'Submit') ?></span>
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><line x1="7" x2="17" y1="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
</button>
</form>
