<?php
require BASE_PATH . '/public/includes/header.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$authUser = $_SESSION['user'] ?? null;
$isAdmin = isset($authUser) && in_array((int) ($authUser['role_id'] ?? 0), [1, 2], true);
?>

<?php require BASE_PATH . '/public/includes/navbar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1><?= htmlspecialchars($ticket->subject) ?></h1>
                <a href="/admin/support" class="btn btn-outline-secondary">← Назад</a>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Номер:</strong><br>
                            <code><?= htmlspecialchars($ticket->ticket_number) ?></code>
                        </div>
                        <div class="col-md-3">
                            <strong>Користувач:</strong><br>
                            <?= $ticket->user_id ? "ID: {$ticket->user_id}" : '<span class="text-muted">Гість</span>' ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Дата:</strong><br>
                            <?= date('d.m.Y H:i', strtotime($ticket->created_at)) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Оновлено:</strong><br>
                            <?= date('d.m.Y H:i', strtotime($ticket->updated_at)) ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Категорія:</strong>
                        <p><?= htmlspecialchars($ticket->category) ?></p>
                    </div>

                    <div class="mb-3">
                        <strong>Пріоритет:</strong>
                        <?php
                        $priority_badges = [
                            'low' => 'secondary',
                            'normal' => 'primary',
                            'high' => 'warning',
                            'urgent' => 'danger',
                        ];
                        $priority_labels = [
                            'low' => 'Низька',
                            'normal' => 'Звичайна',
                            'high' => 'Висока',
                            'urgent' => 'Термінова',
                        ];
                        $badge_class = $priority_badges[$ticket->priority] ?? 'secondary';
                        ?>
                        <p>
                            <span class="badge bg-<?= $badge_class ?>">
                                <?= $priority_labels[$ticket->priority] ?? $ticket->priority ?>
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>Статус:</strong>
                        <?php
                        $status_badges = [
                            'open' => 'primary',
                            'in_progress' => 'info',
                            'resolved' => 'success',
                            'closed' => 'secondary',
                        ];
                        $status_labels = [
                            'open' => 'Відкрита',
                            'in_progress' => 'У розробці',
                            'resolved' => 'Вирішена',
                            'closed' => 'Закрита',
                        ];
                        $badge_class = $status_badges[$ticket->status] ?? 'secondary';
                        ?>
                        <p>
                            <span class="badge bg-<?= $badge_class ?>">
                                <?= $status_labels[$ticket->status] ?? $ticket->status ?>
                            </span>
                        </p>
                    </div>

                    <hr>

                    <h5>Опис проблеми:</h5>
                    <div class="alert alert-light border" style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-height: 400px; overflow-y: auto;">
                        <?= nl2br(htmlspecialchars($ticket->description)) ?>
                    </div>

                    <?php if ($ticket->response): ?>
                        <hr>
                        <h5>Відповідь:</h5>
                        <div class="alert alert-info border" style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-height: 400px; overflow-y: auto;">
                            <?= nl2br(htmlspecialchars($ticket->response)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Керування заявкою</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/support/<?= $ticket->id ?>/status">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Змінити статус</label>
                            <select class="form-select" id="status" name="status">
                                <option value="open" <?= $ticket->status === 'open' ? 'selected' : '' ?>>Відкрита</option>
                                <option value="in_progress" <?= $ticket->status === 'in_progress' ? 'selected' : '' ?>>У розробці</option>
                                <option value="resolved" <?= $ticket->status === 'resolved' ? 'selected' : '' ?>>Вирішена</option>
                                <option value="closed" <?= $ticket->status === 'closed' ? 'selected' : '' ?>>Закрита</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Оновити статус</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Написати відповідь</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/support/<?= $ticket->id ?>/respond">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="mb-3">
                            <label for="response" class="form-label">Ваша відповідь</label>
                            <textarea class="form-control" id="response" name="response" rows="5" placeholder="Напишіть відповідь для користувача"></textarea>
                            <small class="form-text text-muted">Мінімум 10 символів</small>
                        </div>

                        <div class="mb-3">
                            <label for="response_status" class="form-label">Статус після відповіді</label>
                            <select class="form-select" id="response_status" name="status">
                                <option value="in_progress">У розробці</option>
                                <option value="resolved" selected>Вирішена</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Надіслати відповідь</button>
                    </form>
                </div>
            </div>

            <div class="mt-3">
                <form method="POST" action="/admin/support/<?= $ticket->id ?>/delete" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Видалити заявку?')">
                        <i class="bi bi-trash"></i> Видалити
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Close the admin layout
if (isset($isAdmin) && $isAdmin): ?>
            </main>
        </div>
    </div>
<?php endif; ?>

<?php require BASE_PATH . '/public/includes/footer.php'; ?>
