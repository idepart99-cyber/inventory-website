<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center space-x-2">
                <i class="bi bi-box-arrow-up-right text-rose-600"></i>
                <span>Dispatch / Issue Inventory (Stock Out)</span>
            </h1>
            <p class="text-xs text-slate-500">Record outbound stock issuance to internal department, customer sale, or disposal</p>
        </div>
        <a href="/stock/history" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; View Ledger
        </a>
    </div>

    <form action="/stock/out" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
        <?= Csrf::field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Reference / Dispatch #</label>
                <input type="text" name="reference_no" placeholder="e.g. DISP-2026-0041 (auto if empty)" 
                       class="w-full px-3.5 py-2 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Issue Reason <span class="text-rose-500">*</span></label>
                <select name="reason" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="Department Issuance">Department Usage Issuance</option>
                    <option value="Customer Order / Sale">Retail Sale / Customer Order</option>
                    <option value="Scrap / Disposal">Scrapped / Damaged Disposal</option>
                    <option value="Donation">Charity / Office Relocation</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Select Item / Product <span class="text-rose-500">*</span></label>
            <select name="product_id" id="prodOutSelect" required onchange="checkAvailableStock()" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">Select a product...</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($_GET['product_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                        <?= View::e($p['name']) ?> (SKU: <?= View::e($p['sku']) ?>) - Total: <?= $p['total_stock'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Source Location / Warehouse <span class="text-rose-500">*</span></label>
                <select name="location_id" id="locOutSelect" required onchange="checkAvailableStock()" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select source location...</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id'] ?>">
                            <?= View::e($loc['name']) ?> (<?= View::e($loc['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="locStockNotice" class="text-[11px] text-slate-500 mt-1 block">Select product and location to see available balance</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Recipient Department (Optional)</label>
                <select name="department_id" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">External Customer / None</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>"><?= View::e($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Quantity to Issue <span class="text-rose-500">*</span></label>
            <input type="number" min="1" name="quantity" required placeholder="1" 
                   class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Recipient Name / Purpose / Notes</label>
            <textarea name="notes" rows="2" placeholder="Recipient employee name, workstation number, or delivery slip info..." 
                      class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
        </div>

        <div class="pt-3 flex justify-end space-x-2">
            <a href="/stock/history" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-rose-600/20">
                Confirm Stock Out
            </button>
        </div>
    </form>
</div>

<script>
async function checkAvailableStock() {
    const prodId = document.getElementById('prodOutSelect').value;
    const locId = document.getElementById('locOutSelect').value;
    const notice = document.getElementById('locStockNotice');

    if (!prodId || !locId) return;

    try {
        const res = await fetch('/api/stock/' + prodId + '/' + locId);
        const data = await res.json();
        notice.innerHTML = 'Available at this location: <strong class="text-slate-900">' + data.quantity + ' units</strong>';
        if (data.quantity <= 0) {
            notice.innerHTML = '<span class="text-rose-600 font-bold">Out of stock at this location!</span>';
        }
    } catch(e) {
        console.warn(e);
    }
}
</script>
