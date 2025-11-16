<?php
/** @var array $returns */
/** @var array $stats */
/** @var int $page */

include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$stats = $stats ?? [];
?>

<section class="admin-returns-hero position-relative text-white py-4">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(0,0,0,.7), rgba(0,0,0,.5));"></div>

    <div class="container position-relative" style="z-index: 2;">
        <h1 class="display-6 fw-bold mb-2">
            <i class="bi bi-arrow-return-left me-2"></i>Управління поверненнями
        </h1>
        <p class="text-white-50">Запити на повернення та обмін товару</p>
    </div>
</section>

<section class="admin-returns-content py-5 bg-light">
    <div class="container">
        <!-- СТАТИСТИКА -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase mb-1">Всього запитів</p>
                                <h3 class="h2 mb-0"><?= (int)($stats['total'] ?? 0) ?></h3>
                            </div>
                            <i class="bi bi-inbox text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase mb-1">На розгляді</p>
                                <h3 class="h2 mb-0 text-info"><?= (int)($stats['pending'] ?? 0) ?></h3>
                            </div>
                            <i class="bi bi-clock-history text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase mb-1">Схвалено</p>
                                <h3 class="h2 mb-0 text-success"><?= (int)($stats['approved'] ?? 0) ?></h3>
                            </div>
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase mb-1">Завершено</p>
                                <h3 class="h2 mb-0 text-primary"><?= (int)($stats['completed'] ?? 0) ?></h3>
                            </div>
                            <i class="bi bi-check2-all text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ТАБЛИЦЯ ЗАПИТІВ -->
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h4 mb-4">Запити на повернення</h2>

                <?php if (empty($returns)): ?>
                    <div class="alert alert-info rounded-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Запитів на повернення не знайдено.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>№ Запиту</th>
                                    <th>№ Замовлення</th>
                                    <th>Користувач</th>
                                    <th>Причина</th>
                                    <th>Спосіб</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                    <th class="text-end">Дії</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($returns as $return): 
                                    $statusBadgeClass = match ($return['status'] ?? 'pending') {
                                        'pending' => 'bg-info',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'received' => 'bg-primary',
                                        'completed' => 'bg-success',
                                        default => 'bg-secondary',
                                    };

                                    $statusLabel = match ($return['status'] ?? 'pending') {
                                        'pending' => 'На розгляді',
                                        'approved' => 'Схвалено',
                                        'rejected' => 'Відхилено',
                                        'received' => 'Отримано',
                                        'completed' => 'Завершено',
                                        default => 'Невідомо',
                                    };

                                    $reasonLabel = match ($return['reason'] ?? '') {
                                        'defect' => 'Дефект',
                                        'not_matching' => 'Не відповідає',
                                        'damaged' => 'Пошкоджено',
                                        'not_needed' => 'Не потрібен',
                                        'exchange' => 'Обмін',
                                        default => htmlspecialchars($return['reason'] ?? '', ENT_QUOTES, 'UTF-8'),
                                    };

                                    $methodLabel = match ($return['return_method'] ?? 'courier') {
                                        'courier' => 'Кур\'єр',
                                        'nova_poshta' => 'Нова Пошта',
                                        'pickup' => 'Самовивіз',
                                        default => htmlspecialchars($return['return_method'] ?? '', ENT_QUOTES, 'UTF-8'),
                                    };
                                ?>
                                    <tr>
                                        <td class="fw-semibold">
                                            <i class="bi bi-arrow-return-left text-warning me-2"></i>#<?= (int)$return['id'] ?>
                                        </td>
                                        <td>#<?= (int)$return['order_id'] ?></td>
                                        <td>
                                            <div class="small">
                                                <strong><?= htmlspecialchars($return['login'] ?? 'Гість', ENT_QUOTES, 'UTF-8') ?></strong>
                                                <div class="text-muted"><?= htmlspecialchars($return['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                            </div>
                                        </td>
                                        <td><?= $reasonLabel ?></td>
                                        <td><?= $methodLabel ?></td>
                                        <td>
                                            <span class="badge <?= $statusBadgeClass ?> rounded-pill fw-semibold">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            <?= htmlspecialchars($return['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                                           data-bs-target="#viewModal" 
                                                           onclick="viewReturnDetails(<?= (int)$return['id'] ?>, '<?= htmlspecialchars($return['login'] ?? 'Гість', ENT_QUOTES, 'UTF-8') ?>')">
                                                            <i class="bi bi-eye me-2"></i>Переглянути
                                                        </a>
                                                    </li>
                                                    <?php if ($return['status'] === 'pending' || $return['status'] === 'approved'): ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" 
                                                               data-bs-target="#updateStatusModal"
                                                               onclick="prepareStatusUpdate(<?= (int)$return['id'] ?>, 'approved')">
                                                                <i class="bi bi-check-circle me-2"></i>Схвалити
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" 
                                                               data-bs-target="#updateStatusModal"
                                                               onclick="prepareStatusUpdate(<?= (int)$return['id'] ?>, 'rejected')">
                                                                <i class="bi bi-x-circle me-2"></i>Відхилити
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($return['status'] === 'approved'): ?>
                                                        <li>
                                                            <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" 
                                                               data-bs-target="#updateStatusModal"
                                                               onclick="prepareStatusUpdate(<?= (int)$return['id'] ?>, 'received')">
                                                                <i class="bi bi-package-check me-2"></i>Отримано
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($return['status'] === 'received'): ?>
                                                        <li>
                                                            <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" 
                                                               data-bs-target="#updateStatusModal"
                                                               onclick="prepareStatusUpdate(<?= (int)$return['id'] ?>, 'completed')">
                                                                <i class="bi bi-check2-all me-2"></i>Завершити
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- MODAL: Перегляд деталей -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header border-bottom-light">
                <h5 class="modal-title fw-semibold">Деталі запиту</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBodyDetails">
                <p class="text-muted">Завантаження...</p>
            </div>
            <div class="modal-footer border-top-light">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Закрити</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Оновлення статусу -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header border-bottom-light">
                <h5 class="modal-title fw-semibold">Оновити статус</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" onsubmit="submitStatusUpdate(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Новий статус</label>
                        <div id="statusInfo" class="alert alert-info rounded-3 mb-3"></div>
                    </div>
                    <div class="mb-3">
                        <label for="adminComment" class="form-label">Коментар (необов'язково)</label>
                        <textarea id="adminComment" name="comment" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Оновити</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentReturnId = null;
    let currentStatus = null;

    function viewReturnDetails(returnId, userName) {
        // Mock: in real app, would fetch via AJAX
        document.getElementById('modalBodyDetails').innerHTML = `
            <p><strong>№ Запиту:</strong> ${returnId}</p>
            <p><strong>Користувач:</strong> ${userName}</p>
            <p class="text-muted small">Детальна інформація завантажується...</p>
        `;
    }

    function prepareStatusUpdate(returnId, newStatus) {
        currentReturnId = returnId;
        currentStatus = newStatus;

        const statusLabels = {
            'approved': 'Схвалити запит',
            'rejected': 'Відхилити запит',
            'received': 'Позначити як отримане',
            'completed': 'Завершити повернення'
        };

        document.getElementById('statusInfo').innerHTML = `
            <strong>${statusLabels[newStatus] || newStatus}</strong>
        `;
    }

    function submitStatusUpdate(event) {
        event.preventDefault();

        const comment = document.getElementById('adminComment').value || '';

        fetch('/admin/returns/' + currentReturnId + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                return_id: currentReturnId,
                status: currentStatus,
                comment: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Статус оновлено успішно');
                location.reload();
            } else {
                alert('Помилка: ' + (data.error || 'Невідома помилка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Помилка при оновленні статусу');
        });
    }
</script>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
