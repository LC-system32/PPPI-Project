<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrf      = csrf_token();
$images    = $product['images'] ?? [];
$carModels = $product['car_models'] ?? [];

// Основні дані товару
$name        = htmlspecialchars($product['name'] ?? 'Товар', ENT_QUOTES, 'UTF-8');
$category    = htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
$brandName   = htmlspecialchars($product['brand_name'] ?? ($product['brand'] ?? ''), ENT_QUOTES, 'UTF-8');
$sku         = htmlspecialchars($product['sku'] ?? '', ENT_QUOTES, 'UTF-8');
$price       = isset($product['price']) ? (float)$product['price'] : 0;
$oldPrice    = isset($product['old_price']) ? (float)$product['old_price'] : null;
$discountPct = isset($product['discount_percent']) ? (int)$product['discount_percent'] : null;

$inStockQty  = (int)($product['stock'] ?? 0);
$inStock     = $inStockQty > 0;

$tags        = $product['tags'] ?? []; // ['Передні', 'Дискові гальма', 'Комплект']

// Рейтинг / відгуки
$rating       = isset($product['rating']) ? (float)$product['rating'] : null;
$reviewsCount = isset($product['reviews_count']) ? (int)$product['reviews_count'] : 0;
$reviews      = $reviews ?? ($product['reviews'] ?? []);

// Галерея / герой
$heroImage = $images[0]['path'] ?? 'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1600&q=80';
$heroAlt   = $images[0]['alt']  ?? $name;

// Для breadcrumbs
$categorySlug = htmlspecialchars($product['category_slug'] ?? 'catalog', ENT_QUOTES, 'UTF-8');
$brandSlug    = htmlspecialchars($product['brand_slug'] ?? '', ENT_QUOTES, 'UTF-8');

