<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<section class="py-5 bg-light">
    <div class="container">

        <!-- HEADER -->
        <div class="text-center">

            <h2 class="fw-semibold text-dark mb-2">Поради та рекомендації</h2>

            <p class="text-muted fs-5">
                Підбір, замовлення, гарантія — усе, що потрібно знати перед покупкою.
            </p>
        </div>

        <!-- 3 COLUMNS OF CARDS -->
        <div class="row g-4 mb-3">

            <!-- BLOCK 1 -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">

                        <h5 class="fw-semibold text-dark mb-3 d-flex align-items-center">
                            <i class="bi bi-search text-warning me-2 fs-4"></i>
                            Підбір запчастин
                        </h5>

                        <p class="text-muted mb-3">
                            Найважливіші рекомендації для правильного вибору товару.
                        </p>

                        <ul class="list-group list-group-flush">

                            <li class="list-group-item bg-white d-flex py-3">
                                <i class="bi bi-check-circle-fill text-warning me-3 fs-5"></i>
                                <span class="text-dark">Перевіряйте сумісність запчастин за VIN-кодом.</span>
                            </li>

                            <li class="list-group-item bg-white d-flex py-3">
                                <i class="bi bi-check-circle-fill text-warning me-3 fs-5"></i>
                                <span class="text-dark">Порівнюйте характеристики з вашою старою деталлю.</span>
                            </li>

                            <li class="list-group-item bg-white d-flex py-3">
                                <i class="bi bi-check-circle-fill text-warning me-3 fs-5"></i>
                                <span class="text-dark">Уточнюйте аналоги — інколи вони кращі й дешевші.</span>
                            </li>

                        </ul>

                    </div>
                </div>
            </div>

            <!-- BLOCK 2 -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">

                        <h5 class="fw-semibold text-dark mb-3 d-flex align-items-center">
                            <i class="bi bi-truck text-warning me-2 fs-4"></i>
                            Доставка та замовлення
                        </h5>

                        <p class="text-muted mb-3">
                            Корисні факти про терміни та доставку.
                        </p>

                        <ul class="list-group list-group-flush">

                            <li class="list-group-item bg-white d-flex py-3">
                                <i class="bi bi-dot text-warning fs-3 me-2"></i>
                                <span class="text-dark">Відправка можлива в той же день при наявності товару.</span>
                            </li>

                            <li class="list-group-item bg-white d-flex py-3">
                                <i class="bi bi-dot text-warning fs-3 me-2"></i>
                                <span class="text-dark">Перевіряйте упакування перед отриманням.</span>
                            </li>

                            <li class="list-group-item bg-white d-flex py-3">
                                <i class="bi bi-dot text-warning fs-3 me-2"></i>
                                <span class="text-dark">У кабінеті можна відстежити статус замовлення.</span>
                            </li>

                        </ul>

                    </div>
                </div>
            </div>

            <!-- BLOCK 3 -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">

                        <h5 class="fw-semibold text-dark mb-3 d-flex align-items-center">
                            <i class="bi bi-shield-check text-warning me-2 fs-4"></i>
                            Гарантія та повернення
                        </h5>

                        <p class="text-muted mb-3">
                            Важливі нюанси, які варто знати заздалегідь.
                        </p>

                        <ul class="list-group list-group-flush">

                            <li class="list-group-item bg-white py-3">
                                <strong class="text-dark d-block">Гарантія:</strong>
                                <small class="text-muted">
                                    Термін залежить від типу товару, часто 6–12 міс.
                                </small>
                            </li>

                            <li class="list-group-item bg-white py-3">
                                <strong class="text-dark d-block">Повернення:</strong>
                                <small class="text-muted">
                                    Протягом 14 днів, якщо товар не встановлювався.
                                </small>
                            </li>

                            <li class="list-group-item bg-white py-3">
                                <strong class="text-dark d-block">Важливо:</strong>
                                <small class="text-muted">
                                    Електроніка поверненню не підлягає.
                                </small>
                            </li>

                        </ul>

                    </div>
                </div>
            </div>

        </div>


        <!-- FINAL NOTE -->
        <div class="text-center">
            <p class="text-muted mb-1">Маєте питання?</p>
            <a href="/support" class="btn btn-warning fw-semibold px-4">
                <i class="bi bi-chat-dots me-2"></i>
                Зв’язатися з підтримкою
            </a>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
