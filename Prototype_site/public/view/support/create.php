<?php
require BASE_PATH . '/public/includes/header.php';
require BASE_PATH . '/public/includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4">Нова заявка на технічну підтримку</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Помилка валідації:</strong>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="alert alert-info" role="alert">
                <h5>Описание</h5>
                <p>
                    Як користувач, я хочу отримати підтримку на рахунок виршень трудноще які виникли під час 
                    користуванням функціоналом сайту
                </p>
                <hr>
                <p class="mb-0">
                    Реалізувати сторінку де будуть знаходитись найчастіші питання, щоб можна могли знаходитись користувачі
                </p>
            </div>

            <form method="POST" action="/support">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="mb-3">
                    <label for="subject" class="form-label">Тема <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control <?= !empty($errors) && isset($old['subject']) ? 'is-invalid' : '' ?>" 
                        id="subject" 
                        name="subject" 
                        placeholder="Коротко опишіть тему вашої проблеми"
                        value="<?= htmlspecialchars($old['subject'] ?? '') ?>"
                        required
                    >
                    <small class="form-text text-muted">Мінімум 5 символів</small>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Категорія <span class="text-danger">*</span></label>
                    <select class="form-select <?= !empty($errors) && isset($old['category']) ? 'is-invalid' : '' ?>" id="category" name="category" required>
                        <option value="">-- Виберіть категорію --</option>
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= htmlspecialchars($key) ?>" <?= (isset($old['category']) && $old['category'] === $key) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="priority" class="form-label">Пріоритет <span class="text-danger">*</span></label>
                    <select class="form-select <?= !empty($errors) && isset($old['priority']) ? 'is-invalid' : '' ?>" id="priority" name="priority" required>
                        <option value="">-- Виберіть пріоритет --</option>
                        <?php foreach ($priorities as $key => $label): ?>
                            <option value="<?= htmlspecialchars($key) ?>" <?= (isset($old['priority']) && $old['priority'] === $key) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Виберіть пріоритет вашої проблеми</small>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Опис проблеми <span class="text-danger">*</span></label>
                    <textarea 
                        class="form-control <?= !empty($errors) && isset($old['description']) ? 'is-invalid' : '' ?>" 
                        id="description" 
                        name="description" 
                        rows="6" 
                        placeholder="Детально опишіть вашу проблему. Що трапилось? Які дії ви робили?"
                        required
                    ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    <small class="form-text text-muted">Мінімум 20 символів</small>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <button type="submit" class="btn btn-primary">Створити заявку</button>
                    <a href="/support" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>

            <hr class="my-5">

            <div class="alert alert-light border">
                <h5>Корисні поради:</h5>
                <ul class="mb-0">
                    <li>Будьте якомога детальнішим при описі проблеми</li>
                    <li>Якщо це стосується замовлення, посилайтесь на номер замовлення</li>
                    <li>Опис помилок (якщо вони є) допоможе нам швидше знайти рішення</li>
                    <li>Ми відповімо вам найскоріше за змогою</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/public/includes/footer.php'; ?>
