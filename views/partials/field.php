<?php
/**
 * Expects: $name, $label; optional: $type (text|email|tel|url|textarea|select|chips), $required, $options (list),
 * $old, $errors, $hint, $placeholder, $autocomplete, $chipClass (extra class for chips, e.g. 'opt-danger').
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$type = $type ?? 'text';
$required = $required ?? false;
$hint = $hint ?? '';
$value = $old[$name] ?? '';
$value = is_string($value) ? $value : '';
$error = $errors[$name] ?? null;
$id = 'f_' . $name;
$cls = 'input' . ($error ? ' input-error' : '');
$extra = (!empty($placeholder) ? ' placeholder="' . $e($placeholder) . '"' : '')
    . (!empty($autocomplete) ? ' autocomplete="' . $e($autocomplete) . '"' : '');
$attrs = 'id="' . $e($id) . '" name="' . $e($name) . '"' . ($required ? ' required aria-required="true"' : '')
    . ($error ? ' aria-invalid="true" aria-describedby="' . $e($id) . '_err"' : '') . $extra;
$labelHtml = $e($label) . ($required ? ' <span class="text-brand-red" aria-hidden="true">*</span>' : ' <span class="font-medium text-brand-muted">Optional</span>');
?>
<?php if ($type === 'chips') : ?>
<fieldset class="min-w-0"<?= $error ? ' aria-describedby="' . $e($id) . '_err"' : '' ?>>
  <legend class="label mb-3"><?= $labelHtml ?></legend>
  <div class="flex flex-wrap gap-2">
    <?php foreach ($options as $i => $opt) : ?>
    <label class="opt <?= $e($chipClass ?? '') ?>">
      <input class="sr-only" type="radio" name="<?= $e($name) ?>" value="<?= $e($opt) ?>" <?= $value === $opt ? 'checked' : '' ?><?= $required && $i === 0 ? ' required' : '' ?>>
      <?= $e($opt) ?>
    </label>
    <?php endforeach; ?>
  </div>
  <?php if ($error) : ?><p id="<?= $e($id) ?>_err" class="mt-2 text-sm font-semibold text-brand-red"><?= $e($error) ?></p><?php endif; ?>
</fieldset>
<?php else : ?>
<div class="min-w-0">
<label class="label" for="<?= $e($id) ?>"><?= $labelHtml ?></label>
<?php if ($hint !== '') : ?><p class="mt-1 text-[13px] text-brand-muted"><?= $e($hint) ?></p><?php endif; ?>
<?php if ($type === 'textarea') : ?>
<textarea <?= $attrs ?> rows="4" maxlength="2000" class="<?= $cls ?>"><?= $e($value) ?></textarea>
<?php elseif ($type === 'select') : ?>
<select <?= $attrs ?> class="<?= $cls ?>">
<option value="">Select…</option>
<?php foreach ($options as $opt) : ?>
<option value="<?= $e($opt) ?>" <?= $value === $opt ? 'selected' : '' ?>><?= $e($opt) ?></option>
<?php endforeach; ?>
</select>
<?php else : ?>
<input <?= $attrs ?> type="<?= $e($type) ?>" value="<?= $e($value) ?>" maxlength="254" class="<?= $cls ?>">
<?php endif; ?>
<?php if ($error) : ?><p id="<?= $e($id) ?>_err" class="mt-1.5 text-sm font-semibold text-brand-red"><?= $e($error) ?></p><?php endif; ?>
</div>
<?php endif; ?>
