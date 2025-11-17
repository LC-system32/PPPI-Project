<?php
// File: /views/home/index.php (Bootstrap-only carousels with external arrows)
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<!-- === HERO (оригінал, не чіпаємо) === -->
<section class="hero position-relative text-white text-center">
    <div class="overlay position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.7)); z-index:1;"></div>

    <div class="container position-relative py-5" style="z-index:1;">
        <h1 class="fw-bold display-5 mb-3">AutoParts — Все для вашого авто в одному місці</h1>
        <p class="lead mb-4 mx-auto" style="max-width: 800px;">
            Широкий вибір запчастин: оригінальні та якісні аналоги, швидка доставка по всій Україні та зручний пошук за маркою, моделлю або VIN.
        </p>

        <div class="d-flex justify-content-center gap-4 flex-wrap mb-4" aria-label="Ключові переваги">
            <div class="feature-item text-center">
                <i class="bi bi-truck fs-1 mb-2 text-warning" aria-hidden="true"></i>
                <p class="mb-0">Доставка 24/7</p>
            </div>
            <div class="feature-item text-center">
                <i class="bi bi-award fs-1 mb-2 text-warning" aria-hidden="true"></i>
                <p class="mb-0">Гарантія якості</p>
            </div>
            <div class="feature-item text-center">
                <i class="bi bi-headset fs-1 mb-2 text-warning" aria-hidden="true"></i>
                <p class="mb-0">Підтримка онлайн</p>
            </div>
            <div class="feature-item text-center">
                <i class="bi bi-search fs-1 mb-2 text-warning" aria-hidden="true"></i>
                <p class="mb-0">Швидкий пошук</p>
            </div>
        </div>

        <a href="/about" class="btn btn-outline-light btn-lg fw-semibold px-4 shadow-sm">Дізнатись більше</a>
    </div>

    <img src="https://t4.ftcdn.net/jpg/03/80/98/93/240_F_380989347_IKOXAkY4e3pYmCyIrKSngo48EZhLFYDO.jpg" alt="Hero background"
        class="position-absolute top-0 start-0 w-100 h-100"
        style="object-fit: cover; z-index:0;">
</section>

<!-- === SEARCH MODULE (оригінал, не чіпаємо) === -->
<div class="search-module container position-relative mt-n4 z-2">
    <form action="/catalog" method="GET"
        class="search-form d-flex align-items-center gap-3 p-3 rounded-4 shadow-sm bg-white"
        role="search" aria-label="Пошук запчастин">

        <input type="text"
            name="q"
            class="form-control flex-grow-1"
            placeholder="Введіть артикул, VIN або назву деталі, наприклад, 'Фільтр масляний BMW E46'"
            aria-label="Пошуковий запит">

        <button type="submit"
            class="btn btn-dark fw-semibold px-4 shadow-sm">
            Пошук
        </button>
    </form>
</div>

