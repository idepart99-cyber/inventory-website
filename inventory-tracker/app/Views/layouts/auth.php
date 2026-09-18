<?php
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($title ?? 'Sign In') ?> - StockFlow Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="h-full flex items-center justify-center p-4 antialiased bg-radial from-slate-800 to-slate-950">
    <div class="max-w-md w-full">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-600 to-cyan-400 items-center justify-center text-white text-2xl font-bold shadow-xl shadow-sky-500/20 mb-3">
                <i class="bi bi-box-seam"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Stock<span class="text-sky-400">Flow</span> Pro</h1>
            <p class="text-sm text-slate-400 mt-1">Enterprise Multi-Location Inventory & Stock Management</p>
        </div>

        <!-- Flashes -->
        <?php if (!empty($flashes)): ?>
            <div class="mb-4 space-y-2">
                <?php foreach ($flashes as $type => $messages): ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="p-3.5 rounded-xl border text-xs <?= $type === 'success' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : 'bg-rose-500/10 text-rose-300 border-rose-500/30' ?>">
                            <?= View::e($msg) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Card Body -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-slate-100">
            <?= $content ?>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">&copy; <?= date('Y') ?> StockFlow Pro. Enterprise Warehouse & Shop System.</p>
    </div>
</body>
</html>
