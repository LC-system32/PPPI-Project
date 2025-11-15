<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;

class OrderController extends Controller
{
    public function checkout(): void
    {
        $user = $this->requireUser();
        $items = Cart::items();

        if (!$items) {
            $this->flash('errors', ['Спочатку додайте товари у кошик.']);
            $this->redirect('/cart');
        }

        $total = Cart::total();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData') ?? [
            'delivery_address' => '',
            'delivery_method' => 'pickup',
            'payment_method' => 'card',
            'notes' => '',
        ];

        $this->view('checkout/index', compact('user', 'items', 'total', 'errors', 'formData'));
    }

    public function place(): void
    {
        $user = $this->requireUser();
        $payload = $this->sanitize($_POST);
        $items = Cart::items();

        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'delivery_address' => ['required', 'min:5'],
            'delivery_method' => ['required'],
            'payment_method' => ['required'],
        ]);

        if (!$items) {
            $errors[] = 'Кошик порожній.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect('/checkout');
        }

        $order = Order::createFromCart((int) $user['id'], $payload, $items);
        Cart::clear();

        if ($order) {
            $this->flash('message', 'Замовлення №' . $order['id'] . ' успішно створено.');
        }

        $this->redirect('/orders');
    }

    public function userOrders(): void
    {
        $user = $this->requireUser();
        $orders = Order::byUser((int) $user['id'], 50);
        $message = $this->pullFlash('message');

        $this->view('orders/index', compact('orders', 'message'));
    }
}
