<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Requisition
{
    public static function all(array $filters = []): array
    {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "r.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = "r.department_id = ?";
            $params[] = (int)$filters['department_id'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT r.*, 
                d.name as department_name, d.code as department_code,
                u_req.name as requested_by_name,
                u_app.name as approved_by_name,
                COUNT(ri.id) as total_items,
                COALESCE(SUM(ri.requested_quantity), 0) as total_quantity
                FROM requisitions r
                JOIN departments d ON r.department_id = d.id
                JOIN users u_req ON r.requested_by = u_req.id
                LEFT JOIN users u_app ON r.approved_by = u_app.id
                LEFT JOIN requisition_items ri ON r.id = ri.requisition_id
                WHERE {$whereClause}
                GROUP BY r.id
                ORDER BY r.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $sql = "SELECT r.*, 
                d.name as department_name, d.code as department_code,
                u_req.name as requested_by_name, u_req.email as requested_by_email,
                u_app.name as approved_by_name
                FROM requisitions r
                JOIN departments d ON r.department_id = d.id
                JOIN users u_req ON r.requested_by = u_req.id
                LEFT JOIN users u_app ON r.approved_by = u_app.id
                WHERE r.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $req = $stmt->fetch();
        if (!$req) {
            return null;
        }

        // Fetch requisition items with product and current stock details
        $itemSql = "SELECT ri.*, p.name as product_name, p.sku, p.unit, p.cost_price,
                    COALESCE(SUM(i.quantity), 0) as current_global_stock
                    FROM requisition_items ri
                    JOIN products p ON ri.product_id = p.id
                    LEFT JOIN inventory i ON p.id = i.product_id
                    WHERE ri.requisition_id = ?
                    GROUP BY ri.id";
        $itemStmt = $db->prepare($itemSql);
        $itemStmt->execute([$id]);
        $req['items'] = $itemStmt->fetchAll();

        return $req;
    }

    public static function create(array $data, array $items): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $reqNumber = 'REQ-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));

            $stmt = $db->prepare("INSERT INTO requisitions (req_number, department_id, requested_by, status, priority, needed_by_date, notes) VALUES (?, ?, ?, 'pending', ?, ?, ?)");
            $stmt->execute([
                $reqNumber,
                (int)$data['department_id'],
                (int)$data['requested_by'],
                $data['priority'] ?? 'medium',
                !empty($data['needed_by_date']) ? $data['needed_by_date'] : null,
                $data['notes'] ?? null
            ]);

            $reqId = (int)$db->lastInsertId();

            $itemStmt = $db->prepare("INSERT INTO requisition_items (requisition_id, product_id, requested_quantity, status) VALUES (?, ?, ?, 'pending')");
            foreach ($items as $item) {
                if (!empty($item['product_id']) && (int)$item['quantity'] > 0) {
                    $itemStmt->execute([
                        $reqId,
                        (int)$item['product_id'],
                        (int)$item['quantity']
                    ]);
                }
            }

            $db->commit();
            return $reqId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function approve(int $id, int $approvedBy, array $approvedQuantities = []): bool
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("UPDATE requisitions SET status = 'approved', approved_by = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$approvedBy, $id]);

            $itemStmt = $db->prepare("UPDATE requisition_items SET approved_quantity = ?, status = 'approved' WHERE id = ? AND requisition_id = ?");
            foreach ($approvedQuantities as $itemId => $qty) {
                $itemStmt->execute([(int)$qty, (int)$itemId, $id]);
            }

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function reject(int $id, int $approvedBy, ?string $reason = null): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE requisitions SET status = 'rejected', approved_by = ?, notes = COALESCE(notes, '') || ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $appendNotes = $reason ? " [Rejection Reason: {$reason}]" : "";
        return $stmt->execute([$approvedBy, $appendNotes, $id]);
    }

    public static function fulfill(int $id, int $userId, int $sourceLocationId): bool
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $req = self::find($id);
            if (!$req || !in_array($req['status'], ['approved', 'pending'])) {
                throw new \RuntimeException("Only pending or approved requisitions can be fulfilled.");
            }

            // Deduct stock from source warehouse and record stock movements
            foreach ($req['items'] as $item) {
                $fulfillQty = $item['approved_quantity'] > 0 ? $item['approved_quantity'] : $item['requested_quantity'];
                
                // Record stock out
                StockMovement::recordOut(
                    (int)$item['product_id'],
                    $sourceLocationId,
                    (int)$fulfillQty,
                    (int)$req['department_id'],
                    $userId,
                    'Department Issuance',
                    'FULFILL-' . $req['req_number'],
                    "Fulfilled via Requisition #" . $req['req_number']
                );

                // Update requisition item
                $db->prepare("UPDATE requisition_items SET fulfilled_quantity = ?, status = 'fulfilled' WHERE id = ?")
                   ->execute([(int)$fulfillQty, (int)$item['id']]);
            }

            // Update requisition status
            $db->prepare("UPDATE requisitions SET status = 'fulfilled', updated_at = CURRENT_TIMESTAMP WHERE id = ?")
               ->execute([$id]);

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getPendingCount(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM requisitions WHERE status = 'pending'")->fetchColumn();
    }
}
