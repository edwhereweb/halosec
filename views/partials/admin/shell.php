<?php
/**
 * Admin workspace shell: sidebar navigation (grouped by job), top bar with search, and the page content.
 * Expects $content; optional $nav (from AdminController::render), $title, $csrfToken.
 */
$e = static fn (mixed $v): string => HaloSec\View::e($v);
$nav = is_array($nav ?? null) ? $nav : [];
$active = (string) ($nav['active'] ?? '');
$counts = is_array($nav['counts'] ?? null) ? $nav['counts'] : null;
$can = is_array($nav['can'] ?? null) ? $nav['can'] : [];
$allowed = static fn (string $permission): bool => (bool) ($can[$permission] ?? false);
$username = (string) ($nav['username'] ?? '');
$role = (string) ($nav['role'] ?? '');
$searchValue = is_string($_GET['q'] ?? null) ? $_GET['q'] : '';

// Simple 24px stroke icons.
$icons = [
    'dashboard' => '<path d="M4 13h6V4H4v9Zm10 7h6v-9h-6v9ZM4 20h6v-4H4v4Zm10-16v4h6V4h-6Z"/>',
    'analytics' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    'inbox' => '<path d="M3 13h5l2 3h4l2-3h5"/><path d="M5 5h14l2 8v6H3v-6l2-8Z"/>',
    'mine' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    'unassigned' => '<circle cx="12" cy="8" r="4" stroke-dasharray="3 3"/><path d="M4 21a8 8 0 0 1 16 0" stroke-dasharray="3 3"/>',
    'urgent' => '<path d="M12 3 2 20h20L12 3Z"/><path d="M12 10v4M12 17.5v.01"/>',
    'content' => '<path d="M4 5h16M4 10h16M4 15h10M4 20h7"/>',
    'security' => '<path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/>',
    'audit' => '<path d="M9 4h6l1 2h3v15H5V6h3l1-2Z"/><path d="M9 12h6M9 16h4"/>',
    'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
    'account' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6.5 18.5a6.5 6.5 0 0 1 11 0"/>',
    'site' => '<path d="M14 4h6v6M20 4l-9 9M10 6H5v13h13v-5"/>',
];
$icon = static fn (string $name, string $class = 'h-[18px] w-[18px]'): string => '<svg aria-hidden="true" class="' . $class . ' shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">' . ($icons[$name] ?? '') . '</svg>';

