<div class="card p-4 rounded-4 shadow-sm border-0">

    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <p class="text-uppercase text-muted small mb-1">Замовлення</p>

            <h2 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-warning"></i> Останні операції
            </h2>

            <div class="small text-muted">
                Відстежуйте статус доставок і керуйте покупками.
            </div>
        </div>
    </div>

    <div class="border-bottom mb-3 opacity-25"></div>

    <?php
    /* --- Випадкові описи товарів --- */
    $productDescriptions = [
        "Гальмівні колодки Brembo, масло Motul 5W30, фільтр Mann",
        "Масляний і паливний фільтри, прокладка піддона",
        "Комплект ГРМ Contitech + ролики",
        "Антифриз G12+, термостат Mahle",
        "Стойки стабілізатора, амортизатори Sachs",
        "Набір інструментів + домкрат автомобільний",
        "Свічки Bosch, котушка запалювання Delphi",
        "Гальмівні диски ATE (2 шт.)",
        "Масло Castrol Edge 5W40 (4 л) + фільтр",
        "Повітряний фільтр Mahle, салонний фільтр Mann",
    ];

    /* --- Список замовлень --- */
    $ordersAll = [
        ['number' => '#AP-2025-1033', 'date' => '18.10.2025', 'items' => '2', 'status' => 'Доставлено', 'total' => '2 190 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-1001', 'date' => '05.10.2025', 'items' => '4', 'status' => 'Скасовано', 'total' => '6 540 ₴'],
        ['number' => '#AP-2025-0995', 'date' => '03.10.2025', 'items' => '1', 'status' => 'В дорозі', 'total' => '410 ₴'],
        ['number' => '#AP-2025-0980', 'date' => '30.09.2025', 'items' => '2', 'status' => 'Доставлено', 'total' => '1 890 ₴'],
        ['number' => '#AP-2025-0970', 'date' => '29.09.2025', 'items' => '3', 'status' => 'Обробка', 'total' => '5 000 ₴'],
        ['number' => '#AP-2025-0960', 'date' => '26.09.2025', 'items' => '1', 'status' => 'Скасовано', 'total' => '700 ₴'],
        ['number' => '#AP-2025-0950', 'date' => '25.09.2025', 'items' => '6', 'status' => 'Доставлено', 'total' => '7 590 ₴'],
    ];

    /* --- Додаємо випадковий опис --- */
    foreach ($ordersAll as $i => $o)
        $ordersAll[$i]['description'] = $productDescriptions[array_rand($productDescriptions)];

    /* --- Пагінація --- */
    $perPage = 10;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    $totalOrders = count($ordersAll);
    $totalPages = ceil($totalOrders / $perPage);

    $start = ($page - 1) * $perPage;
    $orders = array_slice($ordersAll, $start, $perPage);

    /* --- Класи статусів --- */
    $statusClasses = [
        'Доставлено' => 'bg-success',
        'В дорозі' => 'bg-warning text-dark',
        'Обробка' => 'bg-info text-dark',
        'Скасовано' => 'bg-danger'
    ];
    ?>

    <!-- ТАБЛИЦЯ -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr class="small text-uppercase text-muted">
                    <th class="fw-semibold">Номер</th>
                    <th class="fw-semibold">Дата</th>
                    <th class="fw-semibold">Товарів</th>
                    <th class="fw-semibold">Статус</th>
                    <th class="text-end fw-semibold">Сума</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($orders as $i => $order): ?>
                    <?php
                    $uid = "order_" . $i . "_" . rand(1000, 9999);
                    ?>

                    <tr data-bs-toggle="collapse" data-bs-target="#<?= $uid ?>" style="cursor:pointer;">
                        <td class="fw-semibold"><?= $order['number'] ?></td>
                        <td class="text-muted"><?= $order['date'] ?></td>
                        <td><?= $order['items'] ?></td>
                        <td>
                            <span class="fw-semibold">
                                <?= $order['status'] ?>
                            </span>
                        </td>

                        <td class="text-end fw-semibold"><?= $order['total'] ?></td>
                    </tr>


                    <!-- Опис -->
                    <tr>
                        <td colspan="5" class="p-0">
                            <div id="<?= $uid ?>" class="collapse">
                                <div class="p-3 bg-light border-bottom">
                                    <p class="fw-semibold mb-1">Опис замовлення:</p>
                                    <p class="text-muted mb-0"><?= $order['description'] ?></p>
                                </div>
                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination pagination-sm justify-content-center gap-1 ">

                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link border-0 bg-white text-dark rounded-pill px-3 py-2" href="?page=<?= $page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link border-0 rounded-pill px-3 py-2 
                        <?= $i == $page
                            ? 'bg-white text-warning border border-warning'
                            : 'bg-white text-dark' ?>"
                            href="?page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link border-0 bg-white text-dark rounded-pill px-3 py-2" href="?page=<?= $page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>

            </ul>
        </nav>
    <?php endif; ?>

</div>