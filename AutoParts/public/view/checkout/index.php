<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrf = csrf_token();
?>

<section class="py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold mb-4">Оформлення замовлення</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <form action="/checkout" method="POST" class="row g-3">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                            <div class="col-12">
                                <label class="form-label">Адреса доставки</label>
                                <textarea name="delivery_address" class="form-control" rows="3" required><?= htmlspecialchars($formData['delivery_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Спосіб доставки</label>
                                <select name="delivery_method" class="form-select" required>
                                    <option value="pickup" <?= (($formData['delivery_method'] ?? '') === 'pickup') ? 'selected' : '' ?>>Самовивіз</option>
                                    <option value="nova-poshta" <?= (($formData['delivery_method'] ?? '') === 'nova-poshta') ? 'selected' : '' ?>>Нова Пошта</option>
                                    <option value="courier" <?= (($formData['delivery_method'] ?? '') === 'courier') ? 'selected' : '' ?>>Курʼєр</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Оплата</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="card" <?= (($formData['payment_method'] ?? '') === 'card') ? 'selected' : '' ?>>Картка онлайн</option>
                                    <option value="cod" <?= (($formData['payment_method'] ?? '') === 'cod') ? 'selected' : '' ?>>Післяплата</option>
                                    <option value="bank" <?= (($formData['payment_method'] ?? '') === 'bank') ? 'selected' : '' ?>>Безготівковий рахунок</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Коментар до замовлення</label>
                                <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($formData['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-dark w-100 btn-lg">Підтвердити замовлення</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="mb-3">Ваше замовлення</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="mb-1 fw-semibold"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <small class="text-muted">x<?= (int) $item['quantity'] ?></small>
                                    </div>
                                    <span><?= number_format($item['subtotal'], 2, '.', ' ') ?> ₴</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Разом</span>
                            <span><?= number_format($total, 2, '.', ' ') ?> ₴</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
