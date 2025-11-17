<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
$items   = $products['items']   ?? [];
$page    = $products['page']    ?? 1;
$perPage = $products['perPage'] ?? 12;
$total   = $products['total']   ?? 0;
$pages   = $products['pages']   ?? 1;

$brandName = $brand['name'] ?? '';
$modelName = $carModel['name'] ?? '';
$gen       = $carModel['generation'] ?? '';
$from      = $carModel['year_from'] ?? null;
$to        = $carModel['year_to'] ?? null;

$years = '';
if ($from || $to) {
    $years = ($from ?: '…') . '–' . ($to ?: '…');
}

// Фільтри з контролера (за замовчуванням порожній масив)
$filters = $filters ?? [];
$currentQuery     = $filters['q'] ?? '';
$currentMinPrice  = $filters['price_min'] ?? '';
$currentMaxPrice  = $filters['price_max'] ?? '';
$currentInStock   = $filters['in_stock'] ?? '';
$currentSort      = $filters['sort'] ?? 'default';

// Будуємо URL для пагінації
function buildUrl($page, $params) {
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row g-3 mb-3">

            <!-- Фільтри -->
            <div class="col-12 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">

                        <h2 class="h6 fw-semibold mb-3">Фільтри</h2>

                        <form method="get" class="vstack gap-3">

                            <!-- Назва -->
                            <div>
                                <label class="form-label small text-muted">Назва або артикул</label>
                                <input type="text"
                                    name="q"
                                    class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($currentQuery) ?>"
                                >
                            </div>

                            <!-- Ціна -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted">Ціна від, ₴</label>
                                    <input type="number"
                                           name="price_min"
                                           value="<?= htmlspecialchars($currentMinPrice) ?>"
                                           class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted">Ціна до, ₴</label>
                                    <input type="number"
                                           name="price_max"
                                           value="<?= htmlspecialchars($currentMaxPrice) ?>"
                                           class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- в наявності -->
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                       name="in_stock" value="1"
                                       <?= $currentInStock ? 'checked' : '' ?>>
                                <label class="form-check-label small">Показувати тільки в наявності</label>
                            </div>

                            <button class="btn btn-dark w-100 btn-sm rounded-pill">Застосувати</button>

                            <?php if ($currentQuery || $currentMinPrice || $currentMaxPrice || $currentInStock || $currentSort !== 'default'): ?>
                                <a href="?clear=1" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                                    Очистити фільтри
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Сортування -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">

                        <h3 class="h6 fw-semibold mb-3">Сортування</h3>

                        <form method="get">
                            <?php
                            // Зберігаємо всі поточні фільтри окрім 'sort'
                            $params = [];
                            if ($currentQuery) $params['q'] = $currentQuery;
                            if ($currentMinPrice) $params['price_min'] = $currentMinPrice;
                            if ($currentMaxPrice) $params['price_max'] = $currentMaxPrice;
                            if ($currentInStock) $params['in_stock'] = $currentInStock;
                            ?>

                            <?php foreach ($params as $k => $v): ?>
                                <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                            <?php endforeach; ?>

                            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="default"     <?= $currentSort === 'default'     ? 'selected' : '' ?>>За замовчуванням</option>
                                <option value="price_asc"   <?= $currentSort === 'price_asc'   ? 'selected' : '' ?>>Спочатку дешевші</option>
                                <option value="price_desc"  <?= $currentSort === 'price_desc'  ? 'selected' : '' ?>>Спочатку дорожчі</option>
                                <option value="name_asc"    <?= $currentSort === 'name_asc'    ? 'selected' : '' ?>>Назва A→Z</option>
                                <option value="name_desc"   <?= $currentSort === 'name_desc'   ? 'selected' : '' ?>>Назва Z→A</option>
                            </select>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Товари -->
            <div class="col-12 col-lg-9">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Запчастини</p>
                        <h2 class="h4 fw-semibold mb-0">
                            Для <?= htmlspecialchars($brandName) ?>
                            <?= htmlspecialchars($modelName) ?>
                            <?= $gen ? '(' . htmlspecialchars($gen) . ')' : '' ?>
                            <?= $years ? ' — ' . htmlspecialchars($years) : '' ?>
                        </h2>
                    </div>
                    <span class="text-muted small">Знайдено позицій: <?= $total ?></span>
                </div>

                <?php if ($items): ?>

                    <div class="row g-3 g-lg-4 mb-4">
                        <?php foreach ($items as $product): ?>
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card border-0 shadow-sm rounded-4 h-100">

                                    <a href="/product/<?= $product['slug'] ?>"
                                       class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted text-decoration-none rounded-top">
                                        <i class="bi bi-image fs-3"></i>
                                    </a>

                                    <div class="card-body d-flex flex-column p-3">
                                        <p class="text-uppercase text-muted small mb-1">
                                            <?= htmlspecialchars($product['brand_name']) ?>
                                        </p>

                                        <h3 class="h6 fw-semibold mb-1 text-truncate">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </h3>

                                        <p class="small text-muted mb-2"><?= htmlspecialchars($product['category_name']) ?></p>

                                        <p class="fw-bold fs-5 mb-2">
                                            <?= number_format((float)$product['price'], 2, '.', ' ') ?> ₴
                                        </p>

                                        <p class="text-muted small mb-3">
                                            <?= $product['stock'] > 0 ? 'В наявності' : 'Під замовлення' ?>
                                        </p>

                                        <div class="mt-auto d-grid">
                                            <a href="/product/<?= $product['slug'] ?>"
                                               class="btn btn-outline-dark btn-sm rounded-pill">Деталі</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Пагінація -->
                    <?php if ($pages > 1): ?>
                        <nav class="mt-4" aria-label="Пагінація по товарах">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <p class="mb-0 small text-muted">
                                    Сторінка <span class="fw-semibold"><?= $page ?></span> з <?= $pages ?>
                                </p>

                                <ul class="pagination justify-content-center mb-0">
                                    <?php $p = $page - 1; ?>
                                    <li class="page-item mx-1 <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm
                                                   <?= $page <= 1 ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="<?= buildUrl($p, $filters) ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php
                                    // Інтелектуальна пагінація
                                    $pagesToShow = [];
                                    $deltaPages = 2;

                                    // Перша сторінка
                                    $pagesToShow[] = 1;

                                    // Сторінки навколо поточної
                                    for ($i = max(2, $page - $deltaPages); $i <= min($pages - 1, $page + $deltaPages); $i++) {
                                        $pagesToShow[] = $i;
                                    }

                                    // Остання сторінка
                                    if ($pages > 1) {
                                        $pagesToShow[] = $pages;
                                    }

                                    $pagesToShow = array_values(array_unique($pagesToShow));
                                    sort($pagesToShow);

                                    $lastDisplayed = 0;
                                    foreach ($pagesToShow as $pageNum):
                                        if ($pageNum - $lastDisplayed > 1): ?>
                                            <li class="page-item mx-1">
                                                <span class="page-link border-0 px-2 py-2 text-muted">...</span>
                                            </li>
                                        <?php endif;
                                        $isActive = ($pageNum === (int)$page);
                                        ?>
                                        <li class="page-item mx-1 <?= $isActive ? 'active' : '' ?>">
                                            <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm fw-semibold
                                                       <?= $isActive ? 'bg-dark text-white' : 'bg-white text-dark' ?>"
                                               href="<?= buildUrl($pageNum, $filters) ?>">
                                                <?= $pageNum ?>
                                            </a>
                                        </li>
                                        <?php $lastDisplayed = $pageNum;
                                    endforeach;
                                    ?>

                                    <?php $p = $page + 1; ?>
                                    <li class="page-item mx-1 <?= $page >= $pages ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm
                                                   <?= $page >= $pages ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="<?= buildUrl($p, $filters) ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>

                    <div class="alert alert-warning">
                        Товарів не знайдено.
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>