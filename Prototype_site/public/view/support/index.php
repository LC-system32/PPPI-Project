<?php
require BASE_PATH . '/public/includes/header.php';
require BASE_PATH . '/public/includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Мої заявки на технічну підтримку</h1>

            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <a href="/support/create" class="btn btn-primary">+ Створити нову заявку</a>
            </div>

            <?php if (empty($tickets)): ?>
                <div class="alert alert-info" role="alert">
                    У вас немає заявок на технічну підтримку.
                    <a href="/support/create">Створити першу заявку</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Номер</th>
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
                                    <td><?= htmlspecialchars($ticket->subject) ?></td>
                                    <td><?= htmlspecialchars($ticket->category) ?></td>
                                    <td>
                                        <?php
                                        $priority_badges = [
                                            'low' => 'secondary',
                                            'normal' => 'primary',
                                            'high' => 'warning',
                                            'urgent' => 'danger',
                                        ];
                                        $badge_class = $priority_badges[$ticket->priority] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badge_class ?>">
                                            <?php
                                            $priority_labels = [
                                                'low' => 'Низька',
                                                'normal' => 'Звичайна',
                                                'high' => 'Висока',
                                                'urgent' => 'Термінова',
                                            ];
                                            echo $priority_labels[$ticket->priority] ?? $ticket->priority;
                                            ?>
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
                                        $badge_class = $status_badges[$ticket->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badge_class ?>">
                                            <?php
                                            $status_labels = [
                                                'open' => 'Відкрита',
                                                'in_progress' => 'У розробці',
                                                'resolved' => 'Вирішена',
                                                'closed' => 'Закрита',
                                            ];
                                            echo $status_labels[$ticket->status] ?? $ticket->status;
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($ticket->created_at)) ?></td>
                                    <td>
                                        <a href="/support/<?= $ticket->id ?>" class="btn btn-sm btn-info">Переглянути</a>
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

<?php require BASE_PATH . '/public/includes/footer.php'; ?>
