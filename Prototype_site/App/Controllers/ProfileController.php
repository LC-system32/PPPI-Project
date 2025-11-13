<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use PDO;

class ProfileController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->db = Database::get();
    }

    public function show(): void
    {
        $sessionUser = $this->requireUser();

        $user = User::findById($this->db, (int) $sessionUser['id']);
        if (!$user) {
            $_SESSION = [];
            session_destroy();
            $this->flash('message', 'Сесію завершено. Увійдіть повторно.');
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

        $recentOrders = $this->fakeOrders();
        $savedVehicles = $this->fakeVehicles();

        $this->view('profile', compact(
            'user',
            'message',
            'errors',
            'errorContext',
            'activeSection',
            'formData',
            'recentOrders',
            'savedVehicles'
        ));
    }

    public function updateDetails(): void
    {
        $sessionUser = $this->requireUser();
        $payload = $this->sanitize($_POST);

        if (!$this->validCsrf($payload['csrf_token'] ?? '')) {
            $this->flash('errors', ['Сесію завершено. Оновіть сторінку та спробуйте знову.']);
            $this->flash('errorContext', 'details');
            $this->redirect('/profile');
        }

        $errors = [];

        $login = $payload['login'] ?? '';
        $email = $payload['email'] ?? '';

        if ($login === '' || strlen($login) < 3) {
            $errors[] = 'Логін повинен містити щонайменше 3 символи.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Введено некоректний email.';
        }

        $existingEmail = User::findByEmail($this->db, $email);
        if ($existingEmail && $existingEmail->id !== (int) $sessionUser['id']) {
            $errors[] = 'Користувач із таким email вже існує.';
        }

        $existingLogin = User::findByLogin($this->db, $login);
        if ($existingLogin && $existingLogin->id !== (int) $sessionUser['id']) {
            $errors[] = 'Користувач із таким логіном вже існує.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('errorContext', 'details');
            $this->flash('formData', ['login' => $login, 'email' => $email]);
            $this->flash('activeSection', 'details');
            $this->redirect('/profile');
        }

        User::updateProfile($this->db, (int) $sessionUser['id'], $login, $email);

        $_SESSION['user']['login'] = $login;
        $_SESSION['user']['email'] = $email;

        $this->flash('message', 'Дані акаунту успішно оновлено.');
        $this->flash('activeSection', 'details');
        $this->redirect('/profile');
    }

    public function updatePassword(): void
    {
        $sessionUser = $this->requireUser();
        $payload = $this->sanitize($_POST);

        if (!$this->validCsrf($payload['csrf_token'] ?? '')) {
            $this->flash('errors', ['Сесію завершено. Оновіть сторінку та спробуйте знову.']);
            $this->flash('errorContext', 'security');
            $this->redirect('/profile');
        }

        $errors = [];

        $current = $payload['current_password'] ?? '';
        $new = $payload['new_password'] ?? '';
        $confirm = $payload['confirm_password'] ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            $errors[] = 'Усі поля для зміни пароля обовʼязкові.';
        }

        if (strlen($new) < 8) {
            $errors[] = 'Новий пароль повинен містити щонайменше 8 символів.';
        }

        if ($new !== $confirm) {
            $errors[] = 'Новий пароль і підтвердження не співпадають.';
        }

        $user = User::findById($this->db, (int) $sessionUser['id']);
        if (!$user || !password_verify($current, $user->password_hash)) {
            $errors[] = 'Поточний пароль введено невірно.';
        }

        if ($errors) {
            $this->flash('errors', $errors);
            $this->flash('errorContext', 'security');
            $this->flash('activeSection', 'security');
            $this->redirect('/profile');
        }

        $hash = User::hashPassword($new);
        User::updatePassword($this->db, (int) $sessionUser['id'], $hash);

        $this->flash('message', 'Пароль успішно змінено.');
        $this->flash('activeSection', 'security');
        $this->redirect('/profile');
    }

    private function requireUser(): array
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->flash('message', 'Спочатку увійдіть у систему.');
            $this->flash('activeTab', 'login');
            $this->redirect('/auth');
        }

        return $user;
    }

    private function sanitize(array $input): array
    {
        return array_map(static fn ($value) => is_string($value) ? trim($value) : $value, $input);
    }

    private function validCsrf(string $token): bool
    {
        return $token && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    private function fakeOrders(): array
    {
        return [
            [
                'number' => '#AP-2025-1185',
                'date' => '12.11.2025',
                'items' => 'Гальмівні колодки Brembo',
                'status' => 'В дорозі',
                'total' => '3 240 ₴',
            ],
            [
                'number' => '#AP-2025-1102',
                'date' => '02.11.2025',
                'items' => 'Масляний фільтр Mann',
                'status' => 'Доставлено',
                'total' => '890 ₴',
            ],
        ];
    }

    private function fakeVehicles(): array
    {
        return [
            [
                'title' => 'BMW 530d xDrive',
                'vin' => 'WBAJA91040XXXXX',
                'year' => 2022,
                'mileage' => '68 000 км',
            ],
            [
                'title' => 'Audi Q5 45 TFSI',
                'vin' => 'WAUZZZFY0PXXXXXX',
                'year' => 2021,
                'mileage' => '52 500 км',
            ],
        ];
    }
}
