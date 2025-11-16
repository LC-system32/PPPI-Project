<?php

namespace App\Models;

use PDO;
use Throwable;

class Order extends Model
{
    public static function byUser(int $userId, int $limit = 20): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return self::attachItems($orders);
    }

    public static function findWithItems(int $orderId): ?array
    {
        $stmt = self::query('SELECT * FROM orders WHERE id = :id LIMIT 1', ['id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $order['items'] = self::itemsForOrder($orderId);

        return $order;
    }

    public static function createFromCart(int $userId, array $payload, array $cartItems): ?array
    {
        if (!$cartItems) {
            return null;
        }

        $db = self::db();
        $db->beginTransaction();

        try {
            $total = array_sum(array_column($cartItems, 'subtotal'));

            $stmt = $db->prepare(
                'INSERT INTO orders (user_id, status, total, payment_method, delivery_method, delivery_address, notes, created_at, updated_at)
                 VALUES (:user_id, :status, :total, :payment_method, :delivery_method, :delivery_address, :notes, NOW(), NOW())
                 RETURNING *'
            );
            $stmt->execute([
                'user_id' => $userId,
                'status' => 'new',
                'total' => $total,
                'payment_method' => $payload['payment_method'] ?? 'card',
                'delivery_method' => $payload['delivery_method'] ?? 'pickup',
                'delivery_address' => $payload['delivery_address'] ?? 'Не вказано',
                'notes' => $payload['notes'] ?? null,
            ]);

            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            foreach ($cartItems as $item) {
                $itemStmt = $db->prepare(
                    'INSERT INTO order_items (order_id, product_id, price, quantity, name_snapshot)
                     VALUES (:order_id, :product_id, :price, :quantity, :name_snapshot)'
                );
                $itemStmt->execute([
                    'order_id' => $order['id'],
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name_snapshot' => $item['name'],
                ]);

                Product::decrementStock($item['product_id'], $item['quantity']);
            }

            $db->commit();

            return self::findWithItems((int) $order['id']);
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function createFromCartGuest(array $guest, array $payload, array $cartItems): ?array
    {
        if (!$cartItems) {
            return null;
        }

        $db = self::db();
        $db->beginTransaction();

        try {
            $total = array_sum(array_column($cartItems, 'subtotal'));

            $stmt = $db->prepare(
                'INSERT INTO orders (guest_name, guest_email, guest_phone, status, total, payment_method, delivery_method, delivery_address, notes, created_at, updated_at)
                 VALUES (:guest_name, :guest_email, :guest_phone, :status, :total, :payment_method, :delivery_method, :delivery_address, :notes, NOW(), NOW())
                 RETURNING *'
            );
            $stmt->execute([
                'guest_name' => $guest['name'] ?? null,
                'guest_email' => $guest['email'] ?? null,
                'guest_phone' => $guest['phone'] ?? null,
                'status' => 'new',
                'total' => $total,
                'payment_method' => $payload['payment_method'] ?? 'card',
                'delivery_method' => $payload['delivery_method'] ?? 'pickup',
                'delivery_address' => $payload['delivery_address'] ?? 'Не вказано',
                'notes' => $payload['notes'] ?? null,
            ]);

            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            foreach ($cartItems as $item) {
                $itemStmt = $db->prepare(
                    'INSERT INTO order_items (order_id, product_id, price, quantity, name_snapshot)
                     VALUES (:order_id, :product_id, :price, :quantity, :name_snapshot)'
                );
                $itemStmt->execute([
                    'order_id' => $order['id'],
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name_snapshot' => $item['name'],
                ]);

                Product::decrementStock($item['product_id'], $item['quantity']);
            }

            $db->commit();

            return self::findWithItems((int) $order['id']);
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function all(?string $status = null): array
    {
        if ($status) {
            $stmt = self::db()->prepare('SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC');
            $stmt->execute(['status' => $status]);
        } else {
            $stmt = self::query('SELECT * FROM orders ORDER BY created_at DESC');
        }

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return self::attachItems($orders);
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $stmt = self::db()->prepare(
            'UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id'
        );

        return $stmt->execute([
            'status' => $status,
            'id' => $id,
        ]);
    }

    protected static function attachItems(array $orders): array
    {
        foreach ($orders as &$order) {
            $order['items'] = self::itemsForOrder((int) $order['id']);
        }
        unset($order);

        return $orders;
    }

    protected static function itemsForOrder(int $orderId): array
    {
        $stmt = self::query(
            'SELECT oi.*, p.slug
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = :order_id',
            ['order_id' => $orderId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
