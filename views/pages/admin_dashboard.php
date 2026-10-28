<?php
use HaloSec\Admin\Ui;
use HaloSec\Services\AdminAnalytics;

$e = static fn (mixed $v): string => HaloSec\View::e($v);
$nav = is_array($nav ?? null) ? $nav : [];
$counts = is_array($nav['counts'] ?? null) ? $nav['counts'] : ['open' => 0, 'new' => 0, 'unassigned' => 0, 'urgent' => 0, 'mine' => 0, 'stale' => 0];
$now = new DateTimeImmutable('now', Ui::timezone());
$hour = (int) $now->format('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$yesterday = $now->modify('-1 day')->format('Y-m-d');
$typeLinks = [];
$typeItems = [];
foreach ($byType as $key => $count) {
    $typeItems[Ui::formLabel((string) $key)] = $count;
    $typeLinks[Ui::formLabel((string) $key)] = '/admin/submissions?status=all&type=' . rawurlencode((string) $key);
}
$compare = 'vs previous 30 days';
$responseDelta = AdminAnalytics::delta($medianResponse, $previousMedianResponse);
?>
<div class="mx-auto flex w-full max-w-7xl flex-col gap-8">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <p class="text-sm font-medium text-slate-500"><?= $e($now->format('l, j F')) ?></p>
      <h1 class="mt-1 text-[28px] font-bold tracking-tight text-slate-900"><?= $e($greeting) ?>, <?= $e($nav['username'] ?? '') ?></h1>
      <p class="mt-1 text-[15px] text-slate-600">
        <?php if ($counts['open'] === 0) : ?>The inbox is clear. Nothing is waiting on the team.
        <?php else : ?><?= $e($counts['open']) ?> open <?= $counts['open'] === 1 ? 'request' : 'requests' ?>, <?= $e($counts['new']) ?> not yet picked up.<?php endif; ?>
      </p>
    </div>
    <div class="flex gap-2">
      <a href="/admin/analytics" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50">Analytics</a>
      <a href="/admin/submissions" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white no-underline hover:bg-slate-700">Open inbox</a>
    </div>
  </div>

  <?php if (!($isDatabase ?? true)) : ?>
  <div role="status" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
    <strong>The database isn’t connected,</strong> so submissions are read from files and can’t be assigned or tracked. Check the DB settings in <code>.env</code>.
    <?php if (!empty($dbError)) : ?><span class="mt-1 block font-mono text-xs"><?= $e($dbError) ?></span><?php endif; ?>
  </div>
  <?php endif; ?>

  <section aria-labelledby="attention" class="flex flex-col gap-4">
    <h2 id="attention" class="text-base font-bold text-slate-900">Needs attention</h2>
    <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
      <a href="/admin/submissions?type=urgent" class="flex flex-col gap-1 rounded-2xl border p-5 no-underline transition <?= $counts['urgent'] > 0 ? 'border-red-200 bg-red-50 hover:border-red-300' : 'border-slate-200 bg-white hover:border-slate-300' ?>">
        <span class="flex items-center gap-2 text-[13px] font-semibold <?= $counts['urgent'] > 0 ? 'text-red-800' : 'text-slate-500' ?>">
          <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 3 2 20h20L12 3Z"/><path d="M12 10v4M12 17.5v.01"/></svg>
          Open emergencies
        </span>
        <span class="text-[32px] font-bold leading-tight <?= $counts['urgent'] > 0 ? 'text-red-700' : 'text-slate-900' ?>"><?= $e($counts['urgent']) ?></span>
        <span class="text-xs font-medium <?= $counts['urgent'] > 0 ? 'text-red-800' : 'text-slate-500' ?>"><?= $counts['urgent'] > 0 ? 'Call these back first →' : 'No live incidents' ?></span>
      </a>
      <a href="/admin/submissions?assignee=unassigned" class="flex flex-col gap-1 rounded-2xl border border-slate-200 bg-white p-5 no-underline transition hover:border-slate-300">
        <span class="text-[13px] font-semibold text-slate-500">Waiting for an owner</span>
        <span class="text-[32px] font-bold leading-tight text-slate-900"><?= $e($counts['unassigned']) ?></span>
        <span class="text-xs font-medium text-slate-500">Open and unassigned</span>
      </a>
      <a href="/admin/submissions?assignee=me" class="flex flex-col gap-1 rounded-2xl border border-slate-200 bg-white p-5 no-underline transition hover:border-slate-300">
        <span class="text-[13px] font-semibold text-slate-500">Assigned to you</span>
        <span class="text-[32px] font-bold leading-tight text-slate-900"><?= $e($counts['mine']) ?></span>
        <span class="text-xs font-medium text-slate-500">Open on your list</span>
      </a>
      <a href="/admin/submissions?status=open&amp;to=<?= $e($yesterday) ?>" class="flex flex-col gap-1 rounded-2xl border p-5 no-underline transition <?= $counts['stale'] > 0 ? 'border-amber-200 bg-amber-50 hover:border-amber-300' : 'border-slate-200 bg-white hover:border-slate-300' ?>">
        <span class="text-[13px] font-semibold <?= $counts['stale'] > 0 ? 'text-amber-900' : 'text-slate-500' ?>">Open for over 24 hours</span>
        <span class="text-[32px] font-bold leading-tight text-slate-900"><?= $e($counts['stale']) ?></span>
        <span class="text-xs font-medium <?= $counts['stale'] > 0 ? 'text-amber-900' : 'text-slate-500' ?>"><?= $counts['stale'] > 0 ? 'Follow up or close them' : 'Everything is fresh' ?></span>
      </a>
    </div>

    <?php if ($urgent !== [] || $unassigned !== [] || $mine !== []) : ?>
    <div class="grid gap-4 lg:grid-cols-2">
      <?php if ($urgent !== []) : ?>
      <div class="rounded-2xl border border-red-200 bg-white p-2">
        <h3 class="flex items-center justify-between px-3 pb-1 pt-2 text-sm font-bold text-red-800">Emergencies <a href="/admin/submissions?type=urgent" class="text-xs font-semibold text-red-800">View all</a></h3>
        <ul><?php foreach ($urgent as $row) : ?><?= $this->include('partials/admin/item', ['row' => $row, 'showStatus' => true]) ?><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>
      <?php if ($unassigned !== []) : ?>
      <div class="rounded-2xl border border-slate-200 bg-white p-2">
        <h3 class="flex items-center justify-between px-3 pb-1 pt-2 text-sm font-bold text-slate-900">New and unassigned <a href="/admin/submissions?assignee=unassigned" class="text-xs font-semibold text-sky-800">View all</a></h3>
        <ul><?php foreach ($unassigned as $row) : ?><?= $this->include('partials/admin/item', ['row' => $row]) ?><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>
      <?php if ($mine !== [] && $urgent === []) : ?>
      <div class="rounded-2xl border border-slate-200 bg-white p-2">
        <h3 class="flex items-center justify-between px-3 pb-1 pt-2 text-sm font-bold text-slate-900">On your list <a href="/admin/submissions?assignee=me" class="text-xs font-semibold text-sky-800">View all</a></h3>
        <ul><?php foreach ($mine as $row) : ?><?= $this->include('partials/admin/item', ['row' => $row, 'showStatus' => true]) ?><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </section>

  <section aria-labelledby="last30" class="flex flex-col gap-4">
    <div class="flex items-baseline justify-between gap-3">
      <h2 id="last30" class="text-base font-bold text-slate-900">Last 30 days</h2>
      <a href="/admin/analytics" class="text-sm font-semibold text-sky-800">More analytics →</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <?= $this->include('partials/admin/stat', ['label' => 'New leads', 'value' => Ui::number($summary['leads']['value'] ?? 0), 'delta' => $summary['leads']['delta'], 'previous' => $summary['leads']['previous'], 'compare' => $compare, 'hint' => 'Excludes newsletter sign-ups and spam']) ?>
      <?= $this->include('partials/admin/stat', ['label' => 'Free audits requested', 'value' => Ui::number($summary['audits']['value'] ?? 0), 'delta' => $summary['audits']['delta'], 'previous' => $summary['audits']['previous'], 'compare' => $compare]) ?>
      <?= $this->include('partials/admin/stat', ['label' => 'Median first response', 'value' => AdminAnalytics::duration($medianResponse), 'delta' => $responseDelta, 'goodWhenUp' => false, 'compare' => $compare, 'hint' => 'Time until someone first acts on a request']) ?>
      <?= $this->include('partials/admin/stat', ['label' => 'Emergencies reported', 'value' => Ui::number($summary['urgent']['value'] ?? 0), 'delta' => $summary['urgent']['delta'], 'previous' => $summary['urgent']['previous'], 'goodWhenUp' => null, 'compare' => $compare]) ?>
    </div>
    <div class="grid gap-4 xl:grid-cols-3">
      <div class="rounded-2xl border border-slate-200 bg-white p-5 xl:col-span-2">
        <h3 class="text-sm font-bold text-slate-900">Submissions per day</h3>
        <p class="mb-4 text-xs text-slate-500">All forms, including spam. Hover a day for the count.</p>
        <?= $this->include('partials/admin/chart_columns', ['series' => $series, 'title' => 'Submissions per day, last 30 days']) ?>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <h3 class="text-sm font-bold text-slate-900">What people asked for</h3>
        <p class="mb-4 text-xs text-slate-500">By form, spam excluded.</p>
        <?= $this->include('partials/admin/bar_list', ['items' => $typeItems, 'links' => $typeLinks]) ?>
      </div>
    </div>
  </section>

  <div class="grid gap-4 xl:grid-cols-3">
    <section aria-labelledby="recent" class="rounded-2xl border border-slate-200 bg-white p-2 xl:col-span-2">
      <h2 id="recent" class="flex items-center justify-between px-3 pb-1 pt-2 text-sm font-bold text-slate-900">Latest submissions <a href="/admin/submissions?status=all" class="text-xs font-semibold text-sky-800">View all</a></h2>
      <?php if ($recent === []) : ?>
      <p class="p-6 text-center text-sm text-slate-500">No submissions yet. They appear here as soon as someone fills in a form on the website.</p>
      <?php else : ?>
      <ul><?php foreach ($recent as $row) : ?><?= $this->include('partials/admin/item', ['row' => $row, 'showStatus' => true]) ?><?php endforeach; ?></ul>
      <?php endif; ?>
    </section>
    <section aria-labelledby="sec" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5">
      <h2 id="sec" class="text-sm font-bold text-slate-900">Admin security</h2>
      <div class="flex items-baseline gap-2"><span class="text-[32px] font-bold leading-tight text-slate-900"><?= $e($failedLogins7d) ?></span><span class="text-sm text-slate-500">failed sign-ins in 7 days</span></div>
      <p class="text-xs text-slate-500">A few are normal (typos). A sudden jump can mean someone is guessing passwords.</p>
      <?php if (empty($nav['hasTwoFactor'])) : ?>
      <a href="/admin/account" class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm font-semibold text-amber-900 no-underline">Your account has no 2FA yet. Set it up →</a>
      <?php else : ?>
      <p class="flex items-center gap-2 text-sm font-semibold text-emerald-800"><span aria-hidden="true">✓</span> Two-factor sign-in is on for you</p>
      <?php endif; ?>
      <?php if (!empty($nav['can']['security.read'])) : ?><a href="/admin/security?event=admin_login_failed" class="mt-auto text-sm font-semibold text-sky-800">Open security log →</a><?php endif; ?>
    </section>
  </div>
</div>
