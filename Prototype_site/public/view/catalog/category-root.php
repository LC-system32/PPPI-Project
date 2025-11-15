<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<section class="bg-light border-bottom">
    <div class="container py-4 py-lg-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/">Головна</a></li>
                    <li class="breadcrumb-item"><a href="/categories">Категорії</a></li>
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
                <h1 class="display-6 mb-3"><?= htmlspecialchars($category['name']) ?></h1>
                <?php if (!empty($category['description'])): ?>
                    <p class="lead text-muted mb-0"><?= nl2br(htmlspecialchars($category['description'])) ?></p>
                <?php else: ?>
                    <p class="lead text-muted mb-0">
                        Оберіть групу товарів, щоб перейти до конкретної категорії автозапчастин.
                    </p>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="p-4 bg-white rounded-3 shadow-sm">
                    <p class="text-muted mb-1">Підкатегорій</p>
                    <p class="h3 mb-0"><?= count($children ?? []) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (!empty($children)): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php foreach ($children as $child): ?>
                    <div class="col">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-dark-subtle text-dark-emphasis mb-3">Підкатегорія</span>
                                <h2 class="h5 mb-2"><?= htmlspecialchars($child['name']) ?></h2>
                                <?php if (!empty($child['description'])): ?>
                                    <p class="text-muted small mb-3">
                                        <?= nl2br(htmlspecialchars($child['description'])) ?>
                                    </p>
                                <?php endif; ?>
                                <ul class="list-unstyled small text-muted mb-4">
                                    <li class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">→</span>
                                        Перейти до асортименту
                                    </li>
                                </ul>
                                <a href="/category/<?= htmlspecialchars($child['slug']) ?>" class="btn btn-dark mt-auto">
                                    Переглянути товари
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary">
                Підкатегорії для цієї root-категорії ще не створені.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
