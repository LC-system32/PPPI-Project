<?php

namespace App\Models;

use PDO;

class Category extends Model
{
    public static function all(): array
    {
        return self::query('SELECT * FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function tree(): array
    {
        $categories = self::query(
            'SELECT * FROM categories ORDER BY parent_id NULLS FIRST, name'
        )->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $items = [];
        foreach ($categories as $category) {
            $category['children'] = [];
            $items[$category['id']] = $category;
        }

        $tree = [];
        foreach ($items as $id => &$category) {
            if (!empty($category['parent_id']) && isset($items[$category['parent_id']])) {
                $items[$category['parent_id']]['children'][] = &$category;
                continue;
            }

            $tree[] = &$category;
        }
        unset($category);

        return $tree;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = self::query('SELECT * FROM categories WHERE slug = :slug LIMIT 1', ['slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = self::query('SELECT * FROM categories WHERE id = :id LIMIT 1', ['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function isRoot(array $category): bool
    {
        return empty($category['parent_id']);
    }

    public static function getChildren(int $id): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM categories WHERE parent_id = :id ORDER BY name'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function getParent(int $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT parent.*
             FROM categories child
             JOIN categories parent ON parent.id = child.parent_id
             WHERE child.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function create(array $data): array
    {
        $stmt = self::db()->prepare(
            'INSERT INTO categories (name, slug, parent_id, description, created_at, updated_at)
             VALUES (:name, :slug, :parent_id, :description, NOW(), NOW())
             RETURNING *'
        );

        $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'parent_id' => $data['parent_id'] ?: null,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = self::db()->prepare(
            'UPDATE categories
             SET name = :name,
                 slug = :slug,
                 parent_id = :parent_id,
                 description = :description,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'parent_id' => $data['parent_id'] ?: null,
            'id' => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::query('DELETE FROM categories WHERE id = :id', ['id' => $id])->rowCount() > 0;
    }

    public static function topLevel(int $limit = 8): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function getDescendantIds(int $id): array
    {
        $db = self::db();

        $sql = "
            WITH RECURSIVE subcats AS (
                SELECT id
                FROM categories
                WHERE id = :id

                UNION ALL

                SELECT c.id
                FROM categories c
                JOIN subcats s ON c.parent_id = s.id
            )
            SELECT id FROM subcats;
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);

        // повертаємо просто масив [id, id, id...]
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

}
