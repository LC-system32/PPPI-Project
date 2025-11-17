<?php

namespace App\Models;

use PDO;

class ProductImage extends Model
{
    public static function forProduct(int $productId): array
    {
        return self::query(
            'SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_main DESC, id',
            ['product_id' => $productId]
        )->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
