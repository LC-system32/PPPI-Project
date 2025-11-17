<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$counts = $counts ?? [];
$recentOrders = $recentOrders ?? [];

// Status badge classes for recent orders
$statusClasses = [
    'new' => 'bg-warning text-dark',
    'processing' => 'bg-info text-white',
    'shipped' => 'bg-primary text-white',
    'completed' => 'bg-success text-white',
    'cancelled' => 'bg-danger text-white',
];
?>

<section class="py-5">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1">Адмін-панель</p>
                <h1 class="fw-bold mb-0">Панель керування</h1>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <?php
            $statCards = [
                ['title' => 'Товари', 'count' => (int) ($counts['products'] ?? 0), 'icon' => 'bi-box-seam', 'href' => '/admin/products'],
                ['title' => 'Категорії', 'count' => (int) ($counts['categories'] ?? 0), 'icon' => 'bi-tags', 'href' => '/admin/categories'],
                ['title' => 'Бренди', 'count' => (int) ($counts['brands'] ?? 0), 'icon' => 'bi-bookmarks', 'href' => '/admin/brands'],
                ['title' => 'Моделі авто', 'count' => (int) ($counts['car_models'] ?? 0), 'icon' => 'bi-gear', 'href' => '/admin/car-models'],
            ];

            foreach ($statCards as $card): ?>
                <div class="col-6 col-md-3">
                    <a href="<?= $card['href'] ?>" class="card h-100 text-decoration-none text-dark shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-muted small text-uppercase"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h6>
                                <div class="h3 mb-0 fw-bold"><?= $card['count'] ?></div>
                            </div>
                            <div class="text-warning fs-2"><i class="bi <?= $card['icon'] ?>"></i></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row g-3 mb-4">
            <?php
            $statCards2 = [
                ['title' => 'Замовлення', 'count' => (int) ($counts['orders'] ?? 0), 'icon' => 'bi-receipt', 'href' => '/admin/orders'],
                ['title' => 'Повернення', 'count' => (int) ($counts['returns'] ?? 0), 'icon' => 'bi-arrow-counterclockwise', 'href' => '/admin/returns'],
                ['title' => 'Відгуки', 'count' => (int) ($counts['reviews_pending'] ?? 0), 'icon' => 'bi-chat-left-text', 'href' => '/admin/reviews'],
                ['title' => 'Користувачі', 'count' => (int) ($counts['users'] ?? 0), 'icon' => 'bi-people', 'href' => '/admin/users'],
            ];

            foreach ($statCards2 as $card): ?>
                <div class="col-6 col-md-3">
                    <a href="<?= $card['href'] ?>" class="card h-100 text-decoration-none text-dark shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-muted small text-uppercase"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h6>
                                <div class="h3 mb-0 fw-bold"><?= $card['count'] ?></div>
                                <?php if ($card['title'] === 'Швидкі дії'): ?>
                                    <div class="small text-muted mt-1">Створити / переглянути ресурси</div>
                                <?php endif; ?>
                            </div>
                            <div class="text-warning fs-2"><i class="bi <?= $card['icon'] ?>"></i></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Останні замовлення</h5>
                    <div class="text-muted small">Показано <?= count($recentOrders) ?> записів</div>
                </div>

                <?php if (!empty($recentOrders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:80px;">ID</th>
                                    <th>Користувач</th>
                                    <th style="width:140px;">Сума</th>
                                    <th style="width:160px;">Статус</th>
                                    <th style="width:200px;">Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $o):
                                    $statusKey = strtolower((string) ($o['status'] ?? 'new'));
                                    $badgeClass = $statusClasses[$statusKey] ?? 'bg-secondary text-white';
                                    // user label resolution
                                    $userLabel = '-';
                                    if (!empty($o['login'])) {
                                        $userLabel = htmlspecialchars($o['login'], ENT_QUOTES, 'UTF-8');
                                    } elseif (!empty($o['user_login'])) {
                                        $userLabel = htmlspecialchars($o['user_login'], ENT_QUOTES, 'UTF-8');
                                    } elseif (!empty($o['user']) && is_array($o['user']) && !empty($o['user']['login'])) {
                                        $userLabel = htmlspecialchars($o['user']['login'], ENT_QUOTES, 'UTF-8');
                                    } elseif (!empty($o['email'])) {
                                        $userLabel = htmlspecialchars($o['email'], ENT_QUOTES, 'UTF-8');
                                    } elseif (!empty($o['user_id'])) {
                                        $userLabel = (int) $o['user_id'];
                                    }
                                ?>
                                    <tr>
                                        <td class="fw-semibold">#<?= (int) $o['id'] ?></td>
                                        <td><?= $userLabel ?></td>
                                        <td><?= number_format($o['total'] ?? 0, 2, '.', ' ') ?> ₴</td>
                                        <td><span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2"><?= htmlspecialchars(ucfirst($statusKey), ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td class="text-muted"><?= !empty($o['created_at']) ? htmlspecialchars(date('d.m.Y H:i', strtotime($o['created_at'])), ENT_QUOTES, 'UTF-8') : '' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-0">Немає останніх замовлень.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
