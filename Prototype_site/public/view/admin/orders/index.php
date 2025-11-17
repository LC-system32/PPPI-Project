<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$csrf = csrf_token();
$statuses = ['new' => 'Нові', 'processing' => 'В обробці', 'shipped' => 'Відправлені', 'completed' => 'Виконані', 'cancelled' => 'Скасовані'];

// lightweight style to match admin design used on returns
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
                <li class="breadcrumb-item active" aria-current="page">Замовлення</li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0">Замовлення</h1>
                <p class="text-muted small mb-0">Перегляд та обробка замовлень магазину</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/orders" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-clockwise"></i> Оновити</a>
                <a href="/admin/orders/export" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-file-earmark-arrow-down"></i> Експорт</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="admin-card filters-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Фільтри</h5>
                        <small class="text-muted">Швидка фільтрація</small>
                    </div>
                    <form id="ordersToolbar" class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input id="ordersSearch" name="q" class="form-control" type="search" placeholder="Пошук за № замовлення" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="col-6 col-md-6">
                            <select id="ordersStatusFilter" name="status" class="form-select">
                                <option value="">Усі статуси</option>
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= (($status ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-6 col-md-6 text-end">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">Очистити</button>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary status-chip" data-val="">Всі</button>
                                <button type="button" class="btn btn-outline-warning status-chip" data-val="new">Нові</button>
                                <button type="button" class="btn btn-outline-info status-chip" data-val="processing">В обробці</button>
                                <button type="button" class="btn btn-outline-primary status-chip" data-val="shipped">Відправлені</button>
                                <button type="button" class="btn btn-outline-success status-chip" data-val="completed">Виконані</button>
                                <button type="button" class="btn btn-outline-danger status-chip" data-val="cancelled">Скасовані</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-md-6">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Усього замовлень</div>
                            <div class="h4 mt-2"><?= (int)($stats['orders'] ?? count($orders ?? [])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Нові</div>
                            <div class="h4 mt-2"><?= (int)($stats['new'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card p-4">
                            <div class="small text-muted">В обробці</div>
                            <div class="h4 mt-2"><?= (int)($stats['processing'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card p-4">
                            <div class="small text-muted">Відправлені</div>
                            <div class="h4 mt-2"><?= (int)($stats['shipped'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php
        // filter out return-related orders from this list — returns are handled in /admin/returns
        $visibleOrders = [];
        if (!empty($orders) && is_array($orders)) {
            foreach ($orders as $o) {
                $st = strtolower((string) ($o['status'] ?? ''));
                if (strpos($st, 'return') !== false) continue;
                $visibleOrders[] = $o;
            }
        }
        ?>

        <div class="admin-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Список замовлень</h5>
                <div class="small text-muted">Показано <?= count($visibleOrders) ?> записів</div>
            </div>

            <?php if (empty($visibleOrders)): ?>
                <div class="alert alert-info">Замовлень поки немає.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="ordersTable" class="table table-admin align-middle mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th class="sortable" data-key="id" data-type="number" style="width:100px">№ <i class="bi bi-chevron-expand ms-1"></i></th>
                                <th class="sortable" data-key="user" data-type="string">Користувач <i class="bi bi-chevron-expand ms-1"></i></th>
                                <th class="sortable text-end" data-key="total" data-type="number" style="width:140px">Сума <i class="bi bi-chevron-expand ms-1"></i></th>
                                <th class="sortable" data-key="status" data-type="string" style="width:160px">Статус <i class="bi bi-chevron-expand ms-1"></i></th>
                                <th class="sortable" data-key="date" data-type="date" style="width:170px">Дата <i class="bi bi-chevron-expand ms-1"></i></th>
                                <th style="width:64px" class="text-end">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visibleOrders as $order):
                                $statusKey = $order['status'] ?? 'new';
                                $statusLabel = $statuses[$statusKey] ?? ucfirst($statusKey);
                                $badgeClass = match($statusKey) { 'new'=>'badge-status info','processing'=>'badge-status info','shipped'=>'badge-status primary','completed'=>'badge-status success','cancelled'=>'badge-status danger', default=>'badge-status secondary' };

                                $rowId = (int) ($order['id'] ?? 0);
                                $rowUser = '';
                                if (!empty($order['login'])) $rowUser = $order['login'];
                                elseif (!empty($order['user_login'])) $rowUser = $order['user_login'];
                                elseif (!empty($order['user']) && is_array($order['user']) && !empty($order['user']['login'])) $rowUser = $order['user']['login'];
                                elseif (!empty($order['email'])) $rowUser = $order['email'];
                                elseif (!empty($order['user_id'])) $rowUser = (int)$order['user_id'];
                                $rowTotal = number_format($order['total'] ?? 0, 2, '.', ' ');
                                $rowDateIso = !empty($order['created_at']) ? date('c', strtotime($order['created_at'])) : '';
                            ?>
                                <tr data-id="<?= $rowId ?>" data-user="<?= htmlspecialchars($rowUser, ENT_QUOTES, 'UTF-8') ?>" data-total="<?= htmlspecialchars($rowTotal, ENT_QUOTES, 'UTF-8') ?>" data-status="<?= htmlspecialchars(strtolower($statusKey), ENT_QUOTES, 'UTF-8') ?>" data-date="<?= $rowDateIso ?>">
                                    <td class="fw-semibold">#<?= $rowId ?></td>
                                    <td>
                                        <div class="user-name"><?= htmlspecialchars($rowUser, ENT_QUOTES, 'UTF-8') ?></div>
                                        <?php
                                            $userEmail = '';$userPhone='';
                                            if (!empty($order['user']) && is_array($order['user'])) { $userEmail = $order['user']['email'] ?? ''; $userPhone = $order['user']['phone'] ?? ''; }
                                            else { $userEmail = $order['guest_email'] ?? ($order['email'] ?? ''); $userPhone = $order['guest_phone'] ?? ($order['phone'] ?? ''); }
                                        ?>
                                        <?php if ($userEmail): ?><div class="user-email"><?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                    </td>
                                    <td class="text-end"><?= $rowTotal ?> ₴</td>
                                    <td><span class="<?= $badgeClass ?>"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td class="text-muted small"><?= !empty($rowDateIso) ? htmlspecialchars(date('d.m.Y H:i', strtotime($order['created_at'])), ENT_QUOTES, 'UTF-8') : '' ?></td>
                                    <td class="text-end">
                                        <div class="dropdown d-inline-block">
                                            <button onclick="event.stopPropagation();" class="action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Дії"><i class="bi bi-three-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="/admin/orders/<?= $rowId ?>" onclick="event.stopPropagation();">Переглянути</a></li>
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal" onclick="event.stopPropagation(); prepareStatusUpdate(<?= $rowId ?>,'processing')">Оновити статус</a></li>
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
                                <?php for ($i = 1; $i <= 5; $i++): ?>
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

<!-- minimal modal for status updates reused from returns page -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">Оновити статус замовлення</h5>
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
// reuse and adapt client-side filtering/sorting
(function(){
    const table = document.getElementById('ordersTable'); if(!table) return;
    const tbody = table.querySelector('tbody');
    const searchInput = document.getElementById('ordersSearch');
    const statusSelect = document.getElementById('ordersStatusFilter');
    const toolbarForm = document.getElementById('ordersToolbar');

    function getRowValue(row,key){ return row?.dataset?.[key] ?? ''; }

    function filterRows(){ const qRaw=(searchInput?.value||'').trim(); const q=qRaw.replace(/\D+/g,''); const status=(statusSelect?.value||''); Array.from(tbody.rows).forEach(row=>{ if(!row.dataset) return; const id=String(row.dataset.id||''); const st=String(row.dataset.status||'').toLowerCase(); let visible=true; if(status && st!==status.toLowerCase()) visible=false; if(q && !id.includes(q)) visible=false; row.style.display = visible ? '' : 'none'; }); }

    let currentSort={key:null,dir:1};
    function sortRows(key,type){ if(!key) return; if(currentSort.key===key) currentSort.dir=-currentSort.dir; else currentSort={key,dir:1}; const rows=Array.from(tbody.querySelectorAll('tr')).filter(r=>r.style.display!=='none'); const collator=new Intl.Collator(undefined,{numeric:true,sensitivity:'base'}); rows.sort((a,b)=>{ let va=getRowValue(a,key), vb=getRowValue(b,key); if(type==='number'){ va=parseFloat(va)||0; vb=parseFloat(vb)||0; return (va-vb)*currentSort.dir; } if(type==='date'){ va=Date.parse(va)||0; vb=Date.parse(vb)||0; return (va-vb)*currentSort.dir; } return collator.compare(va,vb)*currentSort.dir; }); rows.forEach(r=>tbody.appendChild(r)); updateSortIcons(); }

    function updateSortIcons(){ table.querySelectorAll('th.sortable').forEach(th=>{ const icon=th.querySelector('i.bi'); const key=th.dataset.key; if(!icon) return; icon.classList.remove('bi-chevron-up','bi-chevron-down'); if(currentSort.key===key){ icon.classList.add(currentSort.dir===1?'bi-chevron-down':'bi-chevron-up'); } else icon.classList.add('bi-chevron-expand'); }); }

    table.querySelectorAll('th.sortable').forEach(th=>{ th.style.cursor='pointer'; th.addEventListener('click',()=>{ sortRows(th.dataset.key, th.dataset.type||'string'); }); });
    searchInput?.addEventListener('input',()=>filterRows()); statusSelect?.addEventListener('change',()=>filterRows());
    toolbarForm?.addEventListener('submit', e=>{ e.preventDefault(); filterRows(); table.scrollIntoView({behavior:'smooth',block:'start'}); });
    updateSortIcons();

    // chips
    document.querySelectorAll('.status-chip').forEach(btn=> btn.addEventListener('click', function(){ const v=this.dataset.val||''; const sel=document.getElementById('ordersStatusFilter'); if(!sel) return; sel.value=v; sel.dispatchEvent(new Event('change')); document.querySelectorAll('.status-chip').forEach(b=>b.classList.remove('active')); this.classList.add('active'); }));

    document.getElementById('clearFilters')?.addEventListener('click', function(){ document.getElementById('ordersSearch').value=''; document.getElementById('ordersStatusFilter').value=''; document.getElementById('ordersSearch').dispatchEvent(new Event('input')); document.getElementById('ordersStatusFilter').dispatchEvent(new Event('change')); });

    // status modal handling
    let currentOrderId=null, currentStatus=null;
    window.prepareStatusUpdate = function(orderId, newStatus){ currentOrderId=orderId; currentStatus=newStatus; document.getElementById('statusInfo').innerText = (newStatus||'Оновити статус'); };
    window.submitStatusUpdate = function(e){ e.preventDefault(); const comment=document.getElementById('adminComment').value||''; if(!currentOrderId) return; fetch('/admin/orders/'+currentOrderId+'/status',{ method:'POST', headers:{ 'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':'<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>' }, body: new URLSearchParams({ order_id: currentOrderId, status: currentStatus, comment: comment, csrf_token: '<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>' }) }).then(r=>r.json()).then(data=>{ if(data.success){ showToast?.('Статус оновлено','Успіх','bg-success text-white'); setTimeout(()=>location.reload(),700); } else { showToast?.('Помилка','Помилка','bg-danger text-white'); } }).catch(()=> showToast?.('Помилка при оновленні','Помилка','bg-danger text-white')); };

})();
</script>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
