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
        $currentType   = (string) ($filters['type'] ?? '');
        $currentSort   = (string) ($filters['sort'] ?? '');

        $brands = Brand::all();

        if ($currentQuery !== '') {
            $q = mb_strtolower($currentQuery);
            $brands = array_values(array_filter($brands, static function (array $brand) use ($q): bool {
                $name = mb_strtolower((string) ($brand['name'] ?? ''));
                $slug = mb_strtolower((string) ($brand['slug'] ?? ''));

                return mb_strpos($name, $q) !== false || mb_strpos($slug, $q) !== false;
            }));
        }

        if ($currentLetter !== '') {
            $letter = mb_strtoupper($currentLetter);
            $brands = array_values(array_filter($brands, static function (array $brand) use ($letter): bool {
                $name = trim((string) ($brand['name'] ?? ''));
                if ($name === '') {
                    return false;
                }
                $first = mb_strtoupper(mb_substr($name, 0, 1));

                return $first === $letter;
            }));
        }

        if ($currentSort === 'name_asc') {
            usort($brands, static function (array $a, array $b): int {
                return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
            });
        } elseif ($currentSort === 'name_desc') {
            usort($brands, static function (array $a, array $b): int {
                return strcasecmp((string) ($b['name'] ?? ''), (string) ($a['name'] ?? ''));
            });
        } elseif ($currentSort === 'popular_desc') {
            usort($brands, static function (array $a, array $b): int {
                $countA = (int) ($a['products_count'] ?? 0);
                $countB = (int) ($b['products_count'] ?? 0);

                if ($countA === $countB) {
                    return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
                }

                return $countB <=> $countA;
            });
        }

        $page = 'brands';

        $this->view('brand/index', [
            'brands' => $brands,
            'page'   => $page,
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

        $query = [
            'brand_id' => $brand['id'],
            'keyword' => $filters['q'] ?? null,
            'category_name' => $filters['category'] ?? null,
            'in_stock' => !empty($filters['in_stock']) ? 1 : null,
            'price_min' => $filters['price_min'] ?? null,
            'price_max' => $filters['price_max'] ?? null,
            'sort' => $filters['sort'] ?? null,
        ];

        $products = Product::paginate($page, 12, $query);

        $carModels = CarModel::forBrand($brand['name']);

        $this->view('brand/show', [
            'brand'     => $brand,
            'products'  => $products,
            'carModels' => $carModels,
        ]);
    }
}
