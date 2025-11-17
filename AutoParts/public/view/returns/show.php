<?php
/** @var \App\Models\User $user */
/** @var array $return */

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

if (!$return) {
    include __DIR__ . '/../../view/errors/404.php';
    include __DIR__ . '/../../includes/footer.php';
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

$reasonLabel = match ($return['reason'] ?? '') {
    'defect' => 'Товар має дефект',
    'not_matching' => 'Товар не відповідає опису',
    'damaged' => 'Товар пошкоджений при доставці',
    'not_needed' => 'Передумала, не потрібен',
    'exchange' => 'Бажаю обміняти',
    default => htmlspecialchars($return['reason'] ?? '', ENT_QUOTES, 'UTF-8'),
};

$returnMethodLabel = match ($return['return_method'] ?? 'courier') {
    'courier' => 'Кур\'єр (Nous Logistics)',
    'nova_poshta' => 'Нова Пошта',
    'pickup' => 'Самовивіз',
    default => htmlspecialchars($return['return_method'] ?? '', ENT_QUOTES, 'UTF-8'),
};
?>

<section class="return-detail-hero position-relative text-white py-5">
    <img src="https://images.pexels.com/photos/3807517/pexels-photo-3807517.jpeg"
         alt="Return Details"
         class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(0,0,0,.85), rgba(0,0,0,.6));"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <p class="text-uppercase text-white-50 mb-2">
                    <a href="/returns" class="text-white-50 text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>До списку
                    </a>
                </p>
                <h1 class="display-5 fw-bold mb-3">
                    Запит на повернення №<?= (int)$return['id'] ?>
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

