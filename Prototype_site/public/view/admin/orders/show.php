<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$csrf = csrf_token();
$statuses = ['new' => 'Нове', 'processing' => 'Обробка', 'shipped' => 'Відправлено', 'completed' => 'Виконано', 'cancelled' => 'Скасовано'];
$statusColors = ['new' => 'info', 'processing' => 'warning', 'shipped' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
?>

<style>
    :root { --sp-md: 1.5rem; --card-radius: 14px; --muted: #6c757d; }
    .admin-card { background: #fff; border-radius: var(--card-radius); box-shadow: 0 6px 18px rgba(21, 28, 64, 0.06); border: 1px solid rgba(16, 24, 40, 0.03); }
    .p-4 { padding: var(--sp-md) !important; }
    .display-6 { font-weight: 700; }
    .breadcrumb .breadcrumb-item { color: var(--muted); }
    .breadcrumb { background: transparent; padding: 0; }
    .form-control, .form-select { height: calc(var(--sp-md) * 2.2); border-radius: 10px; border: 1px solid rgba(16, 24, 40, 0.1); }
    .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1); }
    .status-chip { border-radius: 999px; padding: .5rem 1rem; font-weight: 600; font-size: 0.875rem; }
</style>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item"><a href="/admin/orders" class="text-muted text-decoration-none">Замовлення</a></li>
                <li class="breadcrumb-item active" aria-current="page">№<?= (int)$order['id'] ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0">Замовлення №<?= (int)$order['id'] ?></h1>
                <p class="text-muted small mb-0">Детальна інформація про замовлення</p>
            </div>
            <a href="/admin/orders" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-left"></i> Назад</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="admin-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Інформація про замовлення</h5>
                        <span class="status-chip bg-<?= htmlspecialchars($statusColors[$order['status']] ?? 'secondary', ENT_QUOTES) ?> text-white">
                            <?= htmlspecialchars($statuses[$order['status']] ?? $order['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <?php
                    // Customer details: prefer registered user info, fall back to guest fields
                    $customerName = '—';
                    $customerEmail = '';
                    $customerPhone = '';
                    if (!empty($order['user']) && is_array($order['user'])) {
                        $customerName = htmlspecialchars($order['user']['login'] ?? ($order['user']['first_name'] ?? '—'), ENT_QUOTES, 'UTF-8');
                        $customerEmail = htmlspecialchars($order['user']['email'] ?? '', ENT_QUOTES, 'UTF-8');
                        $customerPhone = htmlspecialchars($order['user']['phone'] ?? '', ENT_QUOTES, 'UTF-8');
                    } else {
                        if (!empty($order['guest_name'])) $customerName = htmlspecialchars($order['guest_name'], ENT_QUOTES, 'UTF-8');
                        if (!empty($order['guest_email'])) $customerEmail = htmlspecialchars($order['guest_email'], ENT_QUOTES, 'UTF-8');
                        if (!empty($order['guest_phone'])) $customerPhone = htmlspecialchars($order['guest_phone'], ENT_QUOTES, 'UTF-8');
                    }
                    ?>

                    <div class="mb-3">
                        <label class="form-label small">Покупець</label>
                        <p class="mb-0"><?= $customerName ?></p>
                    </div>
                    <?php if ($customerEmail): ?>
                        <div class="mb-3">
                            <label class="form-label small">Email</label>
                            <p class="mb-0"><a href="mailto:<?= $customerEmail ?>"><?= $customerEmail ?></a></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($customerPhone): ?>
                        <div class="mb-3">
                            <label class="form-label small">Телефон</label>
                            <p class="mb-0"><a href="tel:<?= $customerPhone ?>"><?= $customerPhone ?></a></p>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label small">Сума замовлення</label>
                        <p class="h5 text-success mb-0"><?= number_format($order['total'], 2, '.', ' ') ?> ₴</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Метод доставки</label>
                        <p class="mb-0"><?= htmlspecialchars(delivery_method_label($order['delivery_method'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Адреса доставки</label>
                        <p class="mb-0"><?= htmlspecialchars($order['delivery_address'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                        <div class="mb-0">
                            <label class="form-label small">Коментар</label>
                            <p class="mb-0"><?= htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <form action="/admin/orders/<?= (int)$order['id'] ?>/status" method="POST" class="admin-card p-4 mt-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                    <label for="status" class="form-label">Оновити статус</label>
                    <div class="d-flex gap-2">
                        <select id="status" name="status" class="form-select">
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary" type="submit">Зберегти</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="admin-card p-4">
                    <h5 class="mb-4">Товари в замовленні</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Назва</th>
                                    <th class="text-end">Кількість</th>
                                    <th class="text-end">Ціна</th>
                                    <th class="text-end">Сума</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['name_snapshot'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-end"><?= (int)$item['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($item['price'], 2, '.', ' ') ?> ₴</td>
                                        <td class="text-end fw-semibold"><?= number_format($item['price'] * $item['quantity'], 2, '.', ' ') ?> ₴</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>ВСЬОГО:</strong></td>
                                    <td class="text-end"><strong class="text-success h5"><?= number_format($order['total'], 2, '.', ' ') ?> ₴</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
