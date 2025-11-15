<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 30%;">
        <img src="https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1600&q=80"
             class="w-100 h-100 object-fit-cover" alt="Усі категорії запчастин">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(115deg, rgba(10,10,12,.9), rgba(35,35,30,.6));"></div>
    <div class="container position-absolute top-50 start-50 translate-middle text-center text-lg-start">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white-50" href="/">Головна</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Категорії</li>
            </ol>
        </nav>
        <p class="text-uppercase text-white-50 small mb-2">Каталог</p>
        <h1 class="display-5 fw-bold mb-3">Усі категорії запчастин</h1>
        <p class="lead text-white-50 mb-0" style="max-width: 640px;">
            Перегляньте повну ієрархію розділів та швидко переходьте до потрібних підкатегорій.
        </p>
    </div>
</section>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($tree)): ?>
                <?php foreach ($tree as $root): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-dark-subtle text-dark-emphasis mb-2">Root-категорія</span>
                                <h2 class="h5 fw-semibold mb-2">
                                    <a href="/category/<?= htmlspecialchars($root['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($root['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h2>
                                <?php if (!empty($root['description'])): ?>
                                    <p class="text-muted small mb-3">
                                        <?= nl2br(htmlspecialchars($root['description'], ENT_QUOTES, 'UTF-8')) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($root['children'])): ?>
                                    <ul class="list-unstyled small mb-3">
                                        <?php foreach ($root['children'] as $child): ?>
                                            <li class="mb-1">
                                                <a href="/category/<?= htmlspecialchars($child['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                   class="text-decoration-none">
                                                    <?= htmlspecialchars($child['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <a href="/category/<?= htmlspecialchars($root['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="btn btn-dark mt-auto w-100">
                                    Перейти до розділу
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-secondary border-0 shadow-sm">
                        Категорії ще не додані.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

