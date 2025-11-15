<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 30%;">
        <img src="https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1600&q=80"
             class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($category['name']) ?>">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(115deg, rgba(16,16,18,.9), rgba(30,30,28,.55));"></div>
    <div class="container position-absolute top-50 start-50 translate-middle text-center text-lg-start">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white-50" href="/">Головна</a></li>
                <li class="breadcrumb-item"><a class="text-white-50" href="/catalog">Каталог</a></li>
                <?php foreach ($breadcrumbs as $crumb): ?>
                    <li class="breadcrumb-item<?= $crumb['id'] == $category['id'] ? ' active' : '' ?>"<?= $crumb['id'] == $category['id'] ? ' aria-current="page"' : '' ?>>
                        <?php if ($crumb['id'] == $category['id']): ?>
                            <?= htmlspecialchars($crumb['name']) ?>
                        <?php else: ?>
                            <a class="text-white" href="/category/<?= htmlspecialchars($crumb['slug']) ?>">
                                <?= htmlspecialchars($crumb['name']) ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($category['name']) ?></h1>
        <p class="lead text-white-50 mb-0">
            <?= !empty($category['description'])
                ? nl2br(htmlspecialchars($category['description']))
                : 'Професійний добір запчастин із гарантією та швидкою доставкою.' ?>
        </p>
    </div>
</section>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Підкатегорій</p>
                        <p class="display-6 fw-bold mb-0"><?= count($children ?? []) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Товарів у розділі</p>
                        <p class="display-6 fw-bold mb-0"><?= (int) ($productsPage['total'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Оновлено</p>
                        <p class="display-6 fw-bold mb-0"><?= date('d.m.Y') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($children)): ?>
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Оберіть підкатегорію</p>
                        <h2 class="h4 fw-semibold mb-0">Групи товарів у розділі</h2>
                    </div>
                    <span class="badge text-bg-dark rounded-pill"><?= count($children) ?> категорій</span>
                </div>
                <div class="row g-3 g-lg-4">
                    <?php foreach ($children as $child): ?>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="card border-0 shadow h-100">
                                <div class="card-body d-flex flex-column">
                                    <span class="text-uppercase text-muted small mb-2">Підкатегорія</span>
                                    <h3 class="h5 mb-2"><?= htmlspecialchars($child['name']) ?></h3>
                                    <?php if (!empty($child['description'])): ?>
                                        <p class="text-muted small mb-3">
                                            <?= nl2br(htmlspecialchars($child['description'])) ?>
                                        </p>
                                    <?php endif; ?>
                                    <a href="/category/<?= htmlspecialchars($child['slug']) ?>" class="btn btn-outline-dark mt-auto">
                                        Переглянути товари
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-uppercase text-muted small mb-1">Товари</p>
                <h2 class="h4 fw-semibold mb-0">Доступні позиції</h2>
            </div>
            <span class="text-muted small">Знайдено: <?= (int) ($productsPage['total'] ?? 0) ?></span>
        </div>

        <?php if (!empty($productsPage['items'])): ?>
            <div class="row g-3 g-lg-4">
                <?php foreach ($productsPage['items'] as $product): ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body d-flex flex-column p-4">
                                <span class="text-muted small mb-2"><?= htmlspecialchars($product['category_name'] ?? '') ?></span>
                                <h3 class="h5 fw-semibold mb-2"><?= htmlspecialchars($product['name']) ?></h3>
                                <p class="fw-bold fs-5 mb-3"><?= number_format($product['price'] ?? 0, 2, '.', ' ') ?> ₴</p>
                                <a href="/product/<?= htmlspecialchars($product['slug']) ?>" class="btn btn-dark mt-auto">
                                    Деталі
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (($productsPage['pages'] ?? 1) > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination pagination-lg justify-content-center">
                        <?php for ($i = 1; $i <= $productsPage['pages']; $i++): ?>
                            <li class="page-item <?= $i == $productsPage['page'] ? 'active' : '' ?>">
                                <a class="page-link" href="/category/<?= htmlspecialchars($category['slug']) ?>?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-secondary border-0 shadow-sm">
                У цій категорії товари поки недоступні.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
