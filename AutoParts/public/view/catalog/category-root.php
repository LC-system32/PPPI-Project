<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
// Очікується: $category (root), $children, $breadcrumbs

$childrenAll = $children ?? [];
$childrenAll = is_array($childrenAll) ? $childrenAll : [];

$currentQuery     = trim($_GET['q'] ?? '');
$currentLetter    = $_GET['letter'] ?? '';
$currentSort      = $_GET['sort'] ?? '';
$onlyWithProducts = ($_GET['with_products'] ?? '') === '1';

// Фільтрація дочірніх категорій
$childrenFiltered = array_filter($childrenAll, static function (array $child) use (
    $currentQuery,
    $currentLetter,
    $onlyWithProducts
): bool {
    $name = mb_strtolower($child['name'] ?? '');
    $slug = mb_strtolower($child['slug'] ?? '');

    if ($currentQuery !== '') {
        $q = mb_strtolower($currentQuery);
        if (mb_strpos($name, $q) === false && mb_strpos($slug, $q) === false) {
            return false;
        }
    }

    if ($currentLetter !== '') {
        $firstLetter = mb_strtoupper(mb_substr($child['name'] ?? '', 0, 1));
        if ($firstLetter !== mb_strtoupper($currentLetter)) {
            return false;
        }
    }

    if ($onlyWithProducts) {
        if ((int)($child['products_count'] ?? 0) < 1) {
            return false;
        }
    }

    return true;
});

$childrenFiltered = array_values($childrenFiltered);

// Сортування підкатегорій
$sortKey = $currentSort ?: 'name_asc';
$sortLabelMap = [
    ''              => 'За замовчуванням',
    'name_asc'      => 'Назва A–Z',
    'name_desc'     => 'Назва Z–A',
    'products_desc' => 'Найбільше товарів',
];
$currentSortLabel = $sortLabelMap[$sortKey] ?? $sortKey;

