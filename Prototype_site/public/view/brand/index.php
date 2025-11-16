<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
$page = 'brands'; // активний пункт в navbar

// Всі бренди (фільтровані/відсортовані, якщо це робить контролер)
$brandsAll = $brands ?? [];
$brandsAll = is_array($brandsAll) ? $brandsAll : [];

// Поточні параметри фільтрації / пошуку / сортування
$currentQuery  = trim($_GET['q']     ?? '');
$currentLetter = $_GET['letter']     ?? '';
$currentType   = $_GET['type']       ?? '';
$currentSort   = $_GET['sort']       ?? '';

// Людські назви для сортування
$sortLabels = [
    'name_asc'     => 'Назва A → Z',
    'name_desc'    => 'Назва Z → A',
    'popular_desc' => 'Популярніші спочатку',
];
$currentSortLabel = $sortLabels[$currentSort] ?? $currentSort;

// ---------- ПАГІНАЦІЯ НА РІВНІ VIEW ----------
$perPage     = 24; // скільки брендів показувати на одній сторінці
$totalBrands = count($brandsAll);
$totalPages  = max(1, (int)ceil($totalBrands / $perPage));

$currentPage = (int)($_GET['page'] ?? 1);
if ($currentPage < 1)          $currentPage = 1;
if ($currentPage > $totalPages) $currentPage = $totalPages;

// ріжемо масив брендів для поточної сторінки
$offset      = ($currentPage - 1) * $perPage;
$brands      = array_slice($brandsAll, $offset, $perPage);
$brandsCount = count($brands);

