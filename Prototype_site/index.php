<?php
require_once __DIR__ . '/routes/web.php';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = rtrim($request, '/');

if ($request === '') $request = '/';

if (isset($routes[$request])) {
    $action = $routes[$request];
    
    if (is_callable($action)) {
        echo $action();
    }
    else {
        list($controller, $method) = explode('@', $action);
        require_once __DIR__ . "/controller/$controller.php";
        $instance = new $controller();
        echo $instance->$method();
    }
} else {
    http_response_code(404);
    echo "<h1>404 — Page not found</h1>";
}
