<?php
namespace App\Core;

class App
{
    protected Router $router;

    public function __construct()
    {
        $this->loadEnv();
        $this->router = new Router();

        require_once __DIR__ . '/../../routes/web.php';
    }

    public function run()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $this->router->dispatch($uri);
    }

    protected function loadEnv()
    {
        $path = __DIR__ . '/../../.env';
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$name, $value] = explode('=', $line, 2);
            $_ENV[$name] = trim($value);
            putenv("$name=$value");
        }
    }
}
