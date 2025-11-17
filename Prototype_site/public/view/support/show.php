<?php
require BASE_PATH . '/public/includes/header.php';
require BASE_PATH . '/public/includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><?= htmlspecialchars($ticket->subject) ?></h1>
                <a href="/support" class="btn btn-outline-secondary">← Назад до списку</a>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Номер заявки:</strong><br>
                            <code><?= htmlspecialchars($ticket->ticket_number) ?></code>
                        </div>
                        <div class="col-md-3">
                            <strong>Статус:</strong><br>
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
                            <span class="badge bg-<?= $badge_class ?>">
                                <?= $status_labels[$ticket->status] ?? $ticket->status ?>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Пріоритет:</strong><br>
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
                            <span class="badge bg-<?= $badge_class ?>">
                                <?= $priority_labels[$ticket->priority] ?? $ticket->priority ?>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Дата створення:</strong><br>
                            <?= date('d.m.Y H:i', strtotime($ticket->created_at)) ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Категорія:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php
                            $categories = [
                                'technical' => 'Технічна проблема',
                                'order' => 'Питання про замовлення',
                                'product' => 'Питання про товар',
                                'delivery' => 'Питання про доставку',
                                'return' => 'Повернення/обмін',
                                'other' => 'Інше',
                            ];
                            echo htmlspecialchars($categories[$ticket->category] ?? $ticket->category);
                            ?>
                        </div>
                    </div>

                    <hr>

                    <h5>Опис проблеми:</h5>
                    <div class="alert alert-light border" style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                        <?= nl2br(htmlspecialchars($ticket->description)) ?>
                    </div>

                    <?php if ($ticket->response): ?>
                        <hr>
                        <h5>Відповідь служби підтримки:</h5>
                        <div class="alert alert-info border" style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                            <?= nl2br(htmlspecialchars($ticket->response)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($ticket->status !== 'closed'): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Додати коментар</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/support/<?= $ticket->id ?>/update">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Ваш коментар <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control" 
                                    id="description" 
                                    name="description" 
                                    rows="4" 
                                    placeholder="Додайте коментар до вашої заявки"
                                    required
                                ></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Додати коментар</button>
                        </form>
                    </div>
                </div>

                <div class="mt-3">
                    <form method="POST" action="/support/<?= $ticket->id ?>/close" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Ви впевнені, що хочете закрити заявку?')">
                            Закрити заявку
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary" role="alert">
                    <strong>Ця заявка закрита.</strong> Якщо у вас нові питання, будь ласка, <a href="/support/create">створіть нову заявку</a>.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/public/includes/footer.php'; ?>
