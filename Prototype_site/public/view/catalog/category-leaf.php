<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
$items   = $productsPage['items'] ?? [];
$page    = (int)($productsPage['page']  ?? 1);
$pages   = (int)($productsPage['pages'] ?? 1);
$total   = (int)($productsPage['total'] ?? 0);

$filters         = $filters ?? [];
$currentQuery    = trim((string)($filters['q'] ?? ''));
$currentInStock  = !empty($filters['in_stock']) ? '1' : '';
$currentSort     = (string)($filters['sort'] ?? '');
$currentMinPrice = (string)($filters['price_min'] ?? '');
$currentMaxPrice = (string)($filters['price_max'] ?? '');

$sortLabels = [
    ''           => 'За замовчуванням',
    'price_asc'  => 'Ціна: від дешевих до дорогих',
    'price_desc' => 'Ціна: від дорогих до дешевих',
    'name_asc'   => 'Назва: A–Z',
    'name_desc'  => 'Назва: Z–A',
    'newest'     => 'Нові надходження',
];
$sortKey          = $currentSort;
$currentSortLabel = $sortLabels[$sortKey] ?? $sortKey;

$baseParams = [
    'q'         => $currentQuery     ?: null,
    'in_stock'  => $currentInStock   ?: null,
    'price_min' => $currentMinPrice  ?: null,
    'price_max' => $currentMaxPrice  ?: null,
    'sort'      => $currentSort      ?: null,
];
$baseParams = array_filter($baseParams, static fn($v) => $v !== null && $v !== '');

$buildQuery = static function (array $params): string {
    $query = http_build_query(array_filter($params, static function ($v) {
        return $v !== '' && $v !== null;
    }));
    return $query;
};

$buildPageUrl = static function (int $page, string $slug, array $baseParams, callable $buildQuery): string {
    $params         = $baseParams;
    $params['page'] = $page;
    $query          = $buildQuery($params);
    return $query ? ("/categories/{$slug}?" . $query) : "/categories/{$slug}?page={$page}";
};
?>
<!-- STATS + PRODUCTS / FILTERS -->
<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row g-3 mb-3">
            <!-- Фільтри -->
            <div class="col-12 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h2 class="h6 fw-semibold mb-3">Фільтри по товарах</h2>

                        <form method="get" class="vstack gap-3">
                            <div>
                                <label class="form-label small text-muted">Назва або артикул</label>
                                <input type="text"
                                       name="q"
                                       class="form-control form-control-sm"
                                       value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>"
                                       placeholder="Наприклад, передні колодки">
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted">Ціна від, ₴</label>
                                    <input type="number"
                                           name="price_min"
                                           class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($currentMinPrice, ENT_QUOTES, 'UTF-8') ?>"
                                           min="0" step="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted">Ціна до, ₴</label>
                                    <input type="number"
                                           name="price_max"
                                           class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($currentMaxPrice, ENT_QUOTES, 'UTF-8') ?>"
                                           min="0" step="1">
                                </div>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="1"
                                       id="in_stock"
                                       name="in_stock"
                                    <?= $currentInStock === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="in_stock">
                                    Показувати тільки в наявності
                                </label>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 btn-sm rounded-pill">
                                Застосувати
                            </button>

                            <?php if ($currentQuery || $currentInStock || $currentMinPrice !== '' || $currentMaxPrice !== '' || $currentSort): ?>
                                <a href="/categories/<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                                    Очистити фільтри
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Сортування</h3>
                        <form method="get" class="d-flex flex-column gap-2">
                            <?php foreach ($baseParams as $key => $val): ?>
                                <?php if ($key === 'sort') continue; ?>
                                <input type="hidden"
                                       name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                                       value="<?= htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>

                            <select name="sort"
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                <?php foreach ($sortLabels as $key => $label): ?>
                                    <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $sortKey === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Список товарів -->
            <div class="col-12 col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Товари категорії</p>
                        <h2 class="h4 fw-semibold mb-0">
                            Запчастини у розділі
                            “<?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>”
                        </h2>
                    </div>
                    <span class="text-muted small">Знайдено позицій: <?= $total ?></span>
                </div>

                <?php if (!empty($items)): ?>
                    <div class="row g-3 g-lg-4 mb-4">
                        <?php foreach ($items as $product): ?>
                            <?php
                            $name       = htmlspecialchars($product['name'] ?? 'Товар', ENT_QUOTES, 'UTF-8');
                            $slug       = htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $brandName  = htmlspecialchars($product['brand_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $catName    = htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $price      = isset($product['price']) ? (float)$product['price'] : null;
                            $stock      = (int)($product['stock'] ?? 0);
                            ?>
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <a href="/product/<?= $slug ?>" class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted text-decoration-none rounded-top">
                                        <i class="bi bi-image fs-3" aria-hidden="true"></i>
                                    </a>
                                    <div class="card-body d-flex flex-column p-3">
                                        <?php if ($brandName !== ''): ?>
                                            <p class="text-uppercase text-muted small mb-1"><?= $brandName ?></p>
                                        <?php endif; ?>
                                        <h3 class="h6 fw-semibold mb-1 text-truncate" title="<?= $name ?>">
                                            <?= $name ?>
                                        </h3>
                                        <?php if ($catName !== ''): ?>
                                            <p class="small text-muted mb-2"><?= $catName ?></p>
                                        <?php endif; ?>

                                        <?php if ($price !== null): ?>
                                            <p class="fw-bold fs-5 mb-2">
                                                <?= number_format($price, 2, '.', ' ') ?> ₴
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted small mb-2">Ціну уточнюйте</p>
                                        <?php endif; ?>

                                        <p class="text-muted small mb-3">
                                            <?= $stock > 0 ? 'В наявності на складі' : 'Під замовлення' ?>
                                        </p>

                                        <div class="mt-auto d-grid">
                                            <a href="/product/<?= $slug ?>" class="btn btn-outline-dark btn-sm rounded-pill">
                                                Деталі
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($pages > 1): ?>
                        <nav class="mt-3" aria-label="Пагінація товарів">
                            <div class="d-flex justify-content-center">
                                <ul class="pagination pagination-sm mb-0">
                                    <?php
                                    $prev = max(1, $page - 1);
                                    $next = min($pages, $page + 1);
                                    $slug = htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <li class="page-item mx-1 <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $page <= 1 ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="<?= $buildPageUrl($prev, $slug, $baseParams, $buildQuery) ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                                        <li class="page-item mx-1 <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link border-0 rounded-pill px-3 py-2 <?= $i === $page ? 'bg-dark text-white' : 'bg-white text-dark' ?>"
                                               href="<?= $buildPageUrl($i, $slug, $baseParams, $buildQuery) ?>">
                                            <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item mx-1 <?= $page >= $pages ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $page >= $pages ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="<?= $buildPageUrl($next, $slug, $baseParams, $buildQuery) ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-info-circle fs-3 text-warning"></i>
            </div>
            <div>
                <h3 class="h6 fw-semibold mb-1">Товари у цій підкатегорії тимчасово відсутні</h3>
                <p class="text-muted small mb-0">
                    Спробуйте повернутися до батьківської категорії або скористайтесь пошуком у каталозі.
                </p>
            </div>
        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

