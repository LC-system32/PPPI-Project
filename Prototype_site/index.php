<?php

use App\Core\App;

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/App/helpers.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require BASE_PATH . '/public/routes/web.php';

$app = new App();
$app->run();
