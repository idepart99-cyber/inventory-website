<?php
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= View::e($title ?? 'Print Document') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-slate-900 p-8 font-sans">
    <div class="max-w-4xl mx-auto">
        <div class="no-print mb-6 pb-4 border-b border-slate-200 flex justify-between items-center">
            <button onclick="window.print()" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-sm font-semibold shadow">
                Print / Save PDF
            </button>
            <button onclick="window.close(); history.back();" class="text-sm text-slate-500 hover:text-slate-800">
                &larr; Back to System
            </button>
        </div>
        <?= $content ?>
    </div>
</body>
</html>
