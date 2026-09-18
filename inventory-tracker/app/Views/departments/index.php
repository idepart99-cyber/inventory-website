<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Departments & Divisions</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage business units for internal stock requisitioning and asset issuance</p>
        </div>
        <button onclick="document.getElementById('addDeptModal').classList.remove('hidden')" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-sky-600/20">
            <i class="bi bi-plus-lg"></i>
            <span>Add Department</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($departments as $d): ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                        <?= View::e($d['code']) ?>
                    </span>
                    <span class="text-xs text-slate-400"><?= $d['staff_count'] ?> Members</span>
                </div>
                <h3 class="font-bold text-slate-900 text-base"><?= View::e($d['name']) ?></h3>
                <p class="text-xs text-slate-500 line-clamp-2"><?= View::e($d['description'] ?? 'No description') ?></p>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Head / Manager:</span>
                        <span class="font-bold text-slate-800"><?= View::e($d['manager_name'] ?? 'Not Assigned') ?></span>
                    </div>
                    <?php if (\App\Core\Auth::hasRole(['admin', 'manager'])): ?>
                        <div class="flex items-center space-x-1.5">
                            <button type="button" 
                                    onclick="openEditDeptModal(<?= htmlspecialchars(json_encode($d), ENT_QUOTES, 'UTF-8') ?>)" 
                                    class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold inline-flex items-center space-x-1 transition-colors" 
                                    title="Edit Department">
                                <i class="bi bi-pencil"></i>
                                <span>Edit</span>
                            </button>
                            <form action="/departments/<?= $d['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete the \'<?= View::e(addslashes($d['name'])) ?>\' department?');">
                                <?= Csrf::field() ?>
                                <button type="submit" 
                                        class="px-2 py-1 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 rounded-lg text-xs font-semibold inline-flex items-center space-x-1 transition-colors" 
                                        title="Delete Department">
                                    <i class="bi bi-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Dept Modal -->
<div id="addDeptModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Add Department</h3>
        <form action="/departments" method="POST" class="space-y-3">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Department Name</label>
                <input type="text" name="name" required placeholder="e.g. Quality Assurance" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Code</label>
                <input type="text" name="code" required placeholder="e.g. DEP-QA" class="w-full px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Department Manager Name</label>
                <input type="text" name="manager_name" placeholder="e.g. Jane Doe" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addDeptModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Dept Modal -->
<div id="editDeptModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Edit Department</h3>
        <form id="editDeptForm" action="" method="POST" class="space-y-3">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Department Name</label>
                <input type="text" id="editDeptName" name="name" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Code</label>
                <input type="text" id="editDeptCode" name="code" required class="w-full px-3 py-2 text-xs font-mono uppercase border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Department Manager Name</label>
                <input type="text" id="editDeptManager" name="manager_name" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
                <textarea id="editDeptDescription" name="description" rows="2" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('editDeptModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditDeptModal(dept) {
    document.getElementById('editDeptForm').action = '/departments/' + dept.id + '/update';
    document.getElementById('editDeptName').value = dept.name || '';
    document.getElementById('editDeptCode').value = dept.code || '';
    document.getElementById('editDeptManager').value = dept.manager_name || '';
    document.getElementById('editDeptDescription').value = dept.description || '';
    document.getElementById('editDeptModal').classList.remove('hidden');
}
</script>

