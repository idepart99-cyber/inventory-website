<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Supplier Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Approved vendors, contact representatives & procurement payment terms</p>
        </div>
        <button onclick="document.getElementById('addSupModal').classList.remove('hidden')" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-sky-600/20">
            <i class="bi bi-plus-lg"></i>
            <span>Add Supplier</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($suppliers as $s): ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded">
                        <?= View::e($s['code']) ?>
                    </span>
                    <span class="text-xs font-semibold text-slate-700"><?= $s['products_count'] ?> Products</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base"><?= View::e($s['name']) ?></h3>
                    <p class="text-xs text-slate-400"><?= View::e($s['contact_person'] ?? 'No representative assigned') ?></p>
                </div>
                <div class="space-y-1 text-xs text-slate-600">
                    <div class="flex items-center space-x-2">
                        <i class="bi bi-envelope text-slate-400"></i>
                        <span><?= View::e($s['email'] ?? '—') ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="bi bi-telephone text-slate-400"></i>
                        <span><?= View::e($s['phone'] ?? '—') ?></span>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                    <span class="text-slate-400">Terms:</span>
                    <span class="font-bold text-slate-800"><?= View::e($s['payment_terms'] ?? 'Net 30') ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Supplier Modal -->
<div id="addSupModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Add Supplier</h3>
        <form action="/suppliers" method="POST" class="space-y-3">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Company / Vendor Name</label>
                <input type="text" name="name" required placeholder="e.g. Global Tech Distributors" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Code</label>
                    <input type="text" name="code" required placeholder="e.g. SUP-GTD" class="w-full px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="Representative" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="orders@vendor.com" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone</label>
                    <input type="text" name="phone" placeholder="+1-800-..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addSupModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold">Save Supplier</button>
            </div>
        </form>
    </div>
</div>
