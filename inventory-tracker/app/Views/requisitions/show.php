<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="/requisitions" class="hover:text-slate-600">Requisitions</a>
                <span>/</span>
                <span class="font-mono text-slate-800 font-bold"><?= View::e($requisition['req_number']) ?></span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Requisition Review</h1>
        </div>
        <div class="flex items-center space-x-2">
            <?= View::badge($requisition['status'], ucfirst($requisition['status'])) ?>
            <?= View::badge($requisition['priority'], ucfirst($requisition['priority']) . ' Priority') ?>
        </div>
    </div>

    <!-- Requisition Overview Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[11px]">Department</span>
                <span class="font-bold text-slate-900"><?= View::e($requisition['department_name']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Requested By</span>
                <span class="font-bold text-slate-900"><?= View::e($requisition['requested_by_name']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Needed By</span>
                <span class="font-bold text-slate-900"><?= View::date($requisition['needed_by_date'], 'M d, Y') ?></span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Reviewed By</span>
                <span class="font-bold text-slate-900"><?= View::e($requisition['approved_by_name'] ?? 'Pending Review') ?></span>
            </div>
        </div>

        <?php if (!empty($requisition['notes'])): ?>
            <div class="p-3.5 bg-slate-50 rounded-xl text-xs text-slate-700">
                <span class="font-bold text-slate-500 block mb-0.5">Notes / Purpose:</span>
                <?= View::e($requisition['notes']) ?>
            </div>
        <?php endif; ?>

        <!-- Requisition Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Item Name & SKU</th>
                        <th class="px-4 py-3 text-center">Requested</th>
                        <th class="px-4 py-3 text-center">Approved</th>
                        <th class="px-4 py-3 text-center">Fulfilled</th>
                        <th class="px-4 py-3 text-right">Available in Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($requisition['items'] as $item): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">
                                <a href="/products/<?= $item['product_id'] ?>" class="font-bold hover:text-sky-600 block">
                                    <?= View::e($item['product_name']) ?>
                                </a>
                                <span class="font-mono text-[10px] text-slate-400"><?= View::e($item['sku']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold font-mono">
                                <?= $item['requested_quantity'] ?> <?= View::e($item['unit']) ?>
                            </td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-sky-600">
                                <?= $item['approved_quantity'] ?>
                            </td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-emerald-600">
                                <?= $item['fulfilled_quantity'] ?>
                            </td>
                            <td class="px-4 py-3 text-right font-black font-mono <?= $item['current_global_stock'] > 0 ? 'text-slate-900' : 'text-rose-600' ?>">
                                <?= $item['current_global_stock'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Action Buttons Workflow -->
        <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-2 justify-end">
            <?php if ($requisition['status'] === 'pending'): ?>
                <!-- Approve Form -->
                <form action="/requisitions/<?= $requisition['id'] ?>/approve" method="POST" class="inline">
                    <?= Csrf::field() ?>
                    <?php foreach ($requisition['items'] as $it): ?>
                        <input type="hidden" name="approved_qty[<?= $it['id'] ?>]" value="<?= $it['requested_quantity'] ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                        <i class="bi bi-check-lg mr-1"></i> Approve Requisition
                    </button>
                </form>

                <!-- Reject Form -->
                <form action="/requisitions/<?= $requisition['id'] ?>/reject" method="POST" class="inline" onsubmit="return confirm('Reject this requisition?');">
                    <?= Csrf::field() ?>
                    <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-xl">
                        <i class="bi bi-x-lg mr-1"></i> Reject
                    </button>
                </form>
            <?php endif; ?>

            <?php if (in_array($requisition['status'], ['pending', 'approved'])): ?>
                <!-- Fulfill Modal Trigger -->
                <button onclick="document.getElementById('fulfillModal').classList.remove('hidden')" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    <i class="bi bi-box-arrow-up-right mr-1"></i> Fulfill from Warehouse Stock
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Fulfill Modal -->
<div id="fulfillModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Fulfill & Dispatch Requisition</h3>
        <p class="text-xs text-slate-500">Select the warehouse location to deduct inventory items from. Stock Out movements will be logged automatically.</p>
        
        <form action="/requisitions/<?= $requisition['id'] ?>/fulfill" method="POST" class="space-y-4">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Source Warehouse Location</label>
                <select name="source_location_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl">
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id'] ?>"><?= View::e($loc['name']) ?> (<?= View::e($loc['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('fulfillModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold">
                    Confirm & Dispatch
                </button>
            </div>
        </form>
    </div>
</div>
