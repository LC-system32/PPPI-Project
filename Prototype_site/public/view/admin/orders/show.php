<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$csrf = csrf_token();
$statuses = ['new', 'processing', 'shipped', 'completed', 'cancelled'];
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold mb-0">Замовлення №<?= (int) $order['id'] ?></h1>
            <a href="/admin/orders" class="btn btn-outline-dark">Назад</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="mb-3">Інформація</h5>
                        <p class="mb-1"><strong>Статус:</strong> <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-1"><strong>Сума:</strong> <?= number_format($order['total'], 2, '.', ' ') ?> ₴</p>
                        <p class="mb-1"><strong>Доставка:</strong> <?= htmlspecialchars($order['delivery_method'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-2"><strong>Адреса:</strong> <?= htmlspecialchars($order['delivery_address'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-0"><strong>Коментар:</strong> <?= htmlspecialchars($order['notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>

                <form action="/admin/orders/<?= (int) $order['id'] ?>/status" method="POST" class="card border-0 shadow-sm rounded-4 mt-4 p-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                    <label class="form-label">Оновити статус</label>
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-dark" type="submit">Зберегти</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="mb-3">Товари</h5>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($order['items'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($item['name_snapshot'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <small class="text-muted">x<?= (int) $item['quantity'] ?> • <?= number_format($item['price'], 2, '.', ' ') ?> ₴</small>
                                    </div>
                                    <span class="fw-bold"><?= number_format($item['price'] * $item['quantity'], 2, '.', ' ') ?> ₴</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
