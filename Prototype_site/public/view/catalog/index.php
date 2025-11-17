<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

// Розпаковуємо структуру пагінації продуктів
$items   = $products['items']   ?? [];
$page    = (int)($products['page']    ?? 1);
$perPage = (int)($products['perPage'] ?? 12);
$total   = (int)($products['total']   ?? 0);
$pages   = (int)($products['pages']   ?? 1);

$filters          = $filters ?? [];
$currentQuery     = trim((string)($filters['q'] ?? ''));
$currentCategory  = (string)($filters['category_id'] ?? '');
$currentBrand     = (string)($filters['brand_id'] ?? '');
$currentInStock   = !empty($filters['in_stock']) ? '1' : '';
$currentSort      = (string)($filters['sort'] ?? '');
$currentMinPrice  = (string)($filters['price_min'] ?? '');
$currentMaxPrice  = (string)($filters['price_max'] ?? '');

// Сортування
$sortLabels = [
    ''           => 'За замовчуванням',
    'price_asc'  => 'Ціна: від дешевих до дорогих',
    'price_desc' => 'Ціна: від дорогих до дорогих',
    'name_asc'   => 'Назва: A–Z',
    'name_desc'  => 'Назва: Z–A',
    'newest'     => 'Нові надходження',
];
$sortKey          = $currentSort;
$currentSortLabel = $sortLabels[$sortKey] ?? $sortKey;

// Базові параметри для побудови URL (пагінація / чіпси)
$baseParams = [
    'q'           => $currentQuery     ?: null,
    'category_id' => $currentCategory  ?: null,
    'brand_id'    => $currentBrand     ?: null,
    'in_stock'    => $currentInStock   ?: null,
    'price_min'   => $currentMinPrice  ?: null,
    'price_max'   => $currentMaxPrice  ?: null,
    'sort'        => $currentSort      ?: null,
];
$baseParams = array_filter($baseParams, static fn($v) => $v !== null && $v !== '');

// Хелпер для побудови URL з параметрами
$buildQuery = static function (array $params): string {
    $query = http_build_query(array_filter($params, static function ($v) {
        return $v !== '' && $v !== null;
    }));
    return $query;
};

$buildPageUrl = static function (int $page, array $baseParams, callable $buildQuery): string {
    $params         = $baseParams;
    $params['page'] = $page;
    $query          = $buildQuery($params);

    return $query ? ('/catalog?' . $query) : '/catalog?page=' . $page;
};

// Чіпси активних фільтрів
$chips = [];
if ($currentQuery !== '') {
    $chips['q'] = 'Пошук: ' . $currentQuery;
}
if ($currentCategory !== '') {
    $chips['category_id'] = 'Категорія';
}
if ($currentBrand !== '') {
    $chips['brand_id'] = 'Бренд запчастини';
}
if ($currentInStock === '1') {
    $chips['in_stock'] = 'Тільки в наявності';
}
if ($currentMinPrice !== '' || $currentMaxPrice !== '') {
    $chips['price'] = 'Ціна: ' . ($currentMinPrice !== '' ? $currentMinPrice : '0') . '–' . ($currentMaxPrice !== '' ? $currentMaxPrice : '∞');
}
if ($currentSort !== '') {
    $chips['sort'] = $currentSortLabel;
}
?>

