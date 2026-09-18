<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $departments = Department::all();
        $this->render('departments/index', [
            'title' => 'Departments & Divisions',
            'departments' => $departments
        ]);
    }

    public function store(): void
    {
        $this->requireRole(['admin']);
        $this->validateCsrf();

        $name = trim((string)$this->request('name', ''));
        $code = trim((string)$this->request('code', ''));

        if (empty($name) || empty($code)) {
            $this->redirect('/departments', 'Department name and code are required.', 'danger');
        }

        Department::create([
            'name' => $name,
            'code' => $code,
            'description' => $this->request('description'),
            'manager_name' => $this->request('manager_name')
        ]);

        $this->redirect('/departments', 'Department created successfully.', 'success');
    }

    public function update(string $id): void
    {
        $this->requireRole(['admin']);
        $this->validateCsrf();

        $name = trim((string)$this->request('name', ''));
        $code = trim((string)$this->request('code', ''));

        if (empty($name) || empty($code)) {
            $this->redirect('/departments', 'Department name and code are required.', 'danger');
        }

        Department::update((int)$id, [
            'name' => $name,
            'code' => $code,
            'description' => $this->request('description'),
            'manager_name' => $this->request('manager_name')
        ]);

        $this->redirect('/departments', 'Department updated successfully.', 'success');
    }

    public function delete(string $id): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->validateCsrf();

        $deptId = (int)$id;
        $dept = Department::find($deptId);
        if (!$dept) {
            $this->redirect('/departments', 'Department not found.', 'danger');
        }

        try {
            Department::delete($deptId);
            $this->redirect('/departments', 'Department "' . $dept['name'] . '" deleted successfully.', 'info');
        } catch (\Throwable $e) {
            $this->redirect('/departments', 'Cannot delete department: it has linked staff or requisitions. Please reassign them first.', 'danger');
        }
    }
}
