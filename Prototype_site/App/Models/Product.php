<?php

namespace App\Models;

use PDO;

class Product extends Model
{
    public static function paginate(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $offset = ($page - 1) * $perPage;
        $conditions = ['p.is_active = true'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $conditions[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = (int) $filters['brand_id'];
        }

        if (!empty($filters['keyword'])) {
            $conditions[] = '(p.name ILIKE :keyword OR p.description ILIKE :keyword)';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['category_name'])) {
            $conditions[] = 'c.name = :category_name';
            $params['category_name'] = $filters['category_name'];
        }

        if (!empty($filters['in_stock'])) {
            $conditions[] = 'p.stock > 0';
        }

        if ($filters['price_min'] !== null && $filters['price_min'] !== '') {
            $conditions[] = 'p.price >= :price_min';
            $params['price_min'] = (float) $filters['price_min'];
        }

        if ($filters['price_max'] !== null && $filters['price_max'] !== '') {
            $conditions[] = 'p.price <= :price_max';
            $params['price_max'] = (float) $filters['price_max'];
        }

        $where = implode(' AND ', $conditions);

        $sort = $filters['sort'] ?? '';
        switch ($sort) {
            case 'price_asc':
                $orderBy = 'p.price ASC';
                break;
            case 'price_desc':
                $orderBy = 'p.price DESC';
                break;
            case 'name_asc':
                $orderBy = 'p.name ASC';
                break;
            case 'name_desc':
                $orderBy = 'p.name DESC';
                break;
            case 'newest':
                $orderBy = 'p.created_at DESC';
                break;
            default:
                $orderBy = 'p.created_at DESC';
                break;
        }

        $fromClause = "FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN brands b ON b.id = p.brand_id";

        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name
                {$fromClause}
                WHERE {$where}
                ORDER BY {$orderBy}
                LIMIT :limit OFFSET :offset";

        $stmt = self::db()->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $countSql = "SELECT COUNT(*) {$fromClause} WHERE {$where}";
        $countStmt = self::db()->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue(':' . $key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();
        $pages = $total > 0 ? (int) ceil($total / $perPage) : 0;

        return [
            'items' => $items,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public static function paginateByCarModel(int $carModelId, int $page = 1, int $perPage = 12): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name
                FROM products p
                JOIN product_car_model pcm ON pcm.product_id = p.id
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN brands b ON b.id = p.brand_id
                WHERE p.is_active = true
                  AND pcm.car_model_id = :car_model_id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':car_model_id', $carModelId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $countStmt = self::db()->prepare(
            'SELECT COUNT(*)
             FROM products p
             JOIN product_car_model pcm ON pcm.product_id = p.id
             WHERE p.is_active = true
               AND pcm.car_model_id = :car_model_id'
        );
        $countStmt->bindValue(':car_model_id', $carModelId, PDO::PARAM_INT);
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();
        $pages = $total > 0 ? (int) ceil($total / $perPage) : 0;

        return [
            'items' => $items,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public static function findByCategory(int $categoryId, int $page = 1, int $perPage = 12): array
    {
        return self::findByCategories([$categoryId], $page, $perPage);
    }

    // alias, якщо десь ще використовується
    public static function byCategory(int $categoryId, int $page = 1, int $perPage = 20): array
    {
        return self::findByCategories([$categoryId], $page, $perPage);
    }

    // НОВИЙ метод – працює по масиву id
    public static function findByCategories(array $categoryIds, int $page = 1, int $perPage = 12): array
    {
        $db = self::db(); // НЕ передаємо PDO ззовні

        if (empty($categoryIds)) {
            return [
                'items'   => [],
                'total'   => 0,
                'page'    => $page,
                'perPage' => $perPage,
                'pages'   => 1,
            ];
        }

        $page    = max($page, 1);
        $perPage = max($perPage, 1);
        $offset  = ($page - 1) * $perPage;

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $sql = "
            SELECT p.*
            FROM products p
            WHERE p.is_active = TRUE
              AND p.category_id IN ($placeholders)
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $db->prepare($sql);

        $i = 1;
        foreach ($categoryIds as $id) {
            $stmt->bindValue($i++, $id, PDO::PARAM_INT);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i,   $offset,  PDO::PARAM_INT);

        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $sqlCount = "
            SELECT COUNT(*)
            FROM products
            WHERE is_active = TRUE
              AND category_id IN ($placeholders)
        ";
        $stmtCount = $db->prepare($sqlCount);

        $i = 1;
        foreach ($categoryIds as $id) {
            $stmtCount->bindValue($i++, $id, PDO::PARAM_INT);
        }

        $stmtCount->execute();
        $total = (int) $stmtCount->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));

        return [
            'items'   => $items,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => $pages,
        ];
    }
    public static function find(int $id): ?array
    {
        $stmt = self::query('SELECT * FROM products WHERE id = :id LIMIT 1', ['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function findBySlugWithRelations(string $slug): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT p.*, c.name AS category_name, b.name AS brand_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.slug = :slug LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        $product['images'] = ProductImage::forProduct((int) $product['id']);

        $carStmt = self::db()->prepare(
            'SELECT cm.*
             FROM product_car_model pcm
             JOIN car_models cm ON cm.id = pcm.car_model_id
             WHERE pcm.product_id = :product_id'
        );
        $carStmt->execute(['product_id' => $product['id']]);
        $product['car_models'] = $carStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return $product;
    }

    public static function create(array $data): array
    {
        $stmt = self::db()->prepare(
            'INSERT INTO products (category_id, brand_id, slug, sku, name, description, price, stock, compatibility, is_active, created_at, updated_at)
             VALUES (:category_id, :brand_id, :slug, :sku, :name, :description, :price, :stock, :compatibility, :is_active, NOW(), NOW())
             RETURNING *'
        );

        $stmt->execute([
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'] ?: null,
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'],
            'compatibility' => $data['compatibility'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = self::db()->prepare(
            'UPDATE products
             SET category_id = :category_id,
                 brand_id = :brand_id,
                 slug = :slug,
                 sku = :sku,
                 name = :name,
                 description = :description,
                 price = :price,
                 stock = :stock,
                 compatibility = :compatibility,
                 is_active = :is_active,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'] ?: null,
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'],
            'compatibility' => $data['compatibility'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'id' => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::query('DELETE FROM products WHERE id = :id', ['id' => $id])->rowCount() > 0;
    }

    public static function decrementStock(int $productId, int $quantity): void
    {
        self::query(
            'UPDATE products SET stock = GREATEST(stock - :quantity, 0) WHERE id = :id',
            ['quantity' => $quantity, 'id' => $productId]
        );
    }

    private static function categoryHasChildren(int $categoryId): bool
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM categories WHERE parent_id = :id');
        $stmt->bindValue(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }
}
