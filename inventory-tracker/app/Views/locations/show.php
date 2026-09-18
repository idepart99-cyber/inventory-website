<?php
use App\Core\View;
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="/locations" class="hover:text-slate-600">Locations</a>
                <span>/</span>
                <span class="font-mono text-slate-800 font-bold"><?= View::e($location['code']) ?></span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight"><?= View::e($location['name']) ?></h1>
            <p class="text-xs text-slate-500 mt-0.5"><?= View::e($location['address'] ?? 'No address registered') ?></p>
        </div>
        <a href="/locations" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Back to Locations
        </a>
    </div>

    <!-- Inventory Table at this Location -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Current Items in Stock</h3>
            <span class="text-xs text-slate-500 font-semibold"><?= count($items) ?> unique products stored</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Product Name & SKU</th>
                        <th class="px-4 py-3.5">Category</th>
                        <th class="px-4 py-3.5">Aisle / Bin</th>
                        <th class="px-4 py-3.5 text-center">Quantity</th>
                        <th class="px-4 py-3.5">Unit Cost</th>
                        <th class="px-4 py-3.5 text-right">Subtotal Valuation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="p-8 text-center text-slate-400">No inventory items stocked at this location.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td class="px-5 py-3">
                                    <a href="/products/<?= $it['id'] ?>" class="font-bold text-slate-900 hover:text-sky-600 block"><?= View::e($it['name']) ?></a>
                                    <span class="font-mono text-[10px] text-slate-400"><?= View::e($it['sku']) ?></span>
                                </td>
                                <td class="px-4 py-3"><?= View::e($it['category_name']) ?></td>
                                <td class="px-4 py-3 font-mono text-[11px] text-slate-500"><?= View::e($it['aisle_bin'] ?? 'General') ?></td>
                                <td class="px-4 py-3 text-center font-bold font-mono text-slate-900"><?= $it['quantity'] ?> <?= View::e($it['unit']) ?></td>
                                <td class="px-4 py-3 font-mono"><?= View::currency($it['cost_price']) ?></td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900"><?= View::currency($it['total_cost']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
