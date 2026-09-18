<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Supplier
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM products WHERE supplier_id = s.id) as products_count
                FROM suppliers s ORDER BY s.name ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO suppliers (name, code, contact_person, email, phone, address, tax_id, payment_terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['contact_person'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['tax_id'] ?? null,
            $data['payment_terms'] ?? 'Net 30'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE suppliers SET name = ?, code = ?, contact_person = ?, email = ?, phone = ?, address = ?, tax_id = ?, payment_terms = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['contact_person'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['tax_id'] ?? null,
            $data['payment_terms'] ?? 'Net 30',
            $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM suppliers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
