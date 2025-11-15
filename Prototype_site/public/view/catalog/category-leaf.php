<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<section class="bg-light border-bottom">
    <div class="container py-4 py-lg-5">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Головна</a></li>
                    <li class="breadcrumb-item"><a href="/category">Категорії</a></li>
                <?php foreach ($breadcrumbs as $crumb): ?>
                    <li class="breadcrumb-item<?= $crumb['id'] === $category['id'] ? ' active' : '' ?>"<?= $crumb['id'] === $category['id'] ? ' aria-current="page"' : '' ?>>
                        <?php if ($crumb['id'] === $category['id']): ?>
                            <?= htmlspecialchars($crumb['name']) ?>
                        <?php else: ?>
                            <a href="/category/<?= htmlspecialchars($crumb['slug']) ?>">
                                <?= htmlspecialchars($crumb['name']) ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <p class="text-uppercase text-muted small mb-2">Категорія</p>
                <h1 class="display-6 mb-3"><?= htmlspecialchars($category['name']) ?></h1>
                <?php if (!empty($category['description'])): ?>
                    <p class="lead text-muted mb-0"><?= nl2br(htmlspecialchars($category['description'])) ?></p>
                <?php else: ?>
                    <p class="lead text-muted mb-0">
                        Добірка товарів, підібраних під конкретний вузол авто. Доступні лише актуальні позиції на складі.
                    </p>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white rounded-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">В наявності</span>
                        <span class="h4 mb-0"><?= (int) $productsPage['total'] ?></span>
                    </div>
                    <?php if (!empty($parent)): ?>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Root-категорія</span>
                            <a href="/category/<?= htmlspecialchars($parent['slug']) ?>" class="link-dark text-decoration-none">
                                <?= htmlspecialchars($parent['name']) ?> →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (!empty($productsPage['items'])): ?>
            <div class="row g-3 g-lg-4 mb-4">
                <?php foreach ($productsPage['items'] as $product): ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body d-flex flex-column">
                                <?php if (!empty($product['brand_name'])): ?>
                                    <span class="text-uppercase text-muted small mb-2"><?= htmlspecialchars($product['brand_name']) ?></span>
                                <?php endif; ?>
                                <h2 class="h5 mb-2"><?= htmlspecialchars($product['name']) ?></h2>
                                <?php if (!empty($product['category_name'])): ?>
                                    <p class="text-muted small mb-2"><?= htmlspecialchars($product['category_name']) ?></p>
                                <?php endif; ?>
                                <p class="fw-bold fs-5 mb-1"><?= number_format((float) $product['price'], 2, '.', ' ') ?> ₴</p>
                                <p class="text-muted small mb-3">
                                    <?= (int) $product['stock'] > 0 ? 'В наявності на складі' : 'Очікується поставка' ?>
                                </p>
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="/product/<?= htmlspecialchars($product['slug']) ?>" class="btn btn-dark flex-grow-1">
                                        Переглянути
                                    </a>
                                    <button class="btn btn-outline-dark" type="button" disabled>В кошик</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($productsPage['pages']) && $productsPage['pages'] > 1): ?>
                <nav aria-label="Пагінація">
                    <ul class="pagination pagination-lg justify-content-center">
                        <?php for ($i = 1; $i <= $productsPage['pages']; $i++): ?>
                            <li class="page-item<?= $i === (int) $productsPage['page'] ? ' active' : '' ?>">
                                <a class="page-link" href="/category/<?= htmlspecialchars($category['slug']) ?>?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-secondary">
                У цій підкатегорії товари ще не додані.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
