<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Location;
use App\Models\Department;
use App\Models\StockMovement;

class StockController extends Controller
{
    public function in(): void
    {
        $this->requireRole(['admin', 'manager', 'staff']);
        $products = Product::all([], 200, 0);
        $locations = Location::all();

        $this->render('stock/in', [
            'title' => 'Stock In / Receive Inventory',
            'products' => $products,
            'locations' => $locations
        ]);
    }

    public function postIn(): void
    {
        $this->requireRole(['admin', 'manager', 'staff']);
        $this->validateCsrf();

        $productId = (int)$this->request('product_id', 0);
        $locationId = (int)$this->request('location_id', 0);
        $quantity = (int)$this->request('quantity', 0);
        $unitCost = (float)$this->request('unit_cost', 0);
        $reason = (string)$this->request('reason', 'Purchase Receipt');
        $referenceNo = trim((string)$this->request('reference_no', ''));
        $notes = $this->request('notes');

        if ($productId <= 0 || $locationId <= 0 || $quantity <= 0) {
            $this->redirect('/stock/in', 'Please select a valid product, destination location, and quantity.', 'danger');
        }

        if (empty($referenceNo)) {
            $referenceNo = 'IN-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }

        try {
            StockMovement::recordIn(
                $productId,
                $locationId,
                $quantity,
                $unitCost,
                Auth::id() ?? 1,
                $reason,
                $referenceNo,
                $notes
            );
            $this->redirect('/stock/history', "Successfully received {$quantity} units into inventory. (Ref: {$referenceNo})", 'success');
        } catch (\Throwable $e) {
            $this->redirect('/stock/in', 'Error processing stock in: ' . $e->getMessage(), 'danger');
        }
    }

    public function out(): void
    {
        $this->requireRole(['admin', 'manager', 'staff']);
        $products = Product::all([], 200, 0);
        $locations = Location::all();
        $departments = Department::all();

        $this->render('stock/out', [
            'title' => 'Stock Out / Dispatch & Issue',
            'products' => $products,
            'locations' => $locations,
            'departments' => $departments
        ]);
    }

    public function postOut(): void
    {
        $this->requireRole(['admin', 'manager', 'staff']);
        $this->validateCsrf();

        $productId = (int)$this->request('product_id', 0);
        $locationId = (int)$this->request('location_id', 0);
        $quantity = (int)$this->request('quantity', 0);
        $departmentId = !empty($this->request('department_id')) ? (int)$this->request('department_id') : null;
        $reason = (string)$this->request('reason', 'Department Issuance');
        $referenceNo = trim((string)$this->request('reference_no', ''));
        $notes = $this->request('notes');

        if ($productId <= 0 || $locationId <= 0 || $quantity <= 0) {
            $this->redirect('/stock/out', 'Please select a valid product, source location, and positive quantity.', 'danger');
        }

        if (empty($referenceNo)) {
            $referenceNo = 'OUT-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }

        try {
            StockMovement::recordOut(
                $productId,
                $locationId,
                $quantity,
                $departmentId,
                Auth::id() ?? 1,
                $reason,
                $referenceNo,
                $notes
            );
            $this->redirect('/stock/history', "Successfully dispatched {$quantity} units. (Ref: {$referenceNo})", 'success');
        } catch (\Throwable $e) {
            $this->redirect('/stock/out', 'Error processing stock out: ' . $e->getMessage(), 'danger');
        }
    }

    public function transfer(): void
    {
        $this->requireRole(['admin', 'manager']);
        $products = Product::all([], 200, 0);
        $locations = Location::all();

        $this->render('stock/transfer', [
            'title' => 'Inter-Location Stock Transfer',
            'products' => $products,
            'locations' => $locations
        ]);
    }

    public function postTransfer(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $productId = (int)$this->request('product_id', 0);
        $sourceLocationId = (int)$this->request('source_location_id', 0);
        $destLocationId = (int)$this->request('dest_location_id', 0);
        $quantity = (int)$this->request('quantity', 0);
        $referenceNo = trim((string)$this->request('reference_no', ''));
        $notes = $this->request('notes');

        if ($productId <= 0 || $sourceLocationId <= 0 || $destLocationId <= 0 || $quantity <= 0) {
            $this->redirect('/stock/transfer', 'All transfer fields must be completed properly.', 'danger');
        }

        if ($sourceLocationId === $destLocationId) {
            $this->redirect('/stock/transfer', 'Source and destination locations cannot be identical.', 'danger');
        }

        if (empty($referenceNo)) {
            $referenceNo = 'TR-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }

        try {
            StockMovement::recordTransfer(
                $productId,
                $sourceLocationId,
                $destLocationId,
                $quantity,
                Auth::id() ?? 1,
                $referenceNo,
                $notes
            );
            $this->redirect('/stock/history', "Transferred {$quantity} units successfully. (Ref: {$referenceNo})", 'success');
        } catch (\Throwable $e) {
            $this->redirect('/stock/transfer', 'Transfer failed: ' . $e->getMessage(), 'danger');
        }
    }

    public function adjust(): void
    {
        $this->requireRole(['admin', 'manager']);
        $products = Product::all([], 200, 0);
        $locations = Location::all();

        $this->render('stock/adjust', [
            'title' => 'Stock Count Adjustment / Audit',
            'products' => $products,
            'locations' => $locations
        ]);
    }

    public function postAdjust(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $productId = (int)$this->request('product_id', 0);
        $locationId = (int)$this->request('location_id', 0);
        $actualCount = (int)$this->request('actual_count', 0);
        $reason = (string)$this->request('reason', 'Physical Recount');
        $referenceNo = trim((string)$this->request('reference_no', ''));
        $notes = $this->request('notes');

        if ($productId <= 0 || $locationId <= 0 || $actualCount < 0) {
            $this->redirect('/stock/adjust', 'Valid product, location, and non-negative physical count required.', 'danger');
        }

        if (empty($referenceNo)) {
            $referenceNo = 'ADJ-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }

        try {
            StockMovement::recordAdjustment(
                $productId,
                $locationId,
                $actualCount,
                Auth::id() ?? 1,
                $reason,
                $referenceNo,
                $notes
            );
            $this->redirect('/stock/history', "Inventory balance adjusted to {$actualCount}. (Ref: {$referenceNo})", 'success');
        } catch (\Throwable $e) {
            $this->redirect('/stock/adjust', 'Adjustment error: ' . $e->getMessage(), 'danger');
        }
    }

    public function history(): void
    {
        $this->requireAuth();

        $filters = [
            'type' => $this->request('type'),
            'product_id' => $this->request('product_id'),
            'location_id' => $this->request('location_id'),
            'department_id' => $this->request('department_id'),
            'date_from' => $this->request('date_from'),
            'date_to' => $this->request('date_to'),
        ];

        $page = max(1, (int)$this->request('page', 1));
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $movements = StockMovement::getHistory($filters, $limit, $offset);
        $products = Product::all([], 300, 0);
        $locations = Location::all();
        $departments = Department::all();

        $this->render('stock/history', [
            'title' => 'Stock Movement Ledger',
            'movements' => $movements,
            'products' => $products,
            'locations' => $locations,
            'departments' => $departments,
            'filters' => $filters,
            'currentPage' => $page
        ]);
    }

    public function exportCsv(): void
    {
        $this->requireAuth();
        $movements = StockMovement::getHistory([], 2000, 0);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=stock_movements_' . date('Y-m-d') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Timestamp', 'Reference', 'Type', 'SKU', 'Product', 'Qty', 'Unit Cost', 'Source Location', 'Destination Location', 'Department', 'Reason', 'User', 'Notes']);

        foreach ($movements as $m) {
            fputcsv($out, [
                $m['created_at'],
                $m['reference_no'],
                $m['type'],
                $m['sku'],
                $m['product_name'],
                $m['quantity'],
                $m['unit_cost'],
                $m['source_location_name'] ?? '—',
                $m['destination_location_name'] ?? '—',
                $m['department_name'] ?? '—',
                $m['reason'] ?? '',
                $m['user_name'] ?? '',
                $m['notes'] ?? ''
            ]);
        }
        fclose($out);
        exit;
    }
}
