<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Department
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT d.*, 
                (SELECT COUNT(*) FROM users WHERE department_id = d.id) as staff_count,
                (SELECT COUNT(*) FROM requisitions WHERE department_id = d.id) as requisition_count
                FROM departments d ORDER BY d.name ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO departments (name, code, description, manager_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['description'] ?? null,
            $data['manager_name'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE departments SET name = ?, code = ?, description = ?, manager_name = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['description'] ?? null,
            $data['manager_name'] ?? null,
            $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM departments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
