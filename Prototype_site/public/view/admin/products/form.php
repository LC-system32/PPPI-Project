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
    textarea.form-control { height: auto; min-height: 100px; }
</style>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item"><a href="/admin/products" class="text-muted text-decoration-none">Товари</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Редагування' : 'Створення' ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0"><?= $isEdit ? 'Редагування товару' : 'Новий товар' ?></h1>
                <p class="text-muted small mb-0"><?= $isEdit ? 'Оновіть інформацію про товар' : 'Заповніть форму для створення нового товару' ?></p>
            </div>
            <a href="/admin/products" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-left"></i> Назад</a>
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

                <h5 class="mb-4">Основна інформація</h5>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Назва товару</label>
                        <input type="text" id="name" class="form-control" name="name" value="<?= htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Наприклад: Спортивна чорна глушителька" required>
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug (ЛП URL)</label>
                        <input type="text" id="slug" class="form-control" name="slug" value="<?= htmlspecialchars($formData['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="sportyvna-chorna-glushitelka" required>
                    </div>
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Категорія</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Оберіть категорію</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id'] ?>" <?= ((int)($formData['category_id'] ?? 0) === (int)$category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="brand_id" class="form-label">Бренд (опційно)</label>
                        <select id="brand_id" name="brand_id" class="form-select">
                            <option value="">— Без бренду</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= (int)$brand['id'] ?>" <?= ((int)($formData['brand_id'] ?? 0) === (int)$brand['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sku" class="form-label">SKU (артикул)</label>
                        <input type="text" id="sku" class="form-control" name="sku" value="<?= htmlspecialchars($formData['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ABC-12345" required>
                    </div>
                </div>

                <h5 class="mb-4">Ціна та залишок</h5>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label for="price" class="form-label">Ціна (₴)</label>
                        <input type="number" id="price" step="0.01" class="form-control" name="price" value="<?= htmlspecialchars($formData['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Залишок на складі</label>
                        <input type="number" id="stock" class="form-control" name="stock" value="<?= htmlspecialchars($formData['stock'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="0" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= !isset($formData['is_active']) || $formData['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Активний товар</label>
                        </div>
                    </div>
                </div>

                <h5 class="mb-4">Опис та сумісність</h5>
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <label for="description" class="form-label">Короткий опис</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Введіть короткий опис товару..."><?= htmlspecialchars($formData['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label for="compatibility" class="form-label">Сумісність (опційно)</label>
                        <textarea id="compatibility" name="compatibility" class="form-control" placeholder="Введіть інформацію про сумісність..."><?= htmlspecialchars($formData['compatibility'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> <?= $isEdit ? 'Оновити' : 'Створити' ?>
                    </button>
                    <a href="/admin/products" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
