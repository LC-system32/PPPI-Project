<?php

namespace App\Controllers\Admin;

use App\Models\Order;

class OrderController extends AdminController
{
    public function index(): void
    {
        $filters = $this->sanitize($_GET);
        $status = $filters['status'] ?? null;
        $orders = Order::all($status);
        $message = $this->pullFlash('message');

        $this->view('admin/orders/index', compact('orders', 'message', 'status'));
    }

    public function show(int $id): void
    {
        $order = Order::findWithItems($id);
        if (!$order) {
            $this->redirect('/admin/orders');
        }

        $this->view('admin/orders/show', compact('order'));
    }

    public function updateStatus(int $id): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'status' => ['required', 'in:new,processing,shipped,completed,cancelled'],
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->redirect("/admin/orders/{$id}");
        }

        Order::updateStatus($id, $payload['status']);
        $this->flash('message', 'Статус замовлення оновлено.');
        $this->redirect('/admin/orders');
    }
}