// базові параметри для побудови лінків пагінації (щоб зберігались фільтри)
$baseParams = [
    'q'      => $currentQuery ?: null,
    'letter' => $currentLetter ?: null,
    'type'   => $currentType ?: null,
    'sort'   => $currentSort ?: null,
];
$baseParams = array_filter($baseParams, fn($v) => $v !== null && $v !== '');
?>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <!-- Заголовок + коротка статистика -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <p class="text-uppercase text-muted small mb-1">Бренди</p>
                <h2 class="h4 fw-semibold mb-1">Усі доступні виробники</h2>
                <p class="text-muted small mb-0">
                    Працюємо тільки з перевіреними брендами запчастин.
                </p>
            </div>

            <div class="text-end">
                <p class="mb-2 text-muted">
                    Показано на сторінці: <span class="fw-semibold"><?= $brandsCount ?></span>
                    з <?= $totalBrands ?>
                </p>
                <?php if ($currentQuery || $currentLetter || $currentType || $currentSort): ?>
                    <p class="mb-0 text-muted">
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
                        <?php if ($currentType): ?>
                            <span class="badge text-bg-light border me-1">
                                Тип: <?= htmlspecialchars($currentType, ENT_QUOTES, 'UTF-8') ?>
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
        </div>

        <div class="row">
            <!-- ЛІВИЙ СТОВПЕЦЬ: пошук + фільтри -->
            <div class="col-12 col-lg-3 mb-4">

                <!-- Пошук по брендах -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Пошук по брендах</h3>
                        <form method="get">
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text bg-body border-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    type="text"
                                    name="q"
                                    value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>"
                                    class="form-control border-0"
                                    placeholder="Назва бренду..."
                                    aria-label="Пошук бренду">
                            </div>

                            <!-- Зберігаємо інші параметри -->
                            <?php if ($currentLetter): ?>
                                <input type="hidden" name="letter" value="<?= htmlspecialchars($currentLetter, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                            <?php if ($currentType): ?>
                                <input type="hidden" name="type" value="<?= htmlspecialchars($currentType, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                            <?php if ($currentSort): ?>
                                <input type="hidden" name="sort" value="<?= htmlspecialchars($currentSort, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>

                            <div class="d-flex justify-content-between mt-2">
                                <button type="submit" class="btn btn-dark btn-sm px-3">
                                    Знайти
                                </button>
                                <?php if ($currentQuery || $currentLetter || $currentType || $currentSort): ?>
                                    <a href="/brands" class="btn btn-outline-secondary btn-sm">
                                        Скинути
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Фільтр за першою літерою -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Фільтр за першою літерою</h3>
                        <div class="d-flex flex-wrap gap-1">
                            <?php
                            $letters = range('A', 'Z');

                            // Лінк "Всі" з урахуванням інших фільтрів
                            $allParams = [
                                'q'    => $currentQuery ?: null,
                                'type' => $currentType ?: null,
                                'sort' => $currentSort ?: null,
                            ];
                            $allParams   = array_filter($allParams, fn($v) => $v !== null && $v !== '');
                            $allQuery    = http_build_query($allParams);
                            $allHref     = $allQuery ? "/brands?{$allQuery}" : "/brands";
                            $allIsActive = $currentLetter === '' || $currentLetter === null;
                            ?>
                            <a href="<?= $allHref ?>"
                               class="badge rounded-pill <?= $allIsActive ? 'text-bg-dark' : 'text-bg-light' ?> text-decoration-none mb-1">
                                Всі
                            </a>

                            <?php foreach ($letters as $letter): ?>
                                <?php
                                $isActive = (strtoupper($currentLetter) === $letter);
                                $params   = [
                                    'letter' => $letter,
                                    'q'      => $currentQuery ?: null,
                                    'type'   => $currentType ?: null,
                                    'sort'   => $currentSort ?: null,
                                ];
                                $queryString = http_build_query(array_filter($params, fn($v) => $v !== null && $v !== ''));
                                ?>
                                <a href="/brands?<?= $queryString ?>"
                                   class="badge rounded-pill <?= $isActive ? 'text-bg-dark' : 'text-bg-light' ?> text-decoration-none mb-1">
                                    <?= $letter ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ПРАВИЙ СТОВПЕЦЬ: сортування + список брендів -->
            <div class="col-12 col-lg-9">

                <!-- Панель сортування -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-funnel"></i>
                        <span>Результати за вашими налаштуваннями фільтрів.</span>
                    </div>

                    <form method="get" class="d-flex align-items-center gap-2">
                        <!-- зберігаємо параметри -->
                        <?php if ($currentQuery): ?>
                            <input type="hidden" name="q" value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <?php if ($currentLetter): ?>
                            <input type="hidden" name="letter" value="<?= htmlspecialchars($currentLetter, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <?php if ($currentType): ?>
                            <input type="hidden" name="type" value="<?= htmlspecialchars($currentType, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>

                        <label for="sort" class="small text-muted mb-0">Сортування:</label>
                        <select name="sort"
                                id="sort"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">
                            <option value="" <?= $currentSort === '' ? 'selected' : '' ?>>За замовчуванням</option>
                            <option value="name_asc" <?= $currentSort === 'name_asc' ? 'selected' : '' ?>>
                                Назва A → Z
                            </option>
                            <option value="name_desc" <?= $currentSort === 'name_desc' ? 'selected' : '' ?>>
                                Назва Z → A
                            </option>
                            <option value="popular_desc" <?= $currentSort === 'popular_desc' ? 'selected' : '' ?>>
                                Популярніші спочатку
                            </option>
                        </select>
                    </form>
                </div>

                <?php if (!empty($brands)): ?>
                    <div class="row g-3 g-lg-4">
                        <?php foreach ($brands as $brandItem): ?>
                            <?php
                            $brandName     = htmlspecialchars($brandItem['name'] ?? 'Бренд', ENT_QUOTES, 'UTF-8');
                            $brandSlug     = htmlspecialchars($brandItem['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $logo          = $brandItem['logo_url'] ?? ($brandItem['logo'] ?? null);
                            $firstLetter   = mb_strtoupper(mb_substr($brandName, 0, 1));
                            $productsCount = $brandItem['products_count'] ?? null;
                            ?>
                            <div class="col-6 col-sm-4 col-xl-3">
                                <a href="/brand/<?= $brandSlug ?>"
                                   class="text-decoration-none text-dark d-block h-100">
                                    <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
                                        <div class="card-body d-flex flex-column text-center p-3">

                                            <!-- Бейдж кількості -->
                                            <div class="d-flex justify-content-center mb-2">
                                                <?php if ($productsCount !== null && $productsCount > 0): ?>
                                                    <span class="badge text-bg-warning text-dark small">
                                                        Кількість запчастин: <?= (int)$productsCount ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-light border small">
                                                        Запчастин не знайдено
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Логотип або ініціал -->
                                            <?php if ($logo): ?>
                                                <div class="mb-3">
                                                    <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') ?>"
                                                         alt="<?= $brandName ?> логотип"
                                                         class="img-fluid img-thumbnail rounded bg-white p-2 mx-auto"
                                                         style="max-height:56px; width:auto;"
                                                         loading="lazy">
                                                </div>
                                            <?php else: ?>
                                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mb-3 mx-auto"
                                                     style="width:56px;height:56px;">
                                                    <span class="fw-bold fs-5"><?= $firstLetter ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Назва -->
                                            <h3 class="h6 fw-semibold mb-1">
                                                <?= $brandName ?>
                                            </h3>

                                            <!-- Дія -->
                                            <div class="mt-auto">
                                                <span class="btn btn-outline-dark btn-sm w-100">
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

                                    <!-- Попередня -->
                                    <?php
                                    $prevPage     = $currentPage > 1 ? $currentPage - 1 : 1;
                                    $prevParams   = http_build_query(array_merge($baseParams, ['page' => $prevPage]));
                                    $prevDisabled = $currentPage <= 1;
                                    ?>
                                    <li class="page-item mx-1 <?= $prevDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm
                                                   <?= $prevDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/brands?<?= $prevParams ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <!-- Номери сторінок -->
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <?php
                                        $pageParams = http_build_query(array_merge($baseParams, ['page' => $i]));
                                        $isActive   = $i === $currentPage;
                                        ?>
                                        <li class="page-item mx-1 <?= $isActive ? 'active' : '' ?>">
                                            <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm fw-semibold
                                                       <?= $isActive ? 'bg-dark text-white' : 'bg-white text-dark' ?>"
                                               href="/brands?<?= $pageParams ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Наступна -->
                                    <?php
                                    $nextPage     = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
                                    $nextParams   = http_build_query(array_merge($baseParams, ['page' => $nextPage]));
                                    $nextDisabled = $currentPage >= $totalPages;
                                    ?>
                                    <li class="page-item mx-1 <?= $nextDisabled ? 'disabled' : '' ?>">
                                        <a class="page-link border-0 rounded-pill px-3 py-2 shadow-sm
                                                   <?= $nextDisabled ? 'bg-body-secondary text-muted' : 'bg-white text-dark' ?>"
                                           href="/brands?<?= $nextParams ?>">
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
