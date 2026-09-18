<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Location
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT l.*, 
                COALESCE(SUM(i.quantity), 0) as total_items_count,
                COUNT(DISTINCT i.product_id) as total_sku_count,
                COALESCE(SUM(i.quantity * p.cost_price), 0) as total_valuation
                FROM locations l
                LEFT JOIN inventory i ON l.id = i.location_id
                LEFT JOIN products p ON i.product_id = p.id
                GROUP BY l.id
                ORDER BY l.name ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM locations WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO locations (name, code, type, address, capacity, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['type'],
            $data['address'] ?? null,
            !empty($data['capacity']) ? (int)$data['capacity'] : 1000,
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE locations SET name = ?, code = ?, type = ?, address = ?, capacity = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            strtoupper(trim($data['code'])),
            $data['type'],
            $data['address'] ?? null,
            !empty($data['capacity']) ? (int)$data['capacity'] : 1000,
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM locations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getInventory(int $locationId): array
    {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name, i.quantity, i.aisle_bin, i.last_counted_at,
                (i.quantity * p.cost_price) as total_cost,
                (i.quantity * p.selling_price) as total_retail
                FROM inventory i
                JOIN products p ON i.product_id = p.id
                JOIN categories c ON p.category_id = c.id
                WHERE i.location_id = ?
                ORDER BY p.name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$locationId]);
        return $stmt->fetchAll();
    }
}
