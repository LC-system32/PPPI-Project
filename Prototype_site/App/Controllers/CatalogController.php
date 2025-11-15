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
        $page = max((int) ($filters['page'] ?? 1), 1);
        $query = array_filter([
            'category_id' => $filters['category_id'] ?? null,
            'brand_id' => $filters['brand_id'] ?? null,
            'keyword' => $filters['q'] ?? null,
        ], static fn($value) => $value !== null && $value !== '');

        $products = Product::paginate($page, 12, $query);
        $categories = Category::tree();
        $brands = Brand::all();

        $this->view('catalog/index', compact('products', 'categories', 'brands', 'filters'));
    }

    public function categories(): void
    {
        $tree = Category::tree();

        $this->view('catalog/categories', compact('tree'));
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
