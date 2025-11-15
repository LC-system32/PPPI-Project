<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrf = csrf_token();
?>

<section class="py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold mb-4">Кошик</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($items): ?>
            <div class="table-responsive shadow-sm rounded-4">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Товар</th>
                            <th>Ціна</th>
                            <th>Кількість</th>
                            <th>Сума</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <p class="mb-1 fw-semibold"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <small class="text-muted">В наявності: <?= (int) $item['stock'] ?></small>
                                </td>
                                <td><?= number_format($item['price'], 2, '.', ' ') ?> ₴</td>
                                <td style="width: 180px;">
                                    <form action="/cart/update" method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                        <input type="number" class="form-control" name="quantity" value="<?= (int) $item['quantity'] ?>" min="1" max="<?= (int) $item['stock'] ?>">
                                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="fw-bold"><?= number_format($item['subtotal'], 2, '.', ' ') ?> ₴</td>
                                <td>
                                    <form action="/cart/remove" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                        <button class="btn btn-link text-danger p-0" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-4 gap-3">
                <div>
                    <p class="text-muted mb-1">Сума замовлення</p>
                    <h3 class="fw-bold mb-0"><?= number_format($total, 2, '.', ' ') ?> ₴</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="/catalog" class="btn btn-outline-dark">Продовжити покупки</a>
                    <a href="/checkout" class="btn btn-dark">Оформити замовлення</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary">
                Ваш кошик порожній. Перейдіть у <a href="/catalog">каталог</a>, щоб додати товари.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
