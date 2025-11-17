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
            <h1 class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-question-circle text-primary"></i>
                Заявки на технічну підтримку
            </h1>

            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Всі заявки</h5>
                    <span class="badge bg-primary"><?= count($tickets) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($tickets)): ?>
                        <div class="alert alert-info" role="alert">
                            Немає заявок на технічну підтримку.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Номер</th>
                                        <th>Користувач</th>
                                        <th>Тема</th>
                                        <th>Категорія</th>
                                        <th>Пріоритет</th>
                                        <th>Статус</th>
                                        <th>Дата</th>
                                        <th>Дії</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($ticket->ticket_number) ?></strong></td>
                                            <td>
                                                <?php if ($ticket->user_id): ?>
                                                    Користувач ID: <?= $ticket->user_id ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Гість</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars(substr($ticket->subject, 0, 50)) ?></td>
                                            <td><?= htmlspecialchars($ticket->category) ?></td>
                                            <td>
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
                                            </td>
                                            <td>
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
                                            </td>
                                            <td><?= date('d.m.Y H:i', strtotime($ticket->created_at)) ?></td>
                                            <td>
                                                <a href="/admin/support/<?= $ticket->id ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Переглянути
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
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
