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
        $page    = max((int) ($filters['page'] ?? 1), 1);

        // Підготовка фільтра категорії: беремо вибрану категорію і всі її нащадки
        $categoryIds = null;
        $categoryId  = null;
        if (!empty($filters['category_id'])) {
            $categoryId  = (int) $filters['category_id'];
            $categoryIds = Category::getDescendantIds($categoryId);
        }

        // Фільтри каталогу: текстовий пошук, категорія (з нащадками), бренд,
        // лише в наявності, діапазон цін та сортування.
        $query = [
            'category_ids' => $categoryIds,
            'brand_id'    => !empty($filters['brand_id']) ? (int) $filters['brand_id'] : null,
            'keyword'     => $filters['q'] ?? null,
            'in_stock'    => !empty($filters['in_stock']) ? 1 : null,
            'price_min'   => $filters['price_min'] ?? null,
            'price_max'   => $filters['price_max'] ?? null,
            'sort'        => $filters['sort'] ?? null,
        ];

        $query = array_filter($query, static fn ($value) => $value !== null && $value !== '');

        $products   = Product::paginate($page, 12, $query);
        $categories = Category::allWithProductCounts();
        // У фільтрі каталогу потрібні саме бренди запчастин, а не марки авто
        $brands     = Brand::allForProducts();

        // зберігаємо нормалізоване значення category_id для форми
        if ($categoryId !== null) {
            $filters['category_id'] = (string) $categoryId;
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
        // Використовуємо плаский список категорій з підрахованою кількістю товарів,
        // оскільки в'юшка categories.php реалізує власну фільтрацію та сортування.
        $categories = Category::allWithProductCounts();

        // В'юшка очікує масив $brands (історична назва), тому передаємо як brands.
        $this->view('catalog/categories', [
            'brands' => $categories,
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

        $breadcrumbs = $this->buildBreadcrumbs($category);

        if (Category::isRoot($category)) {
            $children = Category::getChildren((int) $category['id']);

            $this->view('catalog/category-root', [
                'category' => $category,
                'children' => $children,
                'breadcrumbs' => $breadcrumbs,
            ]);

            return;
        }

        $page = max((int) ($_GET['page'] ?? 1), 1);

        // 1) всі id поточної категорії + підкатегорій
        $categoryIds = Category::getDescendantIds((int) $category['id']);

        // 2) беремо товари по всьому дереву
        $productsPage = Product::findByCategories($categoryIds, $page, 12);

        $parent = Category::getParent((int) $category['id']);

        $this->view('catalog/category-leaf', [
            'category'     => $category,
            'parent'       => $parent,
            'breadcrumbs'  => $breadcrumbs,
            'productsPage' => $productsPage,
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
        $trail = [];
        $current = $category;

        while ($current) {
            array_unshift($trail, $current);

            if (empty($current['parent_id'])) {
                break;
            }

            $current = Category::getParent((int) $current['id']);
        }

        return $trail;
    }
}
