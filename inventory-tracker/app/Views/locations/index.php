<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Locations, Warehouses & Outlets</h1>
            <p class="text-xs text-slate-500 mt-0.5">Physical distribution hubs, storefronts, and office supply rooms</p>
        </div>
        <button onclick="document.getElementById('addLocModal').classList.remove('hidden')" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-sky-600/20">
            <i class="bi bi-plus-lg"></i>
            <span>Add Location</span>
        </button>
    </div>

    <!-- Locations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($locations as $loc): ?>
            <?php 
                $percent = $loc['capacity'] > 0 ? min(100, round(($loc['total_items_count'] / $loc['capacity']) * 100)) : 0;
            ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-xs font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded uppercase">
                            <?= View::e($loc['code']) ?>
                        </span>
                        <span class="text-[10px] uppercase font-bold text-slate-400">
                            <?= View::e($loc['type']) ?>
                        </span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base"><?= View::e($loc['name']) ?></h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?= View::e($loc['address'] ?? 'No street address set') ?></p>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-4 space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Utilization</span>
                        <span class="font-bold text-slate-900"><?= $percent ?>% (<?= number_format((float)$loc['total_items_count']) ?> units)</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-sky-500 rounded-full" style="width: <?= $percent ?>%"></div>
                    </div>
                    <div class="flex justify-between text-xs pt-1">
                        <span class="text-slate-500">Inventory Valuation:</span>
                        <span class="font-bold text-emerald-600"><?= View::currency($loc['total_valuation']) ?></span>
                    </div>
                    <div class="pt-2 flex items-center space-x-1.5">
                        <a href="/locations/<?= $loc['id'] ?>" class="flex-1 text-center py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold border border-slate-200 transition-colors">
                            View Items
                        </a>
                        <?php if (\App\Core\Auth::hasRole(['admin', 'manager'])): ?>
                            <button type="button" 
                                    onclick="openEditLocModal(<?= htmlspecialchars(json_encode($loc), ENT_QUOTES, 'UTF-8') ?>)" 
                                    class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors" 
                                    title="Edit Location">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="/locations/<?= $loc['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete the location \'<?= View::e(addslashes($loc['name'])) ?>\'?');">
                                <?= Csrf::field() ?>
                                <button type="submit" 
                                        class="p-1.5 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 rounded-xl text-xs font-semibold transition-colors" 
                                        title="Delete Location">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Location Modal -->
<div id="addLocModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Create Location</h3>
        <form action="/locations" method="POST" class="space-y-3">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Location Name</label>
                <input type="text" name="name" required placeholder="e.g. East Coast Warehouse" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Code</label>
                    <input type="text" name="code" required placeholder="e.g. LOC-WH03" class="w-full px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
                        <option value="warehouse">Warehouse</option>
                        <option value="shop">Retail Shop</option>
                        <option value="office">Office Stockroom</option>
                        <option value="other">Other Facility</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Capacity (Max Units)</label>
                <input type="number" name="capacity" value="10000" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Street Address</label>
                <textarea name="address" rows="2" placeholder="Full address..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addLocModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold">Save Location</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Location Modal -->
<div id="editLocModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Edit Location</h3>
        <form id="editLocForm" action="" method="POST" class="space-y-3">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Location Name</label>
                <input type="text" id="editLocName" name="name" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Code</label>
                    <input type="text" id="editLocCode" name="code" required class="w-full px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                    <select id="editLocType" name="type" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
                        <option value="warehouse">Warehouse</option>
                        <option value="shop">Retail Shop</option>
                        <option value="office">Office Stockroom</option>
                        <option value="other">Other Facility</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Capacity (Max Units)</label>
                <input type="number" id="editLocCapacity" name="capacity" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Street Address</label>
                <textarea id="editLocAddress" name="address" rows="2" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('editLocModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditLocModal(loc) {
    document.getElementById('editLocForm').action = '/locations/' + loc.id + '/update';
    document.getElementById('editLocName').value = loc.name || '';
    document.getElementById('editLocCode').value = loc.code || '';
    document.getElementById('editLocType').value = loc.type || 'warehouse';
    document.getElementById('editLocCapacity').value = loc.capacity || 1000;
    document.getElementById('editLocAddress').value = loc.address || '';
    document.getElementById('editLocModal').classList.remove('hidden');
}
</script>
