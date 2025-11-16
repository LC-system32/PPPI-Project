<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
// Дані пагінації товарів
$items   = $products['items']   ?? [];
$page    = (int)($products['page']    ?? 1);
$perPage = (int)($products['perPage'] ?? 12);
$total   = (int)($products['total']   ?? 0);
$pages   = (int)($products['pages']   ?? 1);

$brandName = htmlspecialchars($brand['name'] ?? '', ENT_QUOTES, 'UTF-8');
$brandSlug = htmlspecialchars($brand['slug'] ?? '', ENT_QUOTES, 'UTF-8');
$brandLogo = $brand['logo_url'] ?? ($brand['logo'] ?? null);

// Поточні параметри фільтрації / пошуку / сортування по товарах
$currentQuery     = trim($_GET['q']         ?? '');
$currentCategory  = $_GET['category']      ?? '';
$currentInStock   = $_GET['in_stock']      ?? ''; // '1' або ''
$currentSort      = $_GET['sort']          ?? '';

$currentMinPrice  = $_GET['price_min']     ?? '';
$currentMaxPrice  = $_GET['price_max']     ?? '';

// Людські назви сортування
$sortLabels = [
    ''            => 'За замовчуванням',
    'price_asc'   => 'Ціна: від дешевих',
    'price_desc'  => 'Ціна: від дорогих',
    'name_asc'    => 'Назва A → Z',
    'name_desc'   => 'Назва Z → A',
    'newest'      => 'Спочатку нові',
];
$sortKey          = $currentSort ?? '';
$currentSortLabel = $sortLabels[$sortKey] ?? $sortKey;

// Категорії для фільтру (якщо контролер не передає окремо – збираємо з товарів)
$filterCategories = $products['categories'] ?? [];
if (empty($filterCategories)) {
    $tmp = [];
    foreach ($items as $p) {
        if (!empty($p['category_name'])) {
            $tmp[$p['category_name']] = $p['category_name'];
        }
    }
    $filterCategories = array_values($tmp);
}

// базові параметри для побудови лінків (пагінація, сортування)
$baseParams = [
    'q'         => $currentQuery    ?: null,
    'category'  => $currentCategory ?: null,
    'in_stock'  => $currentInStock  ?: null,
    'sort'      => $currentSort     ?: null,
    'price_min' => $currentMinPrice ?: null,
    'price_max' => $currentMaxPrice ?: null,
];
$baseParams = array_filter($baseParams, fn($v) => $v !== null && $v !== '');
?>

