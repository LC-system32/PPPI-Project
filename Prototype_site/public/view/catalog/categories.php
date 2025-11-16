<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
$page = 'brands';

// Усі бренди з контролера
$brandsAll = $brands ?? [];
$brandsAll = is_array($brandsAll) ? $brandsAll : [];

// Поточні параметри
$currentQuery        = trim($_GET['q'] ?? '');
$currentLetter       = $_GET['letter'] ?? '';
$currentSort         = $_GET['sort'] ?? '';
$onlyWithProducts    = ($_GET['with_products'] ?? '') === '1';
$onlyPopular         = ($_GET['popular'] ?? '') === '1';

// ---------- ФІЛЬТРАЦІЯ НА РІВНІ VIEW ----------
$brandsFiltered = array_filter($brandsAll, function ($brand) use (
    $currentQuery,
    $currentLetter,
    $onlyWithProducts,
    $onlyPopular
) {
    $name = mb_strtolower($brand['name'] ?? '');
    $slug = mb_strtolower($brand['slug'] ?? '');

    // Пошук по назві / slug
    if ($currentQuery !== '') {
        $q = mb_strtolower($currentQuery);
        if (mb_strpos($name, $q) === false && mb_strpos($slug, $q) === false) {
            return false;
        }
    }

    // Фільтр за першою літерою
    if ($currentLetter !== '') {
        $firstLetter = mb_strtoupper(mb_substr($brand['name'] ?? '', 0, 1));
        if ($firstLetter !== mb_strtoupper($currentLetter)) {
            return false;
        }
    }

    // Тільки бренди з товарами
    if ($onlyWithProducts) {
        if ((int)($brand['products_count'] ?? 0) < 1) {
            return false;
        }
    }

    // Тільки популярні
    if ($onlyPopular) {
        if (empty($brand['is_popular'])) {
            return false;
        }
    }

    return true;
});

$brandsFiltered = array_values($brandsFiltered);

// ---------- СОРТУВАННЯ НА РІВНІ VIEW ----------
$sortKey   = $currentSort ?: 'name_asc';
$sortLabelMap = [
    ''              => 'За замовчуванням',
    'name_asc'      => 'Назва A → Z',
    'name_desc'     => 'Назва Z → A',
    'popular_desc'  => 'Популярніші спочатку',
    'products_desc' => 'Більше запчастин спочатку',
];
$currentSortLabel = $sortLabelMap[$sortKey] ?? $sortKey;

usort($brandsFiltered, function ($a, $b) use ($sortKey) {
    $nameA = mb_strtolower($a['name'] ?? '');
    $nameB = mb_strtolower($b['name'] ?? '');
    $countA = (int)($a['products_count'] ?? 0);
    $countB = (int)($b['products_count'] ?? 0);
    $popA   = !empty($a['is_popular']) ? 1 : 0;
    $popB   = !empty($b['is_popular']) ? 1 : 0;

    switch ($sortKey) {
        case 'name_desc':
            return $nameB <=> $nameA;
        case 'popular_desc':
            if ($popB !== $popA) {
                return $popB <=> $popA;
            }
            return $nameA <=> $nameB;
        case 'products_desc':
            if ($countB !== $countA) {
                return $countB <=> $countA;
            }
            return $nameA <=> $nameB;
        case 'name_asc':
        default:
            return $nameA <=> $nameB;
    }
});

// ---------- ПАГІНАЦІЯ ----------
$perPage       = 24;
$totalBrands   = count($brandsFiltered);
$totalPages    = max(1, (int)ceil($totalBrands / $perPage));
$currentPage   = (int)($_GET['page'] ?? 1);
if ($currentPage < 1)           $currentPage = 1;
if ($currentPage > $totalPages) $currentPage = $totalPages;

$offset  = ($currentPage - 1) * $perPage;
$brands  = array_slice($brandsFiltered, $offset, $perPage);
$shown   = count($brands);

