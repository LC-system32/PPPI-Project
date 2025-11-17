<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\CarModel;

class BrandController extends Controller
{
    public function index(): void
    {
        $filters = $this->sanitize($_GET);

        $currentQuery  = trim((string) ($filters['q'] ?? ''));
        $currentLetter = (string) ($filters['letter'] ?? '');
        $currentSort   = (string) ($filters['sort'] ?? '');

        // ⬇️ ТУТ ГОЛОВНЕ ВИПРАВЛЕННЯ ⬇️
        $brands = Brand::allCarBrands();  // показує МАРКИ АВТО

        // Пошук
        if ($currentQuery !== '') {
            $q = mb_strtolower($currentQuery);
            $brands = array_values(array_filter($brands, static function ($brand) use ($q) {
                return mb_strpos(mb_strtolower($brand['name']), $q) !== false
                    || mb_strpos(mb_strtolower($brand['slug']), $q) !== false;
            }));
        }

        // Фільтр по першій букві
        if ($currentLetter !== '') {
            $letter = mb_strtoupper($currentLetter);
            $brands = array_values(array_filter($brands, static function ($brand) use ($letter) {
                return mb_strtoupper(mb_substr($brand['name'], 0, 1)) === $letter;
            }));
        }

        // Сортування
        if ($currentSort === 'name_asc') {
            usort($brands, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        } elseif ($currentSort === 'name_desc') {
            usort($brands, fn($a, $b) => strcasecmp($b['name'], $a['name']));
        } elseif ($currentSort === 'popular_desc') {
            usort($brands, fn($a, $b) => $b['products_count'] <=> $a['products_count']);
        }

        $this->view('brand/index', [
            'brands' => $brands,
            'page'   => 'brands',
        ]);
    }
    public function show(string $slug): void
    {
        $brand = Brand::findBySlug($slug);
        if (!$brand) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $filters = $this->sanitize($_GET);
        $page = max((int) ($filters['page'] ?? 1), 1);

        // Моделі авто цього бренду (якщо є) – означає, що це "марка авто"
        $carModels = CarModel::forBrand($brand['name']);
        $hasCarModels = !empty($carModels);

        $query = [
            'keyword'       => $filters['q'] ?? null,
            'category_name' => $filters['category'] ?? null,
            'in_stock'      => !empty($filters['in_stock']) ? 1 : null,
            'price_min'     => $filters['price_min'] ?? null,
            'price_max'     => $filters['price_max'] ?? null,
            'sort'          => $filters['sort'] ?? null,
        ];

        // Якщо бренд – марка авто: показуємо всі запчастини, сумісні з цими авто,
        // незалежно від бренда виробника запчастини.
        if ($hasCarModels) {
            $query['car_brand'] = $brand['name'];
        } else {
            // Якщо це виробник запчастин – фільтр по brand_id
            $query['brand_id'] = $brand['id'];
        }

        $products = Product::paginate($page, 12, $query);

        $this->view('brand/show', [
            'brand'      => $brand,
            'products'   => $products,
            'carModels'  => $carModels,
            'hasCarModels' => $hasCarModels,
        ]);
    }
}
