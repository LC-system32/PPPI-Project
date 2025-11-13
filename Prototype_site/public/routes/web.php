<?php

use App\Controllers\AuthController;
use App\Controllers\FooterController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Core\Router;

Router::get('/', [HomeController::class, 'index']);
Router::get('/auth', [AuthController::class, 'auth']);
Router::get('/profile', [ProfileController::class, 'show']);

Router::get('/about', [FooterController::class, 'about']);
Router::get('/privacy-policy', [FooterController::class, 'privacyPolicy']);
Router::get('/faq', [FooterController::class, 'faq']);
Router::get('/support', [FooterController::class, 'support']);
Router::get('/returns', [FooterController::class, 'returns']);
Router::get('/delivery-payment', [FooterController::class, 'deliveryPayment']);
Router::get('/information', [FooterController::class, 'information']);

Router::post('/register', [AuthController::class, 'register']);
Router::post('/login', [AuthController::class, 'login']);
Router::post('/logout', [AuthController::class, 'logout']);
Router::post('/profile/details', [ProfileController::class, 'updateDetails']);
Router::post('/profile/password', [ProfileController::class, 'updatePassword']);
