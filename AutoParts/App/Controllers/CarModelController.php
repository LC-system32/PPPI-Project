<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Product;

class CarModelController extends Controller
{
    public function showBySlug(string $brandSlug, string $modelSlug): void
    {
        $brand = Brand::findBySlug($brandSlug);
        if (!$brand) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $models = CarModel::forBrand($brand['name']);
        $carModel = null;

        foreach ($models as $candidate) {
            if (CarModel::slugFor($candidate) === $modelSlug) {
                $carModel = $candidate;
                break;
            }
        }

        if (!$carModel) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        // Обробка параметрів фільтрів та сортування
        $filters = [];
        
        // Пошук по назві або артикулу
        if (!empty($_GET['q'])) {
            $filters['q'] = $_GET['q'];
        }
        
        // Фільтр по ціні
        if (!empty($_GET['price_min']) || (isset($_GET['price_min']) && $_GET['price_min'] === '0')) {
            $filters['price_min'] = $_GET['price_min'];
        }
        if (!empty($_GET['price_max']) || (isset($_GET['price_max']) && $_GET['price_max'] === '0')) {
            $filters['price_max'] = $_GET['price_max'];
        }
        
        // Фільтр по наявності
        if (!empty($_GET['in_stock'])) {
            $filters['in_stock'] = $_GET['in_stock'];
        }
        
        // Сортування
        if (!empty($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        }
        
        // Очищення фільтрів
        if (!empty($_GET['clear'])) {
            $filters = [];
        }

        $page = max((int) ($_GET['page'] ?? 1), 1);
        $products = Product::paginateByCarModel((int) $carModel['id'], $page, 12, $filters);

        $this->view('car_model/show', [
            'carModel' => $carModel,
            'products' => $products,
            'filters' => $filters,
            'brand' => $brand,
            'brandSlug' => $brandSlug,
            'modelSlug' => $modelSlug,
        ]);
    }
}
