<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use PDO;

class AuthController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->startSession();
        $this->ensureCsrfToken();
        $this->db = Database::get();
    }

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
            $user = User::findByEmail($this->db, $payload['email']);
            if ($user && $user->verifyPassword($payload['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'login' => $user->login,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                ];
                $this->redirect('/');
            }
            $errors[] = 'Неправильна пара email / пароль.';
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
            if (User::findByEmail($this->db, $payload['email'])) {
                $errors[] = 'Користувач із таким email вже існує.';
            }
            if (User::findByLogin($this->db, $payload['login'])) {
                $errors[] = 'Користувач із таким логіном вже існує.';
            }
        }

        if ($errors) {
            $this->flashErrorsAndOld($errors, $payload, 'register');
            $this->redirect('/auth');
        }

        $user = User::create($this->db, [
            'login' => $payload['login'],
            'email' => $payload['email'],
            'password_hash' => User::hashPassword($payload['password']),
        ]);

        if (!$user instanceof User) {
            $this->flash('errors', ['Не вдалося створити обліковий запис. Спробуйте ще раз.']);
            $this->flash('activeTab', 'register');
            $this->redirect('/auth');
        }

        session_regenerate_id(true);

        /** @var User $user */
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

    private function sanitize(array $input): array
    {
        return array_map(static fn($value) => is_string($value) ? trim($value) : $value, $input);
    }

    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function ensureCsrfToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && $value === '') {
                    $errors[] = "Поле {$field} є обов'язковим.";
                    break;
                }

                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Введено некоректний email.';
                    break;
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen((string) $value) < $min) {
                        $errors[] = "Поле {$field} повинно містити щонайменше {$min} символів.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (strlen((string) $value) > $max) {
                        $errors[] = "Поле {$field} повинно містити не більше {$max} символів.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'same:')) {
                    $other = substr($rule, 5);
                    if (($data[$other] ?? null) !== $value) {
                        $errors[] = 'Паролі не співпадають.';
                        break;
                    }
                }

                if ($rule === 'csrf' && !$this->validCsrf($value)) {
                    $errors[] = 'Сесію закінчено. Оновіть сторінку й спробуйте знову.';
                    break;
                }
            }
        }

        return $errors;
    }

    private function validCsrf(string $token): bool
    {
        return $token && hash_equals($_SESSION['csrf_token'] ?? '', $token);
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
