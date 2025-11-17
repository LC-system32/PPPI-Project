<?php
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\BrandController as AdminBrandController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Admin\CarModelController as AdminCarModelController;
use App\Controllers\AuthController;
use App\Controllers\BrandController;
use App\Controllers\CarModelController;
use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Controllers\FooterController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Controllers\ReturnController;
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
Router::post('/product/{slug}/review', [CatalogController::class, 'addReview']);

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
Router::get('/checkout/success', [OrderController::class, 'success']);
Router::post('/checkout', [OrderController::class, 'place']);
Router::get('/orders', [OrderController::class, 'userOrders']);

// Повернення/обмін товару
Router::get('/returns', [ReturnController::class, 'index']);
Router::get('/returns/create', [ReturnController::class, 'create']);
Router::post('/returns', [ReturnController::class, 'store']);
Router::get('/returns/{id}', [ReturnController::class, 'show']);

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

    // Повернення товару
    Router::get('/returns', [ReturnController::class, 'adminIndex']);
    Router::get('/returns/{id}', [ReturnController::class, 'adminShow']);
    Router::post('/returns/{id}/status', [ReturnController::class, 'adminUpdateStatus']);

    // Головна панель адміна (дашборд)
    Router::get('/', [AdminDashboardController::class, 'index']);

    // Бренди
    Router::get('/brands', [AdminBrandController::class, 'index']);
    Router::get('/brands/create', [AdminBrandController::class, 'create']);
    Router::post('/brands', [AdminBrandController::class, 'store']);
    Router::get('/brands/{id}/edit', [AdminBrandController::class, 'edit']);
    Router::post('/brands/{id}', [AdminBrandController::class, 'update']);
    Router::post('/brands/{id}/delete', [AdminBrandController::class, 'destroy']);

    // Користувачі
    Router::get('/users', [AdminUserController::class, 'index']);
    Router::get('/users/{id}/edit', [AdminUserController::class, 'edit']);
    Router::post('/users/{id}', [AdminUserController::class, 'update']);
    Router::post('/users/{id}/delete', [AdminUserController::class, 'destroy']);

    // Моделі авто
    Router::get('/car-models', [AdminCarModelController::class, 'index']);
    Router::get('/car-models/create', [AdminCarModelController::class, 'create']);
    Router::post('/car-models', [AdminCarModelController::class, 'store']);
    Router::post('/car-models/{id}/delete', [AdminCarModelController::class, 'destroy']);

    // Відгуки (модерація)
    Router::get('/reviews', [\App\Controllers\Admin\ReviewController::class, 'index']);
    Router::get('/reviews/{id}', [\App\Controllers\Admin\ReviewController::class, 'show']);
    Router::post('/reviews/{id}/approve', [\App\Controllers\Admin\ReviewController::class, 'approve']);
    Router::post('/reviews/{id}/reject', [\App\Controllers\Admin\ReviewController::class, 'reject']);
});
