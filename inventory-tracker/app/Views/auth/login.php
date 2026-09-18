<?php
use App\Core\Csrf;
?>
<form action="/login" method="POST" class="space-y-4">
    <?= Csrf::field() ?>

    <div>
        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Work Email</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" id="email" name="email" required placeholder="admin@stockflow.com" 
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:outline-none">
        </div>
    </div>

    <div>
        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" id="password" name="password" required placeholder="••••••••" 
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:outline-none">
        </div>
    </div>

    <button type="submit" class="w-full py-2.5 px-4 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl text-sm shadow-md shadow-sky-600/20 transition-all">
        Sign In to Portal
    </button>
</form>

<!-- 1-Click Demo Accounts -->
<div class="mt-6 pt-5 border-t border-slate-100">
    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center mb-3">Quick Demo Login (1-Click Fill)</p>
    <div class="grid grid-cols-2 gap-2 text-xs">
        <button type="button" onclick="fillLogin('admin@stockflow.com', 'admin123')" class="p-2 rounded-lg border border-slate-200 hover:border-sky-400 hover:bg-sky-50/50 text-left transition-colors">
            <span class="font-bold text-slate-800 block">Super Admin</span>
            <span class="text-slate-500 text-[10px]">admin@stockflow.com</span>
        </button>
        <button type="button" onclick="fillLogin('warehouse@stockflow.com', 'manager123')" class="p-2 rounded-lg border border-slate-200 hover:border-sky-400 hover:bg-sky-50/50 text-left transition-colors">
            <span class="font-bold text-slate-800 block">Warehouse Mgr</span>
            <span class="text-slate-500 text-[10px]">warehouse@stockflow.com</span>
        </button>
        <button type="button" onclick="fillLogin('it.lead@stockflow.com', 'lead123')" class="p-2 rounded-lg border border-slate-200 hover:border-sky-400 hover:bg-sky-50/50 text-left transition-colors">
            <span class="font-bold text-slate-800 block">IT Dept Lead</span>
            <span class="text-slate-500 text-[10px]">it.lead@stockflow.com</span>
        </button>
        <button type="button" onclick="fillLogin('staff@stockflow.com', 'staff123')" class="p-2 rounded-lg border border-slate-200 hover:border-sky-400 hover:bg-sky-50/50 text-left transition-colors">
            <span class="font-bold text-slate-800 block">Staff / Cashier</span>
            <span class="text-slate-500 text-[10px]">staff@stockflow.com</span>
        </button>
    </div>
</div>

<script>
function fillLogin(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
}
</script>
