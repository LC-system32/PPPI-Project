<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$authUser = $_SESSION['user'] ?? null;
$cartItems = $_SESSION['cart'] ?? [];
$cartCount = (int) array_sum(is_array($cartItems) ? $cartItems : []);
$csrfToken = function_exists('csrf_token') ? csrf_token() : '';
?>

<?php if (isset($isAdmin) && $isAdmin): ?>
    <!-- Admin topbar -->
    <nav class="navbar navbar-dark bg-dark shadow-sm py-2 admin-topbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-light d-md-none me-2" id="adminSidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand fw-bold d-flex align-items-center" href="/">
                    <i class="bi bi-gear-fill me-2 text-warning"></i>
                    Admin
                </a>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-md-inline">Вітаємо, <?= htmlspecialchars($authUser['login'] ?? 'Адмін', ENT_QUOTES, 'UTF-8') ?></span>
                <form action="/logout" method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="btn btn-outline-light btn-sm">Вихід</button>
                </form>
            </div>
        </div>
    </nav>

    <?php
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
    function _navClass($currentPath, $path)
    {
        // return active classes when current path starts with $path
        return strpos($currentPath, $path) === 0 ? 'nav-link active text-warning' : 'nav-link text-light';
    }
    ?>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-12 col-md-2 bg-dark text-light p-3 min-vh-100 d-flex flex-column">
                <ul class="nav flex-column">
                    <li class="nav-item mb-1"><a href="/admin" class="<?= _navClass($currentPath, '/admin') ?>"> <i class="bi bi-speedometer2 me-2"></i>Панель</a></li>
                    <li class="nav-item mb-1"><a href="/admin/orders" class="<?= _navClass($currentPath, '/admin/orders') ?>"> <i class="bi bi-basket me-2"></i>Замовлення</a></li>
                    <li class="nav-item mb-1"><a href="/admin/products" class="<?= _navClass($currentPath, '/admin/products') ?>"> <i class="bi bi-box-seam me-2"></i>Продукти</a></li>
                    <li class="nav-item mb-1"><a href="/admin/brands" class="<?= _navClass($currentPath, '/admin/brands') ?>"> <i class="bi bi-bookmarks me-2"></i>Бренди</a></li>
                    <li class="nav-item mb-1"><a href="/admin/car-models" class="<?= _navClass($currentPath, '/admin/car-models') ?>"> <i class="bi bi-gear me-2"></i>Моделі</a></li>
                    <li class="nav-item mb-1"><a href="/admin/categories" class="<?= _navClass($currentPath, '/admin/categories') ?>"> <i class="bi bi-tags me-2"></i>Категорії</a></li>
                    <li class="nav-item mb-1"><a href="/admin/returns" class="<?= _navClass($currentPath, '/admin/returns') ?>"> <i class="bi bi-arrow-counterclockwise me-2"></i>Повернення</a></li>
                    <li class="nav-item mb-1"><a href="/admin/reviews" class="<?= _navClass($currentPath, '/admin/reviews') ?>"> <i class="bi bi-chat-left-text me-2"></i>Відгуки</a></li>
                    <li class="nav-item mb-1"><a href="/admin/users" class="<?= _navClass($currentPath, '/admin/users') ?>"> <i class="bi bi-people me-2"></i>Користувачі</a></li>
                </ul>
            </nav>

            <main class="col-12 col-md-10 p-4">
                <!-- admin content starts -->
            <?php else: ?>
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
                    <div class="container">
                        <a class="navbar-brand fw-bold d-flex align-items-center" href="/">
                            <i class="bi bi-gear-fill me-2 text-warning"></i>
                            AutoParts
                        </a>

                        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="mainNav">
                            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                                <li class="nav-item">
                                    <a href="/" class="nav-link">
                                        <i class="bi bi-house-door me-1"></i>Головна
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/catalog" class="nav-link">
                                        <i class="bi bi-grid me-1"></i>Каталог
                                    </a>
                                </li>
                                <?php if ($authUser): ?>
                                    <li class="nav-item">
                                        <a href="/profile" class="nav-link">
                                            <i class="bi bi-person-circle me-1"></i>Кабінет
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/orders" class="nav-link">
                                            <i class="bi bi-receipt me-1"></i>Мої замовлення
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item position-relative">
                                    <a href="/cart" class="nav-link d-flex align-items-center">
                                        <i class="bi bi-cart3 me-1"></i>Кошик
                                        <span class="badge rounded-pill bg-warning text-dark ms-2"><?= $cartCount ?></span>
                                    </a>
                                </li>
                                <?php if ($authUser && in_array((int) ($authUser['role_id'] ?? 0), [1, 2], true)): ?>
                                    <li class="nav-item">
                                        <a href="/admin" class="nav-link text-warning">
                                            <i class="bi bi-speedometer2 me-1"></i>Адмін-панель
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <div class="ms-lg-3 mt-3 mt-lg-0">
                                <?php if ($authUser): ?>
                                    <form action="/logout" method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <span class="text-white-50 small">Вітаємо, <?= htmlspecialchars($authUser['login'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <button type="submit" class="btn btn-outline-light btn-sm">
                                            Вихід
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="/auth" class="btn btn-warning text-dark fw-semibold">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Увійти / Реєстрація
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </nav>
            <?php endif; ?>