<?php

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    private string $usersFile = __DIR__ . '/../../data/users.json';

    protected function loadUsers(): array
    {
        if (!file_exists($this->usersFile)) return [];
        return json_decode(file_get_contents($this->usersFile), true) ?? [];
    }

    protected function saveUsers(array $users): void
    {
        $dir = dirname($this->usersFile);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents($this->usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    public function auth()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $message = $_SESSION['message'] ?? null;
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['message'], $_SESSION['errors']);

        $this->view('auth', compact('message', 'errors'));
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Невірний формат email.";
        if (strlen($password) < 6) $errors[] = "Пароль має містити мінімум 6 символів.";

        if (empty($errors)) {
            $users = $this->loadUsers();
            $found = null;

            foreach ($users as $u) {
                if ($u['email'] === $email) {
                    $found = $u;
                    break;
                }
            }

            if ($found && password_verify($password, $found['password'])) {
                $_SESSION['user'] = $found['name'];
                $_SESSION['message'] = "✅ Вітаємо, {$found['name']}!";
                $_SESSION['activeTab'] = 'login';
            } else {
                $errors[] = "❌ Невірний email або пароль.";
            }
        }

        if (!empty($errors)) $_SESSION['errors'] = $errors;

        // Встановлюємо активну вкладку для відображення після редиректу
        $_SESSION['activeTab'] = 'login';

        header('Location: /auth');
        exit;
    }

    public function register()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');
        $errors = [];

        if (empty($name)) $errors[] = "Ім’я обов’язкове.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Невірний email.";
        if (strlen($password) < 6) $errors[] = "Пароль має містити мінімум 6 символів.";
        if ($password !== $confirm) $errors[] = "Паролі не збігаються.";

        $users = $this->loadUsers();
        foreach ($users as $u) {
            if ($u['email'] === $email) {
                $errors[] = "Користувач з таким email вже існує.";
                break;
            }
        }

        if (empty($errors)) {
            $users[] = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->saveUsers($users);
            $_SESSION['message'] = "✅ Реєстрація успішна, {$name}!";
            $_SESSION['activeTab'] = 'login'; // після успіху — відображаємо логін
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['activeTab'] = 'register'; // при помилці — відображаємо вкладку реєстрації
        }

        header('Location: /auth');
        exit;
    }
}
