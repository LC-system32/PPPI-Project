<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
$items   = $products['items']   ?? [];
$page    = $products['page']    ?? 1;
$perPage = $products['perPage'] ?? 12;
$total   = $products['total']   ?? 0;
$pages   = $products['pages']   ?? 1;
?>

<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 28%;">
        <img src="https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1600&q=80"
             class="w-100 h-100 object-fit-cover"
             alt="<?= htmlspecialchars($brand['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(8,8,10,.9), rgba(30,30,30,.6));"></div>
    <div class="container position-absolute top-50 start-50 translate-middle text-center text-lg-start">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white-50" href="/">Головна</a></li>
                <li class="breadcrumb-item"><a class="text-white-50" href="/brands">Бренди</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    <?= htmlspecialchars($brand['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </li>
            </ol>
        </nav>
        <p class="text-uppercase text-white-50 small mb-2">Бренд</p>
        <h1 class="display-5 fw-bold mb-3">
            <?= htmlspecialchars($brand['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="lead text-white-50 mb-0" style="max-width: 640px;">
            Запчастини <?= htmlspecialchars($brand['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>: популярні позиції, актуальна наявність
            та офіційні канали постачання.
        </p>
    </div>
</section>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <!-- Моделі авто бренду -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-uppercase text-muted small mb-1">Моделі авто</p>
                <h2 class="h4 fw-semibold mb-0">
                    Моделі, для яких доступні запчастини <?= htmlspecialchars($brand['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h2>
            </div>
        </div>

        <?php if (!empty($carModels ?? [])): ?>
            <div class="row g-2 mb-4">
                <?php foreach ($carModels as $cm): ?>
                    <?php
                    $fullNameParts = [];
                    $fullNameParts[] = $cm['brand'] ?? $brand['name'] ?? '';
                    $fullNameParts[] = $cm['model'] ?? '';
                    if (!empty($cm['generation'])) {
                        $fullNameParts[] = $cm['generation'];
                    }
                    $years = '';
                    if (!empty($cm['year_from']) || !empty($cm['year_to'])) {
                        $from = !empty($cm['year_from']) ? (int) $cm['year_from'] : null;
                        $to = !empty($cm['year_to']) ? (int) $cm['year_to'] : null;
                        $years = ($from ?? '...') . '–' . ($to ?? '...');
                    }
                    $fullName = trim(implode(' ', array_filter($fullNameParts)));

                    $slugSource = strtolower($fullName);
                    $modelSlug = preg_replace('/[^a-z0-9]+/i', '-', $slugSource) ?? '';
                    $modelSlug = trim($modelSlug, '-');
                    ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="/brand/<?= htmlspecialchars($brand['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($modelSlug, ENT_QUOTES, 'UTF-8') ?>"
                           class="text-decoration-none text-dark">
                            <div class="border rounded-3 bg-white px-3 py-2 h-100">
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php if ($years !== ''): ?>
                                    <div class="text-muted small">
                                        (<?= htmlspecialchars($years, ENT_QUOTES, 'UTF-8') ?>)
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

        <!-- Товари бренду -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-uppercase text-muted small mb-1">Товари бренду</p>
                <h2 class="h4 fw-semibold mb-0">
                    Запчастини <?= htmlspecialchars($brand['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h2>
            </div>
            <span class="text-muted small">Всього позицій: <?= (int) $total ?></span>
        </div>

        <?php if (!empty($items)): ?>
            <div class="row g-3 g-lg-4">
                <?php foreach ($items as $product): ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body d-flex flex-column p-4">
                                <span class="text-muted small mb-1">
                                    <?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <h3 class="h5 fw-semibold mb-2">
                                    <?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <p class="fw-bold fs-5 mb-3">
                                    <?= number_format((float) ($product['price'] ?? 0), 2, '.', ' ') ?> ₴
                                </p>
                                <a href="/product/<?= htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="btn btn-dark mt-auto w-100">
                                    Деталі
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($pages > 1): ?>
                <nav class="mt-4" aria-label="Пагінація по товарах бренду">
                    <ul class="pagination pagination-lg justify-content-center">
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                            <li class="page-item <?= $i === (int) $page ? 'active' : '' ?>">
                                <a class="page-link"
                                   href="/brand/<?= htmlspecialchars($brand['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-secondary border-0 shadow-sm">
                Для цього бренду товари поки не додані.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
