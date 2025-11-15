<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$csrf = csrf_token();
$statuses = ['new' => 'Нові', 'processing' => 'В обробці', 'shipped' => 'Відправлені', 'completed' => 'Виконані', 'cancelled' => 'Скасовані'];
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
            <div>
                <p class="text-uppercase text-muted mb-1">Адмін-панель</p>
                <h1 class="fw-bold mb-0">Замовлення</h1>
            </div>
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select">
                    <option value="">Усі статуси</option>
                    <?php foreach ($statuses as $value => $label): ?>
                        <option value="<?= $value ?>" <?= (($status ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-dark" type="submit">Фільтрувати</button>
            </form>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="table-responsive shadow-sm rounded-4">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Користувач</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="fw-semibold">№<?= (int) $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['user_id'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format($order['total'], 2, '.', ' ') ?> ₴</td>
                                <td class="text-capitalize"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                <td class="text-end">
                                    <a href="/admin/orders/<?= (int) $order['id'] ?>" class="btn btn-outline-dark btn-sm">Деталі</a>
                                    <form action="/admin/orders/<?= (int) $order['id'] ?>/status" method="POST" class="d-inline-flex align-items-center ms-2">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <select name="status" class="form-select form-select-sm">
                                            <?php foreach (array_keys($statuses) as $value): ?>
                                                <option value="<?= $value ?>" <?= ($order['status'] === $value) ? 'selected' : '' ?>><?= $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-sm btn-outline-secondary ms-2" type="submit">OK</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Замовлень поки немає.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
