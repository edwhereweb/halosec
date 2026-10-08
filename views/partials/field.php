<?php
/**
 * Expects: $name, $label; optional: $type (text|email|tel|textarea|select), $required, $options (list), $old, $errors.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$type = $type ?? 'text';
$required = $required ?? false;
$value = $old[$name] ?? '';
$value = is_string($value) ? $value : '';
$error = $errors[$name] ?? null;
$id = 'f_' . $name;
$cls = 'mt-1 block w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500 text-sm ' . ($error ? 'border-red-400' : '');
$attrs = 'id="' . $e($id) . '" name="' . $e($name) . '"' . ($required ? ' required aria-required="true"' : '')
    . ($error ? ' aria-invalid="true" aria-describedby="' . $e($id) . '_err"' : '');
?>
<div>
<label class="block text-sm font-semibold text-slate-700" for="<?= $e($id) ?>"><?= $e($label) ?><?= $required ? ' <span class="text-red-600">*</span>' : '' ?></label>
<?php if ($type === 'textarea') : ?>
<textarea <?= $attrs ?> rows="5" maxlength="2000" class="<?= $cls ?>"><?= $e($value) ?></textarea>
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
<?php if ($error) : ?><p id="<?= $e($id) ?>_err" class="mt-1 text-xs text-red-600"><?= $e($error) ?></p><?php endif; ?>
</div>
