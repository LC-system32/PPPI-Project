<?php
class Controller {
    public function index() {
        ob_start();
        include __DIR__ . '/../view/home.php';
        return ob_get_clean();
    }
}
