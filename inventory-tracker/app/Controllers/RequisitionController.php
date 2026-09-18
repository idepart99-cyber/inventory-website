<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Requisition;
use App\Models\Product;
use App\Models\Department;
use App\Models\Location;

class RequisitionController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $filters = [
            'status' => $this->request('status'),
            'department_id' => $this->request('department_id')
        ];

        $requisitions = Requisition::all($filters);
        $departments = Department::all();

        $this->render('requisitions/index', [
            'title' => 'Department Requisitions',
            'requisitions' => $requisitions,
            'departments' => $departments,
            'filters' => $filters
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $departments = Department::all();
        $products = Product::all([], 200, 0);

        $this->render('requisitions/create', [
            'title' => 'Create Requisition Request',
            'departments' => $departments,
            'products' => $products
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $deptId = (int)$this->request('department_id', 0);
        $neededBy = $this->request('needed_by_date');
        $priority = $this->request('priority', 'medium');
        $notes = $this->request('notes');

        $productIds = $this->request('product_id', []);
        $quantities = $this->request('quantity', []);

        $items = [];
        if (is_array($productIds)) {
            foreach ($productIds as $idx => $prodId) {
                $qty = (int)($quantities[$idx] ?? 0);
                if ((int)$prodId > 0 && $qty > 0) {
                    $items[] = [
                        'product_id' => (int)$prodId,
                        'quantity' => $qty
                    ];
                }
            }
        }

        if (empty($items)) {
            $this->redirect('/requisitions/create', 'Please add at least one valid item with positive quantity.', 'danger');
        }

        $reqId = Requisition::create([
            'department_id' => $deptId,
            'requested_by' => Auth::id() ?? 1,
            'priority' => $priority,
            'needed_by_date' => $neededBy,
            'notes' => $notes
        ], $items);

        $this->redirect('/requisitions/' . $reqId, 'Requisition submitted successfully and queued for review.', 'success');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $requisition = Requisition::find((int)$id);
        if (!$requisition) {
            $this->redirect('/requisitions', 'Requisition not found.', 'danger');
        }

        $locations = Location::all();

        $this->render('requisitions/show', [
            'title' => 'Requisition #' . $requisition['req_number'],
            'requisition' => $requisition,
            'locations' => $locations
        ]);
    }

    public function approve(string $id): void
    {
        $this->requireRole(['admin', 'manager', 'dept_lead']);
        $this->validateCsrf();

        $quantities = $this->request('approved_qty', []);
        Requisition::approve((int)$id, Auth::id() ?? 1, is_array($quantities) ? $quantities : []);

        $this->redirect('/requisitions/' . $id, 'Requisition approved successfully.', 'success');
    }

    public function fulfill(string $id): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $locationId = (int)$this->request('source_location_id', 0);
        if ($locationId <= 0) {
            $this->redirect('/requisitions/' . $id, 'Please specify the source warehouse location to fulfill items from.', 'danger');
        }

        try {
            Requisition::fulfill((int)$id, Auth::id() ?? 1, $locationId);
            $this->redirect('/requisitions/' . $id, 'Requisition fulfilled! Inventory deducted and issuance movements recorded.', 'success');
        } catch (\Throwable $e) {
            $this->redirect('/requisitions/' . $id, 'Fulfillment failed: ' . $e->getMessage(), 'danger');
        }
    }

    public function reject(string $id): void
    {
        $this->requireRole(['admin', 'manager', 'dept_lead']);
        $this->validateCsrf();

        $reason = trim((string)$this->request('reason', ''));
        Requisition::reject((int)$id, Auth::id() ?? 1, $reason);

        $this->redirect('/requisitions/' . $id, 'Requisition marked as rejected.', 'info');
    }
}
