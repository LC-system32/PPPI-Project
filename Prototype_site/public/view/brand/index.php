<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<?php $page = 'brands'; // якщо в navbar підсвічуєш активний пункт ?>

<section class="position-relative text-white overflow-hidden">
    <div class="ratio" style="--bs-aspect-ratio: 30%;">
        <img src="https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1600&q=80"
             class="w-100 h-100 object-fit-cover" alt="Бренди авто">
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(10,10,12,.9), rgba(35,35,35,.6));"></div>
    <div class="container position-absolute top-50 start-50 translate-middle text-center text-lg-start">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a class="text-white-50 text-decoration-none" href="/">Головна</a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    Бренди
                </li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-3">Виробники запчастин</h1>
        <p class="lead text-white-50 mb-0" style="max-width: 640px;">
            Обирайте запчастини від улюблених брендів. Оригінали та якісні аналоги з офіційних каналів постачання.
        </p>
    </div>
</section>

<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted small mb-1">Бренди</p>
                <h2 class="h4 fw-semibold mb-0">Усі доступні виробники</h2>
            </div>
            <span class="text-muted small">Всього: <?= count($brands ?? []) ?></span>
        </div>

        <?php if (!empty($brands)): ?>
            <div class="row g-3 g-lg-4">
                <?php foreach ($brands as $brandItem): ?>
                    <div class="col-6 col-sm-4 col-lg-3">
                        <a href="/brand/<?= htmlspecialchars($brandItem['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <h3 class="h5 fw-semibold mb-2">
                                        <?= htmlspecialchars($brandItem['name'] ?? 'Бренд', ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                    <p class="text-muted small mb-0">
                                        Переглянути товари бренду
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary border-0 shadow-sm">
                Бренди поки не додані.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
