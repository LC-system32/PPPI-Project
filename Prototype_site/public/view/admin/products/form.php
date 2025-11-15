<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$formData = $formData ?? [];
$errors = $errors ?? [];
$csrf = csrf_token();
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold mb-0"><?= htmlspecialchars($title ?? 'Товар', ENT_QUOTES, 'UTF-8') ?></h1>
            <a href="/admin/products" class="btn btn-outline-dark">До списку</a>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <form action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" method="POST" class="row g-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="col-md-6">
                        <label class="form-label">Назва</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" value="<?= htmlspecialchars($formData['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Категорія</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Оберіть категорію</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= ((int) ($formData['category_id'] ?? 0) === (int) $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Бренд</label>
                        <select name="brand_id" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= (int) $brand['id'] ?>" <?= ((int) ($formData['brand_id'] ?? 0) === (int) $brand['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SKU</label>
                        <input type="text" class="form-control" name="sku" value="<?= htmlspecialchars($formData['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ціна</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="<?= htmlspecialchars($formData['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Залишок</label>
                        <input type="number" class="form-control" name="stock" value="<?= htmlspecialchars($formData['stock'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($formData['is_active']) || $formData['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label">Активний</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Короткий опис</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($formData['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Сумісність (текст)</label>
                        <textarea name="compatibility" class="form-control" rows="3"><?= htmlspecialchars($formData['compatibility'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark btn-lg">Зберегти</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