// Схожі товари
$relatedProducts  = $relatedProducts  ?? [];
$frequentlyBought = $frequentlyBought ?? [];
?>
<!-- CONTENT -->
<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row g-4">
            <!-- ЛІВА КОЛОНКА: фото -->
            <div class="col-lg-6">
                <div class="mb-3">
                    <?php if ($images): ?>
                        <div class="ratio ratio-4x3 bg-body rounded-4 overflow-hidden">
                            <img id="mainProductImage"
                                src="<?= htmlspecialchars($images[0]['path'], ENT_QUOTES, 'UTF-8') ?>"
                                class="w-100 h-100 object-fit-cover"
                                alt="<?= htmlspecialchars($images[0]['alt'] ?? $name, ENT_QUOTES, 'UTF-8') ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#productImageModal"
                                style="cursor: zoom-in;">
                        </div>
                    <?php else: ?>
                        <div class="ratio ratio-4x3 bg-body-secondary rounded-4 d-flex flex-column align-items-center justify-content-center text-muted text-center">
                            <i class="bi bi-image fs-1 mt-4"></i>
                            <span class="fw-semibold">Фото тимчасово відсутнє</span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (count($images) > 1): ?>
                    <div class="row g-2">
                        <?php foreach ($images as $image): ?>
                            <div class="col-3">
                                <button type="button"
                                    class="btn p-0 w-100 bg-transparent border-0"
                                    onclick="changeMainImage('<?= htmlspecialchars($image['path'], ENT_QUOTES, 'UTF-8') ?>',
                                                                 '<?= htmlspecialchars($image['alt'] ?? $name, ENT_QUOTES, 'UTF-8') ?>')">
                                    <div class="ratio ratio-1x1 bg-white rounded-3 overflow-hidden">
                                        <img src="<?= htmlspecialchars($image['path'], ENT_QUOTES, 'UTF-8') ?>"
                                            class="w-100 h-100 object-fit-cover"
                                            alt="<?= htmlspecialchars($image['alt'] ?? $name, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ПРАВА КОЛОНКА: основний блок (одна велика картка) -->
            <div class="col-lg-6">
                <div class="sticky-lg-top" style="top: 96px;">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 p-lg-5 d-flex flex-column gap-4">

                            <!-- Назва + мета всередині картки -->
                            <div>
                                <h2 class="h4 fw-semibold mb-1"><?= $name ?></h2>
                                <div class="small text-muted">
                                    <?php if ($brandName): ?>
                                        Бренд: <span class="fw-semibold"><?= $brandName ?></span>
                                    <?php endif; ?>
                                    <?php if ($sku): ?>
                                        <?php if ($brandName): ?> · <?php endif; ?>
                                        Артикул: <span class="fw-semibold"><?= $sku ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Ціна -->
                            <div>
                                <p class="small text-muted mb-1">Ціна за комплект</p>
                                <div class="d-flex flex-wrap align-items-baseline gap-2">
                                    <span class="fs-2 fw-bold mb-0">
                                        <?= number_format($price, 2, '.', ' ') ?> ₴
                                    </span>
                                    <?php if ($oldPrice && $oldPrice > $price): ?>
                                        <span class="text-muted text-decoration-line-through">
                                            <?= number_format($oldPrice, 2, '.', ' ') ?> ₴
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($discountPct && $discountPct > 0): ?>
                                        <span class="badge text-bg-warning-subtle text-warning fw-semibold">
                                            -<?= $discountPct ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Наявність / оплата / доставка (без рамок всередині) -->
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-box-seam text-warning"></i>
                                            <span class="small text-uppercase text-muted">Наявність</span>
                                        </div>
                                        <span class="fw-semibold">
                                            <?= $inStock ? 'На складі' : 'Під замовлення' ?>
                                        </span>
                                        <?php if ($inStock): ?>
                                            <span class="small text-muted">Доступно: <?= $inStockQty ?> шт</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-credit-card text-warning"></i>
                                            <span class="small text-uppercase text-muted">Оплата</span>
                                        </div>
                                        <span class="fw-semibold">Банківська карта</span>
                                        <span class="small text-muted">Онлайн-оплата</span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-truck text-warning"></i>
                                            <span class="small text-uppercase text-muted">Доставка</span>
                                        </div>
                                        <span class="fw-semibold">1–3 робочі дні</span>
                                        <span class="small text-muted">По всій Україні</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Кількість + кнопка -->
                            <form id="addToCartForm" action="/cart/add" method="POST" class="d-flex flex-column gap-3">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">

                                <div class="d-flex flex-wrap align-items-center gap-3 mb-1">
                                    <div class="input-group input-group-lg" style="max-width: 240px;">
                                        <button class="btn btn-outline-dark quantity-btn" type="button"
                                            onclick="this.nextElementSibling.stepDown()">
                                            −
                                        </button>
                                        <input type="number"
                                            class="form-control text-center"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="<?= max($inStockQty, 1) ?>">
                                        <button class="btn btn-outline-dark quantity-btn" type="button"
                                            onclick="this.previousElementSibling.stepUp()">
                                            +
                                        </button>
                                    </div>

                                    <p class="mb-0 text-muted small">
                                        Мінімальне замовлення: 1 шт.
                                    </p>
                                </div>

                                <button type="submit"
                                    class="btn btn-dark btn-lg w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold"
                                    <?= $inStock ? '' : 'disabled' ?>>
                                    <i class="bi bi-bag-check"></i>
                                    <?= $inStock ? 'Додати до кошика' : 'Немає в наявності' ?>
                                </button>
                            </form>

                            <!-- Короткі бейджі довіри (мінімалізм) -->
                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check text-warning"></i>
                                    <span>12 міс. гарантії</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-repeat text-warning"></i>
                                    <span>Обмін 14 днів</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-patch-check text-warning"></i>
                                    <span>Офіційні постачальники</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладки: мінімалістичні, nav-underline -->
        <ul class="nav nav-underline mb-3" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active"
                    id="tab-description"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-description"
                    type="button"
                    role="tab">
                    Опис
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link"
                    id="tab-delivery"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-delivery"
                    type="button"
                    role="tab">
                    Доставка та оплата
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link"
                    id="tab-warranty"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-warranty"
                    type="button"
                    role="tab">
                    Гарантія та повернення
                </button>
            </li>
        </ul>

        <div class="tab-content border rounded-4 bg-white p-4">
            <!-- ОПИС -->
            <div class="tab-pane fade show active" id="pane-description" role="tabpanel">
                <p class="mb-3">
                    <?= nl2br(htmlspecialchars(
                        $product['description'] ?? 'Детальна інформація про товар незабаром буде додана.',
                        ENT_QUOTES,
                        'UTF-8'
                    )) ?>
                </p>

                <h3 class="h6 fw-semibold mb-2">Основні характеристики</h3>
                <ul class="small text-muted mb-0">
                    <?php if ($brandName): ?>
                        <li>Бренд: <strong><?= $brandName ?></strong></li>
                    <?php endif; ?>
                    <?php if ($category): ?>
                        <li>Тип: <strong><?= $category ?></strong></li>
                    <?php endif; ?>
                    <?php if ($sku): ?>
                        <li>Артикул: <strong><?= $sku ?></strong></li>
                    <?php endif; ?>
                    <li>Комплектація: <strong><?= !empty($tags) ? htmlspecialchars(implode(', ', $tags), ENT_QUOTES, 'UTF-8') : 'комплект гальмівних колодок' ?></strong></li>
                    <li>Гарантія: <strong>12 місяців</strong></li>
                </ul>
            </div>

            <!-- ДОСТАВКА -->
            <div class="tab-pane fade" id="pane-delivery" role="tabpanel">
                <h3 class="h6 fw-semibold mb-2">Доставка</h3>
                <ul class="small text-muted mb-3">
                    <li>Відправка по всій Україні протягом 1–3 робочих днів.</li>
                    <li>Служби доставки: Нова Пошта, Укрпошта, інші перевізники (за домовленістю).</li>
                    <li>Після відправки надішлемо номер ТТН для відстеження.</li>
                </ul>

                <h3 class="h6 fw-semibold mb-2">Оплата</h3>
                <ul class="small text-muted mb-0">
                    <li>Онлайн-оплата банківською карткою (Visa / Mastercard).</li>
                    <li>Google Pay / Apple Pay.</li>
                    <li>Оплата при отриманні (накладений платіж).</li>
                    <li>Безготівковий розрахунок для юридичних осіб.</li>
                </ul>
            </div>

            <!-- ГАРАНТІЯ -->
            <div class="tab-pane fade" id="pane-warranty" role="tabpanel">
                <h3 class="h6 fw-semibold mb-2">Гарантія</h3>
                <ul class="small text-muted mb-3">
                    <li>Гарантія на товар — <strong>12 місяців</strong> з дати продажу.</li>
                    <li>Поширюється на виробничі дефекти та невідповідність характеристикам.</li>
                    <li>Рекомендується професійне встановлення на СТО.</li>
                </ul>

                <h3 class="h6 fw-semibold mb-2">Повернення та обмін</h3>
                <ul class="small text-muted mb-0">
                    <li>Повернення / обмін можливі протягом <strong>14 днів</strong>.</li>
                    <li>Товар має бути без слідів монтажу, з повною комплектацією та упаковкою.</li>
                    <li>Для оформлення потрібні чек / накладна, а при рекламації — висновок СТО.</li>
                </ul>
            </div>
        </div>

        <!-- СУМІСНІ МОДЕЛІ -->
        <?php if ($carModels): ?>
            <div class="mt-5 border rounded-4 bg-white p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Сумісність</p>
                        <h2 class="h5 fw-semibold mb-0">Автомобілі, для яких підходить ця деталь</h2>
                        <p class="small text-muted mb-0">
                            Перед покупкою рекомендуємо перевірити сумісність за VIN-кодом або уточнити в менеджера.
                        </p>
                    </div>
                    <span class="badge text-bg-dark rounded-pill">
                        <?= count($carModels) ?> моделей
                    </span>
                </div>

                <div class="row g-2 g-md-3">
                    <?php foreach ($carModels as $car): ?>
                        <?php
                        $carTitle = trim(($car['brand'] ?? '') . ' ' . ($car['model'] ?? ''));
                        $gen      = trim($car['generation'] ?? '');
                        $years    = trim(($car['year_from'] ?? '') . '–' . ($car['year_to'] ?? ''));
                        ?>
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="border rounded-3 p-3 h-100 small">
                                <strong class="d-block mb-1">
                                    <?= htmlspecialchars($carTitle, ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                                <?php if ($gen): ?>
                                    <div class="text-muted mb-1">
                                        Покоління: <?= htmlspecialchars($gen, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (trim($years, '–')): ?>
                                    <div class="text-muted">
                                        Роки випуску: <?= htmlspecialchars($years, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ВІДГУКИ (мінімалістично) -->
        <div class="mt-5 border rounded-4 bg-white p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-semibold mb-0">Відгуки</h2>
                <?php if ($rating !== null): ?>
                    <div class="d-flex align-items-center gap-2 small">
                        <span class="fw-semibold"><?= number_format($rating, 1) ?></span>
                        <div>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($rating)): ?>
                                    <i class="bi bi-star-fill text-warning"></i>
                                <?php else: ?>
                                    <i class="bi bi-star text-warning"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="text-muted">
                            (<?= $reviewsCount > 0 ? $reviewsCount . ' відгуків' : 'ще немає відгуків' ?>)
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($reviews)): ?>
                <div class="row g-3">
                    <?php foreach ($reviews as $review): ?>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 small bg-white">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong><?= htmlspecialchars($review['author'] ?? 'Користувач', ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span class="text-muted">
                                        <?= htmlspecialchars($review['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <?php $revRating = (int)($review['rating'] ?? 0); ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $revRating): ?>
                                            <i class="bi bi-star-fill text-warning small"></i>
                                        <?php else: ?>
                                            <i class="bi bi-star text-warning small"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <?php if (!empty($review['text'])): ?>
                                    <p class="text-muted mb-0">
                                        <?= nl2br(htmlspecialchars($review['text'], ENT_QUOTES, 'UTF-8')) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="small text-muted mb-0">
                    Ще немає відгуків. Будьте першим, хто залишить відгук про цю деталь.
                </p>
            <?php endif; ?>
        </div>
        <!-- Форма додавання відгуку -->
        <?php
        // Flash messages (message / errors) set by controller
        if (!empty($_SESSION['flash']['message'])): ?>
            <div class="mt-3 alert alert-success">
                <?= htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['flash']['message']);
        endif;

        if (!empty($_SESSION['flash']['errors'])): ?>
            <div class="mt-3 alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ((array)$_SESSION['flash']['errors'] as $err): ?>
                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['flash']['errors']);
        endif;

        // Old input (if validation failed)
        $oldRating = old('rating');
        $oldText   = old('text');
        // clear old after using
        if (!empty($_SESSION['flash']['old'])) { unset($_SESSION['flash']['old']); }

        ?>

        <?php if (!empty($_SESSION['user'])): ?>
            <div class="mt-4 border rounded-4 bg-white p-4">
                <h3 class="h6 fw-semibold mb-3">Залишити відгук</h3>
                <form action="/product/<?= htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>/review" method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <div class="mb-3">
                        <label class="form-label d-block">Оцінка</label>
                        <div class="btn-group" role="group" aria-label="rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" class="btn-check" name="rating" id="rating-<?= $i ?>" autocomplete="off" value="<?= $i ?>" <?= ($oldRating == (string)$i) ? 'checked' : '' ?><?= ($oldRating === null && $i === 5) ? ' checked' : '' ?>>
                                <label class="btn btn-outline-warning btn-sm me-1" for="rating-<?= $i ?>">
                                    <?php for ($s = 1; $s <= $i; $s++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="review-text" class="form-label">Коментар</label>
                        <textarea id="review-text" name="text" rows="4" class="form-control" maxlength="2000" required><?= htmlspecialchars($oldText ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="mb-0">
                        <button class="btn btn-primary" type="submit">Надіслати відгук</button>
                        <small class="text-muted ms-2">Ваш відгук зберігатиметься як чернетка до модерації.</small>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="mt-4 small text-muted">
                Щоб залишити відгук, будь ласка, <a href="/auth">увійдіть в акаунт</a>.
            </div>
        <?php endif; ?>

        <!-- СХОЖІ ТОВАРИ -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mt-5">
                <h2 class="h5 fw-semibold mb-3">Схожі товари</h2>
                <div class="row g-3 g-lg-4">
                    <?php foreach ($relatedProducts as $rp): ?>
                        <?php
                        $rpName  = htmlspecialchars($rp['name'] ?? '', ENT_QUOTES, 'UTF-8');
                        $rpSlug  = htmlspecialchars($rp['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                        $rpPrice = (float)($rp['price'] ?? 0);
                        $rpImg   = $rp['image_url'] ?? null;
                        ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="border rounded-4 bg-white h-100 d-flex flex-column">
                                <?php if ($rpImg): ?>
                                    <a href="/product/<?= $rpSlug ?>" class="ratio ratio-4x3">
                                        <img src="<?= htmlspecialchars($rpImg, ENT_QUOTES, 'UTF-8') ?>"
                                            class="w-100 h-100 object-fit-cover rounded-top"
                                            alt="<?= $rpName ?>" loading="lazy">
                                    </a>
                                <?php endif; ?>
                                <div class="p-3 d-flex flex-column">
                                    <h3 class="h6 fw-semibold mb-2"><?= $rpName ?></h3>
                                    <p class="fw-semibold mb-3"><?= number_format($rpPrice, 2, '.', ' ') ?> ₴</p>
                                    <a href="/product/<?= $rpSlug ?>" class="btn btn-outline-dark btn-sm mt-auto w-100">
                                        Деталі
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- З ЦИМ КУПУЮТЬ -->
        <?php if (!empty($frequentlyBought)): ?>
            <div class="mt-5">
                <h2 class="h5 fw-semibold mb-3">З цим купують</h2>
                <div class="row g-3 g-lg-4">
                    <?php foreach ($frequentlyBought as $fb): ?>
                        <?php
                        $fbName  = htmlspecialchars($fb['name'] ?? '', ENT_QUOTES, 'UTF-8');
                        $fbSlug  = htmlspecialchars($fb['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                        $fbPrice = (float)($fb['price'] ?? 0);
                        $fbImg   = $fb['image_url'] ?? null;
                        ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="border rounded-4 bg-white h-100 d-flex flex-column">
                                <?php if ($fbImg): ?>
                                    <a href="/product/<?= $fbSlug ?>" class="ratio ratio-4x3">
                                        <img src="<?= htmlspecialchars($fbImg, ENT_QUOTES, 'UTF-8') ?>"
                                            class="w-100 h-100 object-fit-cover rounded-top"
                                            alt="<?= $fbName ?>" loading="lazy">
                                    </a>
                                <?php endif; ?>
                                <div class="p-3 d-flex flex-column">
                                    <h3 class="h6 fw-semibold mb-2"><?= $fbName ?></h3>
                                    <p class="fw-semibold mb-3"><?= number_format($fbPrice, 2, '.', ' ') ?> ₴</p>
                                    <a href="/product/<?= $fbSlug ?>" class="btn btn-outline-dark btn-sm mt-auto w-100">
                                        Деталі
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- Модалка для збільшення фото -->
<div class="modal fade" id="productImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark border-0 rounded-4">
            <div class="modal-body p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="productImageModalImg"
                    src="<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') ?>"
                    class="w-100 h-100 object-fit-contain">
            </div>
        </div>
    </div>
</div>

<!-- Мобільний sticky-бар -->
<div class="d-lg-none fixed-bottom bg-white border-top">
    <div class="container py-2 d-flex align-items-center justify-content-between gap-3">
        <div>
            <div class="fw-bold">
                <?= number_format($price, 2, '.', ' ') ?> ₴
                <?php if ($oldPrice && $oldPrice > $price): ?>
                    <span class="text-muted text-decoration-line-through small ms-1">
                        <?= number_format($oldPrice, 2, '.', ' ') ?> ₴
                    </span>
                <?php endif; ?>
            </div>
            <div class="small text-muted">Ціна за комплект</div>
        </div>
        <button class="btn btn-dark btn-sm flex-grow-1"
            onclick="document.getElementById('addToCartForm').submit();"
            <?= $inStock ? '' : 'disabled' ?>>
            Додати до кошика
        </button>
    </div>
</div>

<script>
    function changeMainImage(src, alt) {
        var main = document.getElementById('mainProductImage');
        var modalImg = document.getElementById('productImageModalImg');
        if (main) {
            main.src = src;
            if (alt) main.alt = alt;
        }
        if (modalImg) {
            modalImg.src = src;
        }
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>