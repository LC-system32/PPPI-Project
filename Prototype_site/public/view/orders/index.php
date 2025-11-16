<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1">Мої замовлення</p>
                <h1 class="fw-bold mb-0">Історія покупок</h1>
            </div>
            <a href="/catalog" class="btn btn-outline-dark">У каталог</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($orders): ?>
            <div class="accordion" id="ordersAccordion">
                <?php foreach ($orders as $index => $order): ?>
                    <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="heading<?= $index ?>">
                            <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#order<?= $index ?>">
                                <span class="me-3 fw-semibold">№<?= (int) $order['id'] ?></span>
                                <span class="me-auto text-muted"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                                <span class="badge text-bg-light me-3 text-capitalize"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="fw-bold"><?= number_format($order['total'], 2, '.', ' ') ?> ₴</span>
                            </button>
                        </h2>
                        <div id="order<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#ordersAccordion">
                            <div class="accordion-body bg-white">
                                <p class="mb-1"><strong>Доставка:</strong> <?= htmlspecialchars(delivery_method_label($order['delivery_method'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="mb-3"><strong>Адреса:</strong> <?= htmlspecialchars($order['delivery_address'], ENT_QUOTES, 'UTF-8') ?></p>
                                <h6 class="fw-semibold mb-3">Склад замовлення</h6>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <div>
                                                <p class="mb-0"><?= htmlspecialchars($item['name_snapshot'], ENT_QUOTES, 'UTF-8') ?></p>
                                                <small class="text-muted">x<?= (int) $item['quantity'] ?></small>
                                            </div>
                                            <strong><?= number_format($item['price'] * $item['quantity'], 2, '.', ' ') ?> ₴</strong>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary">У вас ще немає замовлень.</div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
