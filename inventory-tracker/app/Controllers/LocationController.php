<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Location;

class LocationController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $locations = Location::all();
        $this->render('locations/index', [
            'title' => 'Locations & Warehouses',
            'locations' => $locations
        ]);
    }

    public function store(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $name = trim((string)$this->request('name', ''));
        $code = trim((string)$this->request('code', ''));
        $type = (string)$this->request('type', 'warehouse');

        if (empty($name) || empty($code)) {
            $this->redirect('/locations', 'Location name and code are required.', 'danger');
        }

        Location::create([
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'address' => $this->request('address'),
            'capacity' => (int)$this->request('capacity', 1000),
            'is_active' => (int)$this->request('is_active', 1)
        ]);

        $this->redirect('/locations', 'Location created successfully.', 'success');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $location = Location::find((int)$id);
        if (!$location) {
            $this->redirect('/locations', 'Location not found.', 'danger');
        }

        $items = Location::getInventory((int)$id);

        $this->render('locations/show', [
            'title' => $location['name'] . ' Inventory',
            'location' => $location,
            'items' => $items
        ]);
    }

    public function update(string $id): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $name = trim((string)$this->request('name', ''));
        $code = trim((string)$this->request('code', ''));
        $type = (string)$this->request('type', 'warehouse');

        if (empty($name) || empty($code)) {
            $this->redirect('/locations', 'Location name and code are required.', 'danger');
        }

        Location::update((int)$id, [
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'address' => $this->request('address'),
            'capacity' => (int)$this->request('capacity', 1000),
            'is_active' => (int)$this->request('is_active', 1)
        ]);

        $this->redirect('/locations', 'Location updated.', 'success');
    }

    public function delete(string $id): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $locId = (int)$id;
        $loc = Location::find($locId);
        if (!$loc) {
            $this->redirect('/locations', 'Location not found.', 'danger');
        }

        // Check if location has items stored
        $items = Location::getInventory($locId);
        $totalUnits = 0;
        foreach ($items as $it) {
            $totalUnits += (int)($it['quantity'] ?? 0);
        }

        if ($totalUnits > 0) {
            $this->redirect('/locations', "Cannot delete '{$loc['name']}': it currently holds {$totalUnits} items in inventory. Please transfer or dispatch the stock first.", 'danger');
        }

        try {
            Location::delete($locId);
            $this->redirect('/locations', "Location '{$loc['name']}' deleted successfully.", 'info');
        } catch (\Throwable $e) {
            $this->redirect('/locations', "Cannot delete location: it has linked movement history records. Deactivate it instead.", 'danger');
        }
    }
}
