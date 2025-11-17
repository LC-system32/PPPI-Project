<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<!-- Hero -->
<section class="hero position-relative text-white text-center">
    <div class="overlay position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.75)); z-index:1;"></div>

    <div class="container position-relative py-5" style="z-index:2;">
        <h1 class="fw-bold display-5 mb-3 fade-in-delay">Питання та відповіді</h1>
        <p class="lead mx-auto fade-in-delay2" style="max-width: 700px;">
            Дізнайтесь більше про доставку, оплату, повернення та інші деталі — усе, що допоможе вам користуватись сервісом максимально зручно.
        </p>
    </div>

    <img src="https://abrakadabra.fun/uploads/posts/2022-01/thumbs/1643492493_25-abrakadabra-fun-p-fon-dlya-vizitki-avtoservisa-29.jpg"
        alt="FAQ background"
        class="position-absolute top-0 start-0 w-100 h-100"
        style="object-fit: cover; z-index:0;">
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light slide-up">
    <div class="container">
        <div class="text-center mb-5 fade-in">
            <h2 class="fw-bold fade-in-delay">Часті запитання</h2>
            <p class="text-muted fade-in-delay2">
                Якщо не знайшли відповідь — зверніться до 
                <a href="/support" class="text-warning text-decoration-none fw-semibold">техпідтримки</a>.
            </p>
        </div>

        <?php 
        $faq = [
            ["question" => "Як оформити замовлення на сайті?",
             "answer" => "Щоб оформити замовлення, оберіть потрібний товар, додайте його у кошик і натисніть «Оформити». Далі вкажіть контактні дані, виберіть зручний спосіб доставки та оплату. Після підтвердження ви отримаєте email або SMS із деталями."],
            ["question" => "Скільки часу займає доставка?",
             "answer" => "Доставка по Україні зазвичай триває від 1 до 3 робочих днів, залежно від перевізника. Ми працюємо з Новою поштою, Укрпоштою та іншими логістичними партнерами."],
            ["question" => "Чи можу я повернути товар?",
             "answer" => "Так, повернення можливе протягом 14 днів з моменту отримання, якщо товар не був у використанні та збережено упаковку."],
            ["question" => "Які способи оплати доступні?",
             "answer" => "Ми приймаємо оплату картками Visa, MasterCard, через Apple Pay, Google Pay або готівкою при отриманні."],
            ["question" => "Як зв’язатися з техпідтримкою?",
             "answer" => "Ви можете написати через форму <a href='/support' class='text-warning text-decoration-none'>Техпідтримка</a> або на <strong>support@autoparts.com</strong>."],
            ["question" => "Коли працює служба підтримки?",
             "answer" => "Щоденно з 9:00 до 18:00, включно з суботою. У неділю відповіді обробляються у пріоритеті."],
            ["question" => "Чи можна змінити або скасувати замовлення?",
             "answer" => "Так, якщо замовлення ще не відправлено, зв’яжіться з нашою підтримкою телефоном або через кабінет користувача."],
            ["question" => "Як перевірити статус замовлення?",
             "answer" => "Після оформлення ви отримаєте номер замовлення, який можна відстежити у кабінеті або на сайті перевізника."],
            ["question" => "Чи є гарантія на товари?",
             "answer" => "Так, усі товари мають офіційну гарантію виробника. Термін вказано у картці товару."],
            ["question" => "Які є варіанти доставки?",
             "answer" => "Доставка через Нову Пошту, Укрпошту або самовивіз із нашого складу. Вартість залежить від перевізника."],
            ["question" => "Чи можна оформити замовлення без реєстрації?",
             "answer" => "Так, але зареєстровані користувачі отримують знижки та доступ до історії замовлень."],
            ["question" => "Чи можу я отримати консультацію перед покупкою?",
             "answer" => "Так, наші менеджери допоможуть підібрати найкращий варіант через чат або телефоном."]
        ];
        $half = ceil(count($faq) / 2);
        ?>

        <div class="row g-4">
            <div class="col-md-6 fade-in-delay">
                <div class="accordion" id="faqLeft">
                    <?php foreach (array_slice($faq, 0, $half) as $i => $item): ?>
                        <div class="accordion-item mb-3 rounded-4 shadow-sm border-0 hover-scale">
                            <h2 class="accordion-header" id="headingL<?= $i ?>">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseL<?= $i ?>">
                                    <?= htmlspecialchars($item["question"]) ?>
                                </button>
                            </h2>
                            <div id="collapseL<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqLeft">
                                <div class="accordion-body"><?= $item["answer"] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-6 fade-in-delay">
                <div class="accordion" id="faqRight">
                    <?php foreach (array_slice($faq, $half) as $i => $item): ?>
                        <div class="accordion-item mb-3 rounded-4 shadow-sm border-0 hover-scale">
                            <h2 class="accordion-header" id="headingR<?= $i ?>">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseR<?= $i ?>">
                                    <?= htmlspecialchars($item["question"]) ?>
                                </button>
                            </h2>
                            <div id="collapseR<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqRight">
                                <div class="accordion-body"><?= $item["answer"] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 zoom-in">
            <i class="bi bi-question-circle fs-1 mb-3 text-warning"></i>
            <h5 class="fw-bold">Не знайшли потрібне питання?</h5>
            <p>Залиште своє звернення — ми відповімо якнайшвидше!</p>
            <a href="mailto:support@autoparts.com" class="btn btn-warning rounded-pill px-4 fw-semibold text-dark hover-scale">Написати нам</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<link rel="stylesheet" href="/view/footer/footer-styles.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
.faq-card, .accordion-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.accordion-button:not(.collapsed) {
    background: linear-gradient(90deg, #ffc107, #ffdb58);
    color: #000;
    box-shadow: none;
}
.accordion-button:focus {
    box-shadow: none;
}
</style>
</body>
</html>