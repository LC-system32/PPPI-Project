<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$authUser = $_SESSION['user'] ?? null;
$cartItems = $_SESSION['cart'] ?? [];
$cartCount = (int) array_sum(is_array($cartItems) ? $cartItems : []);
$csrfToken = function_exists('csrf_token') ? csrf_token() : '';
?>

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
                        <a href="/admin/products" class="nav-link text-warning">
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