<!-- === ПОПУЛЯРНІ МАРКИ АВТО (Bootstrap carousel + зовнішні кнопки) === -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0 h3">Популярні марки авто</h2>
            <a href="/brands" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">Всі марки</a>
        </div>

        <?php if (!empty($brands) && is_array($brands)): ?>
            <?php $brandsPerSlide = 6;
            $brandsCount = count($brands); ?>

            <div class="d-flex align-items-center">
                <!-- Ліва кнопка -->
                <button class="btn btn-outline-secondary me-2 shadow-sm"
                    type="button"
                    data-bs-target="#brandsCarousel"
                    data-bs-slide="prev"
                    aria-label="Попередні бренди">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <!-- Карусель брендів -->
                <div id="brandsCarousel" class="carousel slide flex-grow-1" data-bs-ride="false">
                    <div class="carousel-inner">

                        <?php foreach ($brands as $i => $brand): ?>
                            <?php
                            $brandName = htmlspecialchars($brand['name'] ?? 'Марка', ENT_QUOTES, 'UTF-8');
                            $brandSlug = htmlspecialchars($brand['slug'] ?? '#', ENT_QUOTES, 'UTF-8');
                            $brandLogo = $brand['logo_url'] ?? null;

                            if ($i % $brandsPerSlide === 0):
                                $activeClass = ($i === 0) ? ' active' : '';
                            ?>
                                <div class="carousel-item<?= $activeClass; ?>">
                                    <div class="row g-3 justify-content-start">
                                    <?php endif; ?>

                                    <div class="col-6 col-md-4 col-lg-2">
                                        <a href="/brand/<?= $brandSlug ?>" class="text-decoration-none">
                                            <div class="card text-center border-0 shadow-sm py-3 h-100 rounded-4 bg-white">
                                                <?php if ($brandLogo): ?>
                                                    <img src="<?= htmlspecialchars($brandLogo, ENT_QUOTES, 'UTF-8') ?>"
                                                        alt="<?= $brandName ?> логотип"
                                                        class="img-fluid img-thumbnail rounded bg-white p-1 mx-auto mb-2"
                                                        loading="lazy"
                                                        style="max-height:42px; width:auto;">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-dark text-white mx-auto mb-2 d-flex align-items-center justify-content-center"
                                                        style="width:42px;height:42px;">
                                                        <span class="fw-bold"><?= mb_strtoupper(mb_substr($brandName, 0, 1)) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <h6 class="fw-semibold mb-0 text-dark"><?= $brandName ?></h6>
                                            </div>
                                        </a>
                                    </div>

                                    <?php
                                    $isLastInSlide = ($i % $brandsPerSlide === $brandsPerSlide - 1) || ($i === $brandsCount - 1);
                                    if ($isLastInSlide): ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Права кнопка -->
                <button class="btn btn-outline-secondary ms-2 shadow-sm"
                    type="button"
                    data-bs-target="#brandsCarousel"
                    data-bs-slide="next"
                    aria-label="Наступні бренди">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary mb-0" role="status">Список брендів тимчасово недоступний.</div>
        <?php endif; ?>
    </div>
</section>

<!-- === ПОПУЛЯРНІ КАТЕГОРІЇ (Bootstrap carousel + зовнішні кнопки) === -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0 h3">Популярні категорії запчастин</h2>
            <a href="/categories" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">Дивитися всі</a>
        </div>

        <?php if (!empty($categories) && is_array($categories)): ?>
            <?php $catsPerSlide = 4;
            $catsCount = count($categories); ?>

            <div class="d-flex align-items-center">
                <!-- Ліва кнопка -->
                <button class="btn btn-outline-secondary me-2 shadow-sm"
                    type="button"
                    data-bs-target="#categoriesCarousel"
                    data-bs-slide="prev"
                    aria-label="Попередні категорії">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <!-- Карусель категорій -->
                <div id="categoriesCarousel" class="carousel slide flex-grow-1" data-bs-ride="false">
                    <div class="carousel-inner">

                        <?php foreach ($categories as $i => $category): ?>
                            <?php
                            $catName = htmlspecialchars($category['name'] ?? 'Категорія', ENT_QUOTES, 'UTF-8');
                            $catSlug = htmlspecialchars($category['slug'] ?? '#', ENT_QUOTES, 'UTF-8');
                            $catDesc = trim((string)($category['description'] ?? ''));
                            $catDesc = $catDesc !== '' ? nl2br(str_replace('\n', "\n", htmlspecialchars($catDesc, ENT_QUOTES, 'UTF-8'))) : 'Перевірені виробники, швидка доставка.';
                            $catIcon = $category['icon'] ?? null;

                            if ($i % $catsPerSlide === 0):
                                $activeClass = ($i === 0) ? ' active' : '';
                            ?>
                                <div class="carousel-item<?= $activeClass; ?>">
                                    <div class="row g-4">
                                    <?php endif; ?>

                                    <div class="col-6 col-md-3">
                                        <a href="/categories/<?= $catSlug ?>" class="text-decoration-none text-dark" aria-label="Категорія: <?= $catName ?>">
                                            <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="mb-2">
                                                        <?php if ($catIcon): ?>
                                                            <img src="<?= htmlspecialchars($catIcon, ENT_QUOTES, 'UTF-8') ?>"
                                                                alt="Іконка <?= $catName ?>" loading="lazy"
                                                                class="img-fluid"
                                                                style="height:28px;width:auto;">
                                                        <?php else: ?>
                                                            <i class="bi bi-grid fs-4" aria-hidden="true"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <h5 class="card-title fw-semibold mb-2"><?= $catName ?></h5>
                                                    <p class="text-muted small mb-3"><?= $catDesc ?></p>
                                                    <span class="btn btn-dark btn-sm px-3 py-2 fw-semibold shadow-sm w-100 mt-auto">Переглянути</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <?php
                                    $isLastInSlide = ($i % $catsPerSlide === $catsPerSlide - 1) || ($i === $catsCount - 1);
                                    if ($isLastInSlide): ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Права кнопка -->
                <button class="btn btn-outline-secondary ms-2 shadow-sm"
                    type="button"
                    data-bs-target="#categoriesCarousel"
                    data-bs-slide="next"
                    aria-label="Наступні категорії">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary mb-0">Категорії тимчасово недоступні.</div>
        <?php endif; ?>
    </div>
