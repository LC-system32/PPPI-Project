<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;

class OrderController extends Controller
{
    public function delivery(): void
    {
        $user = $this->requireUser();
        $items = Cart::items();

        if (!$items) {
            $this->flash('errors', ['Кошик порожній.']);
            $this->redirect('/cart');
        }

        $total = Cart::total();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData')
            ?? ($_SESSION['checkout']['delivery'] ?? [
                'delivery_address' => '',
                'delivery_method' => 'pickup',
                'notes' => '',
            ]);

        $this->view('checkout/delivery', compact('user', 'items', 'total', 'errors', 'formData'));
    }

    // Alias для зворотної сумісності, якщо хтось звернеться на /checkout
    public function checkout(): void
    {
        $this->delivery();
    }

    public function storeDelivery(): void
    {
        $user = $this->requireUser();
        $payload = $this->sanitize($_POST);
        $items = Cart::items();

        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'delivery_address' => ['required', 'min:5'],
            'delivery_method' => ['required'],
        ]);

        if (!$items) {
            $errors[] = 'Кошик порожній.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect('/checkout/delivery');
        }

        $_SESSION['checkout']['delivery'] = [
            'delivery_address' => $payload['delivery_address'],
            'delivery_method' => $payload['delivery_method'],
            'notes' => $payload['notes'] ?? '',
        ];

        $this->redirect('/checkout/payment');
    }

    public function payment(): void
    {
        $user = $this->requireUser();
        $items = Cart::items();

        if (!$items) {
            $this->flash('errors', ['Кошик порожній.']);
            $this->redirect('/cart');
        }

        if (empty($_SESSION['checkout']['delivery'])) {
            $this->redirect('/checkout/delivery');
        }

        $total = Cart::total();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData')
            ?? ($_SESSION['checkout']['payment'] ?? [
                'payment_method' => 'card',
            ]);

        $delivery = $_SESSION['checkout']['delivery'];

        $this->view('checkout/payment', compact('user', 'items', 'total', 'errors', 'formData', 'delivery'));
    }

    public function storePayment(): void
    {
        $user = $this->requireUser();
        $payload = $this->sanitize($_POST);

        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'payment_method' => ['required'],
        ]);

        if (empty($_SESSION['checkout']['delivery'])) {
            $errors[] = 'Спочатку заповніть дані доставки.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('formData', $payload);
            $this->redirect('/checkout/payment');
        }

        $_SESSION['checkout']['payment'] = [
            'payment_method' => $payload['payment_method'],
        ];

        $this->redirect('/checkout/confirm');
    }

    public function confirm(): void
    {
        $user = $this->requireUser();
        $items = Cart::items();

        if (!$items) {
            $this->flash('errors', ['Кошик порожній.']);
            $this->redirect('/cart');
        }

        $delivery = $_SESSION['checkout']['delivery'] ?? null;
        $payment = $_SESSION['checkout']['payment'] ?? null;

        if (!$delivery) {
            $this->redirect('/checkout/delivery');
        }

        if (!$payment) {
            $this->redirect('/checkout/payment');
        }

        $total = Cart::total();
        $errors = $this->pullFlash('errors') ?? [];

        $this->view('checkout/confirm', compact('user', 'items', 'total', 'errors', 'delivery', 'payment'));
    }

    public function place(): void
    {
        $user = $this->requireUser();
        $items = Cart::items();

        $delivery = $_SESSION['checkout']['delivery'] ?? null;
        $payment = $_SESSION['checkout']['payment'] ?? null;

        $errors = [];

        if (!$items) {
            $errors[] = 'Кошик порожній.';
        }
        if (!$delivery) {
            $errors[] = 'Немає даних доставки.';
        }
        if (!$payment) {
            $errors[] = 'Немає обраного способу оплати.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->redirect('/checkout/confirm');
        }

        $payload = [
            'delivery_address' => $delivery['delivery_address'],
            'delivery_method' => $delivery['delivery_method'],
            'payment_method' => $payment['payment_method'],
            'notes' => $delivery['notes'] ?? '',
        ];

        $order = Order::createFromCart((int) $user['id'], $payload, $items);
        Cart::clear();
        unset($_SESSION['checkout']);

        if ($order) {
            $this->flash('message', 'Замовлення №' . $order['id'] . ' створено.');
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

