<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$items = $users ?? [];
$message = $message ?? null;
$csrf = function_exists('csrf_token') ? csrf_token() : '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 20;
$search = trim($_GET['q'] ?? '');

// Filter by search
$filtered = [];
if (!empty($search)) {
    foreach ($items as $item) {
        if (stripos($item['login'] ?? '', $search) !== false || stripos($item['email'] ?? '', $search) !== false || stripos((string)($item['id'] ?? ''), $search) !== false) {
            $filtered[] = $item;
        }
    }
} else {
    $filtered = $items;
}

// Paginate
$total = count($filtered);
$pages = max(1, ceil($total / $perPage));
$page = min($page, $pages);
$offset = ($page - 1) * $perPage;
$paginated = array_slice($filtered, $offset, $perPage);
?>

<style>
    :root { --sp-md: 1.5rem; --card-radius: 14px; --muted: #6c757d; }
    .admin-card { background: #fff; border-radius: var(--card-radius); box-shadow: 0 6px 18px rgba(21, 28, 64, 0.06); border: 1px solid rgba(16, 24, 40, 0.03); }
    .p-4 { padding: var(--sp-md) !important; }
    .display-6 { font-weight: 700; }
    .breadcrumb .breadcrumb-item { color: var(--muted); }
    .filters-card .form-control { height: calc(var(--sp-md) * 2.2); border-radius: 10px; }
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
                <li class="breadcrumb-item active" aria-current="page">Користувачі</li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0">Користувачі</h1>
                <p class="text-muted small mb-0">Керування користувачами системи</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/users" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-clockwise"></i> Оновити</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="admin-card filters-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Пошук</h5>
                        <small class="text-muted">Введіть дані</small>
                    </div>
                    <form id="usersToolbar" class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input id="usersSearch" name="q" class="form-control" type="search" placeholder="Логін, email, ID" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Шукати</button>
                        </div>
                        <div class="col-6 text-end">
                            <a href="/admin/users" class="btn btn-outline-secondary btn-sm w-100">Очистити</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-md-6">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Всього користувачів</div>
                            <div class="h4 mt-2"><?= count($items) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Результатів пошуку</div>
                            <div class="h4 mt-2"><?= count($filtered) ?></div>
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
                <h5 class="mb-0">Список користувачів</h5>
                <div class="small text-muted">Показано <?= count($paginated) ?> записів</div>
            </div>

            <?php if (empty($paginated)): ?>
                <div class="alert alert-info mb-0">Користувачів не знайдено.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-admin align-middle mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th>ID</th>
                                <th>Логін</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th style="width: 180px;" class="text-end">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paginated as $u): ?>
                                <tr>
                                    <td class="fw-semibold"><?= (int)($u['id'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars($u['login'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="status-chip" style="background: #e7f3ff; color: #0056b3;">
                                            <?= htmlspecialchars($u['role_name'] ?? ($u['role_id'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/admin/users/<?= (int)($u['id'] ?? 0) ?>/edit" class="btn btn-sm btn-outline-primary mb-2">Редагувати</a>
                                        <form action="/admin/users/<?= (int)($u['id'] ?? 0) ?>/delete" method="POST" class="d-inline-block ms-1">
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

            <?php if ($pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <?php for ($i = 1; $i <= $pages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?><?= $search ? '&q=' . urlencode($search) : '' ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <div class="small text-muted">Сторінка <?= $page ?> з <?= $pages ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