</section>

<!-- === РЕКОМЕНДОВАНІ ТОВАРИ (Bootstrap carousel + зовнішні кнопки) === -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0 h3">Рекомендовані товари</h2>
            <a href="/catalog" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">Дивитися все</a>
        </div>

        <?php if (!empty($featured) && is_array($featured)): ?>
            <?php $productsPerSlide = 4;
            $prodCount = count($featured); ?>

            <div class="d-flex align-items-center">
                <!-- Ліва кнопка -->
                <button class="btn btn-outline-secondary me-2 shadow-sm"
                    type="button"
                    data-bs-target="#featuredCarousel"
                    data-bs-slide="prev"
                    aria-label="Попередні товари">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <!-- Карусель товарів -->
                <div id="featuredCarousel" class="carousel slide flex-grow-1" data-bs-ride="false">
                    <div class="carousel-inner" itemscope itemtype="https://schema.org/ItemList">

                        <?php foreach ($featured as $i => $product): ?>
                            <?php
                            $pName   = htmlspecialchars($product['name'] ?? 'Товар', ENT_QUOTES, 'UTF-8');
                            $pSlug   = htmlspecialchars($product['slug'] ?? '#', ENT_QUOTES, 'UTF-8');
                            $pCat    = htmlspecialchars($product['category_name'] ?? 'Категорія', ENT_QUOTES, 'UTF-8');
                            $pPrice  = isset($product['price']) ? (float)$product['price'] : null;
                            $pImg    = $product['image_url'] ?? null;
                            $pInStock = (bool)($product['in_stock'] ?? true);
                            $pRating  = isset($product['rating']) ? (float)$product['rating'] : null;
                            $pReviews = isset($product['reviews_count']) ? (int)$product['reviews_count'] : null;

                            if ($i % $productsPerSlide === 0):
                                $activeClass = ($i === 0) ? ' active' : '';
                            ?>
                                <div class="carousel-item<?= $activeClass; ?>">
                                    <div class="row g-4">
                                    <?php endif; ?>

                                    <div class="col-6 col-md-3" itemprop="itemListElement" itemscope itemtype="https://schema.org/Product">
                                        <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">

                                            <?php if ($pImg): ?>
                                                <a href="/product/<?= $pSlug ?>" class="ratio ratio-4x3">
                                                    <img src="<?= htmlspecialchars($pImg, ENT_QUOTES, 'UTF-8') ?>"
                                                        class="card-img-top rounded-top"
                                                        alt="<?= $pName ?>" loading="lazy"
                                                        style="object-fit:cover; border-top-left-radius:inherit;border-top-right-radius:inherit;">
                                                </a>
                                            <?php else: ?>
                                                <a href="/product/<?= $pSlug ?>" class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted text-decoration-none">
                                                    <i class="bi bi-image fs-3" aria-hidden="true"></i>
                                                </a>
                                            <?php endif; ?>

                                            <div class="card-body d-flex flex-column">
                                                <meta itemprop="category" content="<?= $pCat ?>">
                                                <a href="/product/<?= $pSlug ?>" class="text-decoration-none text-dark">
                                                    <h5 class="fw-semibold mb-1" itemprop="name"><?= $pName ?></h5>
                                                </a>
                                                <p class="text-muted small mb-2"><?= $pCat ?></p>

                                                <?php if ($pRating !== null): ?>
                                                    <div class="d-flex align-items-center mb-2" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                                                        <meta itemprop="ratingValue" content="<?= max(0, min(5, $pRating)) ?>">
                                                        <?php if ($pReviews !== null): ?>
                                                            <meta itemprop="reviewCount" content="<?= $pReviews ?>">
                                                        <?php endif; ?>
                                                        <div class="me-2 text-warning" aria-label="Рейтинг <?= number_format($pRating, 1) ?> з 5">
                                                            <?php
                                                            $full  = (int)floor($pRating);
                                                            $half  = ($pRating - $full) >= 0.5 ? 1 : 0;
                                                            $empty = 5 - $full - $half;
                                                            echo str_repeat('<i class="bi bi-star-fill me-1"></i>', $full);
                                                            echo str_repeat('<i class="bi bi-star-half me-1"></i>', $half);
                                                            echo str_repeat('<i class="bi bi-star me-1"></i>', $empty);
                                                            ?>
                                                        </div>
                                                        <small class="text-muted">
                                                            <?= number_format($pRating, 1) ?>
                                                            <?= $pReviews ? ' · ' . $pReviews . ' відгуків' : '' ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>

                                                <p class="fw-bold fs-5 mb-3" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                    <?php if ($pPrice !== null): ?>
                                                        <meta itemprop="priceCurrency" content="UAH">
                                                        <span itemprop="price"><?= number_format($pPrice, 2, '.', ' ') ?></span> ₴
                                                    <?php else: ?>
                                                        <span class="text-muted">Ціну уточнюйте</span>
                                                    <?php endif; ?>
                                                </p>

                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <span class="badge <?= $pInStock ? 'text-bg-success' : 'text-bg-secondary' ?> rounded-pill small">
                                                        <?= $pInStock ? 'В наявності' : 'Під замовлення' ?>
                                                    </span>
                                                </div>
                                                <div class="d-grid mt-auto">
                                                    <a href="/product/<?= $pSlug ?>" class="btn btn-dark btn-sm px-3 py-2 fw-semibold shadow-sm w-100">До товару</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $isLastInSlide = ($i % $productsPerSlide === $productsPerSlide - 1) || ($i === $prodCount - 1);
                                    if ($isLastInSlide): ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Права кнопка -->
                <button class="btn btn-outline-secondary ms-2 shadow-sm"
                    type="button"
                    data-bs-target="#featuredCarousel"
                    data-bs-slide="next"
                    aria-label="Наступні товари">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary mb-0">Рекомендації тимчасово недоступні.</div>
        <?php endif; ?>
    </div>
