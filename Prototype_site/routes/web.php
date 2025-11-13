<?php
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\FooterController;

// === РЕЄСТРАЦІЯ МАРШРУТІВ ===
Router::get('/', [HomeController::class, 'index']);
Router::get('/auth', [AuthController::class, 'auth']);
Router::get('/about', [FooterController::class, 'about']);
Router::get('/privacy-policy', [FooterController::class, 'privacyPolicy']);
Router::get('/faq', [FooterController::class, 'faq']);
Router::get('/support', [FooterController::class, 'support']);
Router::get('/returns', [FooterController::class, 'returns']);
Router::get('/delivery-payment', [FooterController::class, 'deliveryPayment']);
Router::get('/information', [FooterController::class, 'information']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('/login', [AuthController::class, 'login']);