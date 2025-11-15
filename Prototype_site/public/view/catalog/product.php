<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrf = csrf_token();
$images = $product['images'] ?? [];
$carModels = $product['car_models'] ?? [];
$heroImage = $images[0]['path'] ?? 'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1600&q=80';
$heroAlt = $images[0]['alt'] ?? $product['name'];
?>

<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 28%;">
        <img src="<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') ?>"
             class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($heroAlt, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(115deg, rgba(8,8,10,.9), rgba(26,26,28,.55));"></div>
    <div class="container position-absolute top-50 start-50 translate-middle text-center text-lg-start">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white-50" href="/">Головна</a></li>
                <li class="breadcrumb-item"><a class="text-white-50" href="/catalog">Каталог</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                </li>
            </ol>
        </nav>
        <p class="text-uppercase text-white-50 small mb-2">
            <?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="lead text-white-50 mb-1">SKU: <?= htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') ?></p>
        <p class="display-6 fw-bold text-white mb-0"><?= number_format($product['price'], 2, '.', ' ') ?> ₴</p>
    </div>
</section>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg mb-3">
                    <?php if ($images): ?>
                        <img src="<?= htmlspecialchars($images[0]['path'], ENT_QUOTES, 'UTF-8') ?>"
                             class="card-img-top object-fit-cover" style="max-height: 480px;"
                             alt="<?= htmlspecialchars($images[0]['alt'] ?? $product['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php else: ?>
                        <div class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted">
                            Зображення недоступне
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="row g-2">
                        <?php foreach (array_slice($images, 1) as $image): ?>
                            <div class="col-3">
                                <div class="ratio ratio-1x1 bg-white border rounded-3 overflow-hidden">
                                    <img src="<?= htmlspecialchars($image['path'], ENT_QUOTES, 'UTF-8') ?>"
                                         class="w-100 h-100 object-fit-cover"
                                         alt="<?= htmlspecialchars($image['alt'] ?? $product['name'], ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body p-4 p-lg-5 d-flex flex-column">
                        <div class="mb-4">
                            <p class="text-muted small text-uppercase mb-1">Опис</p>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($product['description'] ?? 'Детальна інформація про товар незабаром буде додана.', ENT_QUOTES, 'UTF-8')) ?></p>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted small mb-1">Наявність</p>
                                    <p class="fw-semibold mb-0"><?= (int) $product['stock'] > 0 ? 'На складі' : 'Очікується' ?></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted small mb-1">Оплата</p>
                                    <p class="fw-semibold mb-0">Банківська карта / накладений платіж</p>
                                </div>
                            </div>
                        </div>

                        <form action="/cart/add" method="POST" class="d-flex flex-column gap-3">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <div class="input-group input-group-lg" style="max-width: 220px;">
                                <button class="btn btn-outline-dark" type="button" onclick="this.nextElementSibling.stepDown()">−</button>
                                <input type="number" class="form-control text-center" name="quantity" value="1"
                                       min="1" max="<?= (int) $product['stock'] ?>">
                                <button class="btn btn-outline-dark" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                            </div>
                            <button type="submit" class="btn btn-dark btn-lg d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-bag-check"></i>
                                Додати до кошика
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="row g-3 text-muted small">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check text-success fs-4"></i>
                                    <div>
                                        <strong class="d-block text-dark">12 міс. гарантія</strong>
                                        Офіційне покриття
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-truck text-success fs-4"></i>
                                    <div>
                                        <strong class="d-block text-dark">Доставка 1–3 дні</strong>
                                        Усі регіони
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-counterclockwise text-success fs-4"></i>
                                    <div>
                                        <strong class="d-block text-dark">Обмін 14 днів</strong>
                                        Без зайвих питань
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($carModels): ?>
            <div class="card border-0 shadow-sm mt-5">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <p class="text-uppercase text-muted small mb-1">Сумісність</p>
                            <h2 class="h4 fw-semibold mb-0">Автомобілі, для яких підходить</h2>
                        </div>
                        <span class="badge text-bg-dark rounded-pill"><?= count($carModels) ?></span>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($carModels as $car): ?>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <strong><?= htmlspecialchars($car['brand'] . ' ' . $car['model'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <p class="mb-0 text-muted small">
                                        <?= htmlspecialchars(trim(($car['generation'] ?? '') . ' ' . ($car['year_from'] ?? '') . '–' . ($car['year_to'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
