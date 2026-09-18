<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">New Department Requisition</h1>
            <p class="text-xs text-slate-500">Request materials, workstations, or office items from warehouse</p>
        </div>
        <a href="/requisitions" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Back to List
        </a>
    </div>

    <form action="/requisitions" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
        <?= Csrf::field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Requesting Department <span class="text-rose-500">*</span></label>
                <select name="department_id" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select department...</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= View::e($d['name']) ?> (<?= View::e($d['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Urgency / Priority</label>
                <select name="priority" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="low">Low (Standard)</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent / Critical</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Needed By Date</label>
                <input type="date" name="needed_by_date" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
        </div>

        <!-- Dynamic Items Table -->
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Requested Line Items</h3>
                <button type="button" onclick="addItemRow()" class="inline-flex items-center space-x-1 text-xs font-semibold text-sky-600 hover:text-sky-800">
                    <i class="bi bi-plus-circle"></i>
                    <span>Add Item</span>
                </button>
            </div>

            <div class="space-y-2.5" id="itemRowsContainer">
                <div class="flex items-center space-x-2 item-row">
                    <select name="product_id[]" required class="flex-1 px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">Select product to request...</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= View::e($p['name']) ?> (SKU: <?= View::e($p['sku']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="quantity[]" min="1" value="1" required placeholder="Qty" class="w-24 px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <button type="button" onclick="removeItemRow(this)" class="p-2 text-slate-400 hover:text-rose-600">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Business Justification / Project Purpose</label>
            <textarea name="notes" rows="2" placeholder="e.g. New employee onboardings, showroom redesign, maintenance repairs..." 
                      class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
        </div>

        <div class="pt-3 flex justify-end space-x-2">
            <a href="/requisitions" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-sky-600/20">
                Submit for Approval
            </button>
        </div>
    </form>
</div>

<script>
function addItemRow() {
    const container = document.getElementById('itemRowsContainer');
    const firstRow = container.querySelector('.item-row');
    const clone = firstRow.cloneNode(true);
    clone.querySelector('select').value = '';
    clone.querySelector('input').value = '1';
    container.appendChild(clone);
}
function removeItemRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('.item-row').remove();
    }
}
</script>
