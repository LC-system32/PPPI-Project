<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php
$items   = $products['items']   ?? [];
$page    = $products['page']    ?? 1;
$perPage = $products['perPage'] ?? 12;
$total   = $products['total']   ?? 0;
$pages   = $products['pages']   ?? 1;

$brand  = $carModel['brand'] ?? '';
$model  = $carModel['model'] ?? '';
$gen    = $carModel['generation'] ?? '';
$from   = $carModel['year_from'] ?? null;
$to     = $carModel['year_to'] ?? null;

$titleParts = array_filter([$brand, $model, $gen]);
$title = trim(implode(' ', $titleParts));

$years = '';
if (!empty($from) || !empty($to)) {
    $fromText = $from !== null ? (int) $from : '...';
    $toText = $to !== null ? (int) $to : '...';
    $years = $fromText . '–' . $toText;
}

$brandSlug = $brandSlug ?? null;
$modelSlug = $modelSlug ?? null;
?>

<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 28%;">
        <img src="https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1600&q=80"
             class="w-100 h-100 object-fit-cover"
             alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(8,8,10,.9), rgba(30,30,30,.6));"></div>
    <div class="container position-absolute top-50 start-50 translate-middle text-center text-lg-start">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white-50" href="/">Головна</a></li>
                <li class="breadcrumb-item"><a class="text-white-50" href="/brands">Бренди</a></li>
                <?php if ($brandSlug): ?>
                    <li class="breadcrumb-item">
                        <a class="text-white-50" href="/brand/<?= htmlspecialchars($brandSlug, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                </li>
            </ol>
        </nav>
        <p class="text-uppercase text-white-50 small mb-2">Модель авто</p>
        <h1 class="display-5 fw-bold mb-2">
            <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            <?php if ($years !== ''): ?>
                <small class="fs-5 text-white-50">(<?= htmlspecialchars($years, ENT_QUOTES, 'UTF-8') ?>)</small>
            <?php endif; ?>
        </h1>
        <p class="lead text-white-50 mb-0" style="max-width: 640px;">
            Запчастини, сумісні з автомобілем <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?><?= $years !== '' ? ' (' . htmlspecialchars($years, ENT_QUOTES, 'UTF-8') . ')' : '' ?>.
        </p>
    </div>
</section>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-uppercase text-muted small mb-1">Каталог</p>
                <h2 class="h4 fw-semibold mb-0">
                    Запчастини для <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
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
                                <?php if (!empty($product['brand_name'])): ?>
                                    <p class="text-muted small mb-1">
                                        Бренд: <?= htmlspecialchars($product['brand_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                <?php endif; ?>
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

            <?php if ($pages > 1 && $brandSlug && $modelSlug): ?>
                <nav class="mt-4" aria-label="Пагінація по запчастинах моделі">
                    <ul class="pagination pagination-lg justify-content-center">
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                            <li class="page-item <?= $i === (int) $page ? 'active' : '' ?>">
                                <a class="page-link"
                                   href="/brand/<?= htmlspecialchars($brandSlug, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($modelSlug, ENT_QUOTES, 'UTF-8') ?>?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-secondary border-0 shadow-sm">
                Для цієї моделі авто поки немає прив’язаних запчастин.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

