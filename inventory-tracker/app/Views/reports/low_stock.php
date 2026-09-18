<?php
use App\Core\View;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Procurement & Replenishment Reorder Report</h1>
            <p class="text-xs text-slate-500 mt-0.5">Automated inventory reorder recommendations based on configured safety stock levels</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs">
            <i class="bi bi-printer text-sky-600"></i>
            <span>Print Reorder List</span>
        </button>
    </div>

    <!-- Low Stock Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Item Name & SKU</th>
                        <th class="px-4 py-3.5">Category</th>
                        <th class="px-4 py-3.5 text-center">Current Stock</th>
                        <th class="px-4 py-3.5 text-center">Safety Alert Min</th>
                        <th class="px-4 py-3.5 text-center">Suggested Reorder</th>
                        <th class="px-4 py-3.5">Est. Replenish Cost</th>
                        <th class="px-5 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($lowStockItems)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-shield-check text-3xl text-emerald-500 mb-2 block"></i>
                                Excellent! All catalog items are currently stocked above safety thresholds.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lowStockItems as $item): ?>
                            <?php 
                                $suggested = max(0, $item['max_stock_level'] - $item['total_stock']);
                                $estCost = $suggested * $item['cost_price'];
                            ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <a href="/products/<?= $item['id'] ?>" class="font-bold text-slate-900 hover:text-sky-600 block">
                                        <?= View::e($item['name']) ?>
                                    </a>
                                    <span class="font-mono text-[10px] text-slate-400"><?= View::e($item['sku']) ?></span>
                                </td>
                                <td class="px-4 py-3.5"><?= View::e($item['category_name']) ?></td>
                                <td class="px-4 py-3.5 text-center font-black font-mono <?= $item['total_stock'] == 0 ? 'text-rose-600' : 'text-amber-600' ?>">
                                    <?= $item['total_stock'] ?> <?= View::e($item['unit']) ?>
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-slate-500">
                                    <?= $item['min_stock_alert'] ?>
                                </td>
                                <td class="px-4 py-3.5 text-center font-black font-mono text-sky-600">
                                    +<?= $suggested ?> <?= View::e($item['unit']) ?>
                                </td>
                                <td class="px-4 py-3.5 font-bold font-mono text-slate-800">
                                    <?= View::currency($estCost) ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/stock/in?product_id=<?= $item['id'] ?>" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs inline-flex items-center space-x-1">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                        <span>Order / Stock In</span>
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
