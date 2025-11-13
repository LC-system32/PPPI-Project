<?php

namespace App\Core;

class Controller
{
    function view(string $name, array $data = [])
    {
        extract($data);
        require BASE_PATH . '/view/' . $name . '.php';
    }
    protected function getStoragePath(): string
    {
        return __DIR__ . '/../../storage/users.json';
    }

    protected function loadUsers(): array
    {
        $file = $this->getStoragePath();
        if (!file_exists($file)) return [];
        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }

    protected function saveUsers(array $users): void
    {
        file_put_contents($this->getStoragePath(), json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
