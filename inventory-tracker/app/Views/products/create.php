<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Create New Inventory Product</h1>
            <p class="text-xs text-slate-500">Register an item into the centralized catalog with pricing and reorder triggers</p>
        </div>
        <a href="/products" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Back to Catalog
        </a>
    </div>

    <form action="/products" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
        <?= Csrf::field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Product SKU <span class="text-rose-500">*</span></label>
                <div class="flex space-x-2">
                    <input type="text" id="skuInput" name="sku" required placeholder="e.g. TECH-LAP-003" 
                           class="flex-1 px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <button type="button" onclick="generateSku()" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                        Auto
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Barcode (EAN / UPC / Code-128)</label>
                <div class="flex space-x-2">
                    <input type="text" id="barcodeInput" name="barcode" placeholder="e.g. 890100100200" 
                           class="flex-1 px-3 py-2 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <button type="button" onclick="generateBarcode()" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                        Generate
                    </button>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Product Title / Description <span class="text-rose-500">*</span></label>
            <input type="text" name="name" required placeholder="e.g. Lenovo ThinkPad X1 Carbon Gen 11 14"" 
                   class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Detailed Technical Specifications / Notes</label>
            <textarea name="description" rows="2" placeholder="Hardware specs, material composition, or internal warehouse handling instructions..." 
                      class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= View::e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select Supplier (Optional)</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>"><?= View::e($sup['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Unit of Measure</label>
                <select name="unit" class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="pcs">Pieces (pcs)</option>
                    <option value="box">Box</option>
                    <option value="pack">Pack</option>
                    <option value="roll">Roll</option>
                    <option value="kg">Kilogram (kg)</option>
                    <option value="meter">Meter (m)</option>
                    <option value="set">Set / Kit</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2 border-t border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cost Price ($)</label>
                <input type="number" step="0.01" min="0" name="cost_price" value="0.00" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Selling Price ($)</label>
                <input type="number" step="0.01" min="0" name="selling_price" value="0.00" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Min Reorder Alert</label>
                <input type="number" min="1" name="min_stock_alert" value="10" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Max Target Level</label>
                <input type="number" min="1" name="max_stock_level" value="500" required 
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 bg-slate-50/60 p-4 rounded-xl space-y-2">
            <h4 class="text-xs font-bold text-slate-800">Initial Stock Inbound (Optional)</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Primary Storing Location</label>
                    <select name="initial_location_id" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-300 rounded-xl">
                        <option value="">No Initial Stock (Register catalog only)</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>"><?= View::e($loc['name']) ?> (<?= View::e($loc['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Initial Quantity In</label>
                    <input type="number" min="0" name="initial_quantity" value="0" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-300 rounded-xl">
                </div>
            </div>
        </div>

        <div class="pt-3 flex justify-end space-x-2">
            <a href="/products" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-sky-600/20">
                Save & Register Product
            </button>
        </div>
    </form>
</div>

<script>
function generateSku() {
    const rand = Math.floor(1000 + Math.random() * 9000);
    document.getElementById('skuInput').value = 'PROD-' + rand;
}
function generateBarcode() {
    const code = '890' + Math.floor(100000000 + Math.random() * 900000000);
    document.getElementById('barcodeInput').value = code;
}
</script>
