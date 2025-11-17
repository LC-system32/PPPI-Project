<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$reviewsPage = $reviewsPage ?? ['data' => [], 'total' => 0, 'page' => 1, 'perPage' => 20];
$message = $message ?? null;
$stats = $stats ?? ['pending' => 0, 'approved' => 0, 'rejected' => 0];
?>

<style>
    :root{ --sp-sm:1rem; --sp-md:1.5rem; --card-radius:14px; --muted:#6c757d }
    .admin-card{ background:#fff; border-radius:var(--card-radius); box-shadow:0 6px 18px rgba(21,28,64,0.06); border:1px solid rgba(16,24,40,0.03) }
    .p-4 { padding: var(--sp-md) !important; }
    .display-6{ font-weight:700 }
    .breadcrumb .breadcrumb-item{ color:var(--muted) }
    .filters-card .form-control, .filters-card .form-select{ height: calc(var(--sp-md)*2.2); border-radius:10px }
    .status-chip{ border-radius:999px; padding:.35rem .9rem; font-size:.85rem; }
    .table-admin thead th{ border-bottom:1px solid rgba(2,6,23,0.06); font-weight:600 }
    .table-admin tbody tr{ height:60px; transition:background .12s, transform .08s }
    .table-admin tbody tr:hover{ background:#fbfcfd; transform:translateY(-1px) }
    .user-name{ font-weight:700 } .user-email{ color:var(--muted); font-size:.85rem }
    .badge-status{ padding:.45rem .7rem; border-radius:999px; font-weight:600; color:#fff }
    .action-btn{ width:38px;height:38px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;border:1px solid rgba(2,6,23,0.06);background:#fff }
    .pagination .page-link{ border-radius:999px; min-width:40px; min-height:40px }
</style>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item active" aria-current="page">Відгуки</li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0">Модерація відгуків</h1>
                <p class="text-muted small mb-0">Перегляд та модерація відгуків користувачів</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/reviews" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-clockwise"></i> Оновити</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="admin-card filters-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Фільтри</h5>
                        <small class="text-muted">Швидка фільтрація</small>
                    </div>
                    <form id="reviewsToolbar" class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input id="reviewsSearch" name="q" class="form-control" type="search" placeholder="Пошук за товаром або автором" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="col-6 col-md-6 text-end">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">Очистити</button>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                <?php $currentStatus = $_GET['status'] ?? 'pending'; ?>
                                <button type="button" class="btn btn-outline-secondary status-chip <?= $currentStatus === 'all' ? 'active' : '' ?>" data-val="all">Усі</button>
                                <button type="button" class="btn btn-outline-warning status-chip <?= $currentStatus === 'pending' ? 'active' : '' ?>" data-val="pending">На модерації</button>
                                <button type="button" class="btn btn-outline-success status-chip <?= $currentStatus === 'approved' ? 'active' : '' ?>" data-val="approved">Схвалені</button>
                                <button type="button" class="btn btn-outline-danger status-chip <?= $currentStatus === 'rejected' ? 'active' : '' ?>" data-val="rejected">Відхилені</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-md-6">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="admin-card p-4">
                            <div class="small text-muted">На модерації</div>
                            <div class="h4 mt-2"><?= (int)($stats['pending'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Схвалені</div>
                            <div class="h4 mt-2"><?= (int)($stats['approved'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Відхилені</div>
                            <div class="h4 mt-2"><?= (int)($stats['rejected'] ?? 0) ?></div>
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
                <h5 class="mb-0">Список відгуків</h5>
                <div class="small text-muted">Показано <?= count($reviewsPage['data'] ?? []) ?> записів</div>
            </div>

            <?php if (empty($reviewsPage['data'])): ?>
                <div class="alert alert-info">Немає відгуків для модерації.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="reviewsTable" class="table table-admin align-middle mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th style="width:40%">Товар / Автор</th>
                                <th style="width:15%">Оцінка</th>
                                <th style="width:35%">Відгук</th>
                                <th style="width:120px">Дата</th>
                                <th style="width:140px" class="text-end">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviewsPage['data'] as $r): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($r['product_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($r['author'] ?? 'Користувач', ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td>
                                        <?php $rr = (int)($r['rating'] ?? 0); ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $rr): ?>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            <?php else: ?>
                                                <i class="bi bi-star text-muted"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </td>
                                    <td class="small text-truncate" style="max-width:500px;"><?= nl2br(htmlspecialchars(substr($r['text'] ?? '', 0, 600), ENT_QUOTES, 'UTF-8')) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end">
                                        <a href="/admin/reviews/<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary">Деталі</a>

                                        <form action="/admin/reviews/<?= (int)$r['id'] ?>/approve" method="post" class="d-inline-block ms-1">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <button class="btn btn-sm btn-success" type="submit">Схвалити</button>
                                        </form>

                                        <form action="/admin/reviews/<?= (int)$r['id'] ?>/reject" method="post" class="d-inline-block ms-1">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">Відхилити</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <?php if (!empty($reviewsPage['page'])): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <?php for ($i = 1; $i <= max(1, ceil(($reviewsPage['total'] ?? 0) / max(1,$reviewsPage['perPage'] ?? 20))); $i++): ?>
                                    <li class="page-item <?= $i === (int)($reviewsPage['page'] ?? 1) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
                <div class="small text-muted">Останнє оновлення: <?= date('Y-m-d H:i') ?></div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
<script>
// simple toolbar interactions
(function(){
    const chips = document.querySelectorAll('.status-chip');
    const search = document.getElementById('reviewsSearch');
    const clearBtn = document.getElementById('clearFilters');
    chips.forEach(b=> b.addEventListener('click', function(){ const val=this.dataset.val||'all'; const q=encodeURIComponent(search?.value||''); const params = new URLSearchParams(window.location.search); if(val==='all') params.set('status','all'); else params.set('status', val); if(q) params.set('q', q); else params.delete('q'); params.delete('page'); window.location.search = params.toString(); }));
    clearBtn?.addEventListener('click', ()=>{ const params=new URLSearchParams(window.location.search); params.delete('q'); params.delete('status'); params.delete('page'); window.location.search = params.toString(); });
    // submit on Enter in search
    search?.addEventListener('keydown', (e)=>{ if(e.key === 'Enter'){ e.preventDefault(); const params=new URLSearchParams(window.location.search); const q=search.value.trim(); if(q) params.set('q', q); else params.delete('q'); params.delete('page'); window.location.search = params.toString(); } });
})();
</script>
