<?php

namespace App\Controllers\Admin;

use App\Models\Category;

class CategoryController extends AdminController
{
    public function index(): void
    {
        $categories = Category::tree();
        $message = $this->pullFlash('message');

        $this->view('admin/categories/index', compact('categories', 'message'));
    }

    public function create(): void
    {
        $allCategories = Category::all();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? [];

        $this->view('admin/categories/form', [
            'categories' => $allCategories,
            'errors' => $errors,
            'formData' => $formData,
            'title' => 'Створення категорії',
            'action' => '/admin/categories',
        ]);
    }

    public function store(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validateCategory($payload);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect('/admin/categories/create');
        }

        Category::create($payload);
        $this->flash('message', 'Категорію створено.');
        $this->redirect('/admin/categories');
    }

    public function edit(int $id): void
    {
        $category = Category::find($id);
        if (!$category) {
            $this->redirect('/admin/categories');
        }

        $allCategories = Category::all();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? $category;

        $this->view('admin/categories/form', [
            'categories' => $allCategories,
            'errors' => $errors,
            'formData' => $formData,
            'title' => 'Редагування категорії',
            'action' => "/admin/categories/{$id}",
        ]);
    }

    public function update(int $id): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validateCategory($payload);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect("/admin/categories/{$id}/edit");
        }

        Category::update($id, $payload);
        $this->flash('message', 'Категорію оновлено.');
        $this->redirect('/admin/categories');
    }

    public function destroy(int $id): void
    {
        Category::delete($id);
        $this->flash('message', 'Категорію видалено.');
        $this->redirect('/admin/categories');
    }

    private function validateCategory(array $payload): array
    {
        return $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'name' => ['required', 'min:3'],
            'slug' => ['required', 'min:3'],
            'parent_id' => ['integer'],
        ]);
    }
}
