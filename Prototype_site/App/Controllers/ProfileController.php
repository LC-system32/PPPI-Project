<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $sessionUser = $this->requireUser();
        $user = User::findById((int) $sessionUser['id']);

        if (!$user) {
            $_SESSION = [];
            session_destroy();
            $this->flash('message', 'Сесія завершена. Увійдіть повторно.');
            $this->redirect('/auth');
        }

        $message = $this->pullFlash('message');
        $errors = $this->pullFlash('errors') ?? [];
        $errorContext = $this->pullFlash('errorContext') ?? null;
        $activeSection = $this->pullFlash('activeSection') ?? 'overview';
        $formData = $this->pullFlash('formData') ?? [
            'login' => $user->login,
            'email' => $user->email,
        ];

        $recentOrders = Order::byUser((int) $user->id, 5);

        $this->view('profile', compact(
            'user',
            'message',
            'errors',
            'errorContext',
            'activeSection',
            'formData',
            'recentOrders'
        ));
    }

    public function updateDetails(): void
    {
        $sessionUser = $this->requireUser();
        $payload = $this->sanitize($_POST);

        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'login' => ['required', 'min:3', 'max:32'],
            'email' => ['required', 'email'],
        ]);

        $login = (string) ($payload['login'] ?? '');
        $email = (string) ($payload['email'] ?? '');

        if (!$errors) {
            $existingEmail = User::findByEmail($email);
            if ($existingEmail && $existingEmail->id !== (int) $sessionUser['id']) {
                $errors[] = 'Email вже використовується іншим користувачем.';
            }

            $existingLogin = User::findByLogin($login);
            if ($existingLogin && $existingLogin->id !== (int) $sessionUser['id']) {
                $errors[] = 'Логін вже зайнято.';
            }
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('errorContext', 'details');
            $this->flash('formData', ['login' => $login, 'email' => $email]);
            $this->flash('activeSection', 'details');
            $this->redirect('/profile');
        }

        User::updateProfile((int) $sessionUser['id'], $login, $email);

        $_SESSION['user']['login'] = $login;
        $_SESSION['user']['email'] = $email;

        $this->flash('message', 'Профіль оновлено.');
        $this->flash('activeSection', 'details');
        $this->redirect('/profile');
    }

    public function updatePassword(): void
    {
        $sessionUser = $this->requireUser();
        $payload = $this->sanitize($_POST);

        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8'],
            'confirm_password' => ['required', 'same:new_password'],
        ]);

        $user = User::findById((int) $sessionUser['id']);
        if (!$user || !$user->verifyPassword((string) ($payload['current_password'] ?? ''))) {
            $errors[] = 'Поточний пароль вказано невірно.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('errorContext', 'security');
            $this->flash('activeSection', 'security');
            $this->redirect('/profile');
        }

        $hash = User::hashPassword((string) $payload['new_password']);
        User::updatePassword((int) $sessionUser['id'], $hash);

        $this->flash('message', 'Пароль змінено.');
        $this->flash('activeSection', 'security');
        $this->redirect('/profile');
    }
}
