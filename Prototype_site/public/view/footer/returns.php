<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<link rel="stylesheet" href="/view/footer/footer-styles.css">

<section class="hero position-relative text-white text-center">
    <div class="overlay position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.75)); z-index:1;"></div>

    <div class="container position-relative py-5" style="z-index:1;">
        <h1 class="fw-bold display-5 mb-3 fade-in">Повернення товарів</h1>
        <p class="lead mb-4 mx-auto fade-in-delay" style="max-width: 720px;">
            Ми прагнемо, щоб покупки в AutoParts були максимально зручними. Якщо товар не підійшов — ви легко зможете його повернути або обміняти.
        </p>
        <a href="#how-to-return" class="btn btn-warning btn-lg pulse-hover">Як повернути товар</a>
    </div>

    <img src="https://abrakadabra.fun/uploads/posts/2022-03/thumbs/1647208236_13-abrakadabra-fun-p-fon-dlya-bannera-avtozapchasti-23.jpg"
        class="position-absolute top-0 start-0 w-100 h-100"
        style="object-fit: cover; z-index:0;" alt="Повернення товарів">
</section>

<section class="py-5 slide-up">
    <div class="container text-center">
        <h2 class="fw-bold mb-4 fade-in">Умови повернення</h2>
        <p class="mx-auto fade-in-delay" style="max-width: 850px; font-size: 1.1rem; line-height: 1.7;">
            Повернення товару можливе протягом <strong>14 календарних днів</strong> з моменту отримання, якщо він не був у використанні,
            має повний комплект, товарний вигляд і супровідні документи. Ми дотримуємося Закону України “Про захист прав споживачів”.
        </p>

        <div class="row g-4 mt-5">
            <div class="col-md-4 fade-in-delay">
                <div class="p-4 rounded-4 shadow-sm h-100 bg-light hover-scale">
                    <i class="bi bi-box-arrow-in-left fs-1 text-warning mb-3"></i>
                    <h5 class="fw-bold">Просте оформлення</h5>
                    <p>Заявку можна створити онлайн або через підтримку — без складних процедур і зайвих документів.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-delay">
                <div class="p-4 rounded-4 shadow-sm h-100 bg-light hover-scale">
                    <i class="bi bi-truck fs-1 text-warning mb-3"></i>
                    <h5 class="fw-bold">Зручна доставка</h5>
                    <p>Повернення приймаємо Новою Поштою або самостійно в одному з наших складів.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-delay">
                <div class="p-4 rounded-4 shadow-sm h-100 bg-light hover-scale">
                    <i class="bi bi-cash-stack fs-1 text-warning mb-3"></i>
                    <h5 class="fw-bold">Швидке повернення коштів</h5>
                    <p>Ми повертаємо кошти протягом <strong>3–5 робочих днів</strong> після перевірки товару.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light fade-in">
    <div class="container">
        <h2 class="fw-bold mb-5 text-center">Що можна та не можна повернути</h2>
        <div class="row g-5">
            <div class="col-md-6 slide-up">
                <div class="p-4 rounded-4 shadow-sm bg-white h-100 hover-rise">
                    <h5 class="fw-bold text-success mb-3"><i class="bi bi-check-circle text-warning me-2"></i>Можна повернути</h5>
                    <ul class="list-unstyled text-secondary lh-lg">
                        <li><i class="bi bi-dot"></i> Нові товари без ознак використання</li>
                        <li><i class="bi bi-dot"></i> Деталі в оригінальній непошкодженій упаковці</li>
                        <li><i class="bi bi-dot"></i> Комплектні вироби з усіма частинами та документами</li>
                        <li><i class="bi bi-dot"></i> Товари з помилкою у замовленні (не та модель, артикул)</li>
                        <li><i class="bi bi-dot"></i> Товари, які не підходять за розміром або сумісністю (за умови цілісності)</li>
                        <li><i class="bi bi-dot"></i> Несправні товари, якщо дефект підтверджено перевіркою</li>
                        <li><i class="bi bi-dot"></i> Аксесуари, які не були розпаковані</li>
                        <li><i class="bi bi-dot"></i> Запасні частини, що не встановлювались</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6 slide-up">
                <div class="p-4 rounded-4 shadow-sm bg-white h-100 hover-rise">
                    <h5 class="fw-bold text-danger mb-3"><i class="bi bi-x-circle text-warning me-2"></i>Не підлягає поверненню</h5>
                    <ul class="list-unstyled text-secondary lh-lg">
                        <li><i class="bi bi-dot"></i> Електронні компоненти після підключення або встановлення</li>
                        <li><i class="bi bi-dot"></i> Товари зі слідами монтажу, подряпинами чи пошкодженнями</li>
                        <li><i class="bi bi-dot"></i> Масла, автохімія, рідини після розкриття упаковки</li>
                        <li><i class="bi bi-dot"></i> Деталі, виготовлені або замовлені індивідуально</li>
                        <li><i class="bi bi-dot"></i> Продукція, що втратила товарний вигляд (бруд, сліди мастила)</li>
                        <li><i class="bi bi-dot"></i> Товари без чеку або документів, що підтверджують покупку</li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="text-center mt-4 fst-italic text-muted fade-in-delay">* Якщо ви сумніваєтесь, чи підлягає товар поверненню — зв’яжіться з нашим менеджером для уточнення.</p>
    </div>
</section>

<section class="py-5 fade-in" id="how-to-return">
    <div class="container">
        <h2 class="fw-bold mb-4 text-center slide-up">Як оформити повернення</h2>
        <div class="row justify-content-center mb-4 fade-in-delay">
            <div class="col-md-8">
                <ol class="list-group list-group-numbered shadow-sm rounded-4">
                    <li class="list-group-item py-3">Заповніть форму зворотного зв’язку або створіть заявку в особистому кабінеті.</li>
                    <li class="list-group-item py-3">Отримайте підтвердження від менеджера та адресу складу для відправлення.</li>
                    <li class="list-group-item py-3">Упакуйте товар разом із копією чеку або накладної.</li>
                    <li class="list-group-item py-3">Надішліть товар через “Нову Пошту” або доставте особисто.</li>
                    <li class="list-group-item py-3">Після перевірки товару ми повернемо кошти протягом 3–5 робочих днів.</li>
                </ol>
            </div>
        </div>

        <div class="text-center fade-in-delay2">
            <a href="/return-form" class="btn btn-warning btn-lg pulse-hover"><i class="bi bi-arrow-repeat me-2"></i>Заповнити заявку на повернення</a>
        </div>
    </div>
</section>

<section class="py-5 bg-light text-center fade-in-delay">
    <div class="container">
        <h2 class="fw-bold mb-3">Потрібна допомога?</h2>
        <p class="mb-4" style="font-size: 1.1rem;">Ми завжди поруч, щоб допомогти з оформленням повернення, обміну або консультацією щодо умов гарантії.</p>
        <a href="/support" class="btn btn-warning btn-lg pulse-hover"><i class="bi bi-chat-dots me-2"></i>Зв’язатися з підтримкою</a>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>