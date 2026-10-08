<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<section class="sky-hero-backdrop pt-32 pb-24 md:pt-40 md:pb-28 rounded-b-[40px] shadow-xl">
<div class="cloud-layer"></div>
<div class="max-w-4xl mx-auto px-4 text-center relative z-10">
<?php if (!empty($eyebrow)) : ?><p class="inline-block bg-white/20 border border-white/40 text-white text-xs font-semibold px-3.5 py-1.5 rounded-full mb-4"><?= $e($eyebrow) ?></p><?php endif; ?>
<h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight leading-tight"><?= $e($heading) ?></h1>
<?php if (!empty($lead)) : ?><p class="mt-5 text-base md:text-lg text-sky-50 max-w-2xl mx-auto"><?= $e($lead) ?></p><?php endif; ?>
</div>
</section>
