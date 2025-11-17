<?php
/** @var \App\Models\User $user */
/** @var array $eligibleOrders */
/** @var array $reasons */
/** @var array $returnMethods */

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrfToken = csrf_token();
$message = $message ?? null;
$errors = $errors ?? [];
?>

<section class="returns-hero position-relative text-white py-5">
    <img src="https://images.pexels.com/photos/3807517/pexels-photo-3807517.jpeg"
         alt="Returns"
         class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(120deg, rgba(0,0,0,.85), rgba(0,0,0,.6));"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <p class="text-uppercase text-white-50 mb-2">
                    <i class="bi bi-arrow-left-circle me-2"></i>Повернення товару
                </p>
                <h1 class="display-5 fw-bold mb-3">Оформити повернення</h1>
                <p class="fs-5 text-white-50">
                    Ви можете повернути або обміняти товар протягом 14 днів з дня купівлі згідно Закону України 
                    «Про захист прав споживачів»
                </p>
            </div>
        </div>
    </div>
</section>

<section class="returns-content py-5 bg-light">
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm rounded-4 mb-4">
                <p class="fw-semibold mb-2"><i class="bi bi-exclamation-circle me-2"></i>Помилка</p>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h3 mb-4">Форма повернення товару</h2>

                        <?php if (empty($eligibleOrders)): ?>
                            <div class="alert alert-info rounded-3" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>У вас немає замовлень, доступних для повернення.</strong>
                                <p class="mb-0 mt-2 text-muted small">
                                    Товар можна повернути протягом 14 днів з дня купівлі. 
                                    <a href="/orders" class="link-primary">Переглянути всі замовлення</a>
                                </p>
                            </div>
                        <?php else: ?>
                            <form action="/returns" method="POST" class="row g-4">
                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                <!-- Вибір замовлення -->
                                <div class="col-12">
                                    <label for="order_id" class="form-label fw-semibold">
                                        Вибберіть замовлення <span class="text-danger">*</span>
                                    </label>
                                    <select id="order_id" name="order_id" class="form-select rounded-3" required onchange="updateOrderDetails()">
                                        <option value="">-- Виберіть замовлення --</option>
                                        <?php foreach ($eligibleOrders as $order): 
                                            $daysAgo = (int)(strtotime('now') - strtotime($order['created_at'])) / 86400;
                                            $daysRemaining = max(0, 14 - $daysAgo);
                                        ?>
                                            <option value="<?= (int)$order['id'] ?>"
                                                    data-total="<?= htmlspecialchars($order['total'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-created="<?= htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-items='<?= json_encode($order['items'] ?? []) ?>'>
                                                Замовлення №<?= (int)$order['id'] ?> – 
                                                <?= htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8') ?> 
                                                (<?= (int)$order['total'] ?> грн) – Залишилось <?= $daysRemaining ?> днів
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Період повернення: 14 днів з дня купівлі
                                    </small>
                                </div>

                                <!-- Інформація про вибране замовлення -->
                                <div id="order-info" class="col-12" style="display: none;">
                                    <div class="alert alert-light rounded-3 border">
                                        <div class="row g-3 text-sm">
                                            <div class="col-sm-6">
                                                <strong>Дата замовлення:</strong>
                                                <div id="info-date" class="text-muted"></div>
                                            </div>
                                            <div class="col-sm-6">
                                                <strong>Сума:</strong>
                                                <div id="info-total" class="text-muted"></div>
                                            </div>
                                            <div class="col-12">
                                                <strong>Товари в замовленні:</strong>
                                                <ul id="info-items" class="list-unstyled text-muted small mt-2 mb-0"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Причина повернення -->
                                <div class="col-12">
                                    <label for="reason" class="form-label fw-semibold">
                                        Причина повернення <span class="text-danger">*</span>
                                    </label>
                                    <select id="reason" name="reason" class="form-select rounded-3" required>
                                        <option value="">-- Виберіть причину --</option>
                                        <?php foreach ($reasons as $key => $label): ?>
                                            <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Опис проблеми -->
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">
                                        Опис проблеми (необов'язково)
                                    </label>
                                    <textarea id="description" name="description" 
                                              class="form-control rounded-3" rows="4"
                                              placeholder="Напишіть деталі: що саме вас не задовольнило, який дефект тощо..."></textarea>
                                    <small class="text-muted d-block mt-2">
                                        Максимум 1000 символів
                                    </small>
                                </div>

                                <!-- Вибір товарів для повернення -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Товари для повернення (необов'язково)
                                    </label>
                                    <div id="items-list" class="list-group rounded-3">
                                        <p class="text-muted text-center py-3 mb-0">
                                            Виберіть замовлення, щоб побачити товари
                                        </p>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        Якщо не вибрати товари, повернення буде оформлено на все замовлення
                                    </small>
                                </div>

                                <!-- Спосіб повернення -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Спосіб повернення <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-3">
                                        <?php foreach ($returnMethods as $key => $label): ?>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-custom">
                                                    <input class="form-check-input" type="radio" id="method_<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" 
                                                           name="return_method" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                                                           <?= $key === 'courier' ? 'checked' : '' ?> required>
                                                    <label class="form-check-label" for="method_<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Більшість способів повернення безкоштовні. Детальна інструкція буде надіслана на вашу пошту.
                                    </small>
                                </div>

                                <!-- Кнопки -->
                                <div class="col-12 d-flex gap-3 justify-content-end">
                                    <a href="/orders" class="btn btn-outline-secondary rounded-pill px-4">Скасувати</a>
                                    <button type="submit" class="btn btn-warning text-dark fw-semibold rounded-pill px-4">
                                        <i class="bi bi-check-circle me-2"></i>Оформити повернення
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ІНСТРУКЦІЇ -->
            <div class="col-lg-4">
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-question-circle text-warning me-2"></i>Як повернути товар
                        </h3>
                        <ol class="small mb-0">
                            <li class="mb-2">Заповніть цю форму і виберіть спосіб повернення</li>
                            <li class="mb-2">Ми підтвердимо ваш запит по електронній пошті</li>
                            <li class="mb-2">Відправте товар за вказаною адресою</li>
                            <li class="mb-2">Після отримання товару ми перевіримо його стан</li>
                            <li>Повернемо кошти на вашу карту протягом 5-10 робочих днів</li>
                        </ol>
                    </div>
                </div>

                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-shield-check text-success me-2"></i>Умови повернення
                        </h3>
                        <ul class="small mb-0 ps-3">
                            <li class="mb-2">Товар повинен бути у оригінальному стані</li>
                            <li class="mb-2">Упаковка та ярлики не пошкоджені</li>
                            <li class="mb-2">Період повернення: 14 днів</li>
                            <li class="mb-2">Товар не мав користування</li>
                            <li>Дефектний товар повертається з документами</li>
                        </ul>
                    </div>
                </div>

                <div class="card rounded-4 border-0 shadow-sm bg-warning bg-opacity-10 border border-warning border-opacity-50">
                    <div class="card-body p-4">
                        <p class="mb-0 small">
                            <i class="bi bi-info-circle text-warning me-2"></i>
                            <strong>14 днів</strong> – строк повернення товару по Закону України 
                            «Про захист прав споживачів»
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .returns-hero {
        overflow: hidden;
    }

    .form-check-custom .form-check-input {
        border-radius: 0.375rem;
    }

    .form-check-custom .form-check-input:checked {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .list-group-item {
        border: 1px solid #e9ecef;
        padding: 0.75rem 1rem;
    }
</style>

<script>
    function updateOrderDetails() {
        var select = document.getElementById('order_id');
        var option = select.options[select.selectedIndex];
        
        if (!option.value) {
            document.getElementById('order-info').style.display = 'none';
            document.getElementById('items-list').innerHTML = '<p class="text-muted text-center py-3 mb-0">Виберіть замовлення</p>';
            return;
        }

        // Show order info
        var infoDiv = document.getElementById('order-info');
        document.getElementById('info-date').textContent = option.dataset.created;
        document.getElementById('info-total').textContent = option.dataset.total + ' грн';
        infoDiv.style.display = 'block';

        // Load items
        try {
            var items = JSON.parse(option.dataset.items);
            var itemsList = document.getElementById('items-list');
            itemsList.innerHTML = '';

            if (items && items.length > 0) {
                items.forEach(function (item, idx) {
                    var label = (item.name_snapshot || 'Товар ' + (idx + 1)) + 
                               ' x' + (item.quantity || 1) + ' – ' + (item.price || '0') + ' грн';
                    
                    var checkbox = document.createElement('div');
                    checkbox.className = 'list-group-item';
                    checkbox.innerHTML = '<div class="form-check">' +
                        '<input class="form-check-input" type="checkbox" name="items[]" value="' + idx + '" id="item_' + idx + '">' +
                        '<label class="form-check-label" for="item_' + idx + '">' +
                        label +
                        '</label></div>';
                    itemsList.appendChild(checkbox);
                });
            } else {
                itemsList.innerHTML = '<p class="text-muted text-center py-3 mb-0">Товари не знайдені</p>';
            }
        } catch (e) {
            document.getElementById('items-list').innerHTML = '<p class="text-muted text-center py-3 mb-0">Помилка при завантаженні товарів</p>';
        }
    }

    // Валідація текстової області
    document.getElementById('description')?.addEventListener('input', function () {
        if (this.value.length > 1000) {
            this.value = this.value.substring(0, 1000);
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
