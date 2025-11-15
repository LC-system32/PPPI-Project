<?php

namespace App\Core;

use PDO;

class Controller
{
    protected ?PDO $connection = null;

    public function __construct()
    {
        $this->startSession();
        $this->ensureCsrfToken();
    }

    protected function view(string $name, array $data = []): void
    {
        extract($data);
        require BASE_PATH . '/public/view/' . $name . '.php';
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    protected function ensureCsrfToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function db(): PDO
    {
        if (!$this->connection instanceof PDO) {
            $this->connection = Database::get();
        }

        return $this->connection;
    }

    protected function flash(string $key, mixed $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    protected function pullFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);

        return $value;
    }

    protected function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function requireUser(): array
    {
        $user = $this->user();
        if ($user) {
            return $user;
        }

        $this->flash('message', 'Будь ласка, авторизуйтеся, щоб продовжити.');
        $this->redirect('/auth');
    }

    protected function requireRole(array $roles): array
    {
        $user = $this->requireUser();
        $roleId = (int) ($user['role_id'] ?? 0);

        if (!in_array($roleId, $roles, true)) {
            http_response_code(403);
            $this->view('errors/403');
            exit;
        }

        return $user;
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $constraints) {
            $value = $data[$field] ?? null;

            foreach ($constraints as $rule) {
                if ($rule === 'required' && ($value === null || $value === '' || (is_array($value) && $value === []))) {
                    $errors[] = "Поле {$field} є обов'язковим.";
                    break;
                }

                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Вкажіть коректний email.';
                    break;
                }

                if ($rule === 'integer' && $value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[] = "Поле {$field} має бути цілим числом.";
                    break;
                }

                if ($rule === 'numeric' && $value !== null && $value !== '' && !is_numeric($value)) {
                    $errors[] = "Поле {$field} має бути числом.";
                    break;
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (is_string($value) && mb_strlen($value) < $min) {
                        $errors[] = "Поле {$field} повинно містити щонайменше {$min} символів.";
                        break;
                    }
                    if (is_numeric($value) && $value < $min) {
                        $errors[] = "Поле {$field} повинно бути не менше {$min}.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (is_string($value) && mb_strlen($value) > $max) {
                        $errors[] = "Поле {$field} повинно містити не більше {$max} символів.";
                        break;
                    }
                    if (is_numeric($value) && $value > $max) {
                        $errors[] = "Поле {$field} повинно бути не більше {$max}.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'same:')) {
                    $other = substr($rule, 5);
                    if (($data[$other] ?? null) !== $value) {
                        $errors[] = 'Значення полів не співпадають.';
                        break;
                    }
                }

                if (str_starts_with($rule, 'in:')) {
                    $options = explode(',', substr($rule, 3));
                    if (!in_array((string) $value, $options, true)) {
                        $errors[] = "Непідтримуване значення для {$field}.";
                        break;
                    }
                }

                if ($rule === 'csrf' && !$this->isCsrfTokenValid((string) $value)) {
                    $errors[] = 'Невірний CSRF-токен. Спробуйте надіслати форму ще раз.';
                    break;
                }
            }
        }

        return $errors;
    }

    protected function isCsrfTokenValid(?string $token): bool
    {
        return $token !== null && $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    protected function sanitize(array $input): array
    {
        return array_map(static fn($value) => is_string($value) ? trim($value) : $value, $input);
    }

    protected function redirectBack(string $fallback = '/'): void
    {
        $target = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($target);
    }
    protected function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404'); // зроби простий шаблон errors/404.php
        exit;
    }
}
