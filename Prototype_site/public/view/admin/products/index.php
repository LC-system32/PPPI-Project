<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$items = $products['items'] ?? [];
$page = $products['page'] ?? 1;
$perPage = $products['perPage'] ?? 20;
$total = $products['total'] ?? 0;
$totalPages = $perPage ? (int)ceil($total / $perPage) : 1;
$csrf = csrf_token();
$search = trim($_GET['q'] ?? '');
$message = $message ?? null;
?>

<style>
    :root { --sp-md: 1.5rem; --card-radius: 14px; --muted: #6c757d; }
    .admin-card { background: #fff; border-radius: var(--card-radius); box-shadow: 0 6px 18px rgba(21, 28, 64, 0.06); border: 1px solid rgba(16, 24, 40, 0.03); }
    .p-4 { padding: var(--sp-md) !important; }
    .display-6 { font-weight: 700; }
    .breadcrumb .breadcrumb-item { color: var(--muted); }
    .filters-card .form-control, .filters-card .form-select { height: calc(var(--sp-md) * 2.2); border-radius: 10px; }
    .status-chip { border-radius: 999px; padding: .35rem .9rem; font-size: .85rem; }
    .table-admin thead th { border-bottom: 1px solid rgba(2, 6, 23, 0.06); font-weight: 600; font-size: 0.875rem; }
    .table-admin tbody tr { height: 60px; transition: background 0.12s, transform 0.08s; }
    .table-admin tbody tr:hover { background: #fbfcfd; transform: translateY(-1px); }
    .pagination .page-link { border-radius: 999px; min-width: 40px; min-height: 40px; }
    .breadcrumb { background: transparent; padding: 0; }
</style>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item active" aria-current="page">Товари</li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0">Товари</h1>
                <p class="text-muted small mb-0">Керування товарами в каталозі</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/products" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-clockwise"></i> Оновити</a>
                <a href="/admin/products/create" class="btn btn-dark btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-plus-lg"></i> Новий товар</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="admin-card filters-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Фільтри</h5>
                        <small class="text-muted">Пошук і сортування</small>
                    </div>
                    <form id="productsToolbar" class="row g-3">
                        <div class="col-12">
                            <label for="productSearch" class="form-label small">Пошук за назвою/ID</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input id="productSearch" name="q" class="form-control" type="search" placeholder="SKU, назва, ID" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Шукати</button>
                        </div>
                        <div class="col-6 text-end">
                            <a href="/admin/products" class="btn btn-outline-secondary btn-sm w-100">Очистити</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-md-6">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Всього товарів</div>
                            <div class="h4 mt-2"><?= (int)$total ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-card p-4">
                            <div class="small text-muted">На сторінці</div>
                            <div class="h4 mt-2"><?= count($items) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Сторінок</div>
                            <div class="h4 mt-2"><?= (int)$totalPages ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="admin-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Список товарів</h5>
                <div class="small text-muted">Сторінка <?= (int)$page ?> з <?= (int)$totalPages ?></div>
            </div>

            <?php if (empty($items)): ?>
                <div class="alert alert-info mb-0">Товарів не знайдено.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-admin align-middle mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th>ID</th>
                                <th>Назва</th>
                                <th>SKU</th>
                                <th>Категорія</th>
                                <th>Ціна</th>
                                <th>Залишок</th>
                                <th style="width: 200px;" class="text-end">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $product): ?>
                                <?php $stockColor = (int)$product['stock'] > 0 ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;'; ?>
                                <tr>
                                    <td class="fw-semibold"><?= (int)$product['id'] ?></td>
                                    <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><code class="small"><?= htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                    <td class="small"><?= htmlspecialchars($product['category_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-success fw-semibold"><?= number_format($product['price'], 2, '.', ' ') ?> ₴</td>
                                    <td>
                                        <span class="status-chip" style="<?= $stockColor ?>">
                                            <?= (int)$product['stock'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Переглянути</a>
                                        <a href="/admin/products/<?= (int)$product['id'] ?>/edit" class="btn btn-sm btn-outline-primary mt-2 mb-2">Редагувати</a>
                                        <form action="/admin/products/<?= (int)$product['id'] ?>/delete" method="POST" class="d-inline-block ms-1">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                            <button class="btn btn-sm btn-danger" type="button" data-confirm>Видалити</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?><?= $search ? '&q=' . urlencode($search) : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <div class="small text-muted">Сторінка <?= (int)$page ?> з <?= (int)$totalPages ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
