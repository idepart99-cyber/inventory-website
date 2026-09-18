<?php
use App\Core\View;
?>
<div class="space-y-6">
    <div class="no-print flex justify-between items-center pb-3 border-b border-slate-200">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Printable Barcode Label Sheet</h2>
            <p class="text-xs text-slate-500">Standard 3x8 Avery / Continuous Barcode Label Format</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($products as $p): ?>
            <div class="border-2 border-slate-300 rounded-xl p-3 text-center space-y-1 bg-white break-inside-avoid shadow-xs">
                <p class="text-[10px] font-bold text-slate-500 uppercase truncate">StockFlow &bull; <?= View::e($p['category_name'] ?? 'General') ?></p>
                <h4 class="text-xs font-black text-slate-900 truncate"><?= View::e($p['name']) ?></h4>
                <div class="flex justify-center my-1">
                    <svg class="barcode-svg" data-code="<?= View::e($p['barcode'] ?: $p['sku']) ?>"></svg>
                </div>
                <div class="flex justify-between items-center text-[10px] font-mono px-2 text-slate-600">
                    <span class="font-bold"><?= View::e($p['sku']) ?></span>
                    <span class="text-slate-900 font-black"><?= View::currency($p['selling_price']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.barcode-svg').forEach(svg => {
        const code = svg.getAttribute('data-code');
        try {
            JsBarcode(svg, code, {
                format: "CODE128",
                width: 1.5,
                height: 40,
                displayValue: true,
                fontSize: 10
            });
        } catch(e) {
            console.warn(e);
        }
    });
});
</script>
