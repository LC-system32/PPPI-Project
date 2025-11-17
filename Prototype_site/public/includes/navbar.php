<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$authUser = $_SESSION['user'] ?? null;
$cartItems = $_SESSION['cart'] ?? [];
$cartCount = (int) array_sum(is_array($cartItems) ? $cartItems : []);
$csrfToken = function_exists('csrf_token') ? csrf_token() : '';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';

function _navClass($currentPath, $path)
{
    return strpos($currentPath, $path) === 0 ? 'nav-link active text-warning fw-bold' : 'nav-link text-light';
}
?>

<?php if (isset($isAdmin) && $isAdmin): ?>
    <!-- Admin Topbar -->
    <nav class="navbar navbar-dark bg-dark shadow-sm py-2 border-bottom border-warning">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <button class="btn btn-sm btn-outline-light d-md-none me-2" id="adminSidebarToggle" title="Меню">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand fw-bold d-flex align-items-center" href="/admin">
                    <i class="bi bi-gear-fill me-2 text-warning"></i>
                    <span>Адмін-панель</span>
                </a>
                <a href="/" class="btn btn-sm btn-outline-warning ms-2" title="На головну сторінку">
                    <i class="bi bi-house-door me-1"></i>На головну
                </a>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block">
                    <small class="text-white-50">Адміністратор:</small><br>
                    <span class="text-light fw-semibold"><?= htmlspecialchars($authUser['login'] ?? 'Адмін', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Меню профілю">
                        <i class="bi bi-person-circle me-1"></i>
                        <span class="d-md-inline d-none">Профіль</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i>Мій профіль</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="/logout" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Вихід</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Admin Sidebar -->
    <div class="container-fluid">
        <div class="row">
            <nav class="col-12 col-md-2 bg-dark text-light p-3 min-vh-100 d-flex flex-column border-end border-secondary">
                <div class="mb-4">
                    <h6 class="text-warning text-uppercase small fw-bold mb-3">Основне</h6>
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item"><a href="/admin" class="<?= _navClass($currentPath, '/admin') ?> rounded"> <i class="bi bi-speedometer2 me-2"></i>Дашборд</a></li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h6 class="text-warning text-uppercase small fw-bold mb-3">Продажи</h6>
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item"><a href="/admin/orders" class="<?= _navClass($currentPath, '/admin/orders') ?> rounded"> <i class="bi bi-basket me-2"></i>Замовлення</a></li>
                        <li class="nav-item"><a href="/admin/returns" class="<?= _navClass($currentPath, '/admin/returns') ?> rounded"> <i class="bi bi-arrow-counterclockwise me-2"></i>Повернення</a></li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h6 class="text-warning text-uppercase small fw-bold mb-3">Каталог</h6>
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item"><a href="/admin/products" class="<?= _navClass($currentPath, '/admin/products') ?> rounded"> <i class="bi bi-box-seam me-2"></i>Продукти</a></li>
                        <li class="nav-item"><a href="/admin/brands" class="<?= _navClass($currentPath, '/admin/brands') ?> rounded"> <i class="bi bi-bookmarks me-2"></i>Бренди</a></li>
                        <li class="nav-item"><a href="/admin/car-models" class="<?= _navClass($currentPath, '/admin/car-models') ?> rounded"> <i class="bi bi-car-front me-2"></i>Моделі авто</a></li>
                        <li class="nav-item"><a href="/admin/categories" class="<?= _navClass($currentPath, '/admin/categories') ?> rounded"> <i class="bi bi-tags me-2"></i>Категорії</a></li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h6 class="text-warning text-uppercase small fw-bold mb-3">Комунікація</h6>
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item"><a href="/admin/reviews" class="<?= _navClass($currentPath, '/admin/reviews') ?> rounded"> <i class="bi bi-chat-left-text me-2"></i>Відгуки</a></li>
                        <li class="nav-item"><a href="/admin/support" class="<?= _navClass($currentPath, '/admin/support') ?> rounded"> <i class="bi bi-question-circle me-2"></i>Підтримка</a></li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h6 class="text-warning text-uppercase small fw-bold mb-3">Управління</h6>
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item"><a href="/admin/users" class="<?= _navClass($currentPath, '/admin/users') ?> rounded"> <i class="bi bi-people me-2"></i>Користувачі</a></li>
                    </ul>
                </div>

                <div class="mt-auto pt-4 border-top border-secondary">
                    <a href="/" class="nav-link text-light rounded"><i class="bi bi-arrow-left me-2"></i>На головний сайт</a>
                </div>
            </nav>

            <main class="col-12 col-md-10 p-4">
                <!-- admin content starts -->
<?php else: ?>
    <!-- User Navbar - Mega Menu with Icons -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm border-bottom border-warning">
        <div class="container-lg">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center fw-bold" href="/">
                <i class="bi bi-wrench-adjustable text-warning me-2" style="font-size:1.15rem"></i>
                <span>AutoParts</span>
            </a>

            <!-- Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <!-- Left: Primary links / Mega menu -->
                <ul class="navbar-nav me-auto align-items-lg-stretch">
                    <li class="nav-item">
                        <a href="/" class="nav-link <?= $currentPath === '/' ? 'active text-warning fw-bold' : 'text-light' ?>">Головна</a>
                    </li>

                    <!-- Mega Menu: Catalog -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" id="megaCatalog" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Каталог
                        </a>
                        <div class="dropdown-menu dropdown-menu-start p-4 dropdown-menu-dark" aria-labelledby="megaCatalog" style="min-width:750px; width:750px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6 class="text-warning fw-bold mb-3">За категоріями</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><a href="/catalog?cat=engine" class="text-light text-decoration-none small"><i class="bi bi-gear me-2"></i>Двигун</a></li>
                                        <li class="mb-2"><a href="/catalog?cat=brakes" class="text-light text-decoration-none small"><i class="bi bi-disc me-2"></i>Гальма</a></li>
                                        <li class="mb-2"><a href="/catalog?cat=suspension" class="text-light text-decoration-none small"><i class="bi bi-arrows-move me-2"></i>Підвіска</a></li>
                                        <li><a href="/catalog?cat=electronics" class="text-light text-decoration-none small"><i class="bi bi-battery-charging me-2"></i>Електроніка</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-warning fw-bold mb-3">Бренди</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><a href="/brands?brand=bosch" class="text-light text-decoration-none small"><i class="bi bi-bookmarks me-2"></i>BOSCH</a></li>
                                        <li class="mb-2"><a href="/brands?brand=valeau" class="text-light text-decoration-none small"><i class="bi bi-bookmarks me-2"></i>Valeo</a></li>
                                        <li class="mb-2"><a href="/brands?brand=castrol" class="text-light text-decoration-none small"><i class="bi bi-bookmarks me-2"></i>Castrol</a></li>
                                        <li><a href="/brands" class="text-light text-decoration-none small"><i class="bi bi-list-ul me-2"></i>Всі бренди</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-warning fw-bold mb-3">Швидкий пошук</h6>
                                    <form action="/catalog" method="GET" class="d-flex gap-2 mb-3">
                                        <input name="q" type="search" class="form-control form-control-sm" placeholder="Пошук за кодом" aria-label="Search">
                                        <button class="btn btn-sm btn-warning text-dark fw-bold" type="submit"><i class="bi bi-search"></i></button>
                                    </form>
                                    <div class="row small">
                                        <div class="col-6">
                                            <a href="/catalog?filter=top" class="text-light text-decoration-none">Популярні</a>
                                        </div>
                                        <div class="col-6 text-end">
                                            <a href="/support" class="text-light text-decoration-none">Підтримка</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="/brands" class="nav-link <?= strpos($currentPath, '/brands') === 0 ? 'active text-warning fw-bold' : 'text-light' ?>">Бренди</a>
                    </li>
                </ul>

                <!-- Center: optional search on large screens -->
                <form action="/catalog" method="GET" class="d-none d-lg-flex mx-auto" style="max-width:540px; width:100%;">
                    <div class="input-group input-group-sm">
                        <input name="q" type="search" class="form-control" placeholder="Пошук за кодом чи назвою" aria-label="Search">
                        <button class="btn btn-warning text-dark" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <!-- Right: Cart + User -->
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a href="/cart" class="nav-link position-relative <?= strpos($currentPath, '/cart') === 0 ? 'active text-warning fw-bold' : 'text-light' ?>">
                            <i class="bi bi-cart3" style="font-size:1.2rem"></i>
                            <?php if ($cartCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <?php if ($authUser): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="badge bg-secondary rounded-circle me-2" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; font-size:0.85rem;"><?= htmlspecialchars(substr($authUser['login'] ?? 'U', 0, 1), ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="d-none d-md-inline"><?= htmlspecialchars(substr($authUser['login'] ?? 'User', 0, 14), ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="userDropdown">
                                <li><h6 class="dropdown-header text-warning">Кабінет</h6></li>
                                <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i>Профіль</a></li>
                                <li><a class="dropdown-item" href="/orders"><i class="bi bi-receipt me-2"></i>Замовлення</a></li>
                                <li><a class="dropdown-item" href="/returns"><i class="bi bi-arrow-counterclockwise me-2"></i>Повернення</a></li>
                                <li><a class="dropdown-item" href="/support"><i class="bi bi-question-circle me-2"></i>Підтримка</a></li>
                                <?php if (in_array((int) ($authUser['role_id'] ?? 0), [1, 2], true)): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Адмін-панель</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="/logout" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Вихід</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="/auth" class="btn btn-warning btn-sm text-dark fw-bold">Увійти</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
<?php endif; ?>