<?php
namespace App\Core;

class App
{
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function run()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $this->router->dispatch($uri);
    }
}
