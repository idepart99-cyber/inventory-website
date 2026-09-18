<?php
use App\Core\View;
$margin = $stats['total_cost_value'] > 0 ? (($stats['total_retail_value'] - $stats['total_cost_value']) / $stats['total_cost_value']) * 100 : 0;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Inventory Valuation & Financial Audit</h1>
            <p class="text-xs text-slate-500 mt-0.5">Asset capital tied up in inventory, gross potential retail margins & category distribution</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs">
            <i class="bi bi-printer text-sky-600"></i>
            <span>Print Report</span>
        </button>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Asset Value (At Cost)</span>
            <p class="text-2xl font-black text-slate-900 mt-1"><?= View::currency($stats['total_cost_value']) ?></p>
            <p class="text-xs text-slate-500 mt-1">Direct capital investment on hand</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Gross Retail Realization</span>
            <p class="text-2xl font-black text-emerald-600 mt-1"><?= View::currency($stats['total_retail_value']) ?></p>
            <p class="text-xs text-slate-500 mt-1">If all physical units sold at list price</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Projected Profit Margin</span>
            <p class="text-2xl font-black text-sky-600 mt-1"><?= number_format($margin, 1) ?>%</p>
            <p class="text-xs text-slate-500 mt-1">Potential gross markup spread</p>
        </div>
    </div>

    <!-- Breakdown Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- By Category -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Valuation by Category</h3>
            </div>
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                    <tr>
                        <th class="px-4 py-2.5">Category</th>
                        <th class="px-4 py-2.5 text-center">Products</th>
                        <th class="px-4 py-2.5 text-right">Physical Units</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-slate-900"><?= View::e($cat['name']) ?></td>
                            <td class="px-4 py-2.5 text-center"><?= $cat['products_count'] ?></td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold"><?= number_format((float)$cat['total_stock']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- By Location -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Asset Distribution by Facility</h3>
            </div>
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                    <tr>
                        <th class="px-4 py-2.5">Facility / Outlet</th>
                        <th class="px-4 py-2.5 text-center">Stored Items</th>
                        <th class="px-4 py-2.5 text-right">Valuation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($locations as $loc): ?>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-slate-900"><?= View::e($loc['name']) ?></td>
                            <td class="px-4 py-2.5 text-center font-mono"><?= number_format((float)$loc['total_items_count']) ?></td>
                            <td class="px-4 py-2.5 text-right font-bold text-emerald-600"><?= View::currency($loc['total_valuation']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