<!-- HERO / BANNER -->
<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 28%;">
        <img src="https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1600&q=80"
             class="w-100 h-100 object-fit-cover"
             alt="<?= $brandName ?>">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(8,8,10,.9), rgba(30,30,30,.6));"></div>

    <div class="container position-absolute top-50 start-50 translate-middle">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a class="text-white-50 text-decoration-none" href="/">Головна</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="text-white-50 text-decoration-none" href="/brands">Бренди</a>
                        </li>
                        <li class="breadcrumb-item active text-white" aria-current="page">
                            <?= $brandName ?>
                        </li>
                    </ol>
                </nav>

                <p class="text-uppercase text-white-50 small mb-1">Бренд</p>
                <h1 class="display-5 fw-bold mb-3">
                    <?= $brandName ?>
                </h1>
                <p class="lead text-white-50 mb-0" style="max-width: 640px;">
                    Запчастини <?= $brandName ?>: популярні позиції, актуальна наявність
                    та офіційні канали постачання. Оберіть модель авто та відфільтруйте потрібні деталі.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section class="py-5 bg-body-tertiary">
    <div class="container">

        <!-- МОДЕЛІ АВТО БРЕНДУ -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <p class="text-uppercase text-muted small mb-1">Моделі авто</p>
                <h2 class="h4 fw-semibold mb-0">
                    Моделі, для яких доступні запчастини <?= $brandName ?>
                </h2>
            </div>
        </div>

        <?php if (!empty($carModels ?? [])): ?>
            <div class="row g-2 g-md-3 mb-4">
                <?php foreach ($carModels as $cm): ?>
                    <?php
                    $fullNameParts = [];
                    $fullNameParts[] = $cm['brand'] ?? ($brand['name'] ?? '');
                    $fullNameParts[] = $cm['model'] ?? '';
                    if (!empty($cm['generation'])) {
                        $fullNameParts[] = $cm['generation'];
                    }

                    $years = '';
                    if (!empty($cm['year_from']) || !empty($cm['year_to'])) {
                        $from  = !empty($cm['year_from']) ? (int)$cm['year_from'] : null;
                        $to    = !empty($cm['year_to'])   ? (int)$cm['year_to']   : null;
                        $years = ($from ?? '...') . '–' . ($to ?? '...');
                    }

                    $fullName = trim(implode(' ', array_filter($fullNameParts)));

                    $slugSource = strtolower($fullName);
                    $modelSlug  = preg_replace('/[^a-z0-9]+/i', '-', $slugSource) ?? '';
                    $modelSlug  = trim($modelSlug, '-');
                    ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="/brand/<?= $brandSlug ?>/<?= htmlspecialchars($modelSlug, ENT_QUOTES, 'UTF-8') ?>"
                           class="text-decoration-none text-dark d-block h-100">
                            <div class="border-0 rounded-4 bg-white shadow-sm px-3 py-2 h-100 d-flex flex-column justify-content-center">
                                <div class="fw-semibold mb-1">
                                    <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php if ($years !== ''): ?>
                                    <div class="text-muted small">
                                        Роки випуску: <?= htmlspecialchars($years, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary border-0 shadow-sm mb-4">
                Для цього бренду поки немає прив’язаних моделей авто.
            </div>
        <?php endif; ?>

        <!-- ТОВАРИ БРЕНДУ: ФІЛЬТРИ + СПИСОК -->
        <div class="row mt-2">

            <!-- ЛІВИЙ СТОВПЕЦЬ: пошук + фільтри -->
            <div class="col-12 col-lg-3 mb-4">

                <!-- Пошук + категорія + наявність + ціна -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Фільтри по товарах</h3>

                        <form method="get" action="/brand/<?= $brandSlug ?>">

                            <!-- Пошук -->
                            <div class="input-group input-group-sm mb-3">
                                <span class="input-group-text bg-body border-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    type="text"
                                    name="q"
                                    value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>"
                                    class="form-control border-0"
                                    placeholder="Назва або артикул..."
                                    aria-label="Пошук по товарах">
                            </div>

                            <!-- Категорія -->
                            <label class="small text-muted mb-1" for="category">Категорія:</label>
                            <select name="category" id="category"
                                    class="form-select form-select-sm mb-3">
                                <option value="">Всі категорії</option>
                                <?php foreach ($filterCategories as $cat): ?>
                                    <?php $catVal = (string)$cat; ?>
                                    <option value="<?= htmlspecialchars($catVal, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $currentCategory === $catVal ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($catVal, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Наявність -->
                            <div class="form-check mb-3">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="in_stock"
                                       value="1"
                                       id="in_stock"
                                    <?= $currentInStock === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="in_stock">
                                    Показувати тільки в наявності
                                </label>
                            </div>

                            <!-- Ціна -->
                            <div class="border-top pt-3 mt-3">
                                <p class="small text-muted mb-2">Ціна, ₴</p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number"
                                               name="price_min"
                                               class="form-control form-control-sm"
                                               placeholder="Від"
                                               min="0"
                                               value="<?= htmlspecialchars($currentMinPrice, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number"
                                               name="price_max"
                                               class="form-control form-control-sm"
                                               placeholder="До"
                                               min="0"
                                               value="<?= htmlspecialchars($currentMaxPrice, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Зберегти сортування -->
                            <?php if ($currentSort): ?>
                                <input type="hidden" name="sort"
                                       value="<?= htmlspecialchars($currentSort, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>

                            <!-- Кнопки -->
                            <div class="d-flex justify-content-between mt-3">
                                <button type="submit" class="btn btn-dark btn-sm px-3">
                                    Застосувати
                                </button>
                                <?php if ($currentQuery || $currentCategory || $currentInStock || $currentSort || $currentMinPrice !== '' || $currentMaxPrice !== ''): ?>
                                    <a href="/brand/<?= $brandSlug ?>" class="btn btn-outline-secondary btn-sm">
                                        Скинути
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- ПРАВИЙ СТОВПЕЦЬ: сортування + список товарів -->
            <div class="col-12 col-lg-9">

                <!-- Заголовок секції + активні фільтри + сортування -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Товари бренду</p>
                        <h2 class="h4 fw-semibold mb-1">
                            Запчастини <?= $brandName ?>
                        </h2>
                        <p class="text-muted small mb-0">
                            Всього позицій: <span class="fw-semibold"><?= $total ?></span>
                            <?php if ($pages > 1): ?>
                                · Сторінка <span class="fw-semibold"><?= $page ?></span> з <?= $pages ?>
                            <?php endif; ?>
                        </p>

                        <?php if ($currentQuery || $currentCategory || $currentInStock || $currentSort || $currentMinPrice !== '' || $currentMaxPrice !== ''): ?>
                            <p class="mb-0 mt-1 small text-muted">
                                Активні фільтри:
                                <?php if ($currentQuery): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Пошук: “<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>”
                                    </span>
                                <?php endif; ?>
                                <?php if ($currentCategory): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Категорія: <?= htmlspecialchars($currentCategory, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($currentInStock === '1'): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Тільки в наявності
                                    </span>
                                <?php endif; ?>
                                <?php if ($currentMinPrice !== '' || $currentMaxPrice !== ''): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Ціна:
                                        <?= $currentMinPrice !== '' ? htmlspecialchars($currentMinPrice, ENT_QUOTES, 'UTF-8') : 'від 0' ?>
                                        –
                                        <?= $currentMaxPrice !== '' ? htmlspecialchars($currentMaxPrice, ENT_QUOTES, 'UTF-8') : '∞' ?>
                                        ₴
                                    </span>
                                <?php endif; ?>
                                <?php if ($currentSort): ?>
                                    <span class="badge text-bg-light border">
                                        Сортування: <?= htmlspecialchars($currentSortLabel, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Сортування -->
                    <form method="get"
                          action="/brand/<?= $brandSlug ?>"
                          class="d-flex align-items-center gap-2">

                        <!-- зберігаємо фільтри -->
                        <?php if ($currentQuery): ?>
                            <input type="hidden" name="q"
                                   value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <?php if ($currentCategory): ?>
                            <input type="hidden" name="category"
                                   value="<?= htmlspecialchars($currentCategory, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <?php if ($currentInStock === '1'): ?>
                            <input type="hidden" name="in_stock" value="1">
                        <?php endif; ?>
                        <?php if ($currentMinPrice !== ''): ?>
                            <input type="hidden" name="price_min"
                                   value="<?= htmlspecialchars($currentMinPrice, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <?php if ($currentMaxPrice !== ''): ?>
                            <input type="hidden" name="price_max"
                                   value="<?= htmlspecialchars($currentMaxPrice, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>

                        <label for="sort" class="small text-muted mb-0">Сортування:</label>
                        <select name="sort"
                                id="sort"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">
                            <option value="" <?= $currentSort === '' ? 'selected' : '' ?>>За замовчуванням</option>
                            <option value="price_asc" <?= $currentSort === 'price_asc' ? 'selected' : '' ?>>
                                Ціна: від дешевих
                            </option>
                            <option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>
                                Ціна: від дорогих
                            </option>
                            <option value="name_asc" <?= $currentSort === 'name_asc' ? 'selected' : '' ?>>
                                Назва A → Z
                            </option>
                            <option value="name_desc" <?= $currentSort === 'name_desc' ? 'selected' : '' ?>>
                                Назва Z → A
                            </option>
                            <option value="newest" <?= $currentSort === 'newest' ? 'selected' : '' ?>>
                                Спочатку нові
                            </option>
                        </select>
                    </form>
                </div>

                <?php if (!empty($items)): ?>
                    <div class="row g-3 g-lg-4">
                        <?php foreach ($items as $product): ?>
                            <?php
                            $pName   = htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $pSlug   = htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $pCat    = htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $pPrice  = (float)($product['price'] ?? 0);
                            $pImg    = $product['image_url'] ?? null;
                            $inStock = isset($product['in_stock']) ? (bool)$product['in_stock'] : true;
                            ?>
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
                                    <?php if ($pImg): ?>
                                        <a href="/product/<?= $pSlug ?>" class="ratio ratio-4x3">
                                            <img src="<?= htmlspecialchars($pImg, ENT_QUOTES, 'UTF-8') ?>"
                                                 class="card-img-top rounded-top"
                                                 alt="<?= $pName ?>" loading="lazy"
                                                 style="object-fit:cover; border-top-left-radius:inherit; border-top-right-radius:inherit;">
                                        </a>
                                    <?php else: ?>
                                        <a href="/product/<?= $pSlug ?>"
                                           class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted text-decoration-none">
                                            <i class="bi bi-image fs-3"></i>
                                        </a>
                                    <?php endif; ?>

                                    <div class="card-body d-flex flex-column p-3">
                                        <?php if ($pCat): ?>
                                            <p class="text-muted small mb-1">
                                                <?= $pCat ?>
                                            </p>
                                        <?php endif; ?>

                                        <h3 class="h6 fw-semibold mb-2">
                                            <?= $pName ?>
                                        </h3>

                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <p class="fw-bold fs-5 mb-0">
                                                <?= number_format($pPrice, 2, '.', ' ') ?> ₴
                                            </p>
                                        </div>

                                        <span class="badge w-50 mb-3 <?= $inStock ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                            <?= $inStock ? 'В наявності' : 'Під замовлення' ?>
                                        </span>

                                        <a href="/product/<?= $pSlug ?>"
                                           class="btn btn-dark mt-auto w-100">
                                            Деталі
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ПАГІНАЦІЯ ТОВАРІВ -->
                    <?php if ($pages > 1): ?>
                        <nav class="mt-4" aria-label="Пагінація по товарах бренду">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <p class="mb-0 small text-muted">
                                    Сторінка <span class="fw-semibold"><?= $page ?></span> з <?= $pages ?>
                                </p>

                                <ul class="pagination justify-content-center mb-0">
                                    <?php
                                    // Попередня
                                    $prevPage     = $page > 1 ? $page - 1 : 1;
                                    $prevDisabled = $page <= 1;
                                    $prevParams   = http_build_query(array_merge($baseParams, ['page' => $prevPage]));
                                    ?>
                                    <li class="page-item mx-1 <?= $prevDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm
                                                   <?= $prevDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/brand/<?= $brandSlug ?>?<?= $prevParams ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <!-- Номери сторінок -->
                                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                                        <?php
                                        $isActive   = ($i === (int)$page);
                                        $pageParams = http_build_query(array_merge($baseParams, ['page' => $i]));
                                        ?>
                                        <li class="page-item mx-1 <?= $isActive ? 'active' : '' ?>">
                                            <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm fw-semibold
                                                       <?= $isActive ? 'bg-dark text-white' : 'bg-white text-dark' ?>"
                                               href="/brand/<?= $brandSlug ?>?<?= $pageParams ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Наступна -->
                                    <?php
                                    $nextPage     = $page < $pages ? $page + 1 : $pages;
                                    $nextDisabled = $page >= $pages;
                                    $nextParams   = http_build_query(array_merge($baseParams, ['page' => $nextPage]));
                                    ?>
                                    <li class="page-item mx-1 <?= $nextDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm
                                                   <?= $nextDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/brand/<?= $brandSlug ?>?<?= $nextParams ?>">
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
                                <h3 class="h6 fw-semibold mb-1">Для цього бренду товари поки не додані</h3>
                                <p class="text-muted small mb-0">
                                    Спробуйте пізніше або оберіть інший бренд.
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
