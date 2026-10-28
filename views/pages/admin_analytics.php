<?php
use HaloSec\Admin\Ui;
use HaloSec\Services\AdminAnalytics;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$compare = 'vs previous ' . ($days === 365 ? '12 months' : $days . ' days');
$typeItems = [];
$typeLinks = [];
foreach ($byType as $key => $count) {
    $typeItems[Ui::formLabel((string) $key)] = $count;
    $typeLinks[Ui::formLabel((string) $key)] = '/admin/submissions?status=all&type=' . rawurlencode((string) $key);
}
$statusItems = [];
foreach (Ui::statusLabels() as $key => $label) {
    if (($byStatus[$key] ?? 0) > 0) {
        $statusItems[$label] = (int) $byStatus[$key];
    }
}
$card = 'rounded-2xl border border-slate-200 bg-white p-5';
$h3 = 'text-sm font-bold text-slate-900';
$sub = 'mb-4 text-xs text-slate-500';
?>
<div class="mx-auto flex w-full max-w-7xl flex-col gap-8">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <h1 class="text-[28px] font-bold tracking-tight text-slate-900">Analytics</h1>
      <p class="mt-1 text-[15px] text-slate-600">Where leads come from, what they ask for, and how fast the team responds.</p>
    </div>
    <nav aria-label="Date range" class="inline-flex rounded-lg border border-slate-200 bg-white p-1">
      <?php foreach (AdminAnalytics::RANGES as $value => $label) : ?>
      <a href="/admin/analytics?range=<?= $e($value) ?>" class="rounded-md px-3 py-1.5 text-sm font-semibold no-underline <?= $value === $days ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>" <?= $value === $days ? 'aria-current="page"' : '' ?>><?= $e(str_replace('Last ', '', $label)) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>

  <?php if (!($isDatabase ?? true)) : ?>
  <p role="status" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">The database isn’t connected, so only basic counts are available.</p>
  <?php endif; ?>

  <section aria-label="Headline numbers" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <?= $this->include('partials/admin/stat', ['label' => 'New leads', 'value' => Ui::number($summary['leads']['value'] ?? 0), 'delta' => $summary['leads']['delta'], 'previous' => $summary['leads']['previous'], 'compare' => $compare]) ?>
    <?= $this->include('partials/admin/stat', ['label' => 'Free audits', 'value' => Ui::number($summary['audits']['value'] ?? 0), 'delta' => $summary['audits']['delta'], 'previous' => $summary['audits']['previous'], 'compare' => $compare]) ?>
    <?= $this->include('partials/admin/stat', ['label' => 'Emergencies', 'value' => Ui::number($summary['urgent']['value'] ?? 0), 'delta' => $summary['urgent']['delta'], 'previous' => $summary['urgent']['previous'], 'goodWhenUp' => null, 'compare' => $compare]) ?>
    <?= $this->include('partials/admin/stat', ['label' => 'Newsletter sign-ups', 'value' => Ui::number($summary['subscribers']['value'] ?? 0), 'delta' => $summary['subscribers']['delta'], 'previous' => $summary['subscribers']['previous'], 'compare' => $compare]) ?>
    <?= $this->include('partials/admin/stat', ['label' => 'Median first response', 'value' => AdminAnalytics::duration($medianResponse), 'hint' => 'Submission → first action by the team']) ?>
  </section>

  <section class="<?= $card ?>" aria-labelledby="trend">
    <h2 id="trend" class="<?= $h3 ?>">Submissions <?= $days > 60 ? 'per week' : 'per day' ?></h2>
    <p class="<?= $sub ?>"><?= $e(AdminAnalytics::RANGES[$days]) ?>, all forms. Hover a column for the exact count.</p>
    <?= $this->include('partials/admin/chart_columns', ['series' => $series, 'title' => 'Submissions ' . ($days > 60 ? 'per week' : 'per day'), 'height' => 220]) ?>
  </section>

  <div class="grid gap-4 lg:grid-cols-2">
    <section class="<?= $card ?>" aria-labelledby="bytype">
      <h2 id="bytype" class="<?= $h3 ?>">Requests by form</h2>
      <p class="<?= $sub ?>">Spam excluded. Click a form to open those requests.</p>
      <?= $this->include('partials/admin/bar_list', ['items' => $typeItems, 'links' => $typeLinks]) ?>
    </section>
    <section class="<?= $card ?>" aria-labelledby="services">
      <h2 id="services" class="<?= $h3 ?>">Most requested services</h2>
      <p class="<?= $sub ?>">From service enquiry forms.</p>
      <?= $this->include('partials/admin/bar_list', ['items' => $services, 'unit' => 'enquiries', 'empty' => 'No service enquiries in this period.']) ?>
    </section>
  </div>

  <section class="<?= $card ?>" aria-labelledby="when">
    <h2 id="when" class="<?= $h3 ?>">When people get in touch</h2>
    <p class="<?= $sub ?>">Darker = more submissions. Useful for planning who is on call.</p>
    <?= $this->include('partials/admin/heatmap', ['heatmap' => $heatmap]) ?>
  </section>

  <div>
    <h2 class="mb-3 text-base font-bold text-slate-900">Free audit prospects</h2>
    <div class="grid gap-4 lg:grid-cols-3">
      <section class="<?= $card ?>" aria-labelledby="score">
        <h3 id="score" class="<?= $h3 ?>">Security score</h3>
        <p class="<?= $sub ?>">Average <?= $scores['average'] === null ? '—' : '<strong class="text-slate-900">' . $e($scores['average']) . '/100</strong>' ?>. Weak scores are the warmest leads.</p>
        <?= $this->include('partials/admin/bar_list', ['items' => $scores['bands'], 'unit' => 'audits', 'empty' => 'No audits in this period.']) ?>
      </section>
      <section class="<?= $card ?>" aria-labelledby="industry">
        <h3 id="industry" class="<?= $h3 ?>">Industry</h3>
        <p class="<?= $sub ?>">As entered on the audit form.</p>
        <?= $this->include('partials/admin/bar_list', ['items' => $industries, 'unit' => 'audits', 'empty' => 'No audits in this period.']) ?>
      </section>
      <section class="<?= $card ?>" aria-labelledby="size">
        <h3 id="size" class="<?= $h3 ?>">Company size</h3>
        <p class="<?= $sub ?>">Employees, smallest first.</p>
        <?= $this->include('partials/admin/bar_list', ['items' => array_sum($scales) > 0 ? $scales : [], 'unit' => 'audits', 'empty' => 'No audits in this period.']) ?>
      </section>
    </div>
  </div>

  <div class="grid gap-4 lg:grid-cols-2">
    <section class="<?= $card ?>" aria-labelledby="topics">
      <h2 id="topics" class="<?= $h3 ?>">Consultation topics</h2>
      <p class="<?= $sub ?>">What prospects want to talk about.</p>
      <?= $this->include('partials/admin/bar_list', ['items' => $topics, 'unit' => 'requests', 'empty' => 'No consultation requests in this period.']) ?>
    </section>
    <section class="<?= $card ?>" aria-labelledby="attacks">
      <h2 id="attacks" class="<?= $h3 ?>">Reported incidents</h2>
      <p class="<?= $sub ?>">Attack types from emergency and security reports.</p>
      <?= $this->include('partials/admin/bar_list', ['items' => $attacks, 'unit' => 'reports', 'empty' => 'No incidents reported in this period.']) ?>
    </section>
  </div>

  <div>
    <h2 class="mb-3 text-base font-bold text-slate-900">Team</h2>
    <div class="grid gap-4 lg:grid-cols-3">
      <section class="<?= $card ?>" aria-labelledby="pipeline">
        <h3 id="pipeline" class="<?= $h3 ?>">Where requests stand</h3>
        <p class="<?= $sub ?>">Status of everything received in this period.</p>
        <?= $this->include('partials/admin/bar_list', ['items' => $statusItems]) ?>
      </section>
      <section class="<?= $card ?>" aria-labelledby="workload">
        <h3 id="workload" class="<?= $h3 ?>">Open workload</h3>
        <p class="<?= $sub ?>">Open requests from this period, by owner.</p>
        <?= $this->include('partials/admin/bar_list', ['items' => $workload, 'unit' => 'open requests', 'empty' => 'Nothing open. Nice.']) ?>
      </section>
      <section class="<?= $card ?>" aria-labelledby="handled">
        <h3 id="handled" class="<?= $h3 ?>">Completed</h3>
        <p class="<?= $sub ?>">Requests marked Done, by owner.</p>
        <?= $this->include('partials/admin/bar_list', ['items' => $handled, 'unit' => 'requests', 'empty' => 'Nothing completed in this period yet.']) ?>
      </section>
    </div>
  </div>

  <p class="text-xs text-slate-500">Spam rate this period: <?= $summary['spam_rate']['value'] === null ? '—' : $e($summary['spam_rate']['value']) . '%' ?> · Failed admin sign-ins: <?= $e($failedLogins) ?> · Times in <?= $e(Ui::zoneAbbr()) ?></p>
</div>
