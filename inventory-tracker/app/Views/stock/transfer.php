<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center space-x-2">
                <i class="bi bi-arrow-left-right text-amber-500"></i>
                <span>Transfer Inventory Between Locations</span>
            </h1>
            <p class="text-xs text-slate-500">Relocate stock from central warehouse to shop, office, or secondary distribution hub</p>
        </div>
        <a href="/stock/history" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; View Ledger
        </a>
    </div>

    <form action="/stock/transfer" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
        <?= Csrf::field() ?>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Transfer Manifest Reference #</label>
            <input type="text" name="reference_no" placeholder="e.g. TR-2026-881 (auto if empty)" 
                   class="w-full px-3.5 py-2 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Item to Transfer <span class="text-rose-500">*</span></label>
            <select name="product_id" id="transferProdSelect" required onchange="checkSourceStock()" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">Select item...</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= View::e($p['name']) ?> (SKU: <?= View::e($p['sku']) ?>) - Total: <?= $p['total_stock'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">From (Origin Location) <span class="text-rose-500">*</span></label>
                <select name="source_location_id" id="transferSrcSelect" required onchange="checkSourceStock()" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select origin location...</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id'] ?>"><?= View::e($loc['name']) ?> (<?= View::e($loc['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <span id="transferStockNotice" class="text-[11px] text-slate-500 mt-1 block">Origin stock will be checked</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">To (Destination Location) <span class="text-rose-500">*</span></label>
                <select name="dest_location_id" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select target destination...</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id'] ?>"><?= View::e($loc['name']) ?> (<?= View::e($loc['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Transfer Quantity <span class="text-rose-500">*</span></label>
            <input type="number" min="1" name="quantity" required placeholder="5" 
                   class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Transfer Reason / Courier Details</label>
            <textarea name="notes" rows="2" placeholder="Driver name, van license plate, or seasonal storefront replenishment reason..." 
                      class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
        </div>

        <div class="pt-3 flex justify-end space-x-2">
            <a href="/stock/history" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-amber-600/20">
                Execute Stock Transfer
            </button>
        </div>
    </form>
</div>

<script>
async function checkSourceStock() {
    const prodId = document.getElementById('transferProdSelect').value;
    const locId = document.getElementById('transferSrcSelect').value;
    const notice = document.getElementById('transferStockNotice');

    if (!prodId || !locId) return;

    try {
        const res = await fetch('/api/stock/' + prodId + '/' + locId);
        const data = await res.json();
        notice.innerHTML = 'Available at origin: <strong class="text-slate-900">' + data.quantity + ' units</strong>';
    } catch(e) {
        console.warn(e);
    }
}
</script>
