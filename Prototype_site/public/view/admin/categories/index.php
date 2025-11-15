<?php
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/navbar.php';

$csrf = csrf_token();
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1">Адмін-панель</p>
                <h1 class="fw-bold mb-0">Категорії</h1>
            </div>
            <a href="/admin/categories/create" class="btn btn-dark">Нова категорія</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <?php
                $renderTree = function ($nodes, $level = 0) use (&$renderTree, $csrf) {
                    $indent = $level * 1.5;
                    echo '<ul class="list-unstyled" style="margin-left:' . $indent . 'rem">';
                    foreach ($nodes as $node) {
                        echo '<li class="mb-2 d-flex align-items-center justify-content-between">';
                        echo '<div><strong>' . htmlspecialchars($node['name'], ENT_QUOTES, 'UTF-8') . '</strong>';
                        if (!empty($node['description'])) {
                            echo '<br><small class="text-muted">' . htmlspecialchars($node['description'], ENT_QUOTES, 'UTF-8') . '</small>';
                        }
                        echo '</div>';
                        echo '<div class="d-flex gap-2">';
                        echo '<a href="/admin/categories/' . (int) $node['id'] . '/edit" class="btn btn-outline-dark btn-sm">Редагувати</a>';
                        echo '<form action="/admin/categories/' . (int) $node['id'] . '/delete" method="POST" onsubmit="return confirm(\'Видалити категорію?\')">';
                        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') . '">';
                        echo '<button class="btn btn-link text-danger btn-sm" type="submit">Видалити</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</li>';

                        if (!empty($node['children'])) {
                            $renderTree($node['children'], $level + 1);
                        }
                    }
                    echo '</ul>';
                };

                if (!empty($categories)) {
                    $renderTree($categories);
                } else {
                    echo '<p class="text-muted mb-0">Категорій поки немає.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
