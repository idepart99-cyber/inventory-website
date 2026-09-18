<?php
use App\Core\View;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Product & Item Catalog</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage SKUs, barcodes, pricing, stock levels & supplier links</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="/products/labels" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs transition-all">
                <i class="bi bi-printer text-sky-600"></i>
                <span>Print Barcodes</span>
            </a>
            <a href="/products/export" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs transition-all">
                <i class="bi bi-file-earmark-spreadsheet text-emerald-600"></i>
                <span>Export CSV</span>
            </a>
            <a href="/products/create" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-sky-600/20 transition-all">
                <i class="bi bi-plus-lg"></i>
                <span>Add Product</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="/products" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Keyword Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="<?= View::e($filters['search'] ?? '') ?>" placeholder="Search by name, SKU, or barcode..." 
                           class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Category</label>
                <select name="category_id" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= View::e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($filters['supplier_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                            <?= View::e($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-1.5 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition-colors">
                    Filter
                </button>
                <a href="/products" class="py-1.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Product & SKU</th>
                        <th class="px-4 py-3.5">Category</th>
                        <th class="px-4 py-3.5">Barcode</th>
                        <th class="px-4 py-3.5">Unit Cost</th>
                        <th class="px-4 py-3.5">Retail Price</th>
                        <th class="px-4 py-3.5">Stock Level</th>
                        <th class="px-4 py-3.5">Valuation</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-box-seam text-3xl mb-2 block text-slate-300"></i>
                                No products found matching your search filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <?php 
                                $isLow = $p['total_stock'] <= $p['min_stock_alert'];
                                $isOut = $p['total_stock'] == 0;
                            ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 font-bold text-xs flex-shrink-0">
                                            <i class="bi bi-box"></i>
                                        </div>
                                        <div>
                                            <a href="/products/<?= $p['id'] ?>" class="font-bold text-slate-900 hover:text-sky-600 block">
                                                <?= View::e($p['name']) ?>
                                            </a>
                                            <span class="font-mono text-[11px] text-slate-400 font-medium">SKU: <?= View::e($p['sku']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">
                                        <?= View::e($p['category_name']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-500 text-[11px]">
                                    <?= View::e($p['barcode'] ?? '—') ?>
                                </td>
                                <td class="px-4 py-3.5 font-medium">
                                    <?= View::currency($p['cost_price']) ?>
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900">
                                    <?= View::currency($p['selling_price']) ?>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-black text-xs <?= $isOut ? 'text-rose-600' : ($isLow ? 'text-amber-600' : 'text-slate-900') ?>">
                                            <?= number_format((float)$p['total_stock']) ?> <?= View::e($p['unit']) ?>
                                        </span>
                                        <?php if ($isOut): ?>
                                            <span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-800 text-[9px] font-bold uppercase">Out</span>
                                        <?php elseif ($isLow): ?>
                                            <span class="px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 text-[9px] font-bold uppercase">Low</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-[10px] text-slate-400">Min Alert: <?= $p['min_stock_alert'] ?></span>
                                </td>
                                <td class="px-4 py-3.5 font-medium text-slate-700">
                                    <?= View::currency($p['total_valuation']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-1">
                                    <a href="/stock/in?product_id=<?= $p['id'] ?>" class="p-1.5 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-lg inline-flex items-center justify-center transition-colors" title="Stock In">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                    </a>
                                    <a href="/products/labels?product_id=<?= $p['id'] ?>" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg inline-flex items-center justify-center transition-colors" title="Print Barcode Label">
                                        <i class="bi bi-upc"></i>
                                    </a>
                                    <a href="/products/<?= $p['id'] ?>/edit" class="p-1.5 bg-sky-50 hover:bg-sky-600 hover:text-white text-sky-700 rounded-lg inline-flex items-center justify-center transition-colors" title="Edit Product">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Showing <?= count($products) ?> of <?= $totalCount ?> products</span>
                <div class="flex space-x-1">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="/products?page=<?= $i ?>&search=<?= urlencode($filters['search'] ?? '') ?>&category_id=<?= $filters['category_id'] ?? '' ?>" 
                           class="px-2.5 py-1 rounded-lg border <?= $i === $currentPage ? 'bg-sky-600 border-sky-600 text-white font-bold' : 'bg-white border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
