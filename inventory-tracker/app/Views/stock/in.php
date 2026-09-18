<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center space-x-2">
                <i class="bi bi-box-arrow-in-down text-emerald-600"></i>
                <span>Receive Inventory (Stock In)</span>
            </h1>
            <p class="text-xs text-slate-500">Record inbound inventory intake from supplier purchase order or customer return</p>
        </div>
        <a href="/stock/history" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; View Ledger
        </a>
    </div>

    <form action="/stock/in" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
        <?= Csrf::field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Inbound PO / Reference #</label>
                <input type="text" name="reference_no" placeholder="e.g. PO-2026-9921 (auto if empty)" 
                       class="w-full px-3.5 py-2 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Receipt Reason <span class="text-rose-500">*</span></label>
                <select name="reason" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="Purchase Receipt">Supplier Purchase Receipt</option>
                    <option value="Customer Return">Customer Return</option>
                    <option value="Initial Balance">Initial Inventory Load</option>
                    <option value="Supplier Bonus">Supplier Promotional Sample</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Select Item / Product <span class="text-rose-500">*</span></label>
            <select name="product_id" id="productSelect" required onchange="updateProductSpecs(this)" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">Select a product...</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>" data-cost="<?= $p['cost_price'] ?>" data-unit="<?= $p['unit'] ?>" <?= ($_GET['product_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                        <?= View::e($p['name']) ?> (SKU: <?= View::e($p['sku']) ?>) - Current Stock: <?= $p['total_stock'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Receiving Location / Warehouse <span class="text-rose-500">*</span></label>
            <select name="location_id" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">Select destination location...</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc['id'] ?>">
                        <?= View::e($loc['name']) ?> (<?= View::e($loc['code']) ?> - <?= ucfirst($loc['type']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Quantity Received <span class="text-rose-500">*</span></label>
                <input type="number" min="1" name="quantity" required placeholder="10" 
                       class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Unit Cost ($)</label>
                <input type="number" step="0.01" min="0" id="unitCostInput" name="unit_cost" value="0.00" 
                       class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Bill of Lading</label>
            <textarea name="notes" rows="2" placeholder="Driver name, courier tracking number, pallet ID, or delivery inspection remarks..." 
                      class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
        </div>

        <div class="pt-3 flex justify-end space-x-2">
            <a href="/stock/history" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-emerald-600/20">
                Confirm Stock In
            </button>
        </div>
    </form>
</div>

<script>
function updateProductSpecs(select) {
    const opt = select.options[select.selectedIndex];
    if (opt && opt.dataset.cost) {
        document.getElementById('unitCostInput').value = parseFloat(opt.dataset.cost).toFixed(2);
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('productSelect');
    if (el) updateProductSpecs(el);
});
</script>
