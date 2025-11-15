<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$items = $products['items'] ?? [];
$page = $products['page'] ?? 1;
$perPage = $products['perPage'] ?? 20;
$total = $products['total'] ?? 0;
$totalPages = $perPage ? (int) ceil($total / $perPage) : 1;
$csrf = csrf_token();
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1">Адмін-панель</p>
                <h1 class="fw-bold mb-0">Товари</h1>
            </div>
            <a href="/admin/products/create" class="btn btn-dark">Додати товар</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="table-responsive shadow-sm rounded-4">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Назва</th>
                        <th>Категорія</th>
                        <th>SKU</th>
                        <th>Ціна</th>
                        <th>Залишок</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items): ?>
                        <?php foreach ($items as $product): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format($product['price'], 2, '.', ' ') ?> ₴</td>
                                <td><?= (int) $product['stock'] ?></td>
                                <td class="text-end">
                                    <a href="/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary btn-sm">Переглянути</a>
                                    <a href="/admin/products/<?= (int) $product['id'] ?>/edit" class="btn btn-outline-dark btn-sm">Редагувати</a>
                                    <form action="/admin/products/<?= (int) $product['id'] ?>/delete" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <button class="btn btn-link text-danger btn-sm" type="submit" onclick="return confirm('Видалити товар?')">Видалити</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Товари відсутні.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
