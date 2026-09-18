<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Location;
use App\Models\Category;
use App\Models\StockMovement;

class ReportController extends Controller
{
    public function valuation(): void
    {
        $this->requireAuth();
        $stats = Product::getValuationStats();
        $categories = Category::all();
        $locations = Location::all();
        $topValued = Product::all([], 10, 0);

        $this->render('reports/valuation', [
            'title' => 'Stock Valuation & Financial Report',
            'stats' => $stats,
            'categories' => $categories,
            'locations' => $locations,
            'topValued' => $topValued
        ]);
    }

    public function lowStock(): void
    {
        $this->requireAuth();
        $lowStockItems = Product::getLowStockItems(50);

        $this->render('reports/low_stock', [
            'title' => 'Procurement & Replenishment Reorder Report',
            'lowStockItems' => $lowStockItems
        ]);
    }
}
