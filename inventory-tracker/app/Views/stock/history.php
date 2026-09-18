<?php
use App\Core\View;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Stock Movement Ledger</h1>
            <p class="text-xs text-slate-500 mt-0.5">Comprehensive audit trail of all receipts, issuances, transfers, and cycle audits</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="/stock/export" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs transition-all">
                <i class="bi bi-file-earmark-spreadsheet text-emerald-600"></i>
                <span>Export Ledger CSV</span>
            </a>
            <a href="/stock/in" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-emerald-600/20 transition-all">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>Stock In</span>
            </a>
            <a href="/stock/out" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-rose-600/20 transition-all">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Stock Out</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="/stock/history" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Movement Type</label>
                <select name="type" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">All Types</option>
                    <option value="IN" <?= ($filters['type'] ?? '') === 'IN' ? 'selected' : '' ?>>Stock In (Receipt)</option>
                    <option value="OUT" <?= ($filters['type'] ?? '') === 'OUT' ? 'selected' : '' ?>>Stock Out (Issue)</option>
                    <option value="TRANSFER" <?= ($filters['type'] ?? '') === 'TRANSFER' ? 'selected' : '' ?>>Transfer</option>
                    <option value="ADJUSTMENT" <?= ($filters['type'] ?? '') === 'ADJUSTMENT' ? 'selected' : '' ?>>Adjustment</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Item / SKU</label>
                <select name="product_id" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">All Products</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($filters['product_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                            <?= View::e($p['sku']) ?> - <?= View::e($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Location</label>
                <select name="location_id" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">All Locations</option>
                    <?php foreach ($locations as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= ($filters['location_id'] ?? '') == $l['id'] ? 'selected' : '' ?>>
                            <?= View::e($l['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Department</label>
                <select name="department_id" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($filters['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                            <?= View::e($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Date From</label>
                <input type="date" name="date_from" value="<?= View::e($filters['date_from'] ?? '') ?>" 
                       class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-1.5 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition-colors">
                    Filter
                </button>
                <a href="/stock/history" class="py-1.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Timestamp</th>
                        <th class="px-4 py-3.5">Reference</th>
                        <th class="px-4 py-3.5">Type</th>
                        <th class="px-5 py-3.5">Product & SKU</th>
                        <th class="px-4 py-3.5">Route (Source &rarr; Target)</th>
                        <th class="px-4 py-3.5">Quantity</th>
                        <th class="px-4 py-3.5">Balance After</th>
                        <th class="px-4 py-3.5">Staff / Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($movements)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No stock movement records found matching criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movements as $m): ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                    <?= View::date($m['created_at']) ?>
                                </td>
                                <td class="px-4 py-3 font-mono text-[11px] font-bold text-slate-900 whitespace-nowrap">
                                    <?= View::e($m['reference_no']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?= View::badge($m['type'], $m['type']) ?>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="font-bold text-slate-900 block"><?= View::e($m['product_name']) ?></span>
                                    <span class="font-mono text-[10px] text-slate-400"><?= View::e($m['sku']) ?></span>
                                </td>
                                <td class="px-4 py-3 text-[11px] text-slate-600">
                                    <?= View::e($m['source_location_name'] ?? 'Supplier / Ext') ?> &rarr; 
                                    <strong class="text-slate-800"><?= View::e($m['destination_location_name'] ?? ($m['department_name'] ? $m['department_name'] . ' Dept' : 'Dispatched')) ?></strong>
                                </td>
                                <td class="px-4 py-3 font-mono font-black <?= $m['type'] === 'IN' ? 'text-emerald-600' : ($m['type'] === 'OUT' ? 'text-rose-600' : 'text-slate-900') ?>">
                                    <?= $m['type'] === 'IN' ? '+' : ($m['type'] === 'OUT' ? '-' : '') ?><?= $m['quantity'] ?> <?= View::e($m['unit'] ?? '') ?>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-800">
                                    <?= $m['balance_after'] ?>
                                </td>
                                <td class="px-4 py-3 text-[11px] text-slate-500">
                                    <span class="font-semibold text-slate-800 block"><?= View::e($m['reason'] ?? 'Movement') ?></span>
                                    <span>by <?= View::e($m['user_name'] ?? 'System') ?></span>
                                    <?php if (!empty($m['notes'])): ?>
                                        <span class="block text-[10px] text-slate-400 italic"><?= View::e($m['notes']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
