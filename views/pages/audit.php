<?php
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$questions = $audit['questions'] ?? [];
$answerOptions = $audit['options'] ?? [];
$oldAnswers = isset($old['answers']) && is_array($old['answers']) ? $old['answers'] : [];
$answersError = $errors['answers'] ?? null;
$optClass = ['yes' => 'opt-yes', 'no' => 'opt-no'];
?>
<section class="bg-brand-mist px-4 pb-24 pt-14 md:pt-20">
  <div class="mx-auto flex max-w-3xl flex-col gap-8">
    <div class="flex animate-rise flex-col items-center gap-4 text-center">
      <span class="chip chip-lime">Free · No obligation · About 5 minutes</span>
      <h1 class="display text-[44px] md:text-6xl">How exposed is your business?</h1>
      <p class="lead max-w-xl">Answer a few questions and certified Zoho &amp; Sophos architects send you a plain-language report.</p>
    </div>

    <div class="flex flex-col gap-2.5" aria-live="polite">
      <div class="flex justify-between text-[13px] font-bold text-brand-muted">
        <span data-step-label>Three short steps</span>
      </div>
      <div class="h-1.5 overflow-hidden rounded-full bg-slate-200" aria-hidden="true">
        <div data-step-bar class="h-full w-full rounded-full bg-brand-blue transition-all duration-300"></div>
      </div>
    </div>

    <div class="card-form">
      <?= $this->include('partials/flash', ['flash' => $flash]) ?>
      <?= $this->include('partials/form_open', ['formAction' => $formAction, 'formAttrs' => 'data-stepper']) ?>

        <fieldset data-step="Your organisation" class="flex min-w-0 flex-col gap-6">
          <legend class="mb-6 text-2xl font-extrabold tracking-[-0.02em]">About your organisation</legend>
          <?= $this->include('partials/field', ['name' => 'company', 'label' => 'Company name', 'required' => true, 'autocomplete' => 'organization', 'old' => $old, 'errors' => $errors]) ?>
          <div class="grid gap-5 sm:grid-cols-2">
            <?= $this->include('partials/field', ['name' => 'company_website', 'label' => 'Website', 'placeholder' => 'example.com', 'old' => $old, 'errors' => $errors]) ?>
            <?= $this->include('partials/field', ['name' => 'industry', 'label' => 'Industry', 'old' => $old, 'errors' => $errors]) ?>
          </div>
          <?= $this->include('partials/field', ['name' => 'employee_scale', 'label' => 'How many people work there?', 'type' => 'chips', 'required' => true, 'options' => $config['employee_scales'], 'old' => $old, 'errors' => $errors]) ?>
          <p data-step-error hidden class="text-sm font-semibold text-brand-red">Please fill in the required fields to continue.</p>
          <div class="flex justify-end pt-2"><button type="button" data-next class="btn btn-lg btn-cta hidden [.is-stepped_&]:inline-flex">Continue</button></div>
        </fieldset>

        <?php if ($questions !== []) : ?>
        <fieldset data-step="Your setup" class="flex min-w-0 flex-col"<?= $answersError ? ' data-has-error' : '' ?>>
          <legend class="mb-2 text-2xl font-extrabold tracking-[-0.02em]">Your current setup</legend>
          <p class="mb-4 tile-text">“Not sure” is a fine answer. It tells us where to look. <span class="font-medium text-brand-muted">Optional, but it makes your report far more useful.</span></p>
          <?php if ($answersError) : ?><p class="mb-3 text-sm font-semibold text-brand-red" role="alert"><?= $e($answersError) ?></p><?php endif; ?>
          <?php foreach ($questions as $qid => $question) : ?>
          <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 py-4" role="radiogroup" aria-labelledby="q_<?= $e($qid) ?>">
            <span id="q_<?= $e($qid) ?>" class="flex-[1_1_300px] text-[15px] font-semibold leading-snug"><?= $e($question) ?></span>
            <span class="flex flex-wrap gap-1.5">
              <?php foreach ($answerOptions as $value => $optLabel) : ?>
              <label class="opt min-h-[40px] px-3.5 text-[13px] <?= $optClass[$value] ?? '' ?>">
                <input class="sr-only" type="radio" name="answers[<?= $e($qid) ?>]" value="<?= $e($value) ?>" <?= ($oldAnswers[$qid] ?? null) === $value ? 'checked' : '' ?>>
                <?= $e($optLabel) ?>
              </label>
              <?php endforeach; ?>
            </span>
          </div>
          <?php endforeach; ?>
          <div class="flex justify-between pt-4">
            <button type="button" data-prev class="btn hidden text-brand-blue hover:bg-white [.is-stepped_&]:inline-flex">Back</button>
            <button type="button" data-next class="btn btn-lg btn-cta hidden [.is-stepped_&]:inline-flex">Continue</button>
          </div>
        </fieldset>
        <?php endif; ?>

        <fieldset data-step="Where to send it" class="flex min-w-0 flex-col gap-6">
          <legend class="mb-6 text-2xl font-extrabold tracking-[-0.02em]">Where should we send your report?</legend>
          <div class="grid gap-5 sm:grid-cols-2">
            <?= $this->include('partials/field', ['name' => 'name', 'label' => 'Your name', 'required' => true, 'autocomplete' => 'name', 'old' => $old, 'errors' => $errors]) ?>
            <?= $this->include('partials/field', ['name' => 'email', 'label' => 'Work email', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'old' => $old, 'errors' => $errors]) ?>
          </div>
          <?= $this->include('partials/field', ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'autocomplete' => 'tel', 'old' => $old, 'errors' => $errors]) ?>
          <?= $this->include('partials/field', ['name' => 'notes', 'label' => 'Anything else we should know?', 'type' => 'textarea', 'old' => $old, 'errors' => $errors]) ?>
          <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <button type="button" data-prev class="btn hidden text-brand-blue hover:bg-brand-mist [.is-stepped_&]:inline-flex">Back</button>
            <button type="submit" class="btn btn-lg btn-cta sm:min-w-[260px]">
              Get my free report
              <svg class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </button>
          </div>
        </fieldset>
      </form>
    </div>

    <p class="text-center text-[13px] text-brand-muted">Reviewed by certified Zoho &amp; Sophos security architects. We never share your answers.</p>
  </div>
</section>
