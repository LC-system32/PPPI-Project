<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$items = $products['items'] ?? [];
$page = $products['page'] ?? 1;
$perPage = $products['perPage'] ?? 12;
$total = $products['total'] ?? 0;
$totalPages = $perPage ? (int) ceil($total / $perPage) : 1;
$csrf = csrf_token();
?>

<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 32%;">
        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80"
             class="w-100 h-100 object-fit-cover" alt="Автозапчастини фон">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(18,18,18,.85), rgba(30,30,30,.55));"></div>
    <div class="container position-absolute top-50 start-50 translate-middle text-center text-lg-start">
        <p class="text-uppercase text-white-50 mb-2">Преміальний маркетплейс автозапчастин</p>
        <h1 class="display-4 fw-bold mb-3">Знайди точну деталь для свого авто</h1>
        <p class="lead text-white-50 mb-0">
            Понад <?= number_format($total) ?> позицій у каталозі, актуальна наявність і швидка доставка
        </p>
    </div>
</section>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row gy-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4 mb-4">
                            <div>
                                <p class="text-uppercase text-muted small mb-1">Фільтри каталогу</p>
                                <h2 class="h3 fw-semibold mb-0">Знайдіть потрібну позицію за кілька секунд</h2>
                            </div>
                            <div class="text-lg-end">
                                <p class="text-muted small mb-1">Знайдено товарів</p>
                                <p class="display-6 fw-bold mb-0"><?= number_format($total) ?></p>
                            </div>
                        </div>
                        <form class="row g-3 align-items-end" method="GET">
                            <div class="col-12 col-lg-4">
                                <label class="form-label text-muted small text-uppercase">Пошук</label>
                                <input type="text" name="q" class="form-control form-control-lg"
                                       placeholder="Введіть назву або артикул"
                                       value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="col-12 col-lg-4">
                                <label class="form-label text-muted small text-uppercase">Категорія</label>
                                <select name="category_id" class="form-select form-select-lg">
                                    <option value="">Усі категорії</option>
                                    <?php
                                    $renderOptions = function (array $nodes, string $prefix = '') use (&$renderOptions, $filters): void {
                                        foreach ($nodes as $category) {
                                            $selected = ((int) ($filters['category_id'] ?? 0) === (int) $category['id']) ? 'selected' : '';
                                            echo '<option value="' . (int) $category['id'] . "\" {$selected}>{$prefix}" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . '</option>';
                                            if (!empty($category['children'])) {
                                                $renderOptions($category['children'], $prefix . '↳ ');
                                            }
                                        }
                                    };
                                    $renderOptions($categories);
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 col-lg-3">
                                <label class="form-label text-muted small text-uppercase">Бренд</label>
                                <select name="brand_id" class="form-select form-select-lg">
                                    <option value="">Усі бренди</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?= (int) $brand['id'] ?>" <?= ((int) ($filters['brand_id'] ?? 0) === (int) $brand['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-lg-1 d-grid">
                                <button class="btn btn-dark btn-lg" type="submit">
                                    <i class="bi bi-search me-1"></i>
                                    Знайти
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <?php if ($items): ?>
                    <div class="row g-3 g-lg-4">
                        <?php foreach ($items as $product): ?>
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card border-0 shadow h-100">
                                    <div class="ratio ratio-4x3 bg-light rounded-top">
                                        <div class="d-flex flex-column justify-content-between p-3">
                                            <span class="badge bg-dark-subtle text-dark-emphasis align-self-start">
                                                <?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="text-muted small text-end">
                                                SKU: <?= htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column p-4">
                                        <h3 class="h5 fw-semibold mb-2"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                        <p class="text-muted small flex-grow-1 mb-3">
                                            <?= htmlspecialchars(mb_strimwidth($product['description'] ?? '', 0, 110, '...'), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <p class="text-muted small mb-1">Ціна</p>
                                                <p class="h4 fw-bold mb-0"><?= number_format($product['price'], 2, '.', ' ') ?> ₴</p>
                                            </div>
                                            <span class="badge bg-success-subtle text-success-emphasis">
                                                <?= (int) $product['stock'] > 0 ? 'В наявності' : 'Очікується' ?>
                                            </span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-dark flex-grow-1">
                                                Деталі
                                            </a>
                                            <form action="/cart/add" method="POST" class="d-grid flex-grow-1">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-dark">
                                                    <i class="bi bi-bag-plus me-1"></i>
                                                    У кошик
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary border-0 shadow-sm p-4 text-center">
                        Ми не знайшли товарів за заданими фільтрами. Спробуйте змінити параметри пошуку.
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="col-12">
                    <nav>
                        <ul class="pagination pagination-lg justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
