<?php
/** @var array $returns */
/** @var array $stats */
/** @var int $page */

include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$stats = $stats ?? [];
$csrf = function_exists('csrf_token') ? csrf_token() : '';
?>

<style>
    :root{
        --sp-sm: 1rem; /*16*/
        --sp-md: 1.5rem; /*24*/
        --sp-lg: 2rem; /*32*/
        --card-radius: 14px;
        --muted: #6c757d;
    }

    body { background-color: #f6f7f9; }

    .admin-card { background:#fff; border-radius:var(--card-radius); box-shadow:0 6px 18px rgba(21,28,64,0.06); border:1px solid rgba(16,24,40,0.03); }
    .admin-card .card-body, .p-4 { padding: var(--sp-md) !important; }

    .page-hero .display-6 { font-weight:700; }
    .breadcrumb .breadcrumb-item { font-size:.9rem; color:var(--muted); }

    .filters-card .form-control, .filters-card .form-select { height: calc(var(--sp-md) * 2.2); border-radius:10px; }
    .status-chip { border-radius:999px; padding:.35rem .9rem; font-size:.85rem; transition:all .12s ease }
    .status-chip:hover { transform:translateY(-2px); }
    .status-chip.active { box-shadow:0 4px 10px rgba(2,6,23,.06); }

    .stats-value { font-size:1.6rem; font-weight:700 }
    .stats-label { font-size:.85rem; color:var(--muted); text-transform:uppercase }
    .stats-icon { font-size:1.7rem; color:rgba(2,6,23,.08); position:absolute; right:18px; top:16px }

    .table-admin thead th { border-bottom:1px solid rgba(2,6,23,0.06); font-weight:600; font-size:.9rem; color:#374151; padding-top:var(--sp-sm); padding-bottom:var(--sp-sm); }
    .table-admin tbody tr { transition:background-color .12s ease, transform .08s ease; height:60px; }
    .table-admin tbody tr:hover { background:#fbfcfd; transform:translateY(-1px); }
    .table-admin td { vertical-align: middle; border-bottom:1px solid rgba(2,6,23,0.03); }
    .user-name { font-weight:700 }
    .user-email { color:var(--muted); font-size:.85rem }

    .badge-status { padding:.45rem .7rem; border-radius:999px; font-weight:600; font-size:.82rem; color:#fff }
    .badge-status.info { background:#0dcaf0; color:#073642 }
    .badge-status.success { background:#28a745 }
    .badge-status.danger { background:#dc3545 }
    .badge-status.primary { background:#0d6efd }

    .action-btn { width:38px; height:38px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; box-shadow:none; border:1px solid rgba(2,6,23,0.06); background:#fff; transition:transform .08s ease, box-shadow .12s ease }
    .action-btn:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(2,6,23,0.06) }

    .pagination .page-link { border-radius:999px; min-width:40px; min-height:40px; display:flex; align-items:center; justify-content:center; border:1px solid rgba(2,6,23,0.06); }
    .pagination .page-item.active .page-link { background:#111827; color:#fff; border-color:#111827 }

    @media (max-width:768px){ .stats-icon{display:none} }
</style>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item active" aria-current="page">Повернення</li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4 page-hero">
            <div>
                <h1 class="display-6 mb-1">Управління поверненнями</h1>
                <p class="text-muted small mb-0">Перегляд та обробка запитів на повернення і обмін</p>
            </div>

            <div class="d-flex gap-2">
                <a href="/admin/returns" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 btn-sm"><i class="bi bi-arrow-clockwise"></i> Оновити</a>
                <a href="/admin/returns/export" class="btn btn-primary d-inline-flex align-items-center gap-2 btn-sm"><i class="bi bi-file-earmark-arrow-down"></i> Експорт</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="admin-card filters-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Фільтри</h5>
                        <small class="text-muted">Швидка фільтрація</small>
                    </div>

                    <form class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input class="form-control" id="tableSearch" type="search" placeholder="Пошук по користувачу, замовленню, email...">
                            </div>
                        </div>

                        <div class="col-6 col-md-6">
                            <select id="filterStatus" class="form-select">
                                <option value="">Усі статуси</option>
                                <option value="pending">На розгляді</option>
                                <option value="approved">Схвалено</option>
                                <option value="received">Отримано</option>
                                <option value="completed">Завершено</option>
                                <option value="rejected">Відхилено</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-6">
                            <select class="form-select">
                                <option value="">Всі причини</option>
                                <option value="defect">Дефект</option>
                                <option value="not_matching">Не відповідає</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-6">
                            <select class="form-select">
                                <option value="">Всі способи</option>
                                <option value="courier">Кур'єр</option>
                                <option value="nova_poshta">Нова Пошта</option>
                                <option value="pickup">Самовивіз</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-6 text-end">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">Очистити</button>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary status-chip" data-val="">Всі</button>
                                <button type="button" class="btn btn-outline-info status-chip" data-val="pending">На розгляді</button>
                                <button type="button" class="btn btn-outline-success status-chip" data-val="approved">Схвалено</button>
                                <button type="button" class="btn btn-outline-primary status-chip" data-val="received">Отримано</button>
                                <button type="button" class="btn btn-outline-dark status-chip" data-val="completed">Завершено</button>
                                <button type="button" class="btn btn-outline-danger status-chip" data-val="rejected">Відхилено</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-md-6">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="admin-card position-relative p-4">
                            <div class="stats-label">Усього запитів</div>
                            <div class="stats-value mt-2"><?= (int)($stats['total'] ?? 0) ?></div>
                            <i class="bi bi-inbox-fill stats-icon"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="admin-card position-relative p-4">
                            <div class="stats-label">На розгляді</div>
                            <div class="stats-value mt-2 text-info"><?= (int)($stats['pending'] ?? 0) ?></div>
                            <i class="bi bi-clock-history stats-icon"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="admin-card position-relative p-4">
                            <div class="stats-label">Схвалено</div>
                            <div class="stats-value mt-2 text-success"><?= (int)($stats['approved'] ?? 0) ?></div>
                            <i class="bi bi-check-circle stats-icon"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="admin-card position-relative p-4">
                            <div class="stats-label">Завершено</div>
                            <div class="stats-value mt-2 text-primary"><?= (int)($stats['completed'] ?? 0) ?></div>
                            <i class="bi bi-check2-all stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- table -->
        <div class="admin-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Запити на повернення</h2>
                <div class="small text-muted">Показано <?= count($returns) ?> записів</div>
            </div>

            <?php if (empty($returns)): ?>
                <div class="alert alert-info rounded-3 mb-0"><i class="bi bi-info-circle me-2"></i>Запитів на повернення не знайдено.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="returnsTable" class="table table-admin align-middle mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th style="width:120px">№ Запиту</th>
                                <th style="width:110px">№ Замовлення</th>
                                <th>Користувач</th>
                                <th>Причина</th>
                                <th>Спосіб</th>
                                <th style="width:130px">Статус</th>
                                <th style="width:170px">Дата</th>
                                <th class="text-end" style="width:64px">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($returns as $return):
                                $status = $return['status'] ?? 'pending';
                                $statusLabel = match ($status) {
                                    'pending' => 'На розгляді',
                                    'approved' => 'Схвалено',
                                    'rejected' => 'Відхилено',
                                    'received' => 'Отримано',
                                    'completed' => 'Завершено',
                                    default => 'Невідомо'
                                };

                                $badgeClass = match ($status) {
                                    'pending' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'received' => 'primary',
                                    'completed' => 'primary',
                                    default => 'secondary'
                                };

                                $reasonLabel = match ($return['reason'] ?? '') {
                                    'defect' => 'Дефект',
                                    'not_matching' => 'Не відповідає',
                                    'damaged' => 'Пошкоджено',
                                    'not_needed' => 'Не потрібен',
                                    'exchange' => 'Обмін',
                                    default => htmlspecialchars($return['reason'] ?? '', ENT_QUOTES, 'UTF-8')
                                };

                                $methodLabel = match ($return['return_method'] ?? 'courier') {
                                    'courier' => 'Кур\'єр',
                                    'nova_poshta' => 'Нова Пошта',
                                    'pickup' => 'Самовивіз',
                                    default => htmlspecialchars($return['return_method'] ?? '', ENT_QUOTES, 'UTF-8')
                                };
                            ?>
                                <tr class="clickable-row" role="button" onclick="window.location.href='/admin/returns/<?= (int)$return['id'] ?>';">
                                    <td><strong>#<?= (int)$return['id'] ?></strong></td>
                                    <td>#<?= (int)$return['order_id'] ?></td>
                                    <td>
                                        <div>
                                            <div class="user-name"><?= htmlspecialchars($return['login'] ?? 'Гість', ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="user-email"><?= htmlspecialchars($return['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    </td>
                                    <td title="<?= htmlspecialchars($return['description'] ?? $return['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= $reasonLabel ?></td>
                                    <td><?= $methodLabel ?></td>
                                    <td>
                                        <span class="badge-status <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                    </td>
                                    <td class="text-muted small"><?= htmlspecialchars($return['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end">
                                        <div class="dropdown d-inline-block">
                                            <button onclick="event.stopPropagation();" class="action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Дії">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="/admin/returns/<?= (int)$return['id'] ?>" onclick="event.stopPropagation();">Переглянути</a></li>
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal" onclick="event.stopPropagation(); prepareStatusUpdate(<?= (int)$return['id'] ?>, 'approved');">Схвалити</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <?php if (!empty($page)): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <?php for ($i=1;$i<=5;$i++): ?>
                                    <li class="page-item <?= $i === (int)$page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
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

<!-- update status modal (kept minimal) -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">Оновити статус</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" onsubmit="submitStatusUpdate(event)">
                <div class="modal-body">
                    <div id="statusInfo" class="alert alert-info mb-3"></div>
                    <div class="mb-3">
                        <label for="adminComment" class="form-label">Коментар (необов'язково)</label>
                        <textarea id="adminComment" name="comment" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-primary">Оновити</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentReturnId = null;
    let currentStatus = null;

    function prepareStatusUpdate(returnId, newStatus){
        currentReturnId = returnId;
        currentStatus = newStatus;
        const labels = { 'approved':'Схвалити','rejected':'Відхилити','received':'Отримано','completed':'Завершено' };
        document.getElementById('statusInfo').innerText = labels[newStatus] || newStatus;
    }

    function submitStatusUpdate(e){
        e.preventDefault();
        const comment = document.getElementById('adminComment').value || '';
        fetch('/admin/returns/'+currentReturnId+'/status',{ method:'POST', headers:{ 'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':'<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>' }, body: new URLSearchParams({ return_id: currentReturnId, status: currentStatus, comment: comment, csrf_token: '<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>' }) })
        .then(res=>res.json()).then(data=>{ if(data.success){ showToast?.('Статус оновлено','Успіх','bg-success text-white'); setTimeout(()=>location.reload(),700); } else { showToast?.('Помилка','Помилка','bg-danger text-white'); } }).catch(()=> showToast?.('Помилка при оновленні','Помилка','bg-danger text-white'));
    }

    document.getElementById('tableSearch')?.addEventListener('input', function(){ const q = this.value.trim().toLowerCase(); document.querySelectorAll('#returnsTable tbody tr').forEach(r=>{ const text = r.innerText.toLowerCase(); r.style.display = q === '' || text.indexOf(q) !== -1 ? '' : 'none'; }); });

    document.getElementById('filterStatus')?.addEventListener('change', function(){ const val = this.value; document.querySelectorAll('#returnsTable tbody tr').forEach(r=>{ if(!val){ r.style.display=''; return; } const status = r.querySelector('.badge-status')?.innerText?.toLowerCase() ?? ''; r.style.display = status.indexOf(val) !== -1 ? '' : 'none'; }); });

    document.getElementById('clearFilters')?.addEventListener('click', function(){ document.getElementById('tableSearch').value=''; document.getElementById('filterStatus').value=''; document.getElementById('tableSearch').dispatchEvent(new Event('input')); document.getElementById('filterStatus').dispatchEvent(new Event('change')); });

    document.querySelectorAll('.status-chip').forEach(btn=> btn.addEventListener('click', function(){ const v = this.dataset.val ?? ''; const sel = document.getElementById('filterStatus'); if(!sel) return; sel.value = v; sel.dispatchEvent(new Event('change')); document.querySelectorAll('.status-chip').forEach(b=>b.classList.remove('active')); this.classList.add('active'); }));

</script>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
