<?php

namespace App\Models;

use PDO;

class Brand extends Model
{
    public static function all(): array
    {
        $sql = 'SELECT b.*, COUNT(p.id) AS products_count
                FROM brands b
                LEFT JOIN products p ON p.brand_id = b.id
                GROUP BY b.id
                ORDER BY b.name';

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
}
