<?php
declare(strict_types=1);

// PSR-4 Style Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    
    // Handle Database namespace located in /database
    if (str_starts_with($relativeClass, 'Database\\')) {
        $dbClass = substr($relativeClass, strlen('Database\\'));
        $file = __DIR__ . '/../database/' . str_replace('\\', '/', $dbClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }

    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Core\Session;
use App\Database\Database;

// Start session & initialize database if needed
Session::start();
Database::getConnection();

$router = new Router();

// Authentication
$router->get('/login', [\App\Controllers\AuthController::class, 'login']);
$router->post('/login', [\App\Controllers\AuthController::class, 'postLogin']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);
$router->get('/profile', [\App\Controllers\AuthController::class, 'profile'], ['auth']);
$router->post('/profile', [\App\Controllers\AuthController::class, 'updateProfile'], ['auth', 'csrf']);

// Dashboard
$router->get('/', [\App\Controllers\DashboardController::class, 'index'], ['auth']);
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], ['auth']);

// Products
$router->get('/products', [\App\Controllers\ProductController::class, 'index'], ['auth']);
$router->get('/products/create', [\App\Controllers\ProductController::class, 'create'], ['auth']);
$router->post('/products', [\App\Controllers\ProductController::class, 'store'], ['auth', 'csrf']);
$router->get('/products/labels', [\App\Controllers\ProductController::class, 'labels'], ['auth']);
$router->get('/products/export', [\App\Controllers\ProductController::class, 'exportCsv'], ['auth']);
$router->get('/products/{id}', [\App\Controllers\ProductController::class, 'show'], ['auth']);
$router->get('/products/{id}/edit', [\App\Controllers\ProductController::class, 'edit'], ['auth']);
$router->post('/products/{id}/update', [\App\Controllers\ProductController::class, 'update'], ['auth', 'csrf']);
$router->post('/products/{id}/delete', [\App\Controllers\ProductController::class, 'delete'], ['auth', 'csrf']);

// Stock Operations
$router->get('/stock/in', [\App\Controllers\StockController::class, 'in'], ['auth']);
$router->post('/stock/in', [\App\Controllers\StockController::class, 'postIn'], ['auth', 'csrf']);
$router->get('/stock/out', [\App\Controllers\StockController::class, 'out'], ['auth']);
$router->post('/stock/out', [\App\Controllers\StockController::class, 'postOut'], ['auth', 'csrf']);
$router->get('/stock/transfer', [\App\Controllers\StockController::class, 'transfer'], ['auth']);
$router->post('/stock/transfer', [\App\Controllers\StockController::class, 'postTransfer'], ['auth', 'csrf']);
$router->get('/stock/adjust', [\App\Controllers\StockController::class, 'adjust'], ['auth']);
$router->post('/stock/adjust', [\App\Controllers\StockController::class, 'postAdjust'], ['auth', 'csrf']);
$router->get('/stock/history', [\App\Controllers\StockController::class, 'history'], ['auth']);
$router->get('/stock/export', [\App\Controllers\StockController::class, 'exportCsv'], ['auth']);

// Requisitions
$router->get('/requisitions', [\App\Controllers\RequisitionController::class, 'index'], ['auth']);
$router->get('/requisitions/create', [\App\Controllers\RequisitionController::class, 'create'], ['auth']);
$router->post('/requisitions', [\App\Controllers\RequisitionController::class, 'store'], ['auth', 'csrf']);
$router->get('/requisitions/{id}', [\App\Controllers\RequisitionController::class, 'show'], ['auth']);
$router->post('/requisitions/{id}/approve', [\App\Controllers\RequisitionController::class, 'approve'], ['auth', 'csrf']);
$router->post('/requisitions/{id}/fulfill', [\App\Controllers\RequisitionController::class, 'fulfill'], ['auth', 'csrf']);
$router->post('/requisitions/{id}/reject', [\App\Controllers\RequisitionController::class, 'reject'], ['auth', 'csrf']);

// Locations
$router->get('/locations', [\App\Controllers\LocationController::class, 'index'], ['auth']);
$router->post('/locations', [\App\Controllers\LocationController::class, 'store'], ['auth', 'csrf']);
$router->get('/locations/{id}', [\App\Controllers\LocationController::class, 'show'], ['auth']);
$router->post('/locations/{id}/update', [\App\Controllers\LocationController::class, 'update'], ['auth', 'csrf']);
$router->post('/locations/{id}/delete', [\App\Controllers\LocationController::class, 'delete'], ['auth', 'csrf']);

// Departments
$router->get('/departments', [\App\Controllers\DepartmentController::class, 'index'], ['auth']);
$router->post('/departments', [\App\Controllers\DepartmentController::class, 'store'], ['auth', 'csrf']);
$router->post('/departments/{id}/update', [\App\Controllers\DepartmentController::class, 'update'], ['auth', 'csrf']);
$router->post('/departments/{id}/delete', [\App\Controllers\DepartmentController::class, 'delete'], ['auth', 'csrf']);

// Suppliers
$router->get('/suppliers', [\App\Controllers\SupplierController::class, 'index'], ['auth']);
$router->post('/suppliers', [\App\Controllers\SupplierController::class, 'store'], ['auth', 'csrf']);
$router->post('/suppliers/{id}/update', [\App\Controllers\SupplierController::class, 'update'], ['auth', 'csrf']);
$router->post('/suppliers/{id}/delete', [\App\Controllers\SupplierController::class, 'delete'], ['auth', 'csrf']);

// Reports
$router->get('/reports/valuation', [\App\Controllers\ReportController::class, 'valuation'], ['auth']);
$router->get('/reports/low-stock', [\App\Controllers\ReportController::class, 'lowStock'], ['auth']);

// JSON API
$router->get('/api/barcode/{code}', [\App\Controllers\ApiController::class, 'barcodeLookup']);
$router->get('/api/products/search', [\App\Controllers\ApiController::class, 'search']);
$router->get('/api/stock/{productId}/{locationId}', [\App\Controllers\ApiController::class, 'locationStock']);

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
