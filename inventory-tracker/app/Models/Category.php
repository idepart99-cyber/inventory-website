<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Category
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM products WHERE category_id = c.id) as products_count,
                (SELECT COALESCE(SUM(i.quantity), 0) FROM products p JOIN inventory i ON p.id = i.product_id WHERE p.category_id = c.id) as total_stock
                FROM categories c ORDER BY c.name ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO categories (name, code, description) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['description'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE categories SET name = ?, code = ?, description = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['description'] ?? null,
            $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
