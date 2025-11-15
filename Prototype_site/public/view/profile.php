<?php
/** @var \App\Models\User $user */

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$csrfToken = csrf_token();
$roleLabel = match ((int)($user->role_id ?? 0)) {
    1 => 'Адміністратор',
    2 => 'Менеджер',
    3 => 'Покупець',
    default => 'Користувач',
};

/**
 * Дефолти для необов’язкових змінних,
 * щоб не ловити warning-ів у view.
 */
$message       = $message       ?? null;
$errors        = $errors        ?? [];
$errorContext  = $errorContext  ?? null;
$recentOrders  = $recentOrders  ?? [];
$savedVehicles = $savedVehicles ?? [];
?>

<section class="profile-hero position-relative text-white py-5">
    <img src="https://images.pexels.com/photos/4488660/pexels-photo-4488660.jpeg"
         alt="Garage"
         class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(0,0,0,.85), rgba(0,0,0,.6));"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-4">
                    <div class="avatar-wrapper bg-warning bg-opacity-25 rounded-circle p-1">
                        <div class="avatar bg-warning text-dark fw-bold d-flex align-items-center justify-content-center rounded-circle">
                            <?= strtoupper(substr($user->login, 0, 1)) ?>
                        </div>
                    </div>
                    <div>
                        <p class="text-uppercase text-white-50 mb-1">Особистий кабінет</p>
                        <h1 class="display-6 fw-bold mb-2">
                            <?= htmlspecialchars($user->login, ENT_QUOTES, 'UTF-8') ?>
                        </h1>
                        <div class="d-flex flex-wrap gap-3 text-white-50">
                            <span>
                                <i class="bi bi-envelope me-2"></i>
                                <?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span>
                                <i class="bi bi-shield-check me-2"></i><?= $roleLabel ?>
                            </span>
                            <span>
                                <i class="bi bi-hash me-2"></i>ID <?= (int)$user->id ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 bg-dark bg-opacity-50 text-white">
                    <div class="card-body">
                        <p class="text-uppercase text-white-50 mb-1">Статус клубу</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0 text-warning">AutoParts+</h3>
                            <span class="badge bg-warning text-dark">Active</span>
                        </div>
                        <p class="small text-white-50 mt-3 mb-2">
                            Кешбек 5% на кожне замовлення та пріоритетна підтримка.
                        </p>
                        <a href="/support" class="btn btn-outline-light btn-sm rounded-pill">
                            Отримати допомогу
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="profile-content py-5 bg-light">
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm rounded-4 mb-4">
                <p class="fw-semibold mb-2">
                    Виникла проблема (секція:
                    <?= htmlspecialchars($errorContext ?? 'загальна', ENT_QUOTES, 'UTF-8') ?>)
                </p>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- ДАНІ АКАНТУ -->
                <div id="details" class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <p class="text-uppercase text-muted small mb-1">Дані акаунту</p>
                                <h2 class="h4 mb-0">Особиста інформація</h2>
                            </div>
                            <span class="badge bg-light text-dark fw-semibold">
                                Оновлено: <?= date('d.m.Y') ?>
                            </span>
                        </div>

                        <form action="/profile/details" method="POST" class="row g-3">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Логін</label>
                                <input type="text"
                                       name="login"
                                       class="form-control rounded-3"
                                       value="<?= htmlspecialchars($formData['login'] ?? $user->login, ENT_QUOTES, 'UTF-8') ?>"
                                       required minlength="3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email"
                                       name="email"
                                       class="form-control rounded-3"
                                       value="<?= htmlspecialchars($formData['email'] ?? $user->email, ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Сповіщення</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="promoSwitch" checked disabled>
                                            <label class="form-check-label" for="promoSwitch">
                                                Отримувати акції та підбірки
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="serviceSwitch" checked disabled>
                                            <label class="form-check-label" for="serviceSwitch">
                                                Нагадування про сервіс
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-3">
                                <a href="/" class="btn btn-outline-secondary rounded-pill px-4">Скасувати</a>
                                <button type="submit"
                                        class="btn btn-warning text-dark fw-semibold rounded-pill px-4">
                                    Зберегти зміни
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- БЕЗПЕКА -->
                <div id="security" class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <p class="text-uppercase text-muted small mb-1">Безпека</p>
                                <h2 class="h4 mb-0">Змінити пароль</h2>
                            </div>
                            <span class="badge bg-warning text-dark fw-semibold">
                                Рекомендовано міняти кожні 90 днів
                            </span>
                        </div>

                        <form action="/profile/password" method="POST" class="row g-3">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                            <div class="col-12">
                                <label class="form-label fw-semibold">Поточний пароль</label>
                                <input type="password"
                                       name="current_password"
                                       class="form-control rounded-3"
                                       required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Новий пароль</label>
                                <input type="password"
                                       name="new_password"
                                       class="form-control rounded-3"
                                       required minlength="8"
                                       placeholder="Мінімум 8 символів">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Підтвердіть пароль</label>
                                <input type="password"
                                       name="confirm_password"
                                       class="form-control rounded-3"
                                       required minlength="8">
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit"
                                        class="btn btn-dark fw-semibold rounded-pill px-4">
                                    Оновити пароль
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ОСТАННІ ЗАМОВЛЕННЯ -->
                <div id="orders" class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-uppercase text-muted small mb-1">Замовлення</p>
                                <h2 class="h4 mb-0">Останні операції</h2>
                            </div>
                            <a href="/orders" class="btn btn-outline-secondary btn-sm rounded-pill">
                                Усі замовлення
                            </a>
                        </div>

                        <?php if (!empty($recentOrders) && is_array($recentOrders)): ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Номер</th>
                                        <th>Дата</th>
                                        <th>Позиції</th>
                                        <th>Статус</th>
                                        <th class="text-end">Сума</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <?php
                                        // Номер
                                        $number = '';
                                        if (isset($order['number'])) {
                                            $number = (string)$order['number'];
                                        } elseif (isset($order['id'])) {
                                            $number = (string)$order['id'];
                                        }

                                        // Дата
                                        $date = '';
                                        if (isset($order['date'])) {
                                            $date = (string)$order['date'];
                                        } elseif (isset($order['created_at'])) {
                                            $date = (string)$order['created_at'];
                                        }

                                        // Позиції
                                        $items = $order['items'] ?? '';
                                        if (is_array($items)) {
                                            $items = 'Позицій: ' . count($items);
                                        } elseif ($items === null) {
                                            $items = '';
                                        } else {
                                            $items = (string)$items;
                                        }

                                        // Статус
                                        $status = (string)($order['status'] ?? 'Невідомо');
                                        $statusClass = match ($status) {
                                            'Доставлено' => 'bg-success',
                                            'В дорозі'   => 'bg-warning text-dark',
                                            default      => 'bg-secondary',
                                        };

                                        // Сума
                                        $total = $order['total'] ?? $order['sum'] ?? $order['amount'] ?? '';
                                        if (is_array($total)) {
                                            $total = implode(' ', array_map('strval', $total));
                                        } elseif ($total !== null) {
                                            $total = (string)$total;
                                        } else {
                                            $total = '';
                                        }
                                        ?>
                                        <tr>
                                            <td class="fw-semibold">
                                                <?= htmlspecialchars($number, ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($items, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <span class="badge <?= $statusClass ?> rounded-pill">
                                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                <?= htmlspecialchars($total, ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">
                                Замовлень ще не було. Перейти до
                                <a href="/" class="text-decoration-none">каталогу</a>.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ПРАВА КОЛОНКА -->
            <div class="col-lg-4">
                <!-- ЗБЕРЕЖЕНІ АВТО -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-muted small mb-1">Збережені авто</p>
                        <h2 class="h5 mb-3">Ваші транспортні засоби</h2>

                        <?php if (!empty($savedVehicles) && is_array($savedVehicles)): ?>
                            <?php foreach ($savedVehicles as $vehicle): ?>
                                <?php
                                $title   = htmlspecialchars($vehicle['title']   ?? 'Автомобіль', ENT_QUOTES, 'UTF-8');
                                $year    = htmlspecialchars((string)($vehicle['year'] ?? ''), ENT_QUOTES, 'UTF-8');
                                $vin     = htmlspecialchars($vehicle['vin']     ?? '-', ENT_QUOTES, 'UTF-8');
                                $mileage = htmlspecialchars($vehicle['mileage'] ?? '-', ENT_QUOTES, 'UTF-8');
                                ?>
                                <div class="vehicle-tile rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong><?= $title ?></strong>
                                        <?php if ($year !== ''): ?>
                                            <span class="badge bg-light text-dark"><?= $year ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted mb-1 small">VIN: <?= $vin ?></p>
                                    <p class="text-muted mb-0 small">Пробіг: <?= $mileage ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-3">
                                Ви ще не додали жодного авто. Додайте автомобіль, щоб швидко
                                підбирати сумісні запчастини.
                            </p>
                        <?php endif; ?>

                        <button class="btn btn-outline-dark w-100 rounded-pill">
                            Додати автомобіль
                        </button>
                    </div>
                </div>

                <!-- АДРЕСИ ДОСТАВКИ -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-muted small mb-1">Адреси доставки</p>
                        <h2 class="h5 mb-3">Керування адресами</h2>
                        <p class="text-muted small">
                            Додайте адресу, щоб швидко оформляти замовлення. Ви зможете додати
                            кілька адрес і обрати основну.
                        </p>
                        <button class="btn btn-warning text-dark w-100 rounded-pill fw-semibold">
                            Додати адресу
                        </button>
                    </div>
                </div>

                <!-- ПІДТРИМКА -->
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-muted small mb-1">Підтримка</p>
                        <h2 class="h5 mb-3">Потрібна допомога?</h2>
                        <ul class="list-unstyled text-muted small mb-3">
                            <li>
                                <i class="bi bi-telephone me-2 text-warning"></i>
                                0 800 777 404 (безкоштовно)
                            </li>
                            <li>
                                <i class="bi bi-envelope-open me-2 text-warning"></i>
                                support@autoparts.ua
                            </li>
                            <li>
                                <i class="bi bi-clock-history me-2 text-warning"></i>
                                Пн-Нд: 08:00 - 22:00
                            </li>
                        </ul>
                        <a href="/support" class="btn btn-outline-secondary w-100 rounded-pill">
                            Відкрити звернення
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .avatar {
        width: 84px;
        height: 84px;
        font-size: 2rem;
    }

    .profile-hero {
        overflow: hidden;
    }

    .profile-content .card {
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .profile-content .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
    }

    .vehicle-tile {
        background: #f7f8fa;
        border: 1px dashed #d9dee6;
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