// базові параметри для лінків
$baseParams = [
    'q'             => $currentQuery ?: null,
    'letter'        => $currentLetter ?: null,
    'with_products' => $onlyWithProducts ? '1' : null,
    'popular'       => $onlyPopular ? '1' : null,
    'sort'          => $currentSort ?: null,
];
$baseParams = array_filter($baseParams, fn($v) => $v !== null && $v !== '');
?>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row">
            <!-- ЛІВИЙ СТОВПЕЦЬ: Пошук + фільтри -->
            <div class="col-12 col-lg-3 mb-4">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Пошук по брендах</h3>
                        <form method="get">
                            <div class="input-group input-group-sm mb-3">
                                <span class="input-group-text bg-body border-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    type="text"
                                    name="q"
                                    value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>"
                                    class="form-control border-0"
                                    placeholder="Назва або артикул бренду...">
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="with_products" name="with_products"
                                       value="1" <?= $onlyWithProducts ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="with_products">
                                    Показувати тільки бренди з запчастинами
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="popular" name="popular"
                                       value="1" <?= $onlyPopular ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="popular">
                                    Тільки популярні бренди
                                </label>
                            </div>

                            <!-- Зберігаємо літеру -->
                            <?php if ($currentLetter): ?>
                                <input type="hidden" name="letter"
                                       value="<?= htmlspecialchars($currentLetter, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>

                            <!-- Зберігаємо сортування -->
                            <?php if ($currentSort): ?>
                                <input type="hidden" name="sort"
                                       value="<?= htmlspecialchars($currentSort, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>

                            <div class="d-flex justify-content-between mt-2">
                                <button type="submit" class="btn btn-dark btn-sm px-3">
                                    Знайти
                                </button>
                                <?php if ($currentQuery || $onlyWithProducts || $onlyPopular || $currentLetter || $currentSort): ?>
                                    <a href="/categories" class="btn btn-outline-secondary btn-sm">
                                        Скинути
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Фільтр за першою літерою -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Фільтр за першою літерою</h3>
                        <div class="d-flex flex-wrap gap-1">
                            <?php
                            $letters = [
                                'А','Б','В','Г','Ґ','Д','Е','Є','Ж','З','И','І','Ї','Й',
                                'К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч',
                                'Ш','Щ','Ь','Ю','Я',
                            ];
                            $allParams = array_merge($baseParams, ['letter' => null, 'page' => null]);
                            unset($allParams['letter'], $allParams['page']);
                            $allQuery = http_build_query($allParams);
                            $allHref  = $allQuery ? "/categories?{$allQuery}" : "/categories";
                            ?>
                            <a href="<?= $allHref ?>"
                               class="badge rounded-pill <?= $currentLetter === '' ? 'text-bg-dark' : 'text-bg-light' ?> text-decoration-none mb-1">
                                Всі
                            </a>
                            <?php foreach ($letters as $letter): ?>
                                <?php
                                $isActive = (mb_strtoupper($currentLetter, 'UTF-8') === $letter);
                                $params   = array_merge($baseParams, ['letter' => $letter, 'page' => null]);
                                $qs       = http_build_query($params);
                                ?>
                                <a href="/categories?<?= $qs ?>"
                                   class="badge rounded-pill <?= $isActive ? 'text-bg-dark' : 'text-bg-light' ?> text-decoration-none mb-1">
                                    <?= $letter ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ПРАВИЙ СТОВПЕЦЬ: список брендів -->
            <div class="col-12 col-lg-9">
                <!-- Заголовок + сортування -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Бренди</p>
                        <h2 class="h4 fw-semibold mb-1">Усі доступні виробники</h2>
                        <p class="text-muted small mb-0">
                            Показано на сторінці:
                            <span class="fw-semibold"><?= $shown ?></span>
                            з <?= $totalBrands ?>
                        </p>

                        <?php if ($currentQuery || $currentLetter || $onlyWithProducts || $onlyPopular || $currentSort): ?>
                            <p class="mb-0 mt-1 small text-muted">
                                Активні фільтри:
                                <?php if ($currentQuery): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Пошук: “<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>”
                                    </span>
                                <?php endif; ?>
                                <?php if ($currentLetter): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Літера: <?= htmlspecialchars(strtoupper($currentLetter), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($onlyWithProducts): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Тільки з товарами
                                    </span>
                                <?php endif; ?>
                                <?php if ($onlyPopular): ?>
                                    <span class="badge text-bg-light border me-1">
                                        Популярні бренди
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

                    <form method="get" class="d-flex align-items-center gap-2">
                        <!-- зберігаємо параметри -->
                        <?php foreach (['q' => $currentQuery, 'letter' => $currentLetter] as $name => $val): ?>
                            <?php if ($val !== ''): ?>
                                <input type="hidden" name="<?= $name ?>"
                                       value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($onlyWithProducts): ?>
                            <input type="hidden" name="with_products" value="1">
                        <?php endif; ?>
                        <?php if ($onlyPopular): ?>
                            <input type="hidden" name="popular" value="1">
                        <?php endif; ?>

                        <label for="sort" class="small text-muted mb-0">Сортування:</label>
                        <select name="sort" id="sort"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">
                            <option value="name_asc"   <?= $sortKey === 'name_asc' ? 'selected' : '' ?>>Назва A → Z</option>
                            <option value="name_desc"  <?= $sortKey === 'name_desc' ? 'selected' : '' ?>>Назва Z → A</option>
                            <option value="popular_desc" <?= $sortKey === 'popular_desc' ? 'selected' : '' ?>>Популярніші спочатку</option>
                            <option value="products_desc" <?= $sortKey === 'products_desc' ? 'selected' : '' ?>>Більше запчастин спочатку</option>
                        </select>
                    </form>
                </div>

                <?php if (!empty($brands)): ?>
                    <div class="row g-3 g-lg-4">
                        <?php foreach ($brands as $brandItem): ?>
                            <?php
                            $brandName     = htmlspecialchars($brandItem['name'] ?? 'Категорія', ENT_QUOTES, 'UTF-8');
                            $brandSlug     = htmlspecialchars($brandItem['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $logo          = $brandItem['logo_url'] ?? ($brandItem['logo'] ?? null);
                            $firstLetter   = mb_strtoupper(mb_substr($brandName, 0, 1));
                            $productsCount = (int)($brandItem['products_count'] ?? 0);
                            $isPopular     = !empty($brandItem['is_popular']);
                            ?>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <a href="/category/<?= $brandSlug ?>" class="text-decoration-none text-dark d-block h-100">
                                    <div class="card h-100 rounded-4 border-0 shadow-sm bg-white">
                                        <div class="card-body d-flex flex-column">

                                            <!-- Верхній блок: аватар + назва + маленький бейдж -->
                                            <div class="d-flex align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                                        <div>
                                                            <h3 class="h6 fw-semibold mb-1"><?= $brandName ?></h3>
                                                            <p class="small text-muted mb-1">
                                                                Автозапчастини та аксесуари від виробника.
                                                            </p>
                                                        </div>
                                                        <?php if ($isPopular): ?>
                                                            <span class="badge rounded-pill bg-dark text-white small text-uppercase">
                                                                Популярний
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Статистика по кількості запчастин -->
                                                    <p class="small text-muted mb-0">
                                                        <?php if ($productsCount > 0): ?>
                                                            <i class="bi bi-box-seam me-1"></i>
                                                            <?= $productsCount ?> запчастин
                                                        <?php else: ?>
                                                            <span class="text-muted">Немає запчастин</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Кнопка -->
                                            <div class="mt-auto pt-2">
                                                <span class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                                                    Переглянути товари
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ПАГІНАЦІЯ -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <p class="mb-0 small text-muted">
                                    Сторінка <span class="fw-semibold"><?= $currentPage ?></span> з <?= $totalPages ?>
                                </p>

                                <ul class="pagination justify-content-center mb-0">
                                    <?php
                                    // Попередня
                                    $prevPage     = $currentPage > 1 ? $currentPage - 1 : 1;
                                    $prevParams   = http_build_query(array_merge($baseParams, ['page' => $prevPage]));
                                    $prevDisabled = $currentPage <= 1;
                                    ?>
                                    <li class="page-item mx-1 <?= $prevDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $prevDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/categories?<?= $prevParams ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <?php
                                        $pageParams = http_build_query(array_merge($baseParams, ['page' => $i]));
                                        $isActive   = $i === $currentPage;
                                        ?>
                                        <li class="page-item mx-1 <?= $isActive ? 'active' : '' ?>">
                                            <a class="page-link border-0 rounded-pill px-3 py-2 fw-semibold <?= $isActive ? 'bg-dark text-white' : 'bg-white text-dark' ?>"
                                               href="/categories?<?= $pageParams ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php
                                    // Наступна
                                    $nextPage     = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
                                    $nextParams   = http_build_query(array_merge($baseParams, ['page' => $nextPage]));
                                    $nextDisabled = $currentPage >= $totalPages;
                                    ?>
                                    <li class="page-item mx-1 <?= $nextDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $nextDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/categories?<?= $nextParams ?>">
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
                                <h3 class="h6 fw-semibold mb-1">За заданими параметрами брендів не знайдено</h3>
                                <p class="text-muted small mb-0">
                                    Спробуйте змінити пошуковий запит або скинути фільтри.
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
