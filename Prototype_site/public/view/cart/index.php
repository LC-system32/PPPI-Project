<?php
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$csrf = csrf_token();

// Підрахунок кількості товарів у кошику
$totalItems = 0;
if (!empty($items)) {
    foreach ($items as $row) {
        $totalItems += (int) ($row['quantity'] ?? 0);
    }
}
?>

<section class="py-4 py-md-5 bg-body-tertiary">
    <div class="row justify-content-center w-100 mx-0">
        <div class="col-12 col-xl-10">

            <!-- ХЛІБНІ КРИХТИ -->
            <nav aria-label="breadcrumb" class="mb-2 small">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="/" class="text-decoration-none">Головна</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Кошик</li>
                </ol>
            </nav>

            <!-- ЕТАПИ ОФОРМЛЕННЯ -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-4 small">
                <div class="d-flex align-items-center">
                    <span class="badge rounded-pill bg-dark me-2">1</span>
                    <span class="fw-semibold">Кошик</span>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="d-flex align-items-center text-muted">
                    <span class="badge rounded-pill bg-light border me-2">2</span>
                    <span>Доставка</span>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="d-flex align-items-center text-muted">
                    <span class="badge rounded-pill bg-light border me-2">3</span>
                    <span>Оплата</span>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="d-flex align-items-center text-muted">
                    <span class="badge rounded-pill bg-light border me-2">4</span>
                    <span>Підтвердження</span>
                </div>
            </div>
            <!-- ПОВІДОМЛЕННЯ -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-success d-flex align-items-center gap-3 mb-3 js-cart-alert-autohide">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                    <div>
                        <div class="fw-semibold mb-1">
                            Товар додано до кошика. Ви можете продовжити покупки або оформити замовлення.
                        </div>
                        <?php if (is_string($message) && trim($message) !== ''): ?>
                            <div class="small mb-0"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-3">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($items)): ?>
                <div class="row g-4">

                    <!-- ЛІВА КОЛОНКА: КАРТКА КОШИКА З ТОВАРАМИ -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">

                                <!-- Заголовок + сума (десктоп) -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h1 class="h4 fw-bold mb-1">Кошик</h1>
                                        <p class="text-muted small mb-0">
                                            Перевірте товари перед оформленням замовлення.
                                        </p>
                                    </div>
                                </div>

                                <!-- Таблиця товарів -->
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40%;">Товар</th>
                                                <th class="text-nowrap">Ціна за одиницю</th>
                                                <th class="text-center">Кількість</th>
                                                <th class="text-end">Сума</th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                                <?php
                                                $productId   = (int) $item['product_id'];
                                                $productName = htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');
                                                $stock       = (int) ($item['stock'] ?? 0);
                                                $price       = (float) ($item['price'] ?? 0);
                                                $qty         = (int) ($item['quantity'] ?? 1);
                                                $subtotal    = (float) ($item['subtotal'] ?? ($qty * $price));
                                                $sku         = htmlspecialchars($item['sku'] ?? '', ENT_QUOTES, 'UTF-8');
                                                $imageUrl    = $item['image_url'] ?? null;
                                                ?>
                                                <tr>
                                                    <!-- Товар: фото + назва + артикул/наявність -->
                                                    <td>
                                                        <div class="d-flex align-items-start gap-3">
                                                            <!-- Міні-фото -->
                                                            <div class="flex-shrink-0">
                                                                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center"
                                                                    style="width: 64px; height: 64px;">
                                                                    <?php if (!empty($imageUrl)): ?>
                                                                        <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
                                                                            class="img-fluid object-fit-contain rounded-3"
                                                                            alt="<?= $productName ?>">
                                                                    <?php else: ?>
                                                                        <i class="bi bi-box-seam text-muted fs-4"></i>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <!-- Інфо про товар -->
                                                            <div>
                                                                <p class="mb-1 fw-semibold text-truncate">
                                                                    <?= $productName ?>
                                                                </p>
                                                                <div class="small text-muted">
                                                                    <?php if (!empty($sku)): ?>
                                                                        <span class="d-block">Артикул: <?= $sku ?></span>
                                                                    <?php endif; ?>
                                                                    <span class="d-block">
                                                                        В наявності: <?= $stock ?> шт.
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- Ціна -->
                                                    <td class="text-nowrap">
                                                        <?= number_format($price, 2, '.', ' ') ?> ₴
                                                    </td>

                                                    <!-- Кількість з – / + -->
                                                    <td class="text-center" style="width: 210px;">
                                                        <form action="/cart/update" method="POST"
                                                            class="d-flex flex-column align-items-center gap-1"
                                                            data-quantity-wrapper>
                                                            <input type="hidden" name="csrf_token"
                                                                value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="product_id"
                                                                value="<?= $productId ?>">

                                                            <div class="input-group input-group-sm justify-content-center">
                                                                <button class="btn btn-outline-secondary"
                                                                    type="button"
                                                                    data-quantity-btn="minus">
                                                                    <i class="bi bi-dash-lg"></i>
                                                                </button>
                                                                <input type="number"
                                                                    class="form-control text-center"
                                                                    name="quantity"
                                                                    value="<?= $qty ?>"
                                                                    min="1"
                                                                    max="<?= $stock ?>">
                                                                <button class="btn btn-outline-secondary"
                                                                    type="button"
                                                                    data-quantity-btn="plus">
                                                                    <i class="bi bi-plus-lg"></i>
                                                                </button>
                                                            </div>

                                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                                <small class="text-muted mt-1">
                                                                    Максимум <?= $stock ?> шт.
                                                                </small>
                                                            </div>
                                                        </form>
                                                    </td>

                                                    <!-- Сума -->
                                                    <td class="text-end fw-semibold text-nowrap">
                                                        <?= number_format($subtotal, 2, '.', ' ') ?> ₴
                                                    </td>

                                                    <!-- Видалити -->
                                                    <td class="text-center">
                                                        <form action="/cart/remove" method="POST">
                                                            <input type="hidden" name="csrf_token"
                                                                value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="product_id"
                                                                value="<?= $productId ?>">
                                                            <button class="btn btn-sm btn-outline-danger rounded-circle"
                                                                type="submit"
                                                                title="Видалити з кошика">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Кнопка "Продовжити покупки" під таблицею -->
                                <div class="mt-3">
                                    <a href="/catalog" class="btn btn-outline-secondary btn-sm rounded-pill">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Продовжити покупки
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ПРАВА КОЛОНКА: ПІДСУМОК ЗАМОВЛЕННЯ -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 position-sticky top-0">
                            <div class="card-body p-4">
                                <h2 class="h5 fw-bold mb-3">Підсумок замовлення</h2>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Товарів</span>
                                    <span><?= $totalItems ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Сума товарів</span>
                                    <span><?= number_format($total ?? 0, 2, '.', ' ') ?> ₴</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Знижка</span>
                                    <span>0 ₴</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                    <span class="fw-semibold">До сплати</span>
                                    <span class="h4 mb-0">
                                        <?= number_format($total ?? 0, 2, '.', ' ') ?> ₴
                                    </span>
                                </div>

                                <a href="/checkout"
                                    class="btn btn-dark w-100 btn-lg rounded-pill mb-2">
                                    Оформити замовлення
                                </a>

                                <button type="button"
                                    class="btn btn-outline-secondary w-100 btn-sm rounded-pill d-none d-lg-block"
                                    onclick="window.location.href='/catalog'">
                                    Продовжити покупки
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- MOBILE STICKY CTA -->
                <div class="d-md-none fixed-bottom bg-body border-top shadow-sm py-2">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted">До сплати</div>
                                <div class="fw-bold">
                                    <?= number_format($total ?? 0, 2, '.', ' ') ?> ₴
                                </div>
                            </div>
                            <a href="/checkout"
                                class="btn btn-dark btn-sm rounded-pill px-3">
                                Оформити
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>

                <!-- ПОРОЖНІЙ КОШИК -->
                <div class="card border-0 shadow-sm rounded-4 bg-white text-center p-5">
                    <div class="card-body">
                        <div class="display-4 mb-3">
                            <i class="bi bi-cart-x text-muted"></i>
                        </div>
                        <h1 class="h4 fw-bold mb-2">Ваш кошик порожній</h1>
                        <p class="text-muted mb-4">
                            Знайдіть запчастини для вашого авто за номером деталі, VIN або брендом.
                        </p>
                        <a href="/catalog" class="btn btn-dark rounded-pill px-4">
                            Перейти до каталогу
                        </a>
                    </div>
                </div>

            <?php endif; ?>

            <!-- БЛОК "РАЗОМ З ЦИМ ЧАСТО КУПУЮТЬ" -->
            <?php if (!empty($recommendedProducts ?? [])): ?>
                <section class="mt-5">
                    <h2 class="h5 fw-bold mb-3">Разом з цим часто купують</h2>
                    <div class="row g-3">
                        <?php foreach ($recommendedProducts as $product): ?>
                            <?php
                            $pName   = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
                            $pSlug   = htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                            $pBrand  = htmlspecialchars($product['brand_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $pImg    = $product['image_url'] ?? null;
                            $pPrice  = (float) ($product['price'] ?? 0);
                            ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="/product/<?= $pSlug ?>" class="text-decoration-none text-dark">
                                    <div class="card border-0 shadow-sm h-100 rounded-4">
                                        <div class="ratio ratio-4x3 bg-light rounded-top d-flex align-items-center justify-content-center">
                                            <?php if (!empty($pImg)): ?>
                                                <img src="<?= htmlspecialchars($pImg, ENT_QUOTES, 'UTF-8') ?>"
                                                    class="img-fluid object-fit-contain p-2"
                                                    alt="<?= $pName ?>">
                                            <?php else: ?>
                                                <i class="bi bi-box-seam text-muted fs-3"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body p-3">
                                            <h3 class="h6 mb-1 text-truncate"><?= $pName ?></h3>
                                            <?php if (!empty($pBrand)): ?>
                                                <div class="small text-muted mb-1"><?= $pBrand ?></div>
                                            <?php endif; ?>
                                            <div class="fw-bold small">
                                                <?= number_format($pPrice, 2, '.', ' ') ?> ₴
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<!-- JS тільки для Bootstrap 5.3.8 + невеличка логіка -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Авто-згортання зеленого алерта
        var alertEl = document.querySelector('.js-cart-alert-autohide');
        if (alertEl) {
            setTimeout(function() {
                try {
                    var bsAlert = new bootstrap.Alert(alertEl);
                    bsAlert.close();
                } catch (e) {
                    alertEl.remove();
                }
            }, 4000);
        }

        // Кнопки +/- змінюють кількість і одразу відправляють форму оновлення
        document.querySelectorAll('[data-quantity-btn]').forEach(function(btn) {
            btn.addEventListener('click', function(event) {
                event.preventDefault();

                var wrapper = btn.closest('[data-quantity-wrapper]');
                if (!wrapper) return;

                var input = wrapper.querySelector('input[name="quantity"]');
                if (!input) return;

                var current = parseInt(input.value, 10) || 1;
                var min = parseInt(input.min, 10) || 1;
                var max = parseInt(input.max, 10) || 9999;

                if (btn.getAttribute('data-quantity-btn') === 'minus') {
                    current = Math.max(min, current - 1);
                } else {
                    current = Math.min(max, current + 1);
                }

                input.value = current;

                var form = btn.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });

    });
</script>