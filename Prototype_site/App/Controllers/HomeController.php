<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        // Марки авто для головної (тільки бренди, що мають моделі в car_models)
        $brands = Brand::allCarMakers();
        $brands = array_slice($brands, 0, 18);

        // Популярні верхньорівневі категорії
        $categories = Category::topLevel(16);

        // Рекомендовані товари
        $featured = Product::paginate(1, 24)['items'] ?? [];

        $this->view('home', compact('categories', 'featured', 'brands'));
    }
}

