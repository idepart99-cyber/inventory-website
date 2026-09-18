<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\StockMovement;

class ProductController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $page = max(1, (int)$this->request('page', 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $this->request('search'),
            'category_id' => $this->request('category_id'),
            'supplier_id' => $this->request('supplier_id'),
            'status' => $this->request('status'),
        ];

        $totalCount = Product::count($filters);
        $products = Product::all($filters, $limit, $offset);
        $categories = Category::all();
        $suppliers = Supplier::all();

        $this->render('products/index', [
            'title' => 'Product Catalog',
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'filters' => $filters,
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'totalPages' => ceil($totalCount / $limit)
        ]);
    }

    public function create(): void
    {
        $this->requireRole(['admin', 'manager']);
        $categories = Category::all();
        $suppliers = Supplier::all();
        $locations = Location::all();

        $this->render('products/create', [
            'title' => 'Add New Product',
            'categories' => $categories,
            'suppliers' => $suppliers,
            'locations' => $locations
        ]);
    }

    public function store(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $sku = trim((string)$this->request('sku', ''));
        $name = trim((string)$this->request('name', ''));
        $categoryId = (int)$this->request('category_id', 0);

        if (empty($sku) || empty($name) || $categoryId <= 0) {
            $this->redirect('/products/create', 'SKU, Product Name, and Category are required.', 'danger');
        }

        // Check SKU uniqueness
        if (Product::findBySku($sku)) {
            $this->redirect('/products/create', 'A product with this SKU already exists.', 'danger');
        }

        $barcode = trim((string)$this->request('barcode', ''));
        if (empty($barcode)) {
            // Auto generate standard EAN-13 style numeric barcode if left blank
            $barcode = '890' . str_pad((string)random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        }

        $productId = Product::create([
            'sku' => $sku,
            'barcode' => $barcode,
            'name' => $name,
            'description' => $this->request('description'),
            'category_id' => $categoryId,
            'supplier_id' => $this->request('supplier_id'),
            'unit' => $this->request('unit', 'pcs'),
            'cost_price' => (float)$this->request('cost_price', 0),
            'selling_price' => (float)$this->request('selling_price', 0),
            'min_stock_alert' => (int)$this->request('min_stock_alert', 10),
            'max_stock_level' => (int)$this->request('max_stock_level', 500),
            'status' => $this->request('status', 'active')
        ]);

        // Optional initial stock
        $initialLocationId = (int)$this->request('initial_location_id', 0);
        $initialQty = (int)$this->request('initial_quantity', 0);

        if ($initialLocationId > 0 && $initialQty > 0) {
            StockMovement::recordIn(
                $productId,
                $initialLocationId,
                $initialQty,
                (float)$this->request('cost_price', 0),
                (int)$this->request('user_id', 1),
                'Initial Balance',
                'INIT-' . $sku,
                'Initial inventory intake during product creation'
            );
        }

        $this->redirect('/products/' . $productId, 'Product added successfully.', 'success');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $product = Product::find((int)$id);
        if (!$product) {
            $this->redirect('/products', 'Product not found.', 'danger');
        }

        $stockByLocation = Product::getStockByLocation((int)$id);
        $movements = StockMovement::getHistory(['product_id' => (int)$id], 10, 0);

        $this->render('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'stockByLocation' => $stockByLocation,
            'movements' => $movements
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireRole(['admin', 'manager']);
        $product = Product::find((int)$id);
        if (!$product) {
            $this->redirect('/products', 'Product not found.', 'danger');
        }

        $categories = Category::all();
        $suppliers = Supplier::all();

        $this->render('products/edit', [
            'title' => 'Edit ' . $product['name'],
            'product' => $product,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function update(string $id): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $productId = (int)$id;
        $sku = trim((string)$this->request('sku', ''));
        $name = trim((string)$this->request('name', ''));
        $categoryId = (int)$this->request('category_id', 0);

        if (empty($sku) || empty($name) || $categoryId <= 0) {
            $this->redirect('/products/' . $productId . '/edit', 'SKU, Name, and Category are required.', 'danger');
        }

        Product::update($productId, [
            'sku' => $sku,
            'barcode' => $this->request('barcode'),
            'name' => $name,
            'description' => $this->request('description'),
            'category_id' => $categoryId,
            'supplier_id' => $this->request('supplier_id'),
            'unit' => $this->request('unit', 'pcs'),
            'cost_price' => (float)$this->request('cost_price', 0),
            'selling_price' => (float)$this->request('selling_price', 0),
            'min_stock_alert' => (int)$this->request('min_stock_alert', 10),
            'max_stock_level' => (int)$this->request('max_stock_level', 500),
            'status' => $this->request('status', 'active')
        ]);

        $this->redirect('/products/' . $productId, 'Product updated successfully.', 'success');
    }

    public function delete(string $id): void
    {
        $this->requireRole(['admin']);
        $this->validateCsrf();
        Product::delete((int)$id);
        $this->redirect('/products', 'Product deleted.', 'info');
    }

    public function labels(): void
    {
        $this->requireAuth();
        $productId = $this->request('product_id');

        if ($productId) {
            $products = [Product::find((int)$productId)];
        } else {
            $products = Product::all([], 50, 0);
        }

        $this->render('products/labels', [
            'title' => 'Print Barcode Labels',
            'products' => array_filter($products)
        ], 'layouts/print');
    }

    public function exportCsv(): void
    {
        $this->requireAuth();
        $products = Product::all([], 1000, 0);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=inventory_catalog_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'SKU', 'Barcode', 'Product Name', 'Category', 'Supplier', 'Unit', 'Cost Price', 'Selling Price', 'Total Stock', 'Valuation', 'Min Alert', 'Status']);

        foreach ($products as $p) {
            fputcsv($output, [
                $p['id'],
                $p['sku'],
                $p['barcode'],
                $p['name'],
                $p['category_name'],
                $p['supplier_name'] ?? 'N/A',
                $p['unit'],
                $p['cost_price'],
                $p['selling_price'],
                $p['total_stock'],
                $p['total_valuation'],
                $p['min_stock_alert'],
                $p['status']
            ]);
        }
        fclose($output);
        exit;
    }
}
