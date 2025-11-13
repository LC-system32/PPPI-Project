<?php

namespace App\Core;

class Controller
{
    function view(string $name, array $data = [])
    {
        extract($data);
        require BASE_PATH . '/public/view/' . $name . '.php';
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
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
}
