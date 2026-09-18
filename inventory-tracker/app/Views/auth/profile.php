<?php
use App\Core\View;
use App\Core\Csrf;
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <h2 class="text-lg font-bold text-slate-900 mb-4">My Account Profile & Security</h2>
        
        <form action="/profile" method="POST" class="space-y-4">
            <?= Csrf::field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" value="<?= View::e($user['name']) ?>" required 
                       class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="<?= View::e($user['email']) ?>" required 
                       class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">System Role</label>
                    <span class="inline-block px-3 py-1.5 bg-slate-100 text-slate-800 rounded-lg text-xs font-bold uppercase">
                        <?= View::e($user['role']) ?>
                    </span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Assigned Department</label>
                    <span class="inline-block px-3 py-1.5 bg-slate-100 text-slate-800 rounded-lg text-xs font-medium">
                        <?= View::e($user['department_name'] ?? 'Universal / Global') ?>
                    </span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-4">
                <h3 class="text-sm font-bold text-slate-800">Change Password (leave blank to keep current)</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="password" minlength="6" placeholder="••••••••" 
                           class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" minlength="6" placeholder="••••••••" 
                           class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl text-sm shadow-sm shadow-sky-600/20">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
