<?php
/** @var \App\Models\User $user */
/** @var array $returns */

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrfToken = csrf_token();
?>

<section class="returns-list-hero position-relative text-white py-5">
    <img src="https://images.pexels.com/photos/3807517/pexels-photo-3807517.jpeg"
         alt="Returns"
         class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(0,0,0,.85), rgba(0,0,0,.6));"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <p class="text-uppercase text-white-50 mb-2">
                    <i class="bi bi-arrow-left-circle me-2"></i>Мої запити
                </p>
                <h1 class="display-5 fw-bold mb-3">Повернення та обміни</h1>
                <p class="fs-5 text-white-50">
                    Управління вашими запитами на повернення і обмін товарів
                </p>
            </div>
        </div>
    </div>
</section>

<section class="returns-content py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h2 class="h3 mb-1">Мої запити на повернення</h2>
                        <p class="text-muted mb-0">
                            Всього запитів: <strong><?= count($returns) ?></strong>
                        </p>
                    </div>
                    <a href="/returns/create" class="btn btn-warning text-dark fw-semibold rounded-pill px-4">
                        <i class="bi bi-plus-circle me-2"></i>Новий запит
                    </a>
                </div>

                <?php if (empty($returns)): ?>
                    <div class="card rounded-4 border-0 shadow-sm">
                        <div class="card-body p-5 text-center">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h3 class="h5 mb-2">Запитів на повернення немає</h3>
                            <p class="text-muted mb-3">
                                У вас поки що немає активних запитів на повернення або обмін товару.
                            </p>
                            <a href="/returns/create" class="btn btn-warning text-dark fw-semibold rounded-pill">
                                Оформити повернення
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($returns as $return): 
                            $statusClass = match ($return['status'] ?? 'pending') {
                                'pending' => 'bg-info text-white',
                                'approved' => 'bg-success text-white',
                                'rejected' => 'bg-danger text-white',
                                'received' => 'bg-primary text-white',
                                'completed' => 'bg-success text-white',
                                default => 'bg-secondary text-white',
                            };
                            
                            $statusLabel = match ($return['status'] ?? 'pending') {
                                'pending' => 'На розгляді',
                                'approved' => 'Схвалено',
                                'rejected' => 'Відхилено',
                                'received' => 'Отримано',
                                'completed' => 'Завершено',
                                default => 'Невідомий статус',
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
                                'courier' => 'Кур\'єр',
                                'nova_poshta' => 'Нова Пошта',
                                'pickup' => 'Самовивіз',
                                default => htmlspecialchars($return['return_method'] ?? '', ENT_QUOTES, 'UTF-8'),
                            };

                            $daysRemaining = (int)($return['days_remaining'] ?? 0);
                            $daysSincePurchase = (int)($return['days_since_purchase'] ?? 0);
                        ?>
                            <div class="col-12">
                                <div class="card rounded-4 border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="row g-3 align-items-start">
                                            <div class="col-md-8">
                                                <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                                    <div>
                                                        <h4 class="h5 mb-1">
                                                            <i class="bi bi-arrow-return-left me-2 text-warning"></i>
                                                            Запит №<?= (int)$return['id'] ?> 
                                                            (Замовлення №<?= (int)$return['order_id'] ?>)
                                                        </h4>
                                                        <small class="text-muted">
                                                            Створено: <?= htmlspecialchars($return['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge <?= $statusClass ?> rounded-pill fw-semibold">
                                                        <?= $statusLabel ?>
                                                    </span>
                                                </div>

                                                <div class="row g-2 mt-2 small">
                                                    <div class="col-sm-6">
                                                        <strong>Причина:</strong>
                                                        <div class="text-muted"><?= $reasonLabel ?></div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <strong>Спосіб:</strong>
                                                        <div class="text-muted"><?= $returnMethodLabel ?></div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <strong>Сума замовлення:</strong>
                                                        <div class="text-muted"><?= (int)$return['total'] ?? 'N/A' ?> грн</div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <strong>Дата замовлення:</strong>
                                                        <div class="text-muted"><?= htmlspecialchars($return['order_created'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    </div>
                                                </div>

                                                <?php if ($daysRemaining > 0 && $return['status'] === 'pending'): ?>
                                                    <div class="alert alert-warning bg-warning bg-opacity-10 border-warning border-opacity-50 rounded-3 mt-3 mb-0 small">
                                                        <i class="bi bi-clock-history me-2"></i>
                                                        Залишилось <strong><?= $daysRemaining ?> днів</strong> на спеціальне повернення в рамках 14-денного періоду
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="col-md-4 text-md-end">
                                                <div class="d-flex flex-column gap-2">
                                                    <a href="/returns/<?= (int)$return['id'] ?>" 
                                                       class="btn btn-outline-primary btn-sm rounded-pill">
                                                        <i class="bi bi-eye me-1"></i>Переглянути
                                                    </a>
                                                    
                                                    <?php if ($return['status'] === 'pending'): ?>
                                                        <a href="/returns/<?= (int)$return['id'] ?>/edit" 
                                                           class="btn btn-outline-secondary btn-sm rounded-pill">
                                                            <i class="bi bi-pencil me-1"></i>Редагувати
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ІНФОРМАЦІЙНА КОЛОНКА -->
            <div class="col-lg-12 mt-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card rounded-4 border-0 shadow-sm bg-light">
                            <div class="card-body p-4">
                                <h3 class="h6 mb-2 text-uppercase text-muted">
                                    <i class="bi bi-question-circle text-warning me-2"></i>Питання?
                                </h3>
                                <p class="small mb-3">
                                    Якщо у вас виникли проблеми з оформленням повернення, ми допоможемо.
                                </p>
                                <a href="/support" class="btn btn-outline-primary btn-sm rounded-pill">
                                    Звернутися в підтримку
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card rounded-4 border-0 shadow-sm bg-warning bg-opacity-10 border border-warning border-opacity-50">
                            <div class="card-body p-4">
                                <h3 class="h6 mb-2 text-uppercase text-muted">
                                    <i class="bi bi-info-circle text-warning me-2"></i>Важливо знати
                                </h3>
                                <p class="small mb-0">
                                    Товар повинен бути у оригінальному стані з упаковкою. 
                                    Період повернення: <strong>14 днів</strong> з дня купівлі.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .returns-list-hero {
        overflow: hidden;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