<section class="py-5 bg-body-tertiary">
    <div class="container" style="max-width: 1200px;">
        <div class="row gy-4">

            <!-- Заголовок каталогу -->
            <div class="col-12">
                <p class="text-uppercase text-muted small mb-1">Каталог</p>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Каталог автозапчастин</h1>
                        <p class="text-muted small mb-0">
                            Знайдіть потрібну деталь за назвою, категорією, брендом або ціною.
                        </p>
                    </div>

                    <div class="text-end">
                        <p class="small text-muted mb-1">
                            Показано:
                            <span class="fw-semibold"><?= count($items) ?></span>
                            із <?= $total ?>
                        </p>

                        <?php if (!empty($chips)): ?>
                            <div class="d-flex flex-wrap gap-1 justify-content-end">
                                <?php foreach ($chips as $key => $label): ?>
                                    <?php
                                    $params = $baseParams;
                                    unset($params[$key], $params['page']);
                                    $qs = $buildQuery($params);
                                    $href = $qs ? '/catalog?' . $qs : '/catalog';
                                    ?>
                                    <a href="<?= $href ?>"
                                       class="badge rounded-pill text-bg-light border text-decoration-none">
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                        <span class="ms-1">&times;</span>
                                    </a>
                                <?php endforeach; ?>
                                <a href="/catalog"
                                   class="badge rounded-pill text-bg-light border text-decoration-none">
                                    Очистити все
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Фільтри -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h2 class="h6 fw-semibold mb-3">Фільтри по товарах</h2>

                        <form method="get" action="/catalog" class="vstack gap-3">
                            <div>
                                <label class="form-label small text-muted">Назва або артикул</label>
                                <input type="text"
                                       name="q"
                                       class="form-control"
                                       value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>"
                                       placeholder="Наприклад, передні гальмівні колодки">
                            </div>

                            <div>
                                <label class="form-label small text-muted">Категорія</label>
                                <select name="category_id" class="form-select form-select-sm">
                                    <option value="">Усі категорії</option>
                                    <?php foreach ($categories as $category): ?>
                                        <?php
                                        $catId   = (string)($category['id'] ?? '');
                                        $catName = htmlspecialchars($category['name'] ?? 'Категорія', ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <option value="<?= $catId ?>" <?= $catId === $currentCategory ? 'selected' : '' ?>>
                                            <?= $catName ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="form-label small text-muted">Бренд запчастини</label>
                                <select name="brand_id" class="form-select form-select-sm">
                                    <option value="">Усі бренди</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <?php
                                        $bid   = (string)($brand['id'] ?? '');
                                        $bname = htmlspecialchars($brand['name'] ?? 'Бренд', ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <option value="<?= $bid ?>" <?= $bid === $currentBrand ? 'selected' : '' ?>>
                                            <?= $bname ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted">Ціна від, ₴</label>
                                    <input type="number"
                                           name="price_min"
                                           value="<?= htmlspecialchars($currentMinPrice, ENT_QUOTES, 'UTF-8') ?>"
                                           class="form-control form-control-sm"
                                           min="0" step="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted">Ціна до, ₴</label>
                                    <input type="number"
                                           name="price_max"
                                           value="<?= htmlspecialchars($currentMaxPrice, ENT_QUOTES, 'UTF-8') ?>"
                                           class="form-control form-control-sm"
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

                            <button type="submit" class="btn btn-dark w-100 rounded-pill">
                                Застосувати
                            </button>
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
            <div class="col-12 col-md-8 col-lg-9">
                <?php if (!empty($items)): ?>
                    <div class="row g-3 g-lg-4">
                        <?php foreach ($items as $product): ?>
                            <?php
                            $name       = htmlspecialchars($product['name'] ?? 'Товар', ENT_QUOTES, 'UTF-8');
                            $slug       = htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $category   = htmlspecialchars($product['category_name'] ?? 'Категорія', ENT_QUOTES, 'UTF-8');
                            $brandName  = htmlspecialchars($product['brand_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $price      = isset($product['price']) ? (float)$product['price'] : null;
                            $stock      = (int)($product['stock'] ?? 0);
                            ?>
                            <div class="col-6 col-lg-4 col-xl-3">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <a href="/product/<?= $slug ?>" class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted text-decoration-none rounded-top">
                                        <i class="bi bi-image fs-3" aria-hidden="true"></i>
                                    </a>
                                    <div class="card-body d-flex flex-column p-3">
                                        <p class="small text-muted mb-1"><?= $category ?></p>
                                        <h3 class="h6 fw-semibold mb-1 text-truncate" title="<?= $name ?>"><?= $name ?></h3>
                                        <?php if ($brandName !== ''): ?>
                                            <p class="small text-muted mb-2"><?= $brandName ?></p>
                                        <?php endif; ?>

                                        <?php if ($price !== null): ?>
                                            <p class="fw-bold fs-5 mb-2">
                                                <?= number_format($price, 2, '.', ' ') ?> ₴
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted small mb-2">Ціну уточнюйте</p>
                                        <?php endif; ?>

                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="badge <?= $stock > 0 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                                <?= $stock > 0 ? 'В наявності' : 'Під замовлення' ?>
                                            </span>
                                        </div>

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
                        <nav class="mt-4" aria-label="Пагінація каталогу">
                            <div class="d-flex justify-content-center">
                                <ul class="pagination pagination-sm mb-0">
                                    <?php
                                    $prevPage = max(1, $page - 1);
                                    $nextPage = min($pages, $page + 1);
                                    ?>
                                    <li class="page-item mx-1 <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $page <= 1 ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="<?= $buildPageUrl($prevPage, $baseParams, $buildQuery) ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                                        <li class="page-item mx-1 <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link border-0 rounded-pill px-3 py-2 <?= $i === $page ? 'bg-dark text-white' : 'bg-white text-dark' ?>"
                                               href="<?= $buildPageUrl($i, $baseParams, $buildQuery) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item mx-1 <?= $page >= $pages ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $page >= $pages ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="<?= $buildPageUrl($nextPage, $baseParams, $buildQuery) ?>">
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
                                <h3 class="h6 fw-semibold mb-1">За вибраними критеріями товари не знайдені</h3>
                                <p class="text-muted small mb-0">
                                    Спробуйте змінити параметри пошуку або очистити фільтри.
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
