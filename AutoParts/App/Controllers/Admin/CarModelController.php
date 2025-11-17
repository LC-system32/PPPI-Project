<?php

namespace App\Controllers\Admin;

use App\Models\CarModel;

class CarModelController extends AdminController
{
    public function index(): void
    {
        // For simplicity, show all car models (could be paginated)
        $stmt = $this->db()->prepare('SELECT * FROM car_models ORDER BY brand, model');
        $stmt->execute();
        $models = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $message = $this->pullFlash('message');

        $this->view('admin/car_models/index', compact('models', 'message'));
    }

    public function create(): void
    {
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? [];

        $this->view('admin/car_models/form', compact('errors', 'formData'));
    }

    public function store(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'brand' => ['required'],
            'model' => ['required']
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect('/admin/car-models/create');
        }

        $stmt = $this->db()->prepare('INSERT INTO car_models (brand, model, generation, year_from, year_to) VALUES (:brand, :model, :generation, :year_from, :year_to)');
        $stmt->execute([
            'brand' => $payload['brand'] ?? null,
            'model' => $payload['model'] ?? null,
            'generation' => $payload['generation'] ?? null,
            'year_from' => $payload['year_from'] ?? null,
            'year_to' => $payload['year_to'] ?? null,
        ]);
        $this->flash('message', 'Модель авто додано.');
        $this->redirect('/admin/car-models');
    }

    public function destroy(int $id): void
    {
        $stmt = $this->db()->prepare('DELETE FROM car_models WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $this->flash('message', 'Модель видалена.');
        $this->redirect('/admin/car-models');
    }
}
