<?php
use App\Core\View;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Department Requisitions</h1>
            <p class="text-xs text-slate-500 mt-0.5">Internal multi-department supply requests, approvals & warehouse fulfillment</p>
        </div>
        <div>
            <a href="/requisitions/create" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-sky-600/20 transition-all">
                <i class="bi bi-plus-lg"></i>
                <span>Submit Requisition</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="/requisitions" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="w-48">
                <select name="status" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                    <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="fulfilled" <?= ($filters['status'] ?? '') === 'fulfilled' ? 'selected' : '' ?>>Fulfilled</option>
                    <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <div class="w-56">
                <select name="department_id" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($filters['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                            <?= View::e($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="py-1.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold">
                Filter
            </button>
            <a href="/requisitions" class="py-1.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">
                Reset
            </a>
        </form>
    </div>

    <!-- Requisitions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Requisition #</th>
                        <th class="px-4 py-3.5">Department</th>
                        <th class="px-4 py-3.5">Requested By</th>
                        <th class="px-4 py-3.5">Items / Total Qty</th>
                        <th class="px-4 py-3.5">Priority</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Submitted</th>
                        <th class="px-5 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($requisitions)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-clipboard2-check text-3xl mb-2 block text-slate-300"></i>
                                No department requisitions recorded.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requisitions as $r): ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <a href="/requisitions/<?= $r['id'] ?>" class="font-mono font-bold text-sky-600 hover:text-sky-800">
                                        <?= View::e($r['req_number']) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900">
                                    <?= View::e($r['department_name']) ?>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600">
                                    <?= View::e($r['requested_by_name']) ?>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-800"><?= $r['total_items'] ?> line items</span>
                                    <span class="text-[10px] text-slate-400 block">(<?= $r['total_quantity'] ?> units)</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <?= View::badge($r['priority'], ucfirst($r['priority'])) ?>
                                </td>
                                <td class="px-4 py-3.5">
                                    <?= View::badge($r['status'], ucfirst($r['status'])) ?>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-[11px] text-slate-400">
                                    <?= View::date($r['created_at'], 'M d, Y') ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/requisitions/<?= $r['id'] ?>" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
                                        Review &rarr;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
