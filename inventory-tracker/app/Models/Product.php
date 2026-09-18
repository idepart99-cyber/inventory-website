<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Product
{
    public static function all(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $where[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['supplier_id'])) {
            $where[] = "p.supplier_id = ?";
            $params[] = (int)$filters['supplier_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "p.status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT p.*, 
                c.name as category_name, 
                s.name as supplier_name,
                COALESCE(SUM(i.quantity), 0) as total_stock,
                (COALESCE(SUM(i.quantity), 0) * p.cost_price) as total_valuation
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE {$whereClause}
                GROUP BY p.id
                ORDER BY p.name ASC
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(array $filters = []): int
    {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $where[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "p.status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = implode(' AND ', $where);
        $stmt = $db->prepare("SELECT COUNT(*) FROM products p WHERE {$whereClause}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $sql = "SELECT p.*, 
                c.name as category_name, 
                s.name as supplier_name,
                COALESCE(SUM(i.quantity), 0) as total_stock,
                (COALESCE(SUM(i.quantity), 0) * p.cost_price) as total_cost_value,
                (COALESCE(SUM(i.quantity), 0) * p.selling_price) as total_retail_value
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.id = ?
                GROUP BY p.id";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function findBySku(string $sku): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, COALESCE(SUM(i.quantity), 0) as total_stock 
                              FROM products p 
                              JOIN categories c ON p.category_id = c.id 
                              LEFT JOIN inventory i ON p.id = i.product_id 
                              WHERE p.sku = ? 
                              GROUP BY p.id");
        $stmt->execute([trim($sku)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function findByBarcode(string $barcode): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, COALESCE(SUM(i.quantity), 0) as total_stock 
                              FROM products p 
                              JOIN categories c ON p.category_id = c.id 
                              LEFT JOIN inventory i ON p.id = i.product_id 
                              WHERE p.barcode = ? OR p.sku = ?
                              GROUP BY p.id");
        $stmt->execute([trim($barcode), trim($barcode)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getStockByLocation(int $productId): array
    {
        $db = Database::getConnection();
        $sql = "SELECT l.id as location_id, l.name as location_name, l.code as location_code, l.type as location_type,
                COALESCE(i.quantity, 0) as quantity, i.aisle_bin, i.last_counted_at
                FROM locations l
                LEFT JOIN inventory i ON l.id = i.location_id AND i.product_id = ?
                ORDER BY l.type ASC, l.name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO products 
            (sku, barcode, name, description, category_id, supplier_id, unit, cost_price, selling_price, min_stock_alert, max_stock_level, image_url, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            strtoupper(trim($data['sku'])),
            !empty($data['barcode']) ? trim($data['barcode']) : null,
            trim($data['name']),
            $data['description'] ?? null,
            (int)$data['category_id'],
            !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null,
            $data['unit'] ?? 'pcs',
            (float)($data['cost_price'] ?? 0),
            (float)($data['selling_price'] ?? 0),
            (int)($data['min_stock_alert'] ?? 10),
            (int)($data['max_stock_level'] ?? 500),
            $data['image_url'] ?? null,
            $data['status'] ?? 'active'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET 
            sku = ?, barcode = ?, name = ?, description = ?, category_id = ?, supplier_id = ?, unit = ?, 
            cost_price = ?, selling_price = ?, min_stock_alert = ?, max_stock_level = ?, image_url = ?, status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?");
        return $stmt->execute([
            strtoupper(trim($data['sku'])),
            !empty($data['barcode']) ? trim($data['barcode']) : null,
            trim($data['name']),
            $data['description'] ?? null,
            (int)$data['category_id'],
            !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null,
            $data['unit'] ?? 'pcs',
            (float)($data['cost_price'] ?? 0),
            (float)($data['selling_price'] ?? 0),
            (int)($data['min_stock_alert'] ?? 10),
            (int)($data['max_stock_level'] ?? 500),
            $data['image_url'] ?? null,
            $data['status'] ?? 'active',
            $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getLowStockItems(int $limit = 10): array
    {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name, COALESCE(SUM(i.quantity), 0) as total_stock
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.status = 'active'
                GROUP BY p.id
                HAVING total_stock <= p.min_stock_alert
                ORDER BY (total_stock - p.min_stock_alert) ASC
                LIMIT {$limit}";
        return $db->query($sql)->fetchAll();
    }

    public static function getValuationStats(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT 
                COUNT(DISTINCT p.id) as total_products,
                COALESCE(SUM(i.quantity), 0) as total_units,
                COALESCE(SUM(i.quantity * p.cost_price), 0) as total_cost_value,
                COALESCE(SUM(i.quantity * p.selling_price), 0) as total_retail_value,
                (
                    SELECT COUNT(DISTINCT p2.id) 
                    FROM products p2 
                    LEFT JOIN inventory i2 ON p2.id = i2.product_id 
                    WHERE p2.status = 'active' 
                    GROUP BY p2.id 
                    HAVING COALESCE(SUM(i2.quantity), 0) <= p2.min_stock_alert
                ) as low_stock_count,
                (
                    SELECT COUNT(DISTINCT p3.id) 
                    FROM products p3 
                    LEFT JOIN inventory i3 ON p3.id = i3.product_id 
                    WHERE p3.status = 'active' 
                    GROUP BY p3.id 
                    HAVING COALESCE(SUM(i3.quantity), 0) = 0
                ) as out_of_stock_count
                FROM products p
                LEFT JOIN inventory i ON p.id = i.product_id";
        
        $row = $db->query($sql)->fetch();
        return $row ?: [
            'total_products' => 0,
            'total_units' => 0,
            'total_cost_value' => 0,
            'total_retail_value' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0
        ];
    }
}
