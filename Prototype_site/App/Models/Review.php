<?php

namespace App\Models;

use PDO;

class Review extends Model
{
    /**
     * Fetch approved reviews for product
     * @param int $productId
     * @return array
     */
    public static function forProduct(int $productId): array
    {
        try {
            $stmt = self::db()->prepare('SELECT id, product_id, user_id, author, rating, text, status, created_at FROM product_reviews WHERE product_id = :product_id AND status = :status ORDER BY created_at DESC');
            $stmt->execute(['product_id' => $productId, 'status' => 'approved']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            // Table might not exist yet; avoid fatal error in views. Log and show friendly message.
            error_log('Review::forProduct DB error: ' . $e->getMessage());
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            // notify admin/developer once via flash
            if (empty($_SESSION['flash']['message'])) {
                $_SESSION['flash']['message'] = 'Сервіс відгуків тимчасово недоступний (відсутня таблиця product_reviews).';
            }
            return [];
        }
    }

    public static function countApprovedForProduct(int $productId): int
    {
        try {
            $stmt = self::db()->prepare('SELECT COUNT(*) FROM product_reviews WHERE product_id = :product_id AND status = :status');
            $stmt->execute(['product_id' => $productId, 'status' => 'approved']);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log('Review::countApprovedForProduct DB error: ' . $e->getMessage());
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            if (empty($_SESSION['flash']['message'])) {
                $_SESSION['flash']['message'] = 'Сервіс відгуків тимчасово недоступний (відсутня таблиця product_reviews).';
            }
            return 0;
        }
    }

    /**
     * Create new review (initially pending moderation)
     * @param array $data
     * @return array|null
     */
    public static function create(array $data): ?array
    {
        try {
            $db = self::db();
            $stmt = $db->prepare('INSERT INTO product_reviews (product_id, user_id, author, rating, text, status, created_at, updated_at) VALUES (:product_id, :user_id, :author, :rating, :text, :status, NOW(), NOW()) RETURNING *');
        $params = [
            'product_id' => (int) ($data['product_id'] ?? 0),
            'user_id' => $data['user_id'] !== null ? (int) $data['user_id'] : null,
            'author' => $data['author'] ?? null,
            'rating' => (int) ($data['rating'] ?? 0),
            'text' => $data['text'] ?? null,
            'status' => $data['status'] ?? 'pending',
        ];

        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v === null ? null : $v);
        }
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            error_log('Review::create DB error: ' . $e->getMessage());
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            if (empty($_SESSION['flash']['message'])) {
                $_SESSION['flash']['message'] = 'Неможливо зберегти відгук — сервіс тимчасово недоступний.';
            }
            return null;
        }
    }

    /**
     * Return paginated pending reviews (for admin)
     * @param int $page
     * @param int $perPage
     * @return array ['data'=>[], 'total'=>int, 'page'=>int, 'perPage'=>int]
     */
    public static function pending(int $page = 1, int $perPage = 20): array
    {
        return self::listByStatus('pending', $page, $perPage);
    }

    /**
     * Generic listing by status
     */
    public static function listByStatus(string $status, int $page = 1, int $perPage = 20): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        try {
            $db = self::db();
            $stmt = $db->prepare("SELECT pr.*, p.name as product_name FROM product_reviews pr LEFT JOIN products p ON p.id = pr.product_id WHERE (:status = '' OR pr.status = :status) ORDER BY pr.created_at DESC LIMIT :limit OFFSET :offset");
            // bind
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            if ($status === '') {
                $countStmt = $db->prepare('SELECT COUNT(*) FROM product_reviews');
                $countStmt->execute();
            } else {
                $countStmt = $db->prepare('SELECT COUNT(*) FROM product_reviews WHERE status = :status');
                $countStmt->execute(['status' => $status]);
            }
            $total = (int) $countStmt->fetchColumn();

            return ['data' => $data, 'total' => $total, 'page' => $page, 'perPage' => $perPage];
        } catch (\PDOException $e) {
            error_log('Review::listByStatus DB error: ' . $e->getMessage());
            return ['data' => [], 'total' => 0, 'page' => $page, 'perPage' => $perPage];
        }
    }

    /**
     * Find single review by id
     */
    public static function find(int $id): ?array
    {
        try {
            $stmt = self::db()->prepare('SELECT pr.*, p.name as product_name FROM product_reviews pr LEFT JOIN products p ON p.id = pr.product_id WHERE pr.id = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            error_log('Review::find DB error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update review status (approved|rejected)
     */
    public static function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['approved', 'rejected', 'pending'], true)) {
            return false;
        }

        try {
            $stmt = self::db()->prepare('UPDATE product_reviews SET status = :status, updated_at = NOW() WHERE id = :id');
            $stmt->execute(['status' => $status, 'id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('Review::updateStatus DB error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Count reviews by status
     */
    public static function countByStatus(string $status): int
    {
        try {
            $stmt = self::db()->prepare('SELECT COUNT(*) FROM product_reviews WHERE status = :status');
            $stmt->execute(['status' => $status]);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log('Review::countByStatus DB error: ' . $e->getMessage());
            return 0;
        }
    }
}
