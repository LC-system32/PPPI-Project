<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$errors = $errors ?? [];
$formData = $formData ?? [];
$isEdit = !empty($formData['id']);
$action = "/admin/users/{$formData['id']}";
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
    .form-control:disabled { background-color: #f8f9fa; }
    .form-label { font-weight: 600; color: #0d1117; margin-bottom: 0.5rem; }
    .btn { border-radius: 10px; font-weight: 600; }
</style>

<section class="py-4">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="/admin" class="text-muted text-decoration-none">Панель</a></li>
                <li class="breadcrumb-item"><a href="/admin/users" class="text-muted text-decoration-none">Користувачі</a></li>
                <li class="breadcrumb-item active" aria-current="page">Редагування</li>
            </ol>
        </nav>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="display-6 mb-0">Редагування користувача</h1>
                <p class="text-muted small mb-0">Керування правами доступу користувача</p>
            </div>
            <a href="/admin/users" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"><i class="bi bi-arrow-left"></i> Назад</a>
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
                        <label for="login" class="form-label">Логін</label>
                        <input type="text" id="login" class="form-control" value="<?= htmlspecialchars($formData['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
                        <small class="text-muted">Логін не можна змінювати</small>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($formData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
                        <small class="text-muted">Email не можна змінювати</small>
                    </div>

                    <div class="col-md-6">
                        <label for="role_id" class="form-label">Роль в системі</label>
                        <select id="role_id" name="role_id" class="form-select" required>
                            <option value="">— Виберіть роль —</option>
                            <option value="1" <?= (int)($formData['role_id'] ?? 0) === 1 ? 'selected' : '' ?>>Адміністратор</option>
                            <option value="2" <?= (int)($formData['role_id'] ?? 0) === 2 ? 'selected' : '' ?>>Менеджер</option>
                            <option value="3" <?= (int)($formData['role_id'] ?? 0) === 3 ? 'selected' : '' ?>>Клієнт</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Зберегти зміни
                    </button>
                    <a href="/admin/users" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>ч
