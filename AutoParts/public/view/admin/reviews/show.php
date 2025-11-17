<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$review = $review ?? null;
?>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item"><a href="/admin/reviews" class="text-muted text-decoration-none">Відгуки</a></li>
                <li class="breadcrumb-item active" aria-current="page">Деталі</li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0">Деталі відгуку</h1>
                <p class="text-muted small mb-0">Перегляд одного відгуку</p>
            </div>
            <div>
                <a href="/admin/reviews" class="btn btn-outline-secondary btn-sm">Назад</a>
            </div>
        </div>

        <?php if (!$review): ?>
            <div class="alert alert-warning">Відгук не знайдено.</div>
        <?php else: ?>
            <div class="admin-card p-4">
                <div class="mb-3">
                    <div class="h5 mb-0"><?= htmlspecialchars($review['author'] ?? 'Користувач', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-muted">Товар: <?= htmlspecialchars($review['product_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <div class="mb-3 small">
                    <?php $rr = (int)($review['rating'] ?? 0); ?>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= $rr): ?>
                            <i class="bi bi-star-fill text-warning"></i>
                        <?php else: ?>
                            <i class="bi bi-star text-muted"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <div class="mb-3">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($review['text'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                </div>

                <div class="small text-muted">Створено: <?= htmlspecialchars($review['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>

                <div class="mt-3">
                    <form action="/admin/reviews/<?= (int)$review['id'] ?>/approve" method="post" class="d-inline-block">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button class="btn btn-success">Схвалити</button>
                    </form>

                    <form action="/admin/reviews/<?= (int)$review['id'] ?>/reject" method="post" class="d-inline-block ms-2">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button class="btn btn-danger">Відхилити</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
