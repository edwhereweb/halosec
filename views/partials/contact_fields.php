<?php
/** Common name / email / phone / company block. Optional: $phoneRequired, $showCompany, $companyRequired. */
$show = $showCompany ?? true;
?>
<div class="grid gap-5 sm:grid-cols-2">
<?= $this->include('partials/field', ['name' => 'name', 'label' => 'Your name', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'email', 'label' => 'Work email', 'type' => 'email', 'required' => true, 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'required' => $phoneRequired ?? false, 'old' => $old, 'errors' => $errors]) ?>
<?php if ($show) : ?>
<?= $this->include('partials/field', ['name' => 'company', 'label' => 'Company', 'required' => $companyRequired ?? false, 'old' => $old, 'errors' => $errors]) ?>
<?php endif; ?>
</div>
