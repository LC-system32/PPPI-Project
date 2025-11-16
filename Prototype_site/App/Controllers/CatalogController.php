<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index(): void
    {
        $filters = $this->sanitize($_GET);
        $page    = max((int)($filters['page'] ?? 1), 1);

        // Підготовка категорій: вибрана + всі її нащадки
        $categoryIds = null;
        $categoryId  = null;
        if (!empty($filters['category_id'])) {
            $categoryId  = (int)$filters['category_id'];
            $categoryIds = Category::getDescendantIds($categoryId);
        }

        // Фільтри каталогу товарів
        $query = [
            'category_ids' => $categoryIds,
            'brand_id'     => !empty($filters['brand_id']) ? (int)$filters['brand_id'] : null,
            'keyword'      => $filters['q'] ?? null,
            'in_stock'     => !empty($filters['in_stock']) ? 1 : null,
            'price_min'    => $filters['price_min'] ?? null,
            'price_max'    => $filters['price_max'] ?? null,
            'sort'         => $filters['sort'] ?? null,
        ];
        $query = array_filter($query, static fn($v) => $v !== null && $v !== '');

        $products   = Product::paginate($page, 12, $query);
        $categories = Category::allWithProductCounts();
        $brands     = Brand::allForProducts();

        if ($categoryId !== null) {
            $filters['category_id'] = (string)$categoryId;
        }

        $this->view('catalog/index', [
            'products'   => $products,
            'categories' => $categories,
            'brands'     => $brands,
            'filters'    => $filters,
        ]);
    }

    public function categories(): void
    {
        // Всі категорії з підрахунком товарів
        $categories = Category::allWithProductCounts();

        // На /categories показуємо лише кореневі
        $topLevel = array_values(array_filter($categories, static function (array $cat): bool {
            return empty($cat['parent_id']);
        }));

        // Вʼюшка очікує змінну $brands — підставляємо категорії
        $this->view('catalog/categories', [
            'brands' => $topLevel,
        ]);
    }

    public function category(string $slug): void
    {
        $category = Category::findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $filters     = $this->sanitize($_GET);
        $breadcrumbs = $this->buildBreadcrumbs($category);

        // Root‑категорія: показуємо її підкатегорії з можливістю фільтрації/сортування
        if (Category::isRoot($category)) {
            $children = Category::getChildren((int)$category['id']);

            // Підрахунок кількості товарів у кожній дочірній категорії (з урахуванням нащадків)
            foreach ($children as &$child) {
                $childIds  = Category::getDescendantIds((int)($child['id'] ?? 0));
                $childPage = Product::findByCategories($childIds, 1, 1);
                $child['products_count'] = (int)($childPage['total'] ?? 0);
            }
            unset($child);

            $this->view('catalog/category-root', [
                'category'    => $category,
                'children'    => $children,
                'breadcrumbs' => $breadcrumbs,
            ]);

            return;
        }

        // Leaf‑категорія: товари в ній з фільтрами
        $page = max((int)($filters['page'] ?? 1), 1);

        $categoryIds = Category::getDescendantIds((int)$category['id']);
        $query = [
            'category_ids' => $categoryIds,
            'keyword'      => $filters['q'] ?? null,
            'in_stock'     => !empty($filters['in_stock']) ? 1 : null,
            'price_min'    => $filters['price_min'] ?? null,
            'price_max'    => $filters['price_max'] ?? null,
            'sort'         => $filters['sort'] ?? null,
        ];
        $query = array_filter($query, static fn($v) => $v !== null && $v !== '');

        $productsPage = Product::paginate($page, 12, $query);
        $parent       = Category::getParent((int)$category['id']);

        $this->view('catalog/category-leaf', [
            'category'     => $category,
            'parent'       => $parent,
            'breadcrumbs'  => $breadcrumbs,
            'productsPage' => $productsPage,
            'filters'      => $filters,
        ]);
    }

    public function product(string $slug): void
    {
        $product = Product::findBySlugWithRelations($slug);
        if (!$product) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $this->view('catalog/product', compact('product'));
    }

    private function buildBreadcrumbs(array $category): array
    {
        $trail   = [];
        $current = $category;

        while ($current) {
            array_unshift($trail, $current);

            if (empty($current['parent_id'])) {
                break;
            }

            $current = Category::getParent((int)$current['id']);
        }

        return $trail;
    }
}

