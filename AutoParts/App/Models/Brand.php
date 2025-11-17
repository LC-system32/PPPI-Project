<?php

namespace App\Models;

use PDO;

class Brand extends Model
{
    public static function all(): array
    {
        // Повертаємо бренди з кількістю доступних запчастин.
        // Для брендів-виробників рахуємо products.brand_id = b.id.
        // Для брендів-автовиробників також рахуємо запчастини, сумісні
        // через product_car_model + car_models.brand = b.name.
        $sql = '
            SELECT
                b.*,
                (
                    SELECT COUNT(DISTINCT p.id)
                    FROM products p
                    LEFT JOIN product_car_model pcm ON pcm.product_id = p.id
                    LEFT JOIN car_models cm ON cm.id = pcm.car_model_id
                    WHERE p.is_active = TRUE
                      AND (p.brand_id = b.id OR cm.brand = b.name)
                ) AS products_count
            FROM brands b
            ORDER BY b.name
        ';

        return self::query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Бренди лише виробників запчастин (для фільтрів каталогу).
     * Рахуємо тільки products.brand_id = brands.id.
     */
    public static function allForProducts(): array
    {
        $sql = '
            SELECT b.*, COUNT(p.id) AS products_count
            FROM brands b
            JOIN products p ON p.brand_id = b.id AND p.is_active = TRUE
            GROUP BY b.id
            ORDER BY b.name
        ';

        return self::query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Бренди-автовиробники (марки авто), для яких існують моделі в car_models.
     */
    public static function allCarMakers(): array
    {
        $sql = '
            SELECT b.*, COUNT(DISTINCT cm.id) AS car_models_count
            FROM brands b
            JOIN car_models cm ON cm.brand = b.name
            GROUP BY b.id
            ORDER BY b.name
        ';

        return self::query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function find(int $id): ?array
    {
        $stmt = self::query('SELECT * FROM brands WHERE id = :id LIMIT 1', ['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = self::query('SELECT * FROM brands WHERE slug = :slug LIMIT 1', ['slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function create(array $data): array
    {
        $stmt = self::db()->prepare(
            'INSERT INTO brands (name, slug)
             VALUES (:name, :slug)
             RETURNING *'
        );
        $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = self::db()->prepare(
            'UPDATE brands SET name = :name, slug = :slug WHERE id = :id'
        );

        return $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'id' => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::query('DELETE FROM brands WHERE id = :id', ['id' => $id])->rowCount() > 0;
    }
    public static function allCarBrands(): array
    {
        $sql = '
            SELECT
                b.*,
                COUNT(DISTINCT p.id) AS products_count
            FROM brands b
            JOIN car_models cm ON cm.brand = b.name
            LEFT JOIN product_car_model pcm ON pcm.car_model_id = cm.id
            LEFT JOIN products p
                ON p.id = pcm.product_id
               AND p.is_active = TRUE
            GROUP BY b.id
            HAVING COUNT(DISTINCT p.id) > 0
            ORDER BY b.name
        ';

        $stmt = self::query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
