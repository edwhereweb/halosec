<?php
/**
 * Free cybersecurity audit. Without JavaScript this is one long form: every question as a card, then the
 * contact details. public/js/audit.js turns it into a one-question-at-a-time check-up with a live score,
 * instant results and then the contact form. Field names and validation are unchanged.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$questions = $audit['questions'] ?? [];
$details = $audit['details'] ?? [];
$answerOptions = $audit['options'] ?? [];
$oldAnswers = isset($old['answers']) && is_array($old['answers']) ? $old['answers'] : [];
$answersError = $errors['answers'] ?? null;
$total = count($questions);
// After a failed submit: go straight to the form if every question is answered, else to the first gap.
$start = 'q:0';
if ($errors !== [] || $flash !== null) {
    $i = 0;
    $start = 'details';
    foreach (array_keys($questions) as $qid) {
        if (!isset($oldAnswers[$qid])) {
            $start = 'q:' . $i;
            break;
        }
        $i++;
    }
}
$answerStyle = [
    'yes' => ['has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50', 'bg-emerald-100 text-emerald-800 group-has-[:checked]:bg-emerald-600 group-has-[:checked]:text-white', '<path d="m7 12.5 3.2 3.2L17 9"/>'],
    'partial' => ['has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50', 'bg-amber-100 text-amber-800 group-has-[:checked]:bg-amber-500 group-has-[:checked]:text-white', '<circle cx="12" cy="12" r="6.5"/><path d="M12 5.5a6.5 6.5 0 0 1 0 13z" fill="currentColor"/>'],
    'no' => ['has-[:checked]:border-brand-red has-[:checked]:bg-red-50', 'bg-red-100 text-red-800 group-has-[:checked]:bg-brand-red group-has-[:checked]:text-white', '<path d="m8 8 8 8M16 8l-8 8"/>'],
    'unsure' => ['has-[:checked]:border-slate-600 has-[:checked]:bg-slate-100', 'bg-slate-100 text-slate-700 group-has-[:checked]:bg-slate-600 group-has-[:checked]:text-white', '<path d="M9.5 9.5a2.6 2.6 0 1 1 3.7 2.4c-.8.4-1.2 1-1.2 1.8v.3M12 17.2v.1"/>'],
];
$areas = [];
foreach ($details as $d) {
    $areas[$d['area']] = true;
}
?>
<section class="relative overflow-hidden bg-white px-4 pb-14 pt-14 md:pb-20 md:pt-20">
  <?= $this->include('partials/halo', ['size' => 1000, 'class' => '-top-[340px]']) ?>
  <div class="relative mx-auto grid max-w-5xl items-center gap-10 md:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)]">
    <div class="flex animate-rise flex-col items-center gap-5 text-center md:items-start md:text-left">
      <span class="chip chip-lime">Free security check-up</span>
      <h1 class="display text-[44px] md:text-6xl"><span class="sr-only">Free cybersecurity audit: </span>How exposed is <span class="marker">your business?</span></h1>
      <p class="lead max-w-xl">Answer <?= $e($total) ?> quick questions, see your security score instantly, then get a plain-language report from certified Zoho &amp; Sophos architects.</p>
      <ul class="flex flex-wrap justify-center gap-2 text-[13px] font-bold text-brand-ink md:justify-start">
        <li class="flex items-center gap-1.5 rounded-full bg-brand-mist px-3 py-1.5"><span aria-hidden="true">⏱</span> About 3 minutes</li>
        <li class="flex items-center gap-1.5 rounded-full bg-brand-mist px-3 py-1.5"><span aria-hidden="true">◎</span> Instant score</li>
        <li class="flex items-center gap-1.5 rounded-full bg-brand-mist px-3 py-1.5"><span aria-hidden="true">✓</span> No obligation</li>
      </ul>
      <a href="#audit" data-audit-start class="btn btn-lg btn-cta mt-1">Start the check-up <span aria-hidden="true">→</span></a>
    </div>
    <div class="mx-auto w-full max-w-[380px] animate-float" aria-hidden="true">
      <?= $this->include('partials/audit_art', ['name' => 'hero']) ?>
    </div>
  </div>
</section>

<section id="audit" class="scroll-mt-20 bg-brand-mist px-3 pb-24 pt-6 sm:px-4 md:pt-16" data-audit data-start="<?= $e($start) ?>" data-total="<?= $e($total) ?>">
  <div class="mx-auto flex max-w-4xl flex-col gap-6">

    <div data-audit-header hidden class="sticky top-[68px] z-20 flex items-center gap-3 rounded-3xl bg-white/95 px-4 py-3 shadow-sm backdrop-blur sm:gap-6 sm:p-4 sm:px-6">
      <div class="min-w-0 flex-1">
        <div class="flex items-baseline justify-between gap-3">
          <p class="text-sm font-bold text-brand-ink" data-audit-step aria-live="polite">Question 1 of <?= $e($total) ?></p>
          <p class="hidden text-xs font-semibold text-brand-muted sm:block" data-audit-tip>Tip: press 1–4 to answer</p>
        </div>
        <ol class="mt-2.5 flex gap-1" aria-hidden="true">
          <?php for ($i = 0; $i < $total; $i++) : ?><li data-audit-dot class="h-2 flex-1 rounded-full bg-slate-200 transition-colors duration-300"></li><?php endfor; ?>
        </ol>
      </div>
      <div class="relative h-14 w-14 shrink-0 sm:h-[68px] sm:w-[68px]" data-audit-ring role="img" aria-label="Security score so far: not started">
        <svg viewBox="0 0 68 68" class="h-full w-full -rotate-90" aria-hidden="true">
          <circle cx="34" cy="34" r="28" fill="none" stroke="#e2e8f0" stroke-width="7"/>
          <circle cx="34" cy="34" r="28" fill="none" stroke="#0369a1" stroke-width="7" stroke-linecap="round" stroke-dasharray="175.9" stroke-dashoffset="175.9" data-ring-arc class="transition-[stroke-dashoffset,stroke] duration-700 ease-out"/>
        </svg>
        <span class="absolute inset-0 flex flex-col items-center justify-center leading-none"><span class="font-display text-lg font-bold text-brand-ink" data-ring-value>–</span><span class="mt-0.5 hidden text-[9px] font-bold uppercase tracking-wider text-brand-muted sm:block">score</span></span>
      </div>
    </div>

    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction, 'formAttrs' => 'data-audit-form']) ?>

      <?php if ($questions !== []) : ?>
      <div class="flex flex-col gap-5" data-audit-questions>
        <div class="noscript-intro rounded-3xl bg-white p-6 [.is-quiz_&]:hidden">
          <h2 class="text-2xl font-extrabold tracking-[-0.02em]">Your current setup</h2>
          <p class="mt-1 tile-text">“Not sure” is a perfectly good answer. It tells us where to look.</p>
        </div>
        <?php if ($answersError) : ?><p class="rounded-2xl bg-red-50 px-5 py-3 text-sm font-semibold text-brand-red" role="alert"><?= $e($answersError) ?></p><?php endif; ?>
        <?php $n = 0; foreach ($questions as $qid => $fullQuestion) : $n++; $d = $details[$qid] ?? ['area' => 'Security', 'title' => '', 'ask' => $fullQuestion, 'why' => '']; ?>
        <fieldset aria-labelledby="qt_<?= $e($qid) ?>" data-q="<?= $e($qid) ?>" data-title="<?= $e($d['title']) ?>" data-area="<?= $e($d['area']) ?>" class="audit-q grid overflow-hidden rounded-[28px] bg-white shadow-sm md:grid-cols-[300px_minmax(0,1fr)]">
          <div class="relative flex items-center justify-center bg-gradient-to-br from-sky-50 to-white px-4 pb-1 pt-3 md:p-8">
            <span class="absolute left-4 top-3 rounded-full bg-white px-3 py-1 text-xs font-bold text-brand-blue shadow-sm md:left-5 md:top-5"><?= $e($d['area']) ?></span>
            <div class="h-[96px] w-[128px] sm:h-[150px] sm:w-[200px] md:h-[180px] md:w-[240px]"><?= $this->include('partials/audit_art', ['name' => $qid]) ?></div>
          </div>
          <div class="flex flex-col gap-4 px-5 pb-5 pt-3 sm:gap-5 sm:p-8">
            <div>
              <span class="block text-[13px] font-bold text-brand-muted"><span class="[.is-quiz_&]:hidden"><?= $e($n) ?>/<?= $e($total) ?> · </span><?= $e($d['title']) ?></span>
              <span id="qt_<?= $e($qid) ?>" class="mt-1 block text-[20px] font-extrabold leading-snug tracking-[-0.02em] text-brand-ink sm:text-[26px]" data-q-text tabindex="-1" style="outline: none"><?= $e($d['ask']) ?></span>
            </div>
            <?php if ($d['why'] !== '') : ?>
            <p class="-mt-1 flex items-start gap-2 text-[14px] leading-relaxed text-brand-body sm:-mt-2 sm:text-[15px]"><span aria-hidden="true" class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-cta text-[11px] font-black">i</span><?= $e($d['why']) ?></p>
            <?php endif; ?>
            <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
              <?php $k = 0; foreach ($answerOptions as $value => $optLabel) : $k++; $style = $answerStyle[$value] ?? $answerStyle['unsure']; ?>
              <label class="group relative flex min-h-[56px] cursor-pointer items-center gap-2.5 rounded-2xl border-[1.5px] border-slate-200 bg-white px-3.5 py-3 text-[15px] font-bold text-brand-ink transition hover:-translate-y-0.5 hover:border-slate-400 hover:shadow-md has-[:focus-visible]:ring-4 has-[:focus-visible]:ring-sky-700/30 <?= $style[0] ?>">
                <input class="sr-only" type="radio" name="answers[<?= $e($qid) ?>]" value="<?= $e($value) ?>" <?= ($oldAnswers[$qid] ?? null) === $value ? 'checked' : '' ?>>
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full transition <?= $style[1] ?>" aria-hidden="true">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><?= $style[2] ?></svg>
                </span>
                <span class="flex-1"><?= $e($optLabel) ?></span>
                <kbd class="hidden rounded-md border border-slate-200 px-1.5 text-[11px] font-semibold text-slate-400 sm:[.is-quiz_&]:inline" aria-hidden="true"><?= $e($k) ?></kbd>
              </label>
              <?php endforeach; ?>
            </div>
            <p data-feedback aria-live="polite" class="min-h-[24px] text-[15px] font-semibold text-brand-ink"></p>
            <div class="hidden items-center justify-between [.is-quiz_&]:flex">
              <button type="button" data-audit-back class="inline-flex items-center gap-1 rounded-full px-3 py-2 text-sm font-bold text-brand-muted hover:bg-brand-mist hover:text-brand-ink disabled:invisible">← Back</button>
              <button type="button" data-audit-skip class="rounded-full px-3 py-2 text-sm font-bold text-brand-blue hover:bg-brand-mist">Next →</button>
            </div>
          </div>
        </fieldset>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <section data-audit-results hidden aria-labelledby="results-title" class="overflow-hidden rounded-[28px] bg-white shadow-sm">
        <div class="grid items-center gap-8 bg-gradient-to-br from-sky-50 via-white to-lime-50 p-6 sm:p-10 md:grid-cols-[220px_minmax(0,1fr)]">
          <div class="relative mx-auto h-[200px] w-[200px]" data-result-ring>
            <svg viewBox="0 0 200 200" class="h-full w-full -rotate-90" aria-hidden="true">
              <circle cx="100" cy="100" r="84" fill="none" stroke="#e2e8f0" stroke-width="16"/>
              <circle cx="100" cy="100" r="84" fill="none" stroke="#0369a1" stroke-width="16" stroke-linecap="round" stroke-dasharray="527.8" stroke-dashoffset="527.8" data-result-arc class="transition-[stroke-dashoffset] duration-[1400ms] ease-out"/>
            </svg>
            <span class="absolute inset-0 flex flex-col items-center justify-center"><span class="font-display text-6xl font-bold tracking-tight text-brand-ink" data-result-score>0</span><span class="text-sm font-bold text-brand-muted">out of 100</span></span>
          </div>
          <div class="flex flex-col gap-3 text-center md:text-left">
            <span data-result-band class="mx-auto w-fit rounded-full px-3 py-1 text-sm font-bold md:mx-0"></span>
            <h2 id="results-title" class="text-[28px] font-extrabold leading-tight tracking-[-0.02em] sm:text-[34px]" data-result-title tabindex="-1" style="outline: none">Your security score</h2>
            <p class="tile-text" data-result-text></p>
          </div>
        </div>
        <div class="grid gap-6 p-6 sm:p-10 md:grid-cols-2">
          <div>
            <h3 class="flex items-center gap-2 text-base font-extrabold"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white" aria-hidden="true"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m6 12.5 4 4L18 8"/></svg></span>What you’re doing well</h3>
            <ul class="mt-3 flex flex-col gap-2" data-result-good></ul>
          </div>
          <div>
            <h3 class="flex items-center gap-2 text-base font-extrabold"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-red text-white" aria-hidden="true"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" viewBox="0 0 24 24"><path d="M12 7v6M12 17v.5"/></svg></span>Where you’re exposed</h3>
            <ul class="mt-3 flex flex-col gap-2" data-result-gaps></ul>
          </div>
        </div>
        <div class="flex flex-col items-center gap-3 border-t border-slate-100 bg-brand-ink px-6 py-8 text-center sm:px-10 md:flex-row md:text-left">
          <div class="flex-1">
            <p class="text-xl font-extrabold text-white">Get your full report, free</p>
            <p class="mt-1 text-[15px] text-slate-300">A certified architect reviews your answers and sends the fixes that matter most, in priority order.</p>
          </div>
          <div class="flex flex-col items-center gap-2 sm:flex-row">
            <button type="button" data-audit-retake class="rounded-full px-4 py-3 text-sm font-bold text-slate-300 hover:text-white">Change answers</button>
            <button type="button" data-audit-to-form class="btn btn-lg btn-cta">Get my report <span aria-hidden="true">→</span></button>
          </div>
        </div>
      </section>

      <fieldset data-details class="card-form flex min-w-0 flex-col gap-6">
        <legend class="contents">
          <span class="block text-[28px] font-extrabold leading-tight tracking-[-0.02em]" data-details-title tabindex="-1" style="outline: none">Where should we send your report?</span>
        </legend>
        <p class="-mt-3 tile-text">Free, within one business day. We never share your answers.</p>
        <div class="grid gap-5 sm:grid-cols-2">
          <?= $this->include('partials/field', ['name' => 'name', 'label' => 'Your name', 'required' => true, 'autocomplete' => 'name', 'old' => $old, 'errors' => $errors]) ?>
          <?= $this->include('partials/field', ['name' => 'email', 'label' => 'Work email', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'old' => $old, 'errors' => $errors]) ?>
          <?= $this->include('partials/field', ['name' => 'company', 'label' => 'Company name', 'required' => true, 'autocomplete' => 'organization', 'old' => $old, 'errors' => $errors]) ?>
          <?= $this->include('partials/field', ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'autocomplete' => 'tel', 'old' => $old, 'errors' => $errors]) ?>
        </div>
        <?= $this->include('partials/field', ['name' => 'employee_scale', 'label' => 'How many people work there?', 'type' => 'chips', 'required' => true, 'options' => $config['employee_scales'], 'old' => $old, 'errors' => $errors]) ?>
        <details class="group rounded-2xl border border-slate-200 px-5 py-4" <?= !empty($old['industry']) || !empty($old['company_website']) || !empty($old['notes']) ? 'open' : '' ?>>
          <summary class="cursor-pointer list-none text-[15px] font-bold text-brand-ink"><span class="mr-1 inline-block transition group-open:rotate-90" aria-hidden="true">›</span> Add more detail <span class="font-medium text-brand-muted">(optional, sharpens your report)</span></summary>
          <div class="mt-5 flex flex-col gap-5">
            <div class="grid gap-5 sm:grid-cols-2">
              <?= $this->include('partials/field', ['name' => 'company_website', 'label' => 'Website', 'placeholder' => 'example.com', 'old' => $old, 'errors' => $errors]) ?>
              <?= $this->include('partials/field', ['name' => 'industry', 'label' => 'Industry', 'placeholder' => 'e.g. Healthcare', 'old' => $old, 'errors' => $errors]) ?>
            </div>
            <?= $this->include('partials/field', ['name' => 'notes', 'label' => 'Anything else we should know?', 'type' => 'textarea', 'old' => $old, 'errors' => $errors]) ?>
          </div>
        </details>
        <?= $this->include('partials/privacy_notice', ['kind' => 'audit']) ?>
        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
          <button type="button" data-audit-to-results class="btn hidden text-brand-blue hover:bg-brand-mist [.is-quiz_&]:inline-flex">← Back to my score</button>
          <button type="submit" class="btn btn-lg btn-cta sm:min-w-[260px]">
            Send me my report
            <svg class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
          </button>
        </div>
      </fieldset>
    </form>

    <p class="text-center text-[13px] text-brand-muted">Reviewed by certified Zoho &amp; Sophos security architects. Your answers stay private.</p>
  </div>
</section>
<script src="/js/audit.js?v=<?= $e((string) @filemtime(dirname(__DIR__, 2) . '/public/js/audit.js')) ?>" defer></script>
