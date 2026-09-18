<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class StockMovement
{
    public static function recordIn(
        int $productId,
        int $locationId,
        int $quantity,
        float $unitCost,
        int $userId,
        string $reason,
        string $referenceNo,
        ?string $notes = null
    ): int {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity received must be greater than zero.");
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // 1. Update or create inventory row
            $stmt = $db->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND location_id = ?");
            $stmt->execute([$productId, $locationId]);
            $inv = $stmt->fetch();

            $newQty = $quantity;
            if ($inv) {
                $newQty = (int)$inv['quantity'] + $quantity;
                $updateStmt = $db->prepare("UPDATE inventory SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$newQty, $inv['id']]);
            } else {
                $insertStmt = $db->prepare("INSERT INTO inventory (product_id, location_id, quantity) VALUES (?, ?, ?)");
                $insertStmt->execute([$productId, $locationId, $newQty]);
            }

            // 2. Record stock movement
            $movStmt = $db->prepare("INSERT INTO stock_movements 
                (reference_no, type, product_id, source_location_id, destination_location_id, quantity, unit_cost, balance_after, user_id, reason, notes) 
                VALUES (?, 'IN', ?, NULL, ?, ?, ?, ?, ?, ?, ?)");
            $movStmt->execute([
                $referenceNo,
                $productId,
                $locationId,
                $quantity,
                $unitCost,
                $newQty,
                $userId,
                $reason,
                $notes
            ]);

            $movementId = (int)$db->lastInsertId();
            $db->commit();
            return $movementId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function recordOut(
        int $productId,
        int $locationId,
        int $quantity,
        ?int $departmentId,
        int $userId,
        string $reason,
        string $referenceNo,
        ?string $notes = null
    ): int {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity issued must be greater than zero.");
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Verify stock availability
            $stmt = $db->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND location_id = ?");
            $stmt->execute([$productId, $locationId]);
            $inv = $stmt->fetch();

            $currentStock = $inv ? (int)$inv['quantity'] : 0;
            if ($currentStock < $quantity) {
                throw new \RuntimeException("Insufficient stock! Available at this location: {$currentStock}, requested: {$quantity}");
            }

            $newQty = $currentStock - $quantity;
            $updateStmt = $db->prepare("UPDATE inventory SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$newQty, $inv['id']]);

            // Fetch product cost price for movement valuation
            $prodStmt = $db->prepare("SELECT cost_price FROM products WHERE id = ?");
            $prodStmt->execute([$productId]);
            $costPrice = (float)$prodStmt->fetchColumn();

            $movStmt = $db->prepare("INSERT INTO stock_movements 
                (reference_no, type, product_id, source_location_id, destination_location_id, department_id, quantity, unit_cost, balance_after, user_id, reason, notes) 
                VALUES (?, 'OUT', ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?)");
            $movStmt->execute([
                $referenceNo,
                $productId,
                $locationId,
                $departmentId,
                $quantity,
                $costPrice,
                $newQty,
                $userId,
                $reason,
                $notes
            ]);

            $movementId = (int)$db->lastInsertId();
            $db->commit();
            return $movementId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function recordTransfer(
        int $productId,
        int $sourceLocationId,
        int $destLocationId,
        int $quantity,
        int $userId,
        string $referenceNo,
        ?string $notes = null
    ): int {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Transfer quantity must be greater than zero.");
        }
        if ($sourceLocationId === $destLocationId) {
            throw new \InvalidArgumentException("Source and destination locations cannot be the same.");
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Deduct from source
            $srcStmt = $db->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND location_id = ?");
            $srcStmt->execute([$productId, $sourceLocationId]);
            $srcInv = $srcStmt->fetch();

            $srcStock = $srcInv ? (int)$srcInv['quantity'] : 0;
            if ($srcStock < $quantity) {
                throw new \RuntimeException("Insufficient stock at source location! Available: {$srcStock}, transferring: {$quantity}");
            }

            $newSrcQty = $srcStock - $quantity;
            $db->prepare("UPDATE inventory SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
               ->execute([$newSrcQty, $srcInv['id']]);

            // Add to destination
            $destStmt = $db->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND location_id = ?");
            $destStmt->execute([$productId, $destLocationId]);
            $destInv = $destStmt->fetch();

            if ($destInv) {
                $newDestQty = (int)$destInv['quantity'] + $quantity;
                $db->prepare("UPDATE inventory SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                   ->execute([$newDestQty, $destInv['id']]);
            } else {
                $newDestQty = $quantity;
                $db->prepare("INSERT INTO inventory (product_id, location_id, quantity) VALUES (?, ?, ?)")
                   ->execute([$productId, $destLocationId, $newDestQty]);
            }

            // Get product cost price
            $costStmt = $db->prepare("SELECT cost_price FROM products WHERE id = ?");
            $costStmt->execute([$productId]);
            $costPrice = (float)$costStmt->fetchColumn();

            $movStmt = $db->prepare("INSERT INTO stock_movements 
                (reference_no, type, product_id, source_location_id, destination_location_id, quantity, unit_cost, balance_after, user_id, reason, notes) 
                VALUES (?, 'TRANSFER', ?, ?, ?, ?, ?, ?, ?, 'Location Transfer', ?)");
            $movStmt->execute([
                $referenceNo,
                $productId,
                $sourceLocationId,
                $destLocationId,
                $quantity,
                $costPrice,
                $newSrcQty, // records remaining balance at source
                $userId,
                $notes
            ]);

            $movementId = (int)$db->lastInsertId();
            $db->commit();
            return $movementId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function recordAdjustment(
        int $productId,
        int $locationId,
        int $actualCount,
        int $userId,
        string $reason,
        string $referenceNo,
        ?string $notes = null
    ): int {
        if ($actualCount < 0) {
            throw new \InvalidArgumentException("Actual count cannot be negative.");
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND location_id = ?");
            $stmt->execute([$productId, $locationId]);
            $inv = $stmt->fetch();

            $currentStock = $inv ? (int)$inv['quantity'] : 0;
            $diffQuantity = $actualCount - $currentStock; // positive if surplus, negative if loss

            if ($inv) {
                $db->prepare("UPDATE inventory SET quantity = ?, last_counted_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                   ->execute([$actualCount, $inv['id']]);
            } else {
                $db->prepare("INSERT INTO inventory (product_id, location_id, quantity, last_counted_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)")
                   ->execute([$productId, $locationId, $actualCount]);
            }

            // Product cost price
            $costStmt = $db->prepare("SELECT cost_price FROM products WHERE id = ?");
            $costStmt->execute([$productId]);
            $costPrice = (float)$costStmt->fetchColumn();

            $movStmt = $db->prepare("INSERT INTO stock_movements 
                (reference_no, type, product_id, source_location_id, destination_location_id, quantity, unit_cost, balance_after, user_id, reason, notes) 
                VALUES (?, 'ADJUSTMENT', ?, ?, NULL, ?, ?, ?, ?, ?, ?)");
            $movStmt->execute([
                $referenceNo,
                $productId,
                $locationId,
                $diffQuantity,
                $costPrice,
                $actualCount,
                $userId,
                $reason,
                $notes
            ]);

            $movementId = (int)$db->lastInsertId();
            $db->commit();
            return $movementId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getHistory(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "m.type = ?";
            $params[] = $filters['type'];
        }
        if (!empty($filters['product_id'])) {
            $where[] = "m.product_id = ?";
            $params[] = (int)$filters['product_id'];
        }
        if (!empty($filters['location_id'])) {
            $where[] = "(m.source_location_id = ? OR m.destination_location_id = ?)";
            $params[] = (int)$filters['location_id'];
            $params[] = (int)$filters['location_id'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = "m.department_id = ?";
            $params[] = (int)$filters['department_id'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(m.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(m.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT m.*, 
                p.name as product_name, p.sku, p.unit,
                loc_src.name as source_location_name,
                loc_dst.name as destination_location_name,
                d.name as department_name,
                u.name as user_name
                FROM stock_movements m
                JOIN products p ON m.product_id = p.id
                LEFT JOIN locations loc_src ON m.source_location_id = loc_src.id
                LEFT JOIN locations loc_dst ON m.destination_location_id = loc_dst.id
                LEFT JOIN departments d ON m.department_id = d.id
                LEFT JOIN users u ON m.user_id = u.id
                WHERE {$whereClause}
                ORDER BY m.created_at DESC, m.id DESC
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getRecent(int $limit = 8): array
    {
        return self::getHistory([], $limit, 0);
    }

    public static function getFlowTrends(int $days = 14): array
    {
        $db = Database::getConnection();
        $sql = "SELECT 
                DATE(created_at) as movement_date,
                SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END) as inbound_qty,
                SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END) as outbound_qty
                FROM stock_movements
                WHERE created_at >= date('now', '-' || ? || ' days')
                GROUP BY DATE(created_at)
                ORDER BY movement_date ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    public static function getCategoryBreakdown(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT c.name as category_name, 
                COALESCE(SUM(i.quantity), 0) as total_units,
                COALESCE(SUM(i.quantity * p.cost_price), 0) as total_valuation
                FROM categories c
                JOIN products p ON c.id = p.category_id
                LEFT JOIN inventory i ON p.id = i.product_id
                GROUP BY c.id
                HAVING total_units > 0
                ORDER BY total_valuation DESC";
        return $db->query($sql)->fetchAll();
    }
}
