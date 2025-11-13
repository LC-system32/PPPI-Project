<?php
namespace App\Core;

class Router
{
    protected static array $routes = [];

    public static function get(string $path, array|callable $action)
    {
        self::$routes['GET'][$path] = $action;
    }

    public static function post(string $path, array|callable $action)
    {
        self::$routes['POST'][$path] = $action;
    }

    public static function dispatch(string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $action = self::$routes[$method][$path] ?? null;

        if (!$action) {
            http_response_code(404);
            echo "Сторінку не знайдено";
            return;
        }

        if (is_callable($action)) {
            echo call_user_func($action);
            return;
        }

        if (is_array($action) && count($action) === 2) {
            [$controllerName, $methodName] = $action;

            if (!class_exists($controllerName)) {
                throw new \Exception("Клас $controllerName не знайдено");
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $methodName)) {
                throw new \Exception("Метод $methodName не знайдено в $controllerName");
            }

            $controller->$methodName();
            return;
        }

        throw new \Exception("Невірна дія для маршруту $path");
    }
}
