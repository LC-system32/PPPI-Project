<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrf = csrf_token();

// Підрахунок кількості товарів у кошику
$totalItems = 0;
if (!empty($items)) {
    foreach ($items as $row) {
        $totalItems += (int) ($row['quantity'] ?? 0);
    }
}
?>

<section class="py-4 py-md-5 bg-body-tertiary">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <!-- Хлібні крихти -->
            <nav aria-label="breadcrumb" class="mb-2 small">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="/" class="text-decoration-none">Головна</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/cart" class="text-decoration-none">Кошик</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/checkout/delivery" class="text-decoration-none">Доставка</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Оплата</li>
                </ol>
            </nav>

            <!-- Кроки оформлення -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-4 small">
                <div class="d-flex align-items-center">
                    <span class="badge rounded-pill bg-dark me-2">1</span>
                    <span class="fw-semibold">Кошик</span>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="d-flex align-items-center">
                    <span class="badge rounded-pill bg-dark me-2">2</span>
                    <span class="fw-semibold">Доставка</span>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="d-flex align-items-center">
                    <span class="badge rounded-pill bg-dark me-2">3</span>
                    <span class="fw-semibold">Оплата</span>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="d-flex align-items-center text-muted">
                    <span class="badge rounded-pill bg-light border me-2">4</span>
                    <span>Підтвердження</span>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-3">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="row g-4">

                <!-- Ліва колонка: вибір способу оплати -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h1 class="h4 fw-bold mb-3">Спосіб оплати</h1>
                            <p class="text-muted small mb-4">
                                Оберіть зручний спосіб оплати замовлення.
                            </p>

                            <form action="/checkout/payment" method="POST" class="vstack gap-3">
                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="vstack gap-2">
                                    <!-- Оплата карткою онлайн -->
                                    <div class="border rounded-3 p-3 d-flex align-items-start gap-3">
                                        <div class="form-check m-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="payment_method"
                                                   id="pay_card"
                                                   value="card"
                                                <?= (($formData['payment_method'] ?? 'card') === 'card') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold" for="pay_card">
                                                Оплата онлайн банківською карткою
                                            </label>
                                            <div class="small text-muted">
                                                Безпечна оплата через платіжний сервіс.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Післяплата -->
                                    <div class="border rounded-3 p-3 d-flex align-items-start gap-3">
                                        <div class="form-check m-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="payment_method"
                                                   id="pay_cod"
                                                   value="cod"
                                                <?= (($formData['payment_method'] ?? '') === 'cod') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold" for="pay_cod">
                                                Готівкою / карткою при отриманні
                                            </label>
                                            <div class="small text-muted">
                                                Оплата у відділенні перевізника або кур’єру.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Безготівковий розрахунок -->
                                    <div class="border rounded-3 p-3 d-flex align-items-start gap-3">
                                        <div class="form-check m-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="payment_method"
                                                   id="pay_bank"
                                                   value="bank"
                                                <?= (($formData['payment_method'] ?? '') === 'bank') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold" for="pay_bank">
                                                Безготівковий розрахунок
                                            </label>
                                            <div class="small text-muted">
                                                Виставимо рахунок-фактуру для оплати з розрахункового рахунку.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="/checkout/delivery" class="btn btn-outline-secondary rounded-pill">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Назад
                                    </a>
                                    <button type="submit" class="btn btn-dark rounded-pill px-4">
                                        Далі: Підтвердження
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Права колонка: підсумок замовлення -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 position-sticky top-0">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3">Підсумок замовлення</h2>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Товарів у кошику</span>
                                <span><?= $totalItems ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Сума товарів</span>
                                <span><?= number_format($total ?? 0, 2, '.', ' ') ?> ₴</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Доставка</span>
                                <span>0 ₴</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                <span class="fw-semibold">До сплати</span>
                                <span class="h4 mb-0">
                                    <?= number_format($total ?? 0, 2, '.', ' ') ?> ₴
                                </span>
                            </div>

                            <hr class="my-3">

                            <h6 class="fw-semibold mb-2">Доставка</h6>
                            <p class="small mb-1">
                                <span class="text-muted">Адреса:</span>
                                <?= htmlspecialchars($delivery['delivery_address'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="small mb-0">
                                <span class="text-muted">Спосіб:</span>
                                <?= htmlspecialchars(delivery_method_label($delivery['delivery_method'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <hr class="my-3">

                            <h6 class="fw-semibold mb-2">Товари</h6>
                            <ul class="list-group list-group-flush small mb-0">
                                <?php foreach ($items as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                                        <div class="me-2">
                                            <div class="fw-semibold text-truncate">
                                                <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-muted">x<?= (int) $item['quantity'] ?></div>
                                        </div>
                                        <span><?= number_format($item['subtotal'], 2, '.', ' ') ?> ₴</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
