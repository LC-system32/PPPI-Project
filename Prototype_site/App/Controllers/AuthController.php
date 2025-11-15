<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function auth(): void
    {
        $message = $this->pullFlash('message');
        $errors = $this->pullFlash('errors') ?? [];
        $old = $this->pullFlash('old') ?? [];
        $activeTab = $this->pullFlash('activeTab') ?? 'login';

        $this->view('auth', compact('message', 'errors', 'old', 'activeTab'));
    }

    public function login(): void
    {
        $payload = $this->sanitize($_POST);
        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (!$errors) {
            $user = User::findByEmail((string) $payload['email']);

            if ($user && $user->verifyPassword((string) $payload['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'login' => $user->login,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                ];

                $this->redirect('/');
            }

            $errors[] = 'Невірний email або пароль.';
        }

        $this->flashErrorsAndOld($errors, $payload, 'login');
        $this->redirect('/auth');
    }

    public function register(): void
    {
        $payload = $this->sanitize($_POST);

        $errors = $this->validate($payload, [
            'csrf_token' => ['required', 'csrf'],
            'login' => ['required', 'min:3', 'max:32'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'confirm_password' => ['required', 'same:password'],
        ]);

        if (!$errors) {
            if (User::findByEmail((string) $payload['email'])) {
                $errors[] = 'Користувач з таким email вже існує.';
            }
            if (User::findByLogin((string) $payload['login'])) {
                $errors[] = 'Користувач з таким логіном вже існує.';
            }
        }

        if ($errors) {
            $this->flashErrorsAndOld($errors, $payload, 'register');
            $this->redirect('/auth');
        }

        $user = User::create([
            'login' => $payload['login'],
            'email' => $payload['email'],
            'password_hash' => User::hashPassword((string) $payload['password']),
        ]);

        if (!$user instanceof User) {
            $this->flash('errors', ['Сталася помилка під час реєстрації. Спробуйте пізніше.']);
            $this->flash('activeTab', 'register');
            $this->redirect('/auth');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user->id,
            'login' => $user->login,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ];

        $this->redirect('/');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/auth');
    }

    private function flashErrorsAndOld(array $errors, array $payload, string $tab): void
    {
        $this->flash('errors', $errors);
        $this->flash('old', [
            'login' => $payload['login'] ?? '',
            'email' => $payload['email'] ?? '',
        ]);
        $this->flash('activeTab', $tab);
    }
}
