<?php $e = static fn (mixed $v): string => HaloSec\View::e($v); ?>
<?php if ($rows === []) : ?>
<p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No leads yet.</p>
<?php else : ?>
<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500"><tr>
<th class="px-4 py-3">Received (UTC)</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3"></th>
</tr></thead>
<tbody class="divide-y divide-slate-100">
<?php foreach ($rows as $row) : ?>
<tr>
<td class="px-4 py-3 whitespace-nowrap"><?= $e(str_replace('T', ' ', substr($row['created_at'], 0, 19))) ?></td>
<td class="px-4 py-3 capitalize"><?= $e($row['type']) ?></td>
<td class="px-4 py-3"><?= $e($row['data']['name'] ?? '—') ?></td>
<td class="px-4 py-3"><?= $e($row['data']['email'] ?? '—') ?></td>
<td class="px-4 py-3"><a class="font-bold text-sky-700 hover:underline" href="/admin/leads/<?= $e($row['type']) ?>/<?= $e(substr($row['id'], strlen($row['type']) + 1)) ?>">View</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
