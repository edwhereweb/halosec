<?php
$activePage = $activePage ?? '';
$currentType = $currentType ?? null;
$eventType = $eventType ?? '';
$linkClass = static fn (bool $active): string => 'flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-700 '
    . ($active ? 'bg-sky-50 text-sky-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950');
$isCurrent = static fn (array $pages): bool => in_array($activePage, $pages, true);
?>
<div class="flex gap-2 overflow-x-auto p-3 lg:block lg:space-y-6 lg:overflow-visible lg:p-4">
  <a class="<?= $linkClass($isCurrent(['dashboard'])) ?> shrink-0" href="/admin" <?= $isCurrent(['dashboard']) ? 'aria-current="page"' : '' ?>>Dashboard</a>
  <section class="shrink-0 lg:shrink">
    <h2 class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Submissions</h2>
    <ul class="flex gap-1 lg:block lg:space-y-1">
      <li><a class="<?= $linkClass($isCurrent(['submissions', 'submission', 'leads', 'lead']) && $currentType === null) ?>" href="/admin/submissions" <?= $isCurrent(['submissions', 'submission', 'leads', 'lead']) && $currentType === null ? 'aria-current="page"' : '' ?>>All Inbox</a></li>
      <?php foreach ([
          'contact' => 'Contact',
          'enquiry' => 'Enquiry',
          'consultation' => 'Consultation',
          'emergency' => 'Emergency',
          'security_report' => 'Security Reports',
          'newsletter' => 'Newsletter',
          'audit' => 'Audits',
      ] as $type => $label) : ?>
      <li><a class="<?= $linkClass($isCurrent(['submissions', 'submission', 'leads', 'lead']) && $currentType === $type) ?>" href="/admin/submissions?type=<?= HaloSec\View::e($type) ?>" <?= $isCurrent(['submissions', 'submission', 'leads', 'lead']) && $currentType === $type ? 'aria-current="page"' : '' ?>><?= HaloSec\View::e($label) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </section>
  <section class="shrink-0 lg:shrink">
    <h2 class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Security Center</h2>
    <ul class="flex gap-1 lg:block lg:space-y-1">
      <li><a class="<?= $linkClass($activePage === 'security' && $eventType === 'admin_login_failed') ?>" href="/admin/security?event=admin_login_failed" <?= $activePage === 'security' && $eventType === 'admin_login_failed' ? 'aria-current="page"' : '' ?>>Failed Logins</a></li>
      <li><a class="<?= $linkClass($activePage === 'security' && $eventType !== 'admin_login_failed') ?>" href="/admin/security?event=security_events" <?= $activePage === 'security' && $eventType !== 'admin_login_failed' ? 'aria-current="page"' : '' ?>>Security Events</a></li>
    </ul>
  </section>
  <section class="shrink-0 lg:shrink">
    <h2 class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Content &amp; Settings</h2>
    <ul class="flex gap-1 lg:block lg:space-y-1">
      <li><a class="<?= $linkClass($activePage === 'settings' && ($_GET['section'] ?? '') === 'content-blocks') ?>" href="/admin/settings#content-blocks">Content Blocks</a></li>
      <li><a class="<?= $linkClass($activePage === 'settings' && ($_GET['section'] ?? '') !== 'content-blocks') ?>" href="/admin/settings#site-settings">Site Settings</a></li>
    </ul>
  </section>
  <section class="shrink-0 lg:shrink">
    <h2 class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Governance</h2>
    <ul class="flex gap-1 lg:block lg:space-y-1">
      <li><a class="<?= $linkClass($activePage === 'audit') ?>" href="/admin/audit" <?= $activePage === 'audit' ? 'aria-current="page"' : '' ?>>Audit Logs</a></li>
      <li><a class="<?= $linkClass($activePage === 'users') ?>" href="/admin/users" <?= $activePage === 'users' ? 'aria-current="page"' : '' ?>>Admin Users / Roles</a></li>
      <li><a class="<?= $linkClass($activePage === 'account') ?>" href="/admin/account" <?= $activePage === 'account' ? 'aria-current="page"' : '' ?>>My Account &amp; 2FA</a></li>
    </ul>
  </section>
</div>