<section class="return-detail-content py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- СТАТУС И ІНФОРМАЦІЯ -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-4 fw-semibold">Статус запиту</h2>

                        <div class="timeline">
                            <?php 
                            $states = [
                                'pending' => ['label' => 'На розгляді', 'icon' => 'clock-history', 'color' => 'info'],
                                'approved' => ['label' => 'Схвалено', 'icon' => 'check-circle', 'color' => 'success'],
                                'received' => ['label' => 'Отримано', 'icon' => 'package-check', 'color' => 'primary'],
                                'completed' => ['label' => 'Завершено', 'icon' => 'check2-all', 'color' => 'success'],
                                'rejected' => ['label' => 'Відхилено', 'icon' => 'x-circle', 'color' => 'danger'],
                            ];

                            $currentStatus = $return['status'] ?? 'pending';
                            $statusOrder = ['pending', 'approved', 'received', 'completed'];
                            $rejectedStatus = $currentStatus === 'rejected';

                            foreach ($statusOrder as $idx => $state):
                                $stateInfo = $states[$state];
                                $isActive = $state === $currentStatus;
                                $isPassed = array_search($state, $statusOrder) < array_search($currentStatus, $statusOrder);
                                $isCompleted = $currentStatus === 'completed';

                                $activeClass = $isActive ? 'text-' . $stateInfo['color'] : ($isPassed && $isCompleted ? 'text-success' : 'text-muted');
                            ?>
                                <div class="d-flex gap-3 mb-3">
                                    <div class="text-center" style="width: 50px;">
                                        <div class="badge bg-<?= $isActive || ($isPassed && $isCompleted) ? $stateInfo['color'] : 'light' ?> text-<?= $isActive || ($isPassed && $isCompleted) ? 'white' : 'muted' ?> rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.25rem;">
                                            <i class="bi bi-<?= $stateInfo['icon'] ?>"></i>
                                        </div>
                                        <?php if ($idx < count($statusOrder) - 1): ?>
                                            <div class="bg-<?= $isPassed && $isCompleted ? 'success' : 'light' ?>" style="width: 3px; height: 30px; margin: 5px auto;"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1 py-2">
                                        <strong class="<?= $activeClass ?>"><?= $stateInfo['label'] ?></strong>
                                        <div class="small text-muted">
                                            <?php if ($state === 'pending'): ?>
                                                Очікування розгляду вашого запиту
                                            <?php elseif ($state === 'approved'): ?>
                                                Ваш запит схвалений. Інструкції відправлені на пошту
                                            <?php elseif ($state === 'received'): ?>
                                                Ми отримали ваш посилок і перевіряємо товар
                                            <?php else: ?>
                                                Повернення завершено. Кошти повернені на вашу карту
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if ($rejectedStatus): ?>
                                <div class="alert alert-danger rounded-3 mt-3 mb-0">
                                    <strong>Запит відхилений</strong>
                                    <div class="small mt-2">
                                        <?php if (!empty($return['admin_comment'])): ?>
                                            Причина: <?= htmlspecialchars($return['admin_comment'], ENT_QUOTES, 'UTF-8') ?>
                                        <?php else: ?>
                                            Запит на повернення був відхилений. Зв'яжіться з нами для деталей.
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ДЕТАЛІ ЗАПИТУ -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-4 fw-semibold">Деталі запиту</h2>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div>
                                    <label class="small text-muted text-uppercase fw-semibold d-block mb-2">Номер запиту</label>
                                    <strong><?= (int)$return['id'] ?></strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label class="small text-muted text-uppercase fw-semibold d-block mb-2">Дата створення</label>
                                    <strong><?= htmlspecialchars($return['created_at'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label class="small text-muted text-uppercase fw-semibold d-block mb-2">Причина</label>
                                    <strong><?= $reasonLabel ?></strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label class="small text-muted text-uppercase fw-semibold d-block mb-2">Спосіб повернення</label>
                                    <strong><?= $returnMethodLabel ?></strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div>
                                    <label class="small text-muted text-uppercase fw-semibold d-block mb-2">Опис проблеми</label>
                                    <p class="mb-0">
                                        <?= !empty($return['description']) 
                                            ? nl2br(htmlspecialchars($return['description'], ENT_QUOTES, 'UTF-8')) 
                                            : '<em class="text-muted">Опис не надано</em>' ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ТОВАРИ -->
                <?php if (!empty($return['order_items'])): ?>
                    <div class="card rounded-4 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-4 fw-semibold">Товари в замовленні</h2>

                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Товар</th>
                                            <th class="text-end">Ціна</th>
                                            <th class="text-center">Кількість</th>
                                            <th class="text-end">Сума</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($return['order_items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($item['name_snapshot'] ?? 'Товар', ENT_QUOTES, 'UTF-8') ?></strong>
                                                </td>
                                                <td class="text-end"><?= (float)$item['price'] ?> грн</td>
                                                <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                                <td class="text-end fw-semibold">
                                                    <?= (float)$item['price'] * (int)$item['quantity'] ?> грн
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ПРАВА КОЛОНКА -->
            <div class="col-lg-4">
                <!-- ІНФОРМАЦІЯ ПРО ЗАМОВЛЕННЯ -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-3 text-uppercase text-muted fw-semibold">
                            <i class="bi bi-box-seam me-2"></i>Оригінальне замовлення
                        </h3>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2">
                                <strong>Номер:</strong>
                                <div class="text-muted">№<?= (int)$return['order_id'] ?></div>
                            </li>
                            <li class="mb-2">
                                <strong>Дата:</strong>
                                <div class="text-muted"><?= htmlspecialchars($return['order_date'], ENT_QUOTES, 'UTF-8') ?></div>
                            </li>
                            <li class="mb-2">
                                <strong>Сума:</strong>
                                <div class="text-muted"><?= (int)$return['total'] ?? 'N/A' ?> грн</div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- КОНТАКТИ -->
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-3 text-uppercase text-muted fw-semibold">
                            <i class="bi bi-person-check me-2"></i>Ваші дані
                        </h3>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2">
                                <strong>Ім'я:</strong>
                                <div class="text-muted">
                                    <?= htmlspecialchars($user['first_name'] ?? $user['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </li>
                            <li class="mb-2">
                                <strong>Email:</strong>
                                <div class="text-muted">
                                    <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </li>
                            <li>
                                <strong>Телефон:</strong>
                                <div class="text-muted">
                                    <?= htmlspecialchars($user['phone'] ?? 'Не зазначено', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ІНСТРУКЦІЇ -->
                <div class="card rounded-4 border-0 shadow-sm bg-info bg-opacity-10 border border-info border-opacity-50">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-2 text-uppercase text-muted fw-semibold">
                            <i class="bi bi-info-circle me-2"></i>Що далі?
                        </h3>
                        <p class="small mb-0">
                            Детальна інструкція з повернення товару була відправлена на вашу пошту. 
                            Якщо ви її не отримали, <a href="/support" class="link-info">напишіть нам</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- КНОПКА НАЗАД -->
        <div class="mt-4">
            <a href="/returns" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Повернутися до списку
            </a>
        </div>
    </div>
</section>

<style>
    .return-detail-hero {
        overflow: hidden;
    }

    .timeline {
        position: relative;
    }
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
