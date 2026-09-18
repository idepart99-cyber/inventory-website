<?php
use App\Core\View;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="/products" class="hover:text-slate-600">Products</a>
                <span>/</span>
                <span><?= View::e($product['category_name']) ?></span>
                <span>/</span>
                <span class="text-slate-700 font-semibold font-mono"><?= View::e($product['sku']) ?></span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight"><?= View::e($product['name']) ?></h1>
        </div>
        <div class="flex items-center space-x-2">
            <a href="/products/labels?product_id=<?= $product['id'] ?>" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl shadow-xs">
                <i class="bi bi-printer mr-1 text-sky-600"></i> Print Barcode
            </a>
            <a href="/stock/in?product_id=<?= $product['id'] ?>" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                <i class="bi bi-box-arrow-in-down mr-1"></i> Stock In
            </a>
            <a href="/products/<?= $product['id'] ?>/edit" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                <i class="bi bi-pencil mr-1"></i> Edit Product
            </a>
        </div>
    </div>

    <!-- Product Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-5">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Specifications & Description</h3>
                <p class="text-sm text-slate-700 leading-relaxed"><?= nl2br(View::e($product['description'] ?? 'No additional description provided.')) ?></p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Primary Supplier</span>
                    <span class="font-bold text-slate-800"><?= View::e($product['supplier_name'] ?? 'Direct Manufacturer') ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Unit Cost</span>
                    <span class="font-bold text-slate-800"><?= View::currency($product['cost_price']) ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Retail Selling Price</span>
                    <span class="font-bold text-emerald-600"><?= View::currency($product['selling_price']) ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Status</span>
                    <?= View::badge($product['status'], $product['status']) ?>
                </div>
            </div>

            <!-- Locations Breakdown -->
            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-900 mb-3 flex items-center justify-between">
                    <span>Stock Distribution Across Locations</span>
                    <span class="text-slate-400 font-normal">Total: <strong class="text-slate-800"><?= number_format((float)$product['total_stock']) ?> <?= View::e($product['unit']) ?></strong></span>
                </h4>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-500 font-semibold">
                            <tr>
                                <th class="px-4 py-2.5">Location / Hub</th>
                                <th class="px-3 py-2.5">Type</th>
                                <th class="px-3 py-2.5">Aisle / Bin</th>
                                <th class="px-4 py-2.5 text-right">Quantity On Hand</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php foreach ($stockByLocation as $loc): ?>
                                <tr>
                                    <td class="px-4 py-2.5 font-medium text-slate-900">
                                        <?= View::e($loc['location_name']) ?> (<?= View::e($loc['location_code']) ?>)
                                    </td>
                                    <td class="px-3 py-2.5 uppercase text-[10px] text-slate-500">
                                        <?= View::e($loc['location_type']) ?>
                                    </td>
                                    <td class="px-3 py-2.5 font-mono text-[11px] text-slate-500">
                                        <?= View::e($loc['aisle_bin'] ?? 'General') ?>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-black font-mono <?= (int)$loc['quantity'] > 0 ? 'text-slate-900' : 'text-slate-300' ?>">
                                        <?= number_format((float)$loc['quantity']) ?> <?= View::e($product['unit']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Barcode Preview Card -->
        <div class="space-y-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs text-center space-y-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Official Item Barcode</span>
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col items-center justify-center">
                    <svg id="barcodeSvg" class="max-w-full h-16"></svg>
                    <span class="font-mono text-xs font-bold text-slate-800 tracking-widest mt-1"><?= View::e($product['barcode'] ?? $product['sku']) ?></span>
                </div>
                <p class="text-[11px] text-slate-400">Barcode recognized across web camera & handheld laser scanners.</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2.5 text-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Inventory Financials</span>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Stock Cost Value:</span>
                    <span class="font-bold text-slate-900"><?= View::currency($product['total_cost_value']) ?></span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Stock Retail Value:</span>
                    <span class="font-bold text-emerald-600"><?= View::currency($product['total_retail_value']) ?></span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Min Alert Level:</span>
                    <span class="font-bold text-slate-700"><?= $product['min_stock_alert'] ?> <?= View::e($product['unit']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    try {
        JsBarcode("#barcodeSvg", "<?= View::e($product['barcode'] ?: $product['sku']) ?>", {
            format: "CODE128",
            width: 1.8,
            height: 50,
            displayValue: false
        });
    } catch (e) {
        console.warn("Barcode rendering fallback:", e);
    }
});
</script>
