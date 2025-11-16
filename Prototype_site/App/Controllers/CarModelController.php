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

        $page = max((int) ($_GET['page'] ?? 1), 1);
        $products = Product::paginateByCarModel((int) $carModel['id'], $page, 12);

        $this->view('car_model/show', [
            'carModel' => $carModel,
            'products' => $products,
            'brandSlug' => $brandSlug,
            'modelSlug' => $modelSlug,
        ]);
    }
}
