<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<!-- HERO -->
<section class="py-5 bg-white">
    <div class="container text-center">
        <h2 class="fw-semibold text-dark">Ми завжди на зв'язку</h2>
        <p class="text-muted fs-5 mb-0">
            Працюємо щодня з 9:00 до 20:00 — оберіть зручний спосіб зв’язку або надішліть питання.
        </p>
    </div>
</section>

<!-- CONTACT OPTIONS -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row g-4">

            <!-- PHONE -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">

                        <div class="bg-warning d-inline-flex rounded-circle justify-content-center align-items-center mb-3"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-telephone-outbound fs-3 text-dark"></i>
                        </div>

                        <h5 class="fw-semibold">Телефонуйте нам</h5>
                        <p class="fs-4 fw-semibold mb-0">0 800 500 777</p>
                        <small class="text-muted">Безкоштовно по Україні</small>
                    </div>
                </div>
            </div>

            <!-- EMAIL -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">

                        <div class="bg-warning d-inline-flex rounded-circle justify-content-center align-items-center mb-3"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-envelope-paper fs-3 text-dark"></i>
                        </div>

                        <h5 class="fw-semibold">Напишіть нам</h5>
                        <p class="fs-4 fw-semibold mb-0">support@autoparts.ua</p>
                        <small class="text-muted">Відповідаємо в середньому за 15 хв</small>
                    </div>
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">

                        <div class="bg-warning d-inline-flex rounded-circle justify-content-center align-items-center mb-3"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-geo-alt fs-3 text-dark"></i>
                        </div>

                        <h5 class="fw-semibold">Відвідайте нас</h5>
                        <p class="fs-5 fw-semibold mb-0">Київ, просп. Перемоги, 16</p>
                        <small class="text-muted">Пункт видачі</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="py-2 bg-white">
    <div class="container">

        <div class="row justify-content-center rounded-4 p-3">
            <div class="col-lg-8">

                <div class="card shadow-sm border border-dark rounded-4">
                    <div class="card-body p-5">

                        <h4 class="fw-semibold text-center mb-4">
                            Залиште своє запитання
                        </h4>

                        <form action="/support/send" method="POST" class="row g-4 ">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ваше ім’я</label>
                                <input type="text" name="name" class="form-control" placeholder="Іван" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Тема</label>
                                <input type="text" name="subject" class="form-control" placeholder="Питання щодо замовлення">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Ваше повідомлення</label>
                                <textarea name="message" rows="5" class="form-control" placeholder="Опишіть вашу проблему…" required></textarea>
                            </div>

                            <div class="col-12 text-center">
                                <button class="btn btn-warning px-5 fw-semibold">
                                    <i class="bi bi-send-fill me-2"></i>Надіслати
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</section>


<?php include __DIR__ . '/../../includes/footer.php'; ?>