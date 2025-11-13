<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Core\App;

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

$app = new App();
$app->run();
