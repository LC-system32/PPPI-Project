<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<section class="py-5 bg-body-tertiary">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <!-- Іконка успіху -->
                    <div class="mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <!-- Заголовок -->
                    <h1 class="h3 fw-bold mb-3">Замовлення створено успішно!</h1>

                    <!-- Номер замовлення -->
                    <?php if ($order): ?>
                        <div class="mb-4">
                            <p class="text-muted mb-1">Номер вашого замовлення:</p>
                            <p class="h4 fw-bold text-dark">#<?= (int) $order['id'] ?></p>
                        </div>

                        <!-- Деталі замовлення -->
                        <div class="bg-light rounded-3 p-4 mb-4 text-start">
                            <h5 class="fw-semibold mb-3">Деталі замовлення</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p class="small text-muted mb-1">Сума замовлення</p>
                                    <p class="fw-semibold"><?= number_format((float) $order['total'], 2, '.', ' ') ?> ₴</p>
                                </div>

                                <div class="col-md-6">
                                    <p class="small text-muted mb-1">Статус</p>
                                    <p class="fw-semibold">
                                        <span class="badge bg-info">Нове</span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <p class="small text-muted mb-1">Спосіб доставки</p>
                                    <p class="fw-semibold"><?= htmlspecialchars(delivery_method_label($order['delivery_method'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>

                                <div class="col-md-6">
                                    <p class="small text-muted mb-1">Спосіб оплати</p>
                                    <p class="fw-semibold"><?= htmlspecialchars($order['payment_method'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                </div>

                                <div class="col-12">
                                    <p class="small text-muted mb-1">Адреса доставки</p>
                                    <p class="fw-semibold"><?= htmlspecialchars($order['delivery_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>

                            <?php if (!empty($order['notes'])): ?>
                                <hr class="my-3">
                                <p class="small text-muted mb-1">Примітка</p>
                                <p class="mb-0"><?= htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Товари в замовленні -->
                        <div class="mb-4 text-start">
                            <h5 class="fw-semibold mb-3">Товари</h5>
                            <div class="list-group list-group-flush">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($item['name_snapshot'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <small class="text-muted">x<?= (int) $item['quantity'] ?></small>
                                        </div>
                                        <span><?= number_format((float) $item['price'] * (int) $item['quantity'], 2, '.', ' ') ?> ₴</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Contact info for guest orders -->
                        <?php if ($order['guest_email']): ?>
                            <div class="alert alert-info mb-4 text-start">
                                <p class="mb-2">
                                    <strong>Посилання для відстеження замовлення надіслане на:</strong><br>
                                    <code><?= htmlspecialchars($order['guest_email'], ENT_QUOTES, 'UTF-8') ?></code>
                                </p>
                                <p class="mb-0 small text-muted">Ви можете отримати деталі замовлення, перейшовши за посиланням у листі.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Повідомлення про наступні кроки -->
                    <div class="alert alert-light border mb-4">
                        <p class="mb-0">
                            Ми скоро зв'яжемося з вами для підтвердження замовлення та деталей доставки.
                        </p>
                    </div>

                    <!-- Кнопки -->
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                        <a href="/" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-house me-2"></i>
                            На головну
                        </a>
                        <a href="/catalog" class="btn btn-dark rounded-pill px-4">
                            <i class="bi bi-shop me-2"></i>
                            Продовжити покупки
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/../../includes/footer.php';
?>
