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
            <h1 class="fw-bold mb-0"><?= htmlspecialchars($title ?? 'Категорія', ENT_QUOTES, 'UTF-8') ?></h1>
            <a href="/admin/categories" class="btn btn-outline-dark">До списку</a>
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
                    <div class="col-md-6">
                        <label class="form-label">Батьківська категорія</label>
                        <select name="parent_id" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= ((int) ($formData['parent_id'] ?? 0) === (int) $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Опис</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($formData['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
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
