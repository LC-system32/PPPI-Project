<?php
/** @var array $return */
/** @var array $orderItems */

include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

if (!$return) {
    include __DIR__ . '/../../errors/404.php';
    include __DIR__ . '/../../../includes/footer.php';
    return;
}

$statusLabel = match ($return['status'] ?? 'pending') {
    'pending' => 'На розгляді',
    'approved' => 'Схвалено',
    'rejected' => 'Відхилено',
    'received' => 'Отримано',
    'completed' => 'Завершено',
    default => 'Невідомий статус',
};

$statusBadgeClass = match ($return['status'] ?? 'pending') {
    'pending' => 'bg-info',
    'approved' => 'bg-success',
    'rejected' => 'bg-danger',
    'received' => 'bg-primary',
    'completed' => 'bg-success',
    default => 'bg-secondary',
};
$csrf = function_exists('csrf_token') ? csrf_token() : '';
?>

<section class="admin-return-hero position-relative text-white py-4">
    <img src="https://images.pexels.com/photos/3807517/pexels-photo-3807517.jpeg"
         alt="Return Details"
         class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(0,0,0,.85), rgba(0,0,0,.6));"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row">
            <div class="col-lg-8">
                <p class="text-uppercase text-white-50 mb-2">
                    <a href="/admin/returns" class="text-white-50 text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>До списку повернень
                    </a>
                </p>
                <h1 class="display-5 fw-bold mb-3">
                    <i class="bi bi-arrow-return-left me-2"></i>Повернення №<?= (int)$return['id'] ?>
                </h1>
                <div class="d-flex gap-3 flex-wrap">
                    <span class="badge <?= $statusBadgeClass ?> text-white rounded-pill fw-semibold px-3 py-2">
                        <?= $statusLabel ?>
                    </span>
                    <span class="text-white-50">
                        Замовлення №<?= (int)$return['order_id'] ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="admin-return-content py-5 bg-light">
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- ІНФОРМАЦІЯ ПРО ПОВЕРНЕННЯ -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Деталі запиту</h2>
                        <div class="row g-3 text-sm">
                            <div class="col-sm-6">
                                <strong class="text-uppercase text-muted small d-block mb-1">Номер повернення</strong>
                                <div><?= (int)$return['id'] ?></div>
                            </div>
                            <div class="col-sm-6">
                                <strong class="text-uppercase text-muted small d-block mb-1">Номер замовлення</strong>
                                <div>#<?= (int)$return['order_id'] ?></div>
                            </div>
                            <div class="col-sm-6">
                                <strong class="text-uppercase text-muted small d-block mb-1">Причина</strong>
                                <div><?= htmlspecialchars($return['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <div class="col-sm-6">
                                <strong class="text-uppercase text-muted small d-block mb-1">Спосіб повернення</strong>
                                <div><?= htmlspecialchars($return['return_method'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <div class="col-12">
                                <strong class="text-uppercase text-muted small d-block mb-1">Опис</strong>
                                <div class="text-muted small">
                                    <?= !empty($return['description']) 
                                        ? nl2br(htmlspecialchars($return['description'], ENT_QUOTES, 'UTF-8')) 
                                        : '<em>Опис не надано</em>' ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <strong class="text-uppercase text-muted small d-block mb-1">Дата створення</strong>
                                <div><?= htmlspecialchars($return['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- УПРАВЛІННЯ СТАТУСОМ -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Управління статусом</h2>
                        <form id="statusForm" method="POST" action="/admin/returns/<?= (int)$return['id'] ?>/status" class="row g-3" onsubmit="submitStatusForm(event)">
                            <input type="hidden" name="return_id" id="return_id" value="<?= (int)$return['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Новий статус</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach (['pending' => 'На розгляді', 'approved' => 'Схвалено', 'received' => 'Отримано', 'completed' => 'Завершено', 'rejected' => 'Відхилено'] as $status => $label): ?>
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill status-btn" data-status="<?= htmlspecialchars($status) ?>" <?= ($return['status'] === $status) ? 'disabled' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="comment" class="form-label fw-semibold">Коментар адміністратора</label>
                                <textarea id="comment" name="comment" class="form-control rounded-3" rows="3" placeholder="Додайте коментар для клієнта..."></textarea>
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" id="submitBtn" class="btn btn-warning text-dark fw-semibold rounded-pill" style="display: none;">
                                    <i class="bi bi-check-circle me-2"></i>Зберегти
                                </button>
                                <a href="/admin/returns" class="btn btn-outline-secondary rounded-pill">Скасувати</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ПРАВА КОЛОНКА -->
            <div class="col-lg-4">
                <!-- ДАНІ КОРИСТУВАЧА -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-3 text-uppercase text-muted fw-semibold">
                            <i class="bi bi-person me-2"></i>Користувач
                        </h3>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2">
                                <strong>Логін:</strong>
                                <div class="text-muted"><?= htmlspecialchars($return['login'] ?? 'Гість', ENT_QUOTES, 'UTF-8') ?></div>
                            </li>
                            <li class="mb-2">
                                <strong>Email:</strong>
                                <div class="text-muted"><?= htmlspecialchars($return['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </li>
                            <li>
                                <strong>Телефон:</strong>
                                <div class="text-muted"><?= htmlspecialchars($return['phone'] ?? 'Не зазначено', ENT_QUOTES, 'UTF-8') ?></div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ІНФОРМАЦІЯ ПРО ЗАМОВЛЕННЯ -->
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-3 text-uppercase text-muted fw-semibold">
                            <i class="bi bi-box-seam me-2"></i>Замовлення
                        </h3>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2">
                                <strong>Номер:</strong>
                                <div class="text-muted">#<?= (int)$return['order_id'] ?></div>
                            </li>
                            <li class="mb-2">
                                <strong>Сума:</strong>
                                <div class="text-muted"><?= (int)($return['total'] ?? 0) ?> грн</div>
                            </li>
                            <li>
                                <strong>Дата:</strong>
                                <div class="text-muted"><?= htmlspecialchars($return['order_created'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const status = this.dataset.status;
            const form = document.getElementById('statusForm');
            
            // Remove previous hidden input if exists
            const oldInput = form.querySelector('input[name="status"]');
            if (oldInput) oldInput.remove();
            
            // Add hidden status input
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'status';
            input.value = status;
            form.appendChild(input);
            
            // Show submit button
            document.getElementById('submitBtn').style.display = 'inline-block';
            
            // Highlight selected button
            document.querySelectorAll('.status-btn').forEach(b => {
                b.classList.remove('active');
                b.classList.remove('btn-warning');
                b.classList.add('btn-outline-primary');
            });
            this.classList.add('btn-warning');
            this.classList.add('text-dark');
            this.classList.remove('btn-outline-primary');
        });
    });

    function submitStatusForm(event) {
        event.preventDefault();

        const form = document.getElementById('statusForm');
        const formData = new URLSearchParams(new FormData(form));

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>'
            },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Статус оновлено', 'Успіх', 'bg-success text-white');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Помилка: ' + (data.error || 'Невідома помилка'), 'Помилка', 'bg-danger text-white');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Помилка при оновленні статусу', 'Помилка', 'bg-danger text-white');
        });
    }
</script>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
