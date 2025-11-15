<?php

namespace App\Models;

use PDO;

class CarModel extends Model
{
    public static function all(): array
    {
        return self::query('SELECT * FROM car_models ORDER BY brand, model')->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function find(int $id): ?array
    {
        $stmt = self::query('SELECT * FROM car_models WHERE id = :id LIMIT 1', ['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function byIds(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = self::db()->prepare("SELECT * FROM car_models WHERE id IN ({$placeholders})");
        $stmt->execute($ids);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    public static function forBrand(string $brandName): array
    {
        $stmt = self::db()->prepare(
            'SELECT DISTINCT model, generation, year_from, year_to
             FROM car_models
             WHERE brand = :brand
             ORDER BY model, generation, year_from'
        );
        $stmt->execute(['brand' => $brandName]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}