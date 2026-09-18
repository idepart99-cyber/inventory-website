<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $suppliers = Supplier::all();
        $this->render('suppliers/index', [
            'title' => 'Supplier Directory',
            'suppliers' => $suppliers
        ]);
    }

    public function store(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $name = trim((string)$this->request('name', ''));
        $code = trim((string)$this->request('code', ''));

        if (empty($name) || empty($code)) {
            $this->redirect('/suppliers', 'Supplier name and code are required.', 'danger');
        }

        Supplier::create([
            'name' => $name,
            'code' => $code,
            'contact_person' => $this->request('contact_person'),
            'email' => $this->request('email'),
            'phone' => $this->request('phone'),
            'address' => $this->request('address'),
            'tax_id' => $this->request('tax_id'),
            'payment_terms' => $this->request('payment_terms', 'Net 30')
        ]);

        $this->redirect('/suppliers', 'Supplier registered successfully.', 'success');
    }

    public function update(string $id): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $supplierId = (int)$id;
        $name = trim((string)$this->request('name', ''));
        $code = trim((string)$this->request('code', ''));

        if (empty($name) || empty($code)) {
            $this->redirect('/suppliers', 'Supplier name and code are required.', 'danger');
        }

        Supplier::update($supplierId, [
            'name' => $name,
            'code' => $code,
            'contact_person' => $this->request('contact_person'),
            'email' => $this->request('email'),
            'phone' => $this->request('phone'),
            'address' => $this->request('address'),
            'tax_id' => $this->request('tax_id'),
            'payment_terms' => $this->request('payment_terms', 'Net 30')
        ]);

        $this->redirect('/suppliers', 'Supplier details updated.', 'success');
    }

    public function delete(string $id): void
    {
        $this->requireRole(['admin']);
        $this->validateCsrf();
        Supplier::delete((int)$id);
        $this->redirect('/suppliers', 'Supplier removed.', 'info');
    }
}
