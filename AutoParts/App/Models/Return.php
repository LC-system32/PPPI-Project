<?php

namespace App\Models;

use PDO;

class ProductReturn extends Model
{
    protected $table = 'returns';
    protected $fillable = [
        'order_id', 'user_id', 'reason', 'description', 'items_json',
        'status', 'return_method', 'tracking_number', 'notes', 'admin_comment',
        'deadline_days', 'created_at', 'updated_at'
    ];

    /**
     * Create a new return request
     */
    public static function create($data)
    {
        $sql = "
            INSERT INTO returns 
            (order_id, user_id, reason, description, items_json, status, return_method, deadline_days, created_at, updated_at)
            VALUES
            (:order_id, :user_id, :reason, :description, :items_json, 'pending', :return_method, 14, NOW(), NOW())
            RETURNING id, created_at
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':order_id', (int)$data['order_id'], PDO::PARAM_INT);
        $stmt->bindValue(':user_id', isset($data['user_id']) ? (int)$data['user_id'] : null);
        $stmt->bindValue(':reason', $data['reason']);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':items_json', isset($data['items']) ? json_encode($data['items']) : null);
        $stmt->bindValue(':return_method', $data['return_method'] ?? 'courier');

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find a return by ID
     */
    public static function find($id)
    {
        $sql = "
            SELECT r.*, o.guest_name, o.guest_email, o.guest_phone, o.total, o.created_at as order_date
            FROM returns r
            LEFT JOIN orders o ON o.id = r.order_id
            WHERE r.id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $result['items_json']) {
            $result['items'] = json_decode($result['items_json'], true);
        }
        return $result;
    }

    /**
     * Get all returns for a user (with deadline info)
     */
    public static function getByUser($userId)
    {
        $sql = "
            SELECT 
                r.id, r.order_id, r.reason, r.status, r.created_at, r.return_method,
                o.id as order_number, o.total, o.created_at as order_created,
                EXTRACT(DAY FROM (NOW() - o.created_at)) as days_since_purchase,
                (14 - EXTRACT(DAY FROM (NOW() - o.created_at)))::INT as days_remaining
            FROM returns r
            LEFT JOIN orders o ON o.id = r.order_id
            WHERE r.user_id = :user_id
            ORDER BY r.created_at DESC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Check if order is eligible for return (within 14 days)
     */
    public static function isEligibleForReturn($orderId)
    {
        $sql = "
            SELECT EXTRACT(DAY FROM (NOW() - created_at))::INT as days_since_purchase
            FROM orders
            WHERE id = :order_id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':order_id', (int)$orderId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && (int)$result['days_since_purchase'] <= 14;
    }

    /**
     * Get return details with order items
     */
    public static function findWithItems($returnId)
    {
        $returnData = self::find($returnId);
        if (!$returnData) {
            return null;
        }

        // Fetch order items for context
        $sql = "
            SELECT oi.id, oi.product_id, oi.name_snapshot, oi.price, oi.quantity
            FROM order_items oi
            WHERE oi.order_id = :order_id
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':order_id', (int)$returnData['order_id'], PDO::PARAM_INT);
        $stmt->execute();

        $returnData['order_items'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $returnData;
    }

    /**
     * Update return status
     */
    public static function updateStatus($returnId, $status, $adminComment = null)
    {
        $sql = "
            UPDATE returns
            SET status = :status, admin_comment = :admin_comment, updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':admin_comment', $adminComment);
        $stmt->bindValue(':id', (int)$returnId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get all pending returns (for admin)
     */
    public static function getAllPending($limit = 50, $offset = 0)
    {
        $sql = "
            SELECT 
                r.id, r.order_id, r.reason, r.status, r.created_at, r.return_method,
                r.user_id, u.login, u.email, u.phone,
                o.total, o.created_at as order_created
            FROM returns r
            LEFT JOIN orders o ON o.id = r.order_id
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.status IN ('pending', 'approved')
            ORDER BY r.created_at ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get return statistics
     */
    public static function getStats()
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'received' THEN 1 END) as received,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed
            FROM returns
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
