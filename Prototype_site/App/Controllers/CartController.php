<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;

class CartController extends Controller
{
    public function index(): void
    {
        $items = Cart::items();
        $total = Cart::total();
        $message = $this->pullFlash('message');
        $errors = $this->pullFlash('errors') ?? [];

        $this->view('cart/index', compact('items', 'total', 'message', 'errors'));
    }

    public function add(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->redirectBack('/catalog');
        }

        if (!Cart::addProduct((int) $payload['product_id'], (int) $payload['quantity'])) {
            $this->flash('errors', ['Не вдалося додати товар. Перевірте наявність.']);
            $this->redirectBack('/catalog');
        }

        $this->flash('message', 'Товар додано до кошика.');
        $this->redirect('/cart');
    }

    public function update(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer'],
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->redirect('/cart');
        }

        Cart::updateItem((int) $payload['product_id'], (int) $payload['quantity']);
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'product_id' => ['required', 'integer'],
        ]);

        if ($errors) {
            $this->flash('errors', $errors);
            $this->redirect('/cart');
        }

        Cart::removeItem((int) $payload['product_id']);
        $this->redirect('/cart');
    }
}