usort($childrenFiltered, static function (array $a, array $b) use ($sortKey): int {
    $nameA  = mb_strtolower($a['name'] ?? '');
    $nameB  = mb_strtolower($b['name'] ?? '');
    $countA = (int)($a['products_count'] ?? 0);
    $countB = (int)($b['products_count'] ?? 0);

    switch ($sortKey) {
        case 'name_desc':
            return $nameB <=> $nameA;
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

// Пагінація підкатегорій
$perPage       = 24;
$totalChildren = count($childrenFiltered);
$totalPages    = max(1, (int)ceil($totalChildren / $perPage));
$currentPage   = (int)($_GET['page'] ?? 1);
if ($currentPage < 1)          $currentPage = 1;
if ($currentPage > $totalPages) $currentPage = $totalPages;

$offset   = ($currentPage - 1) * $perPage;
$children = array_slice($childrenFiltered, $offset, $perPage);
$shown    = count($children);

$baseParams = [
    'q'             => $currentQuery ?: null,
    'letter'        => $currentLetter ?: null,
    'with_products' => $onlyWithProducts ? '1' : null,
    'sort'          => $currentSort ?: null,
];
$baseParams = array_filter($baseParams, static fn($v) => $v !== null && $v !== '');
?>

<!-- STATS + SUBCATEGORIES -->
<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row mt-4">
            <!-- Фільтри підкатегорій -->
            <div class="col-12 col-lg-3 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h2 class="h6 fw-semibold mb-3">Фільтр по підкатегоріях</h2>

                        <form method="get" class="vstack gap-3">
                            <div>
                                <label class="form-label small text-muted">Назва або ключове слово</label>
                                <input type="text"
                                       name="q"
                                       class="form-control form-control-sm"
                                       value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>"
                                       placeholder="Наприклад, гальмівні диски">
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="1"
                                       id="with_products"
                                       name="with_products"
                                    <?= $onlyWithProducts ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="with_products">
                                    Тільки з товарами
                                </label>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 btn-sm rounded-pill">
                                Застосувати
                            </button>

                            <?php if ($currentQuery || $onlyWithProducts || $currentLetter || $currentSort): ?>
                                <a href="/categories/<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                                    Скинути фільтри
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Буквений фільтр -->
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
                            $allParams = $baseParams;
                            unset($allParams['letter'], $allParams['page']);
                            $allQuery = http_build_query($allParams);
                            $allHref  = $allQuery ? "/categories/{$category['slug']}?{$allQuery}" : "/categories/{$category['slug']}";
                            ?>
                            <a href="<?= $allHref ?>"
                               class="badge rounded-pill <?= $currentLetter === '' ? 'text-bg-dark' : 'text-bg-light' ?> text-decoration-none mb-1">
                                Усі
                            </a>
                            <?php foreach ($letters as $letter): ?>
                                <?php
                                $isActive = (mb_strtoupper($currentLetter, 'UTF-8') === $letter);
                                $params   = array_merge($baseParams, ['letter' => $letter, 'page' => null]);
                                $qs       = http_build_query($params);
                                ?>
                                <a href="/categories/<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>?<?= $qs ?>"
                                   class="badge rounded-pill <?= $isActive ? 'text-bg-dark' : 'text-bg-light' ?> text-decoration-none mb-1">
                                    <?= $letter ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Список підкатегорій -->
            <div class="col-12 col-lg-9">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Підкатегорії</p>
                        <h2 class="h4 fw-semibold mb-0">
                            Підкатегорії в розділі
                            “<?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>”
                        </h2>
                        <p class="text-muted small mb-0">
                            Показано: <span class="fw-semibold"><?= $shown ?></span>
                            з <?= $totalChildren ?>
                        </p>
                    </div>

                    <form method="get" class="d-flex align-items-center gap-2">
                        <?php foreach (['q' => $currentQuery, 'letter' => $currentLetter] as $name => $val): ?>
                            <?php if ($val !== ''): ?>
                                <input type="hidden"
                                       name="<?= $name ?>"
                                       value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($onlyWithProducts): ?>
                            <input type="hidden" name="with_products" value="1">
                        <?php endif; ?>

                        <label for="sort" class="small text-muted mb-0">Сортування:</label>
                        <select name="sort" id="sort"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">
                            <option value="name_asc"   <?= $sortKey === 'name_asc' ? 'selected' : '' ?>>Назва A–Z</option>
                            <option value="name_desc"  <?= $sortKey === 'name_desc' ? 'selected' : '' ?>>Назва Z–A</option>
                            <option value="products_desc" <?= $sortKey === 'products_desc' ? 'selected' : '' ?>>Найбільше товарів</option>
                        </select>
                    </form>
                </div>

                <?php if (!empty($children)): ?>
                    <div class="row g-3 g-lg-4">
                        <?php foreach ($children as $child): ?>
                            <?php
                            $childName = htmlspecialchars($child['name'] ?? 'Категорія', ENT_QUOTES, 'UTF-8');
                            $childSlug = htmlspecialchars($child['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $childDesc = trim((string)($child['description'] ?? ''));
                            $prodCount = (int)($child['products_count'] ?? 0);
                            ?>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <a href="/categories/<?= $childSlug ?>" class="text-decoration-none text-dark d-block h-100">
                                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                                        <div class="card-body d-flex flex-column">
                                            <h3 class="h5 mb-2"><?= $childName ?></h3>
                                            <p class="text-muted small mb-2">
                                                <?= $childDesc !== ''
                                                    ? nl2br(htmlspecialchars($childDesc, ENT_QUOTES, 'UTF-8'))
                                                    : 'Автозапчастини цього типу для різних марок та моделей.' ?>
                                            </p>
                                            <p class="small text-muted mb-3">
                                                <?php if ($prodCount > 0): ?>
                                                    <i class="bi bi-box-seam me-1"></i>
                                                    <?= $prodCount ?> запчастин
                                                <?php else: ?>
                                                    <span class="text-muted">Товари ще не додані</span>
                                                <?php endif; ?>
                                            </p>

                                            <span class="btn btn-outline-dark btn-sm rounded-pill mt-auto">
                                                Переглянути товари
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4" aria-label="Пагінація підкатегорій">
                            <div class="d-flex justify-content-center">
                                <ul class="pagination pagination-sm mb-0">
                                    <?php
                                    $prevPage    = max(1, $currentPage - 1);
                                    $prevParams  = http_build_query(array_merge($baseParams, ['page' => $prevPage]));
                                    $prevDisabled = $currentPage <= 1;
                                    ?>
                                    <li class="page-item mx-1 <?= $prevDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $prevDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/categories/<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>?<?= $prevParams ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <?php
                                        $isActive   = ($i === $currentPage);
                                        $pageParams = http_build_query(array_merge($baseParams, ['page' => $i]));
                                        ?>
                                        <li class="page-item mx-1 <?= $isActive ? 'active' : '' ?>">
                                            <a class="page-link border-0 rounded-pill px-3 py-2 <?= $isActive ? 'bg-dark text-white' : 'bg-white text-dark' ?>"
                                               href="/categories/<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>?<?= $pageParams ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php
                                    $nextPage   = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
                                    $nextParams = http_build_query(array_merge($baseParams, ['page' => $nextPage]));
                                    $nextDisabled = $currentPage >= $totalPages;
                                    ?>
                                    <li class="page-item mx-1 <?= $nextDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 <?= $nextDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/categories/<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>?<?= $nextParams ?>">
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
                                <h3 class="h6 fw-semibold mb-1">Підкатегорій, що відповідають фільтрам, не знайдено</h3>
                                <p class="text-muted small mb-0">
                                    Спробуйте змінити параметри пошуку або скинути фільтри.
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
