<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$errors = $errors ?? [];
$formData = $formData ?? [];
$isEdit = !empty($formData['id']);
$action = $isEdit ? "/admin/car-models/{$formData['id']}" : '/admin/car-models';
$csrf = function_exists('csrf_token') ? csrf_token() : '';
?>

<style>
    :root { --sp-md: 1.5rem; --card-radius: 14px; --muted: #6c757d; }
    .admin-card { background: #fff; border-radius: var(--card-radius); box-shadow: 0 6px 18px rgba(21, 28, 64, 0.06); border: 1px solid rgba(16, 24, 40, 0.03); }
    .p-4 { padding: var(--sp-md) !important; }
    .display-6 { font-weight: 700; }
    .breadcrumb .breadcrumb-item { color: var(--muted); }
    .breadcrumb { background: transparent; padding: 0; }
    .form-control, .form-select { height: calc(var(--sp-md) * 2.2); border-radius: 10px; border: 1px solid rgba(16, 24, 40, 0.1); }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1); }
    .form-label { font-weight: 600; color: #0d1117; margin-bottom: 0.5rem; }
    .btn { border-radius: 10px; font-weight: 600; }
</style>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item"><a href="/admin/car-models" class="text-muted text-decoration-none">Моделі авто</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Редагування' : 'Створення' ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0"><?= $isEdit ? 'Редагування моделі' : 'Нова модель авто' ?></h1>
                <p class="text-muted small mb-0"><?= $isEdit ? 'Оновіть інформацію про модель' : 'Заповніть форму для створення нової моделі' ?></p>
            </div>
            <a href="/admin/car-models" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-left"></i> Назад</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">Помилки валідації</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="admin-card p-4">
            <form action="<?= $action ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="brand" class="form-label">Бренд</label>
                        <input type="text" id="brand" name="brand" class="form-control" value="<?= htmlspecialchars($formData['brand'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Наприклад: BMW" required>
                    </div>

                    <div class="col-md-6">
                        <label for="model" class="form-label">Модель</label>
                        <input type="text" id="model" name="model" class="form-control" value="<?= htmlspecialchars($formData['model'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Наприклад: 3 Series" required>
                    </div>

                    <div class="col-md-6">
                        <label for="generation" class="form-label">Покоління</label>
                        <input type="text" id="generation" name="generation" class="form-control" value="<?= htmlspecialchars($formData['generation'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Наприклад: F30">
                    </div>

                    <div class="col-md-3">
                        <label for="year_from" class="form-label">Рік від</label>
                        <input type="number" id="year_from" name="year_from" class="form-control" value="<?= htmlspecialchars($formData['year_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="2012">
                    </div>

                    <div class="col-md-3">
                        <label for="year_to" class="form-label">Рік до</label>
                        <input type="number" id="year_to" name="year_to" class="form-control" value="<?= htmlspecialchars($formData['year_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="2019">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> <?= $isEdit ? 'Оновити' : 'Створити' ?>
                    </button>
                    <a href="/admin/car-models" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
