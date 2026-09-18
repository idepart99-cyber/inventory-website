<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Location;
use App\Models\Requisition;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $stats = Product::getValuationStats();
        $stats['pending_requisitions'] = Requisition::getPendingCount();

        $lowStockItems = Product::getLowStockItems(8);
        $recentMovements = StockMovement::getRecent(8);
        $locations = Location::all();
        $flowTrends = StockMovement::getFlowTrends(14);
        $categoryBreakdown = StockMovement::getCategoryBreakdown();

        $this->render('dashboard/index', [
            'title' => 'Executive Overview',
            'stats' => $stats,
            'lowStockItems' => $lowStockItems,
            'recentMovements' => $recentMovements,
            'locations' => $locations,
            'flowTrends' => $flowTrends,
            'categoryBreakdown' => $categoryBreakdown
        ]);
    }
}
