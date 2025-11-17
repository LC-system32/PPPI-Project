<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$formData = $formData ?? [];
$errors = $errors ?? [];
$csrf = csrf_token();
$isEdit = !empty($formData['id']);
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
                <li class="breadcrumb-item"><a href="/admin/categories" class="text-muted text-decoration-none">Категорії</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Редагування' : 'Створення' ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0"><?= $isEdit ? 'Редагування категорії' : 'Нова категорія' ?></h1>
                <p class="text-muted small mb-0"><?= $isEdit ? 'Оновіть інформацію про категорію' : 'Заповніть форму для створення нової категорії' ?></p>
            </div>
            <a href="/admin/categories" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-left"></i> Назад</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">Помилки валідації</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="admin-card p-4">
            <form action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Назва категорії</label>
                        <input type="text" id="name" class="form-control" name="name" value="<?= htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Наприклад: Автомобільні аксесуари" required>
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug (ЛП URL)</label>
                        <input type="text" id="slug" class="form-control" name="slug" value="<?= htmlspecialchars($formData['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="napr-avtomobilni-aksesuary" required>
                    </div>
                    <div class="col-md-6">
                        <label for="parent_id" class="form-label">Батьківська категорія (опційно)</label>
                        <select id="parent_id" name="parent_id" class="form-select">
                            <option value="">— Без батьківської</option>
                            <?php foreach ($categories as $category): ?>
                                <?php if (($formData['id'] ?? null) !== $category['id']): // Prevent self-reference ?>
                                    <option value="<?= (int)$category['id'] ?>" <?= ((int)($formData['parent_id'] ?? 0) === (int)$category['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Опис (опційно)</label>
                        <textarea id="description" name="description" class="form-control" rows="5" placeholder="Введіть опис категорії..."><?= htmlspecialchars($formData['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> <?= $isEdit ? 'Оновити' : 'Створити' ?>
                    </button>
                    <a href="/admin/categories" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
