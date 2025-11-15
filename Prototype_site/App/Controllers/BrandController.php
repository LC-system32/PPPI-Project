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
        $brands = Brand::all(); // або Brand::paginate(...)

        $page = 'Бренди'; // або 'Виробники запчастин'

        $this->view('brand/index', [
            'brands' => $brands,
            'page'  => $page,
        ]);
    }

    public function show(string $slug): void
    {
        $brand = Brand::findBySlug($slug);
        if (!$brand) {
            // якщо хочеш 404 – роби як у тебе прийнято
            http_response_code(404);
            echo 'Бренд не знайдено';
            return;
        }

        $page = max((int)($_GET['page'] ?? 1), 1);

        // товари бренду
        $products = Product::paginate($page, 12, [
            'brand_id' => $brand['id'],
        ]);

        // ✳️ моделі авто цього бренду
        $carModels = CarModel::forBrand($brand['name']);

        $this->view('brand/show', [
            'brand'      => $brand,
            'products'   => $products,
            'carModels'  => $carModels,   // ← передаємо у view
        ]);
    }
}
