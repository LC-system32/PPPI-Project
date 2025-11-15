<?php

namespace App\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ProductController extends AdminController
{
    public function index(): void
    {
        $filters = $this->sanitize($_GET);
        $page = max((int) ($filters['page'] ?? 1), 1);
        $products = Product::paginate($page, 20);
        $message = $this->pullFlash('message');

        $this->view('admin/products/index', compact('products', 'message'));
    }

    public function create(): void
    {
        $categories = Category::all();
        $brands = Brand::all();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? [];

        $this->view('admin/products/form', [
            'categories' => $categories,
            'brands' => $brands,
            'errors' => $errors,
            'formData' => $formData,
            'title' => 'Створення товару',
            'action' => '/admin/products',
        ]);
    }

    public function store(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validateProduct($payload);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect('/admin/products/create');
        }

        Product::create($this->mapProductPayload($payload));
        $this->flash('message', 'Товар створено.');
        $this->redirect('/admin/products');
    }

    public function edit(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            $this->redirect('/admin/products');
        }

        $categories = Category::all();
        $brands = Brand::all();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? $product;

        $this->view('admin/products/form', [
            'categories' => $categories,
            'brands' => $brands,
            'errors' => $errors,
            'formData' => $formData,
            'title' => 'Редагування товару',
            'action' => "/admin/products/{$id}",
        ]);
    }

    public function update(int $id): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validateProduct($payload);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect("/admin/products/{$id}/edit");
        }

        Product::update($id, $this->mapProductPayload($payload));
        $this->flash('message', 'Товар оновлено.');
        $this->redirect('/admin/products');
    }

    public function destroy(int $id): void
    {
        $user = $this->requireUser();
        if ((int) ($user['role_id'] ?? 0) !== 1) {
            http_response_code(403);
            $this->view('errors/403');
            return;
        }

        Product::delete($id);
        $this->flash('message', 'Товар видалено.');
        $this->redirect('/admin/products');
    }

    private function validateProduct(array $payload): array
    {
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'name' => ['required', 'min:3'],
            'slug' => ['required', 'min:3'],
            'sku' => ['required'],
            'category_id' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
        ]);

        if (!empty($payload['brand_id']) && filter_var($payload['brand_id'], FILTER_VALIDATE_INT) === false) {
            $errors[] = 'Бренд вказано некоректно.';
        }

        return $errors;
    }

    private function mapProductPayload(array $payload): array
    {
        return [
            'name' => $payload['name'],
            'slug' => $payload['slug'],
            'sku' => $payload['sku'],
            'category_id' => (int) $payload['category_id'],
            'brand_id' => !empty($payload['brand_id']) ? (int) $payload['brand_id'] : null,
            'price' => (float) $payload['price'],
            'stock' => (int) $payload['stock'],
            'description' => $payload['description'] ?? null,
            'compatibility' => $payload['compatibility'] ?? null,
            'is_active' => isset($payload['is_active']),
        ];
    }
}
