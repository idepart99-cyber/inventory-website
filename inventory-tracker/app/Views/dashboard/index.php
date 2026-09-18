<?php
use App\Core\View;
?>
<div class="space-y-6">
    <!-- Top Greeting & Quick Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Inventory Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time stock valuation, location distribution & replenishment alerts</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="/products/create" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-sm transition-all">
                <i class="bi bi-plus-circle"></i>
                <span>New Product</span>
            </a>
            <a href="/stock/transfer" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs transition-all">
                <i class="bi bi-arrow-left-right text-amber-500"></i>
                <span>Transfer Stock</span>
            </a>
            <a href="/requisitions/create" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-sky-600/20 transition-all">
                <i class="bi bi-clipboard2-plus"></i>
                <span>Requisition</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Valuation -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Stock Value</p>
                <p class="text-2xl font-black text-slate-900 mt-1"><?= View::currency($stats['total_cost_value']) ?></p>
                <p class="text-xs text-emerald-600 mt-1 flex items-center space-x-1 font-medium">
                    <span>Retail value: <?= View::currency($stats['total_retail_value']) ?></span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>

        <!-- Card 2: Units & SKUs -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Inventory Items</p>
                <p class="text-2xl font-black text-slate-900 mt-1"><?= number_format($stats['total_units']) ?></p>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    Across <span class="font-bold text-slate-700"><?= $stats['total_products'] ?></span> unique SKUs
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 text-xl">
                <i class="bi bi-boxes"></i>
            </div>
        </div>

        <!-- Card 3: Low Stock Alerts -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Low Stock Warnings</p>
                <p class="text-2xl font-black text-rose-600 mt-1"><?= (int)$stats['low_stock_count'] ?></p>
                <p class="text-xs text-rose-500 mt-1 font-medium">
                    <?= (int)$stats['out_of_stock_count'] ?> items out of stock
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 text-xl">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>

        <!-- Card 4: Pending Requisitions -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Requisitions</p>
                <p class="text-2xl font-black text-amber-600 mt-1"><?= (int)$stats['pending_requisitions'] ?></p>
                <a href="/requisitions?status=pending" class="text-xs text-amber-600 hover:text-amber-700 font-semibold mt-1 inline-block">
                    Review requests &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-xl">
                <i class="bi bi-clipboard2-check"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section: Stock Flow (14 Days) & Category Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 14-day Movement Chart -->
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Stock Movement Flow (Last 14 Days)</h3>
                    <p class="text-xs text-slate-400">Comparing Inbound receipts vs Outbound dispatches</p>
                </div>
                <div class="flex items-center space-x-3 text-xs">
                    <span class="inline-flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-slate-600">Inbound (Stock In)</span></span>
                    <span class="inline-flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span><span class="text-slate-600">Outbound (Stock Out)</span></span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="stockFlowChart"></canvas>
            </div>
        </div>

        <!-- Category Breakdown Doughnut -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Stock by Category</h3>
                <p class="text-xs text-slate-400">Inventory valuation allocation</p>
            </div>
            <div class="h-48 my-2 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="pt-2 border-t border-slate-100 text-xs text-slate-500 space-y-1">
                <div class="flex justify-between font-semibold text-slate-700">
                    <span>Top Category</span>
                    <span><?= View::e($categoryBreakdown[0]['category_name'] ?? 'General') ?></span>
                </div>
                <div class="flex justify-between">
                    <span>Valuation Share</span>
                    <span class="font-bold text-sky-600"><?= View::currency($categoryBreakdown[0]['total_valuation'] ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-Location Capacity & Distribution -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Multi-Location Stock Distribution</h3>
                <p class="text-xs text-slate-400">Warehouse, storefront, and office inventory health</p>
            </div>
            <a href="/locations" class="text-xs font-semibold text-sky-600 hover:text-sky-700">
                Manage Locations &rarr;
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($locations as $loc): ?>
                <?php 
                    $percent = $loc['capacity'] > 0 ? min(100, round(($loc['total_items_count'] / $loc['capacity']) * 100)) : 0;
                    $typeIcon = match($loc['type']) {
                        'warehouse' => 'bi-building-fill-gear text-sky-500',
                        'shop' => 'bi-shop text-emerald-500',
                        'office' => 'bi-laptop text-purple-500',
                        default => 'bi-geo-alt-fill text-slate-500'
                    };
                ?>
                <a href="/locations/<?= $loc['id'] ?>" class="block p-4 rounded-xl border border-slate-200 hover:border-sky-300 hover:shadow-xs transition-all bg-slate-50/50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-800 flex items-center space-x-1.5 truncate">
                            <i class="bi <?= $typeIcon ?>"></i>
                            <span class="truncate"><?= View::e($loc['name']) ?></span>
                        </span>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-200/70 text-slate-700 uppercase"><?= View::e($loc['code']) ?></span>
                    </div>
                    
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Stored Items</span>
                            <span class="font-bold text-slate-900"><?= number_format((float)$loc['total_items_count']) ?> <span class="text-[10px] text-slate-400 font-normal">/ <?= number_format($loc['capacity']) ?></span></span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full <?= $percent > 85 ? 'bg-amber-500' : 'bg-sky-500' ?> rounded-full" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-t border-slate-200/60 flex justify-between items-center text-[11px]">
                        <span class="text-slate-500">Valuation:</span>
                        <span class="font-bold text-slate-700"><?= View::currency($loc['total_valuation']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Two Columns: Critical Low Stock Alerts & Recent Movements -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Alerts Box -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                        <i class="bi bi-shield-exclamation text-rose-500"></i>
                        <span>Replenishment Alerts</span>
                    </h3>
                    <p class="text-xs text-slate-400">Items at or below safety threshold</p>
                </div>
                <a href="/reports/low-stock" class="text-xs font-semibold text-sky-600 hover:text-sky-700">View All &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                <?php if (empty($lowStockItems)): ?>
                    <div class="p-8 text-center text-xs text-slate-400">
                        <i class="bi bi-check-circle text-emerald-500 text-2xl mb-1 block"></i>
                        All items are currently above safe minimum levels!
                    </div>
                <?php else: ?>
                    <?php foreach ($lowStockItems as $item): ?>
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition-colors">
                            <div class="space-y-0.5">
                                <a href="/products/<?= $item['id'] ?>" class="text-xs font-bold text-slate-900 hover:text-sky-600 block">
                                    <?= View::e($item['name']) ?>
                                </a>
                                <div class="flex items-center space-x-2 text-[11px] text-slate-500">
                                    <span class="font-mono bg-slate-100 px-1.5 py-0.2 rounded"><?= View::e($item['sku']) ?></span>
                                    <span>&bull;</span>
                                    <span><?= View::e($item['category_name']) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <span class="text-xs font-black <?= $item['total_stock'] == 0 ? 'text-rose-600' : 'text-amber-600' ?>">
                                        <?= (int)$item['total_stock'] ?> <?= View::e($item['unit']) ?>
                                    </span>
                                    <span class="block text-[10px] text-slate-400">Min: <?= $item['min_stock_alert'] ?></span>
                                </div>
                                <a href="/stock/in?product_id=<?= $item['id'] ?>" class="p-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg text-xs font-medium transition-colors" title="Quick Stock In">
                                    <i class="bi bi-plus-lg"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Movements Activity Feed -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                        <i class="bi bi-activity text-sky-500"></i>
                        <span>Recent Stock Activity</span>
                    </h3>
                    <p class="text-xs text-slate-400">Live inventory audit transactions</p>
                </div>
                <a href="/stock/history" class="text-xs font-semibold text-sky-600 hover:text-sky-700">Full Ledger &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                <?php if (empty($recentMovements)): ?>
                    <div class="p-8 text-center text-xs text-slate-400">No stock movements recorded yet.</div>
                <?php else: ?>
                    <?php foreach ($recentMovements as $mov): ?>
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition-colors">
                            <div class="flex items-start space-x-3">
                                <div class="mt-0.5">
                                    <?= View::badge($mov['type'], $mov['type']) ?>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">
                                        <?= View::e($mov['product_name']) ?>
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        <?= View::e($mov['reason'] ?? 'Movement') ?> 
                                        &bull; <span class="font-mono text-[10px]"><?= View::e($mov['reference_no']) ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold font-mono <?= $mov['type'] === 'IN' ? 'text-emerald-600' : ($mov['type'] === 'OUT' ? 'text-rose-600' : 'text-slate-700') ?>">
                                    <?= $mov['type'] === 'IN' ? '+' : ($mov['type'] === 'OUT' ? '-' : '') ?><?= $mov['quantity'] ?> <?= View::e($mov['unit'] ?? 'pcs') ?>
                                </span>
                                <span class="block text-[10px] text-slate-400"><?= View::date($mov['created_at'], 'M d, H:i') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart initialization script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. 14-day stock flow bar chart
    const trendData = <?= json_encode($flowTrends) ?>;
    const labels = trendData.map(d => d.movement_date);
    const inbound = trendData.map(d => parseInt(d.inbound_qty) || 0);
    const outbound = trendData.map(d => parseInt(d.outbound_qty) || 0);

    const ctxFlow = document.getElementById('stockFlowChart')?.getContext('2d');
    if (ctxFlow) {
        new Chart(ctxFlow, {
            type: 'bar',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [
                    {
                        label: 'Inbound',
                        data: inbound.length ? inbound : [0],
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'Outbound',
                        data: outbound.length ? outbound : [0],
                        backgroundColor: '#f43f5e',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } }
                }
            }
        });
    }

    // 2. Category Doughnut Chart
    const catData = <?= json_encode($categoryBreakdown) ?>;
    const catLabels = catData.map(c => c.category_name);
    const catValues = catData.map(c => parseFloat(c.total_valuation) || 0);

    const ctxCat = document.getElementById('categoryChart')?.getContext('2d');
    if (ctxCat) {
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels.length ? catLabels : ['Empty'],
                datasets: [{
                    data: catValues.length ? catValues : [1],
                    backgroundColor: ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