$groups = [
    'Overview' => [
        ['dashboard', 'Dashboard', '/admin', 'dashboard', null, false],
        ['analytics', 'Analytics', '/admin/analytics', 'dashboard', null, false],
    ],
    'Inbox' => [
        ['inbox', 'Inbox', '/admin/submissions', 'submissions.read', $counts['open'] ?? null, false],
        ['urgent', 'Emergencies', '/admin/submissions?type=urgent', 'submissions.read', $counts['urgent'] ?? null, true],
        ['mine', 'Assigned to me', '/admin/submissions?assignee=me', 'submissions.read', $counts['mine'] ?? null, false],
        ['unassigned', 'Unassigned', '/admin/submissions?assignee=unassigned', 'submissions.read', $counts['unassigned'] ?? null, false],
    ],
    'Website' => [
        ['content', 'Website content', '/admin/settings', 'settings.manage', null, false],
    ],
    'Security' => [
        ['security', 'Security log', '/admin/security', 'security.read', null, false],
        ['audit', 'Audit log', '/admin/audit', 'audit.read', null, false],
        ['users', 'Team & roles', '/admin/users', 'users.manage', null, false],
    ],
];
$navLinks = static function (string $place) use ($groups, $allowed, $active, $icon, $e): string {
    $html = '';
    foreach ($groups as $group => $items) {
        $items = array_values(array_filter($items, static fn (array $i): bool => $allowed($i[3])));
        if ($items === []) {
            continue;
        }
        $html .= '<div class="flex flex-col gap-0.5"><p class="px-3 pb-1 pt-4 text-[11px] font-bold uppercase tracking-[0.08em] text-slate-400">' . $e($group) . '</p>';
        foreach ($items as [$key, $label, $href, , $count, $danger]) {
            $isActive = $active === $key;
            $badge = '';
            if (is_int($count) && $count > 0) {
                $badgeClass = $danger ? 'bg-red-600 text-white' : ($isActive ? 'bg-white text-slate-900' : 'bg-slate-200 text-slate-700');
                $badge = '<span class="ml-auto min-w-[22px] rounded-full px-1.5 py-0.5 text-center text-[11px] font-bold tabular-nums ' . $badgeClass . '">' . $e($count > 999 ? '999+' : $count) . '</span>';
            }
            $html .= '<a href="' . $e($href) . '" data-nav="' . $e($place) . '" class="flex min-h-[38px] items-center gap-3 rounded-lg px-3 text-[14px] font-semibold transition '
                . ($isActive ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                . '"' . ($isActive ? ' aria-current="page"' : '') . '>' . $icon($key) . '<span class="truncate">' . $e($label) . '</span>' . $badge . '</a>';
        }
        $html .= '</div>';
    }

    return $html;
};
?>
<div class="min-h-screen lg:grid lg:grid-cols-[248px_minmax(0,1fr)]">
  <aside class="hidden border-r border-slate-200 bg-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col" aria-label="Admin">
    <a href="/admin" class="flex items-center gap-2.5 border-b border-slate-100 px-5 py-4 no-underline" aria-label="HaloSec admin home">
      <img src="/images/halosec-mark.png" alt="" class="h-8 w-8" width="512" height="512">
      <span class="flex flex-col leading-tight"><span class="text-[15px] font-extrabold text-slate-900">HaloSec</span><span class="text-[11px] font-semibold text-slate-500">Operations</span></span>
    </a>
    <nav aria-label="Admin navigation" class="flex-1 overflow-y-auto px-3 pb-4">
      <?= $navLinks('side') ?>
    </nav>
    <div class="border-t border-slate-100 p-3">
      <a href="/admin/account" class="flex items-center gap-3 rounded-lg px-2 py-2 no-underline hover:bg-slate-100 <?= $active === 'account' ? 'bg-slate-100' : '' ?>">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-extrabold uppercase text-sky-900"><?= $e(mb_substr($username !== '' ? $username : '?', 0, 1)) ?></span>
        <span class="min-w-0 flex-1 leading-tight">
          <span class="block truncate text-sm font-bold text-slate-900"><?= $e($username) ?></span>
          <span class="block text-xs text-slate-500"><?= $e(HaloSec\Admin\Ui::roleLabel($role)) ?> · <?= !empty($nav['hasTwoFactor']) ? '2FA on' : '<span class="font-semibold text-amber-700">2FA off</span>' ?></span>
        </span>
      </a>
      <div class="mt-2 flex gap-2">
        <a href="/" target="_blank" rel="noopener" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-slate-200 px-2 py-2 text-xs font-semibold text-slate-600 no-underline hover:bg-slate-50"><?= $icon('site', 'h-3.5 w-3.5') ?>View site</a>
        <form method="post" action="/admin/logout" class="flex-1">
          <input type="hidden" name="_csrf" value="<?= $e($csrfToken ?? '') ?>">
          <button type="submit" class="w-full rounded-lg border border-slate-200 px-2 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Sign out</button>
        </form>
      </div>
    </div>
  </aside>

  <div class="flex min-w-0 flex-col">
    <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-slate-200 bg-white/90 px-4 py-2.5 backdrop-blur sm:px-6">
      <details class="relative lg:hidden" data-menu>
        <summary class="inline-flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg border border-slate-200 text-slate-700" aria-label="Open menu">
          <svg aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </summary>
        <div class="absolute left-0 top-12 z-40 max-h-[80vh] w-72 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
          <?= $navLinks('menu') ?>
          <div class="mt-3 flex flex-col gap-0.5 border-t border-slate-100 pt-2">
            <a href="/admin/account" class="flex min-h-[38px] items-center gap-3 rounded-lg px-3 text-[14px] font-semibold text-slate-600 hover:bg-slate-100"><?= $icon('account') ?>My account</a>
            <form method="post" action="/admin/logout"><input type="hidden" name="_csrf" value="<?= $e($csrfToken ?? '') ?>"><button type="submit" class="flex min-h-[38px] w-full items-center gap-3 rounded-lg px-3 text-left text-[14px] font-semibold text-slate-600 hover:bg-slate-100">Sign out</button></form>
          </div>
        </div>
      </details>
      <a href="/admin" class="lg:hidden" aria-label="Dashboard"><img src="/images/halosec-mark.png" alt="" class="h-8 w-8" width="512" height="512"></a>
      <?php if ($allowed('submissions.read')) : ?>
      <form method="get" action="/admin/submissions" role="search" class="relative ml-auto w-full max-w-md lg:ml-0">
        <label for="admin-search" class="sr-only">Search the inbox</label>
        <svg aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input id="admin-search" name="q" type="search" value="<?= $e($searchValue) ?>" placeholder="Search name, email, company or #id" autocomplete="off" data-search
          class="h-10 w-full rounded-lg border-slate-200 bg-slate-50 pl-9 pr-12 text-sm placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:ring-sky-500">
        <input type="hidden" name="status" value="all">
        <kbd class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded border border-slate-200 bg-white px-1.5 text-[11px] font-semibold text-slate-400 sm:block">/</kbd>
      </form>
      <?php endif; ?>
      <?php if (is_array($counts) && ($counts['urgent'] ?? 0) > 0 && $active !== 'urgent') : ?>
      <a href="/admin/submissions?type=urgent" class="hidden shrink-0 items-center gap-2 rounded-full bg-red-600 px-3.5 py-2 text-xs font-bold text-white no-underline hover:bg-red-700 md:inline-flex">
        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-white"></span></span>
        <?= $e($counts['urgent']) ?> open <?= $counts['urgent'] === 1 ? 'emergency' : 'emergencies' ?>
      </a>
      <?php endif; ?>
    </header>

    <main id="main" class="min-w-0 flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
      <?= $content ?>
    </main>
  </div>
</div>
<div id="admin-tip" role="tooltip" class="pointer-events-none fixed z-50 hidden max-w-xs rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-medium text-white shadow-lg"></div>
<script src="/js/admin.js?v=<?= $e((string) @filemtime(dirname(__DIR__, 3) . '/public/js/admin.js')) ?>" defer></script>
