<?php
use App\Core\Auth;
use App\Core\View;
use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($title ?? 'StockFlow Pro') ?> - <?= View::e($config['name'] ?? 'Inventory Tracker') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#082f49',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="h-full font-sans antialiased text-slate-800 flex flex-col min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 transition-all duration-300 z-30 shadow-xl">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 bg-slate-950/60 border-b border-slate-800">
                <a href="/dashboard" class="flex items-center space-x-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-sky-400 flex items-center justify-center text-white font-bold shadow-lg shadow-sky-500/20 group-hover:scale-105 transition-transform">
                        <i class="bi bi-box-seam text-lg"></i>
                    </div>
                    <div>
                        <span class="text-white font-extrabold tracking-tight text-lg">Stock<span class="text-sky-400">Flow</span></span>
                        <span class="text-[10px] block text-slate-400 font-medium tracking-wider uppercase">Pro Enterprise</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider px-3 mb-2">Main Menu</div>
                
                <a href="/dashboard" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= ($_SERVER['REQUEST_URI'] === '/dashboard' || $_SERVER['REQUEST_URI'] === '/') ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : '' ?>">
                    <i class="bi bi-grid-1x2-fill text-lg"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/products" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= str_starts_with($_SERVER['REQUEST_URI'], '/products') ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <i class="bi bi-boxes text-lg"></i>
                        <span>Products & Items</span>
                    </div>
                </a>

                <div class="pt-4 text-[11px] font-semibold text-slate-400 uppercase tracking-wider px-3 mb-2">Stock Operations</div>

                <a href="/stock/in" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= $_SERVER['REQUEST_URI'] === '/stock/in' ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-box-arrow-in-down text-emerald-400 text-lg"></i>
                    <span>Receive (Stock In)</span>
                </a>

                <a href="/stock/out" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= $_SERVER['REQUEST_URI'] === '/stock/out' ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-box-arrow-up-right text-rose-400 text-lg"></i>
                    <span>Issue (Stock Out)</span>
                </a>

                <a href="/stock/transfer" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= $_SERVER['REQUEST_URI'] === '/stock/transfer' ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-arrow-left-right text-amber-400 text-lg"></i>
                    <span>Transfer Stock</span>
                </a>

                <a href="/stock/adjust" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= $_SERVER['REQUEST_URI'] === '/stock/adjust' ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-sliders text-sky-400 text-lg"></i>
                    <span>Audit / Adjust</span>
                </a>

                <a href="/stock/history" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= $_SERVER['REQUEST_URI'] === '/stock/history' ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-journal-text text-lg"></i>
                    <span>Movement Ledger</span>
                </a>

                <div class="pt-4 text-[11px] font-semibold text-slate-400 uppercase tracking-wider px-3 mb-2">Organization</div>

                <a href="/requisitions" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= str_starts_with($_SERVER['REQUEST_URI'], '/requisitions') ? 'bg-sky-600 text-white' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <i class="bi bi-clipboard2-check text-lg"></i>
                        <span>Requisitions</span>
                    </div>
                </a>

                <a href="/locations" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= str_starts_with($_SERVER['REQUEST_URI'], '/locations') ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-building text-lg"></i>
                    <span>Locations & Hubs</span>
                </a>

                <a href="/departments" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= str_starts_with($_SERVER['REQUEST_URI'], '/departments') ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-diagram-3 text-lg"></i>
                    <span>Departments</span>
                </a>

                <a href="/suppliers" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= str_starts_with($_SERVER['REQUEST_URI'], '/suppliers') ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-truck text-lg"></i>
                    <span>Suppliers</span>
                </a>

                <div class="pt-4 text-[11px] font-semibold text-slate-400 uppercase tracking-wider px-3 mb-2">Analytics & Reports</div>

                <a href="/reports/valuation" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= $_SERVER['REQUEST_URI'] === '/reports/valuation' ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-graph-up text-lg"></i>
                    <span>Stock Valuation</span>
                </a>

                <a href="/reports/low-stock" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white <?= $_SERVER['REQUEST_URI'] === '/reports/low-stock' ? 'bg-sky-600 text-white' : '' ?>">
                    <i class="bi bi-exclamation-triangle text-amber-400 text-lg"></i>
                    <span>Low Stock Reorders</span>
                </a>
            </nav>

            <!-- User Badge / Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-sm">
                            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="truncate max-w-[120px]">
                            <p class="text-xs font-semibold text-white truncate"><?= View::e($currentUser['name'] ?? 'Guest') ?></p>
                            <span class="text-[10px] text-slate-400 block uppercase tracking-wider"><?= View::e($currentUser['role'] ?? 'Staff') ?></span>
                        </div>
                    </div>
                    <a href="/logout" title="Sign Out" class="text-slate-400 hover:text-rose-400 transition-colors p-1.5 rounded-lg hover:bg-slate-800">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-20 shadow-xs">
                <div class="flex items-center space-x-4">
                    <button id="toggleSidebarBtn" class="text-slate-500 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100 lg:hidden">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                    <!-- Global Search Bar -->
                    <div class="relative w-72 md:w-96">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="globalSearchInput" placeholder="Search by SKU, Barcode, or Product name..." 
                               class="w-full pl-9 pr-4 py-1.5 text-sm bg-slate-100 border border-transparent rounded-lg focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 focus:outline-none transition-all">
                        <div id="searchResultsDropdown" class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200 max-h-80 overflow-y-auto hidden z-50 divide-y divide-slate-100"></div>
                    </div>
                </div>

                <!-- Right Action Bar -->
                <div class="flex items-center space-x-3">
                    <!-- Camera Barcode Scanner Trigger -->
                    <button onclick="openBarcodeScannerModal()" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border border-slate-200" title="Scan Barcode with Camera or Gun">
                        <i class="bi bi-upc-scan text-sky-600"></i>
                        <span class="hidden sm:inline">Scan Barcode</span>
                    </button>

                    <!-- Quick Stock In Button -->
                    <a href="/stock/in" class="inline-flex items-center space-x-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors shadow-sm shadow-emerald-600/20">
                        <i class="bi bi-plus-lg"></i>
                        <span class="hidden sm:inline">Receive</span>
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <a href="/profile" class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-slate-100 text-slate-700 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-800 border border-sky-300 flex items-center justify-center font-bold text-xs">
                                <?= strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body -->
            <main class="flex-1 overflow-y-auto bg-slate-50/70 p-6">
                <!-- Flash Messages -->
                <?php if (!empty($flashes)): ?>
                    <div class="mb-6 space-y-2">
                        <?php foreach ($flashes as $type => $messages): ?>
                            <?php foreach ($messages as $msg): ?>
                                <div class="flex items-center justify-between p-4 rounded-xl border text-sm <?= $type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($type === 'danger' ? 'bg-rose-50 text-rose-800 border-rose-200' : ($type === 'warning' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-sky-50 text-sky-800 border-sky-200')) ?>">
                                    <div class="flex items-center space-x-3">
                                        <i class="bi <?= $type === 'success' ? 'bi-check-circle-fill text-emerald-500' : ($type === 'danger' ? 'bi-x-circle-fill text-rose-500' : ($type === 'warning' ? 'bi-exclamation-triangle-fill text-amber-500' : 'bi-info-circle-fill text-sky-500')) ?> text-lg"></i>
                                        <span><?= View::e($msg) ?></span>
                                    </div>
                                    <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Barcode & QR Scanner Modal -->
    <div id="scannerModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center space-x-2">
                    <i class="bi bi-upc-scan text-sky-600 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-base">Instant Barcode & QR Lookup</h3>
                </div>
                <button onclick="closeBarcodeScannerModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="relative bg-black rounded-xl overflow-hidden aspect-video flex items-center justify-center text-slate-400">
                    <video id="scannerVideo" class="w-full h-full object-cover hidden" playsinline></video>
                    <div id="scannerPlaceholder" class="text-center p-4">
                        <i class="bi bi-camera text-4xl text-slate-500 mb-2 block"></i>
                        <p class="text-xs text-slate-300">Camera preview will activate, or use barcode gun / manual input below.</p>
                        <button onclick="startCameraScanner()" class="mt-3 px-4 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold">
                            Activate Web Camera
                        </button>
                    </div>
                </div>

                <!-- Manual/Gun Barcode Input -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600">Or type / scan with USB gun:</label>
                    <div class="flex space-x-2">
                        <input type="text" id="manualScannerInput" placeholder="Scan or enter barcode / SKU..." class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <button onclick="lookupBarcode(document.getElementById('manualScannerInput').value)" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-sm font-semibold">
                            Lookup
                        </button>
                    </div>
                </div>

                <!-- Scanner Result Box -->
                <div id="scannerResultBox" class="hidden p-4 rounded-xl border border-sky-100 bg-sky-50/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 id="scanResultName" class="font-bold text-slate-900 text-sm"></h4>
                        <span id="scanResultSku" class="text-xs font-mono bg-sky-100 text-sky-800 px-2 py-0.5 rounded"></span>
                    </div>
                    <p id="scanResultStock" class="text-xs text-slate-600"></p>
                    <div class="pt-2 flex space-x-2">
                        <a id="scanResultViewLink" href="#" class="flex-1 text-center py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold">
                            View Product Details
                        </a>
                        <a id="scanResultStockInLink" href="#" class="flex-1 text-center py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                            Receive Stock In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Script -->
    <script src="/js/app.js"></script>
    <script src="/js/scanner.js"></script>
</body>
</html>
