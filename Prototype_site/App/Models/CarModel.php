<?php

namespace App\Models;

use PDO;

class CarModel extends Model
{
    public static function forBrand(string $brandName): array
    {
        $stmt = self::db()->prepare(
            'SELECT id, brand, model, generation, year_from, year_to
             FROM car_models
             WHERE brand = :brand
             ORDER BY model, generation NULLS LAST, year_from NULLS FIRST'
        );

        $stmt->execute(['brand' => $brandName]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT id, brand, model, generation, year_from, year_to
             FROM car_models
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function slugFor(array $carModel): string
    {
        $parts = [];

        if (!empty($carModel['brand'])) {
            $parts[] = (string) $carModel['brand'];
        }

        if (!empty($carModel['model'])) {
            $parts[] = (string) $carModel['model'];
        }

        if (!empty($carModel['generation'])) {
            $parts[] = (string) $carModel['generation'];
        }

        $text = strtolower(trim(implode(' ', $parts)));

        $slug = preg_replace('/[^a-z0-9]+/i', '-', $text) ?? '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'model';
    }
}
