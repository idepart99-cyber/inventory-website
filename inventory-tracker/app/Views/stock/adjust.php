<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center space-x-2">
                <i class="bi bi-sliders text-sky-600"></i>
                <span>Inventory Audit & Stock Adjustment</span>
            </h1>
            <p class="text-xs text-slate-500">Reconcile physical inventory counts against system records (damaged, expired, surplus, or recount)</p>
        </div>
        <a href="/stock/history" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; View Ledger
        </a>
    </div>

    <form action="/stock/adjust" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
        <?= Csrf::field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Audit Reference #</label>
                <input type="text" name="reference_no" placeholder="e.g. AUDIT-2026-Q3 (auto if empty)" 
                       class="w-full px-3.5 py-2 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Adjustment Reason <span class="text-rose-500">*</span></label>
                <select name="reason" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="Physical Recount">Physical Recount / Routine Cycle Audit</option>
                    <option value="Damaged Stock">Damaged / Broken in Transit</option>
                    <option value="Expired Goods">Expired / Shelf Life Deprecated</option>
                    <option value="Theft / Loss Discrepancy">Unaccounted Shrinkage / Loss</option>
                    <option value="System Inaccuracy">Data Entry Correction</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Item to Audit <span class="text-rose-500">*</span></label>
            <select name="product_id" id="adjProdSelect" required onchange="checkAuditStock()" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">Select item...</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= View::e($p['name']) ?> (SKU: <?= View::e($p['sku']) ?>) - Total System: <?= $p['total_stock'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Audited Location <span class="text-rose-500">*</span></label>
            <select name="location_id" id="adjLocSelect" required onchange="checkAuditStock()" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">Select location being audited...</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc['id'] ?>"><?= View::e($loc['name']) ?> (<?= View::e($loc['code']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <span id="adjStockNotice" class="text-[11px] text-slate-500 mt-1 block">Current system balance: —</span>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Actual Physical Count Found <span class="text-rose-500">*</span></label>
            <input type="number" min="0" name="actual_count" required placeholder="Enter the exact quantity physically counted..." 
                   class="w-full px-3.5 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            <p class="text-[11px] text-slate-400 mt-1">The system will automatically record the delta difference and reset current stock to this exact number.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Auditor Remarks / Justification</label>
            <textarea name="notes" rows="2" placeholder="Explain the root cause of variance, auditor initials, or corrective actions taken..." 
                      class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
        </div>

        <div class="pt-3 flex justify-end space-x-2">
            <a href="/stock/history" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-sky-600/20">
                Commit Stock Adjustment
            </button>
        </div>
    </form>
</div>

<script>
async function checkAuditStock() {
    const prodId = document.getElementById('adjProdSelect').value;
    const locId = document.getElementById('adjLocSelect').value;
    const notice = document.getElementById('adjStockNotice');

    if (!prodId || !locId) return;

    try {
        const res = await fetch('/api/stock/' + prodId + '/' + locId);
        const data = await res.json();
        notice.innerHTML = 'Current system recorded stock at this location: <strong class="text-slate-900 font-mono">' + data.quantity + ' units</strong>';
    } catch(e) {
        console.warn(e);
    }
}
</script>
