<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function delivery(): void
    {
        // Дозволяємо як авторизованим, так і гостям
        $user = $this->user();
        $items = Cart::items();

        if (!$items) {
            $this->flash('errors', ['Кошик порожній.']);
            $this->redirect('/cart');
        }

        $total = Cart::total();
        $errors = $this->pullFlash('errors') ?? [];
        $formData = $this->pullFlash('formData')
            ?? ($_SESSION['checkout']['delivery'] ?? [
                'guest_email' => '',
                'guest_phone' => '',
                'guest_name' => '',
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
        $user = $this->user();
        $payload = $this->sanitize($_POST);
        $items = Cart::items();

        // Валідація для гостя
        $guestRules = [
            'csrf_token' => ['required', 'csrf'],
            'delivery_address' => ['required', 'min:5'],
            'delivery_method' => ['required'],
        ];

        // Якщо гість - потрібні додаткові поля
        if (!$user) {
            $guestRules['guest_name'] = ['required', 'min:3'];
            $guestRules['guest_email'] = ['required', 'email'];
            $guestRules['guest_phone'] = ['required', 'min:10'];
        }

        $errors = $this->validate($payload, $guestRules);

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

        // Збережемо дані гостя, якщо це не авторизований користувач
        if (!$user) {
            $_SESSION['checkout']['guest'] = [
                'name' => $payload['guest_name'],
                'email' => $payload['guest_email'],
                'phone' => $payload['guest_phone'],
            ];
        }

        $this->redirect('/checkout/payment');
    }

    public function payment(): void
    {
        $user = $this->user();
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
        $user = $this->user();
        $guest = $_SESSION['checkout']['guest'] ?? null;
        $payload = $this->sanitize($_POST);

        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'payment_method' => ['required'],
        ]);

        if (empty($_SESSION['checkout']['delivery'])) {
            $errors[] = 'Спочатку заповніть дані доставки.';
        }

        if (!$user && !$guest) {
            $errors[] = 'Спочатку заповніть контактні дані.';
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
        $user = $this->user();
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

        // If delivery address is missing but user has address in profile, use it for display
        if (empty($delivery['delivery_address']) && !empty($user['address'])) {
            $delivery['delivery_address'] = $user['address'];
        }

        $this->view('checkout/confirm', compact('user', 'items', 'total', 'errors', 'delivery', 'payment'));
    }

    public function place(): void
    {
        $user = $this->user();
        $guest = $_SESSION['checkout']['guest'] ?? null;
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
        if (!$user && !$guest) {
            $errors[] = 'Дані про замовника не заповнені.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->redirect('/checkout/confirm');
        }

        $payload = [
            'delivery_address' => $delivery['delivery_address'] ?: ($user['address'] ?? ''),
            'delivery_method' => $delivery['delivery_method'],
            'payment_method' => $payment['payment_method'],
            'notes' => $delivery['notes'] ?? '',
        ];

        if ($user) {
            $order = Order::createFromCart((int) $user['id'], $payload, $items);
        } else {
            // Guest checkout - store guest data with order
            $order = Order::createFromCartGuest($guest, $payload, $items);
        }

        Cart::clear();
        unset($_SESSION['checkout']);

        if ($order) {
            $this->flash('message', 'Замовлення №' . $order['id'] . ' створено.');
        }

        if ($user) {
            $this->redirect('/orders');
        } else {
            $this->redirect('/checkout/success?order_id=' . ($order['id'] ?? ''));
        }
    }

    public function success(): void
    {
        $orderId = $_GET['order_id'] ?? null;
        $order = $orderId ? Order::findWithItems((int) $orderId) : null;

        if (!$order || ($order['user_id'] && !$this->user())) {
            $this->redirect('/');
        }

        $this->view('checkout/success', compact('order'));
    }

    public function userOrders(): void
    {
        $user = $this->requireUser();
        $orders = Order::byUser((int) $user['id'], 50);
        $message = $this->pullFlash('message');

        $this->view('orders/index', compact('orders', 'message'));
    }
}

