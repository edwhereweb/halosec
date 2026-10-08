<div class="flex flex-wrap items-center justify-between gap-3 mb-8">
  <nav class="flex gap-4 text-sm font-bold" aria-label="Admin">
    <a class="text-sky-700 hover:underline" href="/admin">Dashboard</a>
    <a class="text-sky-700 hover:underline" href="/admin/leads">All leads</a>
  </nav>
  <form method="post" action="/admin/logout">
    <input type="hidden" name="_csrf" value="<?= HaloSec\View::e($csrfToken) ?>">
    <button type="submit" class="text-xs font-bold uppercase tracking-wider rounded-full border border-slate-300 px-4 py-2 hover:bg-slate-100">Log out</button>
  </form>
</div>