</section>

<!-- === ЯК ЦЕ ПРАЦЮЄ === -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold h3">Як це працює</h2>
            <p class="text-muted">Три кроки до правильної запчастини.</p>
        </div>
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
                    <div class="card-body">
                        <span class="badge bg-dark rounded-pill mb-2">1</span>
                        <h5 class="fw-semibold mb-2">Знаходите деталь</h5>
                        <p class="text-muted mb-0">Введіть артикул, VIN або назву — фільтруємо сумісні позиції.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
                    <div class="card-body">
                        <span class="badge bg-dark rounded-pill mb-2">2</span>
                        <h5 class="fw-semibold mb-2">Оформлюєте замовлення</h5>
                        <p class="text-muted mb-0">Обирайте зручну оплату та доставку, підтверджуємо наявність.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
                    <div class="card-body">
                        <span class="badge bg-dark rounded-pill mb-2">3</span>
                        <h5 class="fw-semibold mb-2">Отримуєте швидко</h5>
                        <p class="text-muted mb-0">Відправляємо того ж дня, надаємо ТТН та консультацію з установки.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- === ВІДГУКИ === -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold h3 mb-0">Відгуки клієнтів</h2>
        </div>
        <div class="row g-4">
            <?php
            $reviews = $reviews ?? [
                ['name' => 'Олександр', 'text' => 'Швидко підібрали по VIN, доставка на наступний день. Рекомендую!', 'rating' => 5],
                ['name' => 'Марина', 'text' => 'Взяла аналог — якість супер, ціна краща за офіціал.', 'rating' => 4.5],
                ['name' => 'Ігор', 'text' => 'Чітка консультація та гарантія. Все підійшло.', 'rating' => 5],
            ];
            ?>
            <?php foreach ($reviews as $r): ?>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 bg-body-tertiary">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center me-2" style="width:36px;height:36px;">
                                    <span class="small"><?= mb_strtoupper(mb_substr($r['name'], 0, 1)) ?></span>
                                </div>
                                <strong><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <p class="mb-2 text-muted"><?= htmlspecialchars($r['text'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php
                            $rv = (float)$r['rating'];
                            $full = (int)floor($rv);
                            $half = ($rv - $full) >= 0.5 ? 1 : 0;
                            $empty = 5 - $full - $half;
                            ?>
                            <div aria-label="Оцінка <?= number_format($rv, 1) ?> з 5" class="text-warning">
                                <?= str_repeat('<i class="bi bi-star-fill me-1"></i>', $full) ?>
                                <?= str_repeat('<i class="bi bi-star-half me-1"></i>', $half) ?>
                                <?= str_repeat('<i class="bi bi-star me-1"></i>', $empty) ?>
                                <small class="text-muted ms-2"><?= number_format($rv, 1) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- === ФІНАЛЬНИЙ CTA === -->
<section class="py-5 text-center bg-light">
    <div class="container">
        <div class="p-4 p-md-5 rounded-4 border shadow-sm bg-white">
            <h2 class="fw-bold h3 mb-2">Готові замовити?</h2>
            <p class="text-muted mb-3">Спробуйте пошук за VIN або напишіть нам — допоможемо обрати оптимальний варіант.</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="#"
                    class="btn btn-dark btn-lg px-4 fw-semibold shadow-sm">Підібрати по VIN</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- JSON-LD (як було) -->
<?php
$orgJson = [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "name" => "AutoParts",
    "url" => rtrim((isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'https') . '://' . ($_SERVER['HTTP_HOST'] ?? ''), '/'),
    "logo" => "/assets/logo.png",
    "sameAs" => ["/about", "/contacts"]
];
$breadcrumbs = ["@context" => "https://schema.org", "@type" => "BreadcrumbList", "itemListElement" => [
    ["@type" => "ListItem", "position" => 1, "name" => "Головна", "item" => "/"]
]];
$productsLd = [];
if (!empty($featured) && is_array($featured)) {
    foreach ($featured as $p) {
        $productsLd[] = [
            "@type" => "Product",
            "name" => $p['name'] ?? 'Товар',
            "image" => $p['image_url'] ?? null,
            "category" => $p['category_name'] ?? null,
            "offers" => [
                "@type" => "Offer",
                "priceCurrency" => "UAH",
                "price" => isset($p['price']) ? (float)$p['price'] : null,
                "availability" => !empty($p['in_stock']) ? "https://schema.org/InStock" : "https://schema.org/PreOrder",
                "url" => "/product/" . ($p['slug'] ?? '#')
            ]
        ];
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>