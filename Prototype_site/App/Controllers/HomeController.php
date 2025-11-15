<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        // Категорії
        $categories = Category::topLevel(2);

        // Рекомендовані товари
        $featured = Product::paginate(1, 20)['items'] ?? [];

        // ⭐ Додаємо бренди — наприклад, максимум 6
        $brands = Brand::all();              // беремо всі
        $brands = array_slice($brands, 0, 20); // обрізаємо до 6

        // Передаємо у view
        $this->view('home', compact('categories', 'featured', 'brands'));
    }
}
