<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrf = csrf_token();

// підрахунок кількості товарів для правого блоку
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
                    <li class="breadcrumb-item active" aria-current="page">Доставка</li>
                </ol>
            </nav>

            <!-- Степпер -->
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
                <div class="d-flex align-items-center text-muted">
                    <span class="badge rounded-pill bg-light border me-2">3</span>
                    <span>Оплата</span>
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

                <!-- Ліва колонка: форма доставки -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h1 class="h4 fw-bold mb-3">Доставка</h1>
                            <p class="text-muted small mb-4">
                                Вкажіть адресу та спосіб доставки замовлення.
                            </p>

                            <form action="/checkout/delivery" method="POST" class="row g-3">
                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="col-12">
                                    <label class="form-label">Адреса доставки</label>
                                    <textarea name="delivery_address"
                                              class="form-control"
                                              rows="3"
                                              required><?= htmlspecialchars($formData['delivery_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Спосіб доставки</label>
                                    <select name="delivery_method" class="form-select" required>
                                        <option value="pickup" <?= (($formData['delivery_method'] ?? '') === 'pickup') ? 'selected' : '' ?>>
                                            Самовивіз
                                        </option>
                                        <option value="nova-poshta" <?= (($formData['delivery_method'] ?? '') === 'nova-poshta') ? 'selected' : '' ?>>
                                            Нова Пошта
                                        </option>
                                        <option value="courier" <?= (($formData['delivery_method'] ?? '') === 'courier') ? 'selected' : '' ?>>
                                            Курʼєр
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Коментар до замовлення</label>
                                    <textarea name="notes"
                                              class="form-control"
                                              rows="2"><?= htmlspecialchars($formData['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>

                                <div class="col-12 d-flex justify-content-between mt-2">
                                    <a href="/cart" class="btn btn-outline-secondary rounded-pill">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Назад до кошика
                                    </a>
                                    <button type="submit" class="btn btn-dark rounded-pill px-4">
                                        Далі: Оплата
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
                                <span class="text-muted">Товарів</span>
                                <span><?= $totalItems ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Сума товарів</span>
                                <span><?= number_format($total ?? 0, 2, '.', ' ') ?> ₴</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Знижка</span>
                                <span>0 ₴</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                <span class="fw-semibold">До сплати</span>
                                <span class="h4 mb-0"><?= number_format($total ?? 0, 2, '.', ' ') ?> ₴</span>
                            </div>

                            <button type="submit"
                                    form="checkout-submit-form"
                                    class="btn btn-dark w-100 btn-lg rounded-pill mb-2"
                                    disabled>
                                Оформити замовлення
                            </button>

                            <button type="button"
                                    class="btn btn-outline-secondary w-100 btn-sm rounded-pill"
                                    onclick="window.location.href='/catalog'">
                                Продовжити покупки
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

