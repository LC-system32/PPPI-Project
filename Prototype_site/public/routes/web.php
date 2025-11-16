<?php
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\AuthController;
use App\Controllers\BrandController;
use App\Controllers\CarModelController;
use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Controllers\FooterController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Core\Router;

Router::get('/', [HomeController::class, 'index']);

// Аутентифікація
Router::get('/auth', [AuthController::class, 'auth']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('/login', [AuthController::class, 'login']);
Router::post('/logout', [AuthController::class, 'logout']);

// Профіль користувача
Router::get('/profile', [ProfileController::class, 'show']);
Router::post('/profile/details', [ProfileController::class, 'updateDetails']);
Router::post('/profile/password', [ProfileController::class, 'updatePassword']);

// Каталог і категорії
Router::get('/catalog', [CatalogController::class, 'index']);
Router::get('/categories', [CatalogController::class, 'categories']);
Router::get('/categories/{slug}', [CatalogController::class, 'category']);
Router::get('/product/{slug}', [CatalogController::class, 'product']);

// Бренди
Router::get('/brands', [BrandController::class, 'index']);
Router::get('/brand/{slug}', [BrandController::class, 'show']);

// Моделі авто
Router::get('/brand/{brandSlug}/{modelSlug}', [CarModelController::class, 'showBySlug']);


// Кошик
Router::get('/cart', [CartController::class, 'index']);
Router::post('/cart/add', [CartController::class, 'add']);
Router::post('/cart/update', [CartController::class, 'update']);
Router::post('/cart/remove', [CartController::class, 'remove']);

// Оформлення замовлення
Router::get('/checkout', [OrderController::class, 'delivery']);
Router::get('/checkout/delivery', [OrderController::class, 'delivery']);
Router::post('/checkout/delivery', [OrderController::class, 'storeDelivery']);
Router::get('/checkout/payment', [OrderController::class, 'payment']);
Router::post('/checkout/payment', [OrderController::class, 'storePayment']);
Router::get('/checkout/confirm', [OrderController::class, 'confirm']);
Router::post('/checkout/confirm', [OrderController::class, 'place']);
Router::post('/checkout', [OrderController::class, 'place']);
Router::get('/orders', [OrderController::class, 'userOrders']);

// Інформаційні сторінки футера
Router::get('/about', [FooterController::class, 'about']);
Router::get('/privacy-policy', [FooterController::class, 'privacyPolicy']);
Router::get('/faq', [FooterController::class, 'faq']);
Router::get('/support', [FooterController::class, 'support']);
Router::get('/returns', [FooterController::class, 'returns']);
Router::get('/delivery-payment', [FooterController::class, 'deliveryPayment']);
Router::get('/information', [FooterController::class, 'information']);

// Адмін-панель
Router::group('/admin', function () {
    // Товари
    Router::get('/products', [AdminProductController::class, 'index']);
    Router::get('/products/create', [AdminProductController::class, 'create']);
    Router::post('/products', [AdminProductController::class, 'store']);
    Router::get('/products/{id}/edit', [AdminProductController::class, 'edit']);
    Router::post('/products/{id}', [AdminProductController::class, 'update']);
    Router::post('/products/{id}/delete', [AdminProductController::class, 'destroy']);

    // Категорії
    Router::get('/categories', [AdminCategoryController::class, 'index']);
    Router::get('/categories/create', [AdminCategoryController::class, 'create']);
    Router::post('/categories', [AdminCategoryController::class, 'store']);
    Router::get('/categories/{id}/edit', [AdminCategoryController::class, 'edit']);
    Router::post('/categories/{id}', [AdminCategoryController::class, 'update']);
    Router::post('/categories/{id}/delete', [AdminCategoryController::class, 'destroy']);

    // Замовлення
    Router::get('/orders', [AdminOrderController::class, 'index']);
    Router::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Router::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
});
