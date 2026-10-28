<?php
/** Common name / email / phone / company block. Optional: $phoneRequired, $showCompany, $companyRequired. */
$show = $showCompany ?? true;
?>
<div class="grid gap-5 sm:grid-cols-2">
<?= $this->include('partials/field', ['name' => 'name', 'label' => 'Your name', 'required' => true, 'autocomplete' => 'name', 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'email', 'label' => 'Work email', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'old' => $old, 'errors' => $errors]) ?>
<?= $this->include('partials/field', ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'required' => $phoneRequired ?? false, 'autocomplete' => 'tel', 'old' => $old, 'errors' => $errors]) ?>
<?php if ($show) : ?>
<?= $this->include('partials/field', ['name' => 'company', 'label' => 'Company', 'required' => $companyRequired ?? false, 'autocomplete' => 'organization', 'old' => $old, 'errors' => $errors]) ?>
<?php endif; ?>
</div>
