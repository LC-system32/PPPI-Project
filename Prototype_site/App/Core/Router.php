<?php

namespace App\Core;

class Router
{
    protected static array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    protected static array $groupStack = [];

    public static function get(string $path, array|callable $action): void
    {
        self::addRoute('GET', $path, $action);
    }

    public static function post(string $path, array|callable $action): void
    {
        self::addRoute('POST', $path, $action);
    }

    public static function group(string $prefix, callable $callback): void
    {
        $normalized = '/' . ltrim($prefix, '/');
        $normalized = $normalized === '/' ? '/' : rtrim($normalized, '/');

        self::$groupStack[] = $normalized === '/' ? '' : $normalized;
        $callback();
        array_pop(self::$groupStack);
    }

    protected static function addRoute(string $method, string $path, array|callable $action): void
    {
        $fullPath = self::applyGroupPrefix($path);
        [$pattern, $params] = self::compilePattern($fullPath);

        self::$routes[$method][] = [
            'path' => $fullPath,
            'pattern' => $pattern,
            'params' => $params,
            'action' => $action,
        ];
    }

    protected static function applyGroupPrefix(string $path): string
    {
        $path = '/' . ltrim($path, '/');

        // Префікс групи
        $prefix = implode('', self::$groupStack) ?: '';

        // Якщо групи немає – просто чистимо шлях
        if ($prefix === '') {
            $clean = rtrim($path, '/');
            return $clean === '' ? '/' : $clean;
        }

        // Якщо група є – будуємо повний шлях
        $full = rtrim($prefix, '/') . $path;

        $clean = rtrim($full, '/');
        return $clean === '' ? '/' : $clean;
    }

    /**
     * @return array{0:string,1:array<int,string>}
     */
    protected static function compilePattern(string $path): array
    {
        $paramNames = [];
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', static function ($matches) use (&$paramNames) {
            $paramNames[] = $matches[1];
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $path);

        $pattern = '#^' . ($pattern === '' ? '/' : $pattern) . '$#';

        return [$pattern, $paramNames];
    }

    public static function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $routes = self::$routes[$method] ?? [];

        foreach ($routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($route['params'] as $name) {
                $params[] = $matches[$name] ?? null;
            }

            self::invokeAction($route['action'], $params);
            return;
        }

        http_response_code(404);
        echo 'Сторінку не знайдено.';
    }

    protected static function invokeAction(array|callable $action, array $params): void
    {
        if (is_callable($action)) {
            echo call_user_func_array($action, $params);
            return;
        }

        if (!is_array($action) || count($action) !== 2) {
            throw new \RuntimeException('Неправильно визначено дію маршруту.');
        }

        [$controllerName, $methodName] = $action;

        if (!class_exists($controllerName)) {
            throw new \RuntimeException("Контролер {$controllerName} не знайдено.");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            throw new \RuntimeException("Метод {$methodName} відсутній у {$controllerName}.");
        }

        call_user_func_array([$controller, $methodName], $params);
    }
}
