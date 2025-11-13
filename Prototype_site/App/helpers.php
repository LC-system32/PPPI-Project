<?php

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        $normalized = strtolower($value);

        return match ($normalized) {
            'true', '(true)', '1' => true,
            'false', '(false)', '0' => false,
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $cache = [];

        [$file, $item] = array_pad(explode('.', $key, 2), 2, null);

        if (!isset($cache[$file])) {
            $path = BASE_PATH . "/config/{$file}.php";
            $cache[$file] = file_exists($path) ? require $path : [];
        }

        if ($item === null) {
            return $cache[$file];
        }

        return $cache[$file][$item] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['flash']['old'][$key] ?? $default;
    }
}
