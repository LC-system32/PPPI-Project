<?php

namespace App\Controllers\Admin;

use App\Models\Brand;

class BrandController extends AdminController
{
    public function index(): void
    {
        $brands = Brand::all();
        $message = $this->pullFlash('message');

        $this->view('admin/brands/index', compact('brands', 'message'));
    }

    public function create(): void
    {
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? [];

        $this->view('admin/brands/form', compact('errors', 'formData'));
    }

    public function store(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'name' => ['required', 'min:2'],
            'slug' => ['required', 'min:2']
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect('/admin/brands/create');
        }

        Brand::create(['name' => $payload['name'], 'slug' => $payload['slug']]);
        $this->flash('message', 'Бренд створено.');
        $this->redirect('/admin/brands');
    }

    public function edit(int $id): void
    {
        $brand = Brand::find($id);
        if (!$brand) {
            $this->redirect('/admin/brands');
        }

        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? $brand;

        $this->view('admin/brands/form', compact('errors', 'formData'));
    }

    public function update(int $id): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'name' => ['required', 'min:2'],
            'slug' => ['required', 'min:2']
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect("/admin/brands/{$id}/edit");
        }

        Brand::update($id, ['name' => $payload['name'], 'slug' => $payload['slug']]);
        $this->flash('message', 'Бренд оновлено.');
        $this->redirect('/admin/brands');
    }

    public function destroy(int $id): void
    {
        Brand::delete($id);
        $this->flash('message', 'Бренд видалено.');
        $this->redirect('/admin/brands');
    }
}
