<?php

namespace App\Models;

class Cart
{
    protected static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public static function items(): array
    {
        self::ensureSession();
        $items = [];

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = Product::find((int) $productId);
            if (!$product || (int) $product['stock'] <= 0) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }

            $qty = min((int) $quantity, (int) $product['stock']);
            $subtotal = (float) $product['price'] * $qty;

            $items[] = [
                'product_id' => (int) $productId,
                'quantity' => $qty,
                'price' => (float) $product['price'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'stock' => (int) $product['stock'],
                'subtotal' => $subtotal,
            ];
        }

        return $items;
    }

    public static function addProduct(int $productId, int $quantity): bool
    {
        self::ensureSession();

        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        if ((int) $product['stock'] <= 0) {
            return false;
        }

        $current = $_SESSION['cart'][$productId] ?? 0;
        $newQuantity = min($current + max($quantity, 1), (int) $product['stock']);
        $_SESSION['cart'][$productId] = $newQuantity;
        return true;
    }

    public static function updateItem(int $productId, int $quantity): bool
    {
        self::ensureSession();

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }

        $product = Product::find($productId);
        if (!$product) {
            unset($_SESSION['cart'][$productId]);
            return false;
        }

        $_SESSION['cart'][$productId] = min($quantity, (int) $product['stock']);
        return true;
    }

    public static function removeItem(int $productId): void
    {
        self::ensureSession();
        unset($_SESSION['cart'][$productId]);
    }

    public static function clear(): void
    {
        self::ensureSession();
        $_SESSION['cart'] = [];
    }

    public static function total(): float
    {
        return array_reduce(self::items(), static fn($carry, $item) => $carry + $item['subtotal'], 0.0);
    }

    public static function count(): int
    {
        self::ensureSession();

        return (int) array_sum($_SESSION['cart']);
    }
}
