<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Product: <?= View::e($product['name']) ?></h1>
            <p class="text-xs text-slate-500">Update item pricing, category tags, barcode, and alert boundaries</p>
        </div>
        <a href="/products/<?= $product['id'] ?>" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Back to Details
        </a>
    </div>

    <form action="/products/<?= $product['id'] ?>/update" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
        <?= Csrf::field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Product SKU <span class="text-rose-500">*</span></label>
                <input type="text" name="sku" value="<?= View::e($product['sku']) ?>" required 
                       class="w-full px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Barcode (EAN / UPC / Code-128)</label>
                <input type="text" name="barcode" value="<?= View::e($product['barcode'] ?? '') ?>" 
                       class="w-full px-3 py-2 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="<?= View::e($product['name']) ?>" required 
                   class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Description / Notes</label>
            <textarea name="description" rows="2" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none"><?= View::e($product['description'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= View::e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">None</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= $product['supplier_id'] == $sup['id'] ? 'selected' : '' ?>>
                            <?= View::e($sup['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Unit</label>
                <select name="unit" class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <?php foreach (['pcs', 'box', 'pack', 'roll', 'kg', 'meter', 'set'] as $u): ?>
                        <option value="<?= $u ?>" <?= $product['unit'] === $u ? 'selected' : '' ?>><?= $u ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2 border-t border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cost Price ($)</label>
                <input type="number" step="0.01" min="0" name="cost_price" value="<?= $product['cost_price'] ?>" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Selling Price ($)</label>
                <input type="number" step="0.01" min="0" name="selling_price" value="<?= $product['selling_price'] ?>" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Min Reorder Alert</label>
                <input type="number" min="1" name="min_stock_alert" value="<?= $product['min_stock_alert'] ?>" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl">
                    <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="discontinued" <?= $product['status'] === 'discontinued' ? 'selected' : '' ?>>Discontinued</option>
                </select>
            </div>
        </div>

        <div class="pt-3 flex justify-between items-center">
            <button type="button" onclick="if(confirm('Delete product?')) { document.getElementById('deleteForm').submit(); }" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">
                Delete Product
            </button>
            <div class="flex space-x-2">
                <a href="/products/<?= $product['id'] ?>" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-sm">
                    Save Changes
                </button>
            </div>
        </div>
    </form>

    <form id="deleteForm" action="/products/<?= $product['id'] ?>/delete" method="POST" class="hidden">
        <?= Csrf::field() ?>
    </form>
</div>
