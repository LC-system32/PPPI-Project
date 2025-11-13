<?php
include 'includes/header.php';
include 'includes/navbar.php';

$activeTab = $_SESSION['activeTab'] ?? 'login';
unset($_SESSION['activeTab']);
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<section class="auth-page min-vh-100 d-flex align-items-center justify-content-center text-white position-relative overflow-hidden">
    <img src="https://abrakadabra.fun/uploads/posts/2022-03/thumbs/1647661616_13-abrakadabra-fun-p-fon-remont-avto-15.jpg"
        alt="Auth Background"
        class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>

    <div class="container position-relative" style="max-width: 420px; z-index: 2;">
        <div class="card bg-transparent border border-light border-opacity-25 rounded-5 shadow-lg backdrop-blur text-center p-4 p-md-5 text-white">

            <?php if ($message): ?>
                <div class="alert alert-success mb-3"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-3">
                    <?php foreach ($errors as $err): ?>
                        <div>⚠️ <?= htmlspecialchars($err) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-center mb-4 gap-2">
                <button id="loginTab" class="btn btn-outline-light rounded-pill fw-semibold px-4 py-2 <?= $activeTab === 'login' ? 'active-tab' : '' ?>">Вхід</button>
                <button id="registerTab" class="btn btn-outline-light rounded-pill fw-semibold px-4 py-2 <?= $activeTab === 'register' ? 'active-tab' : '' ?>">Реєстрація</button>
            </div>

            <form id="loginForm" action="/login" method="POST" class="needs-validation <?= $activeTab === 'login' ? 'show' : 'd-none' ?>">
                <input type="hidden" name="action" value="login">
                <h3 class="fw-bold mb-4 text-warning">Увійти до акаунту</h3>

                <div class="mb-3 text-start">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control bg-transparent text-white border-white border-opacity-50" required placeholder="example@gmail.com">
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control bg-transparent text-white border-white border-opacity-50" required minlength="6" placeholder="Введіть пароль">
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-semibold py-2 mt-2">Увійти</button>
            </form>

            <form id="registerForm" action="/register" method="POST" class="needs-validation <?= $activeTab === 'register' ? 'show' : 'd-none' ?>">
                <input type="hidden" name="action" value="register">
                <h3 class="fw-bold mb-4 text-warning">Створити акаунт</h3>

                <div class="mb-3 text-start">
                    <label class="form-label">Ім’я</label>
                    <input type="text" name="name" class="form-control bg-transparent text-white border-white border-opacity-50" required placeholder="Введіть ім’я">
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control bg-transparent text-white border-white border-opacity-50" required placeholder="example@gmail.com">
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control bg-transparent text-white border-white border-opacity-50" required minlength="6" placeholder="Мінімум 6 символів">
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Підтвердження паролю</label>
                    <input type="password" name="confirm_password" class="form-control bg-transparent text-white border-white border-opacity-50" required placeholder="Повторіть пароль">
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-semibold py-2 mt-2">Зареєструватися</button>
            </form>
        </div>
    </div>
</section>

<style>
    .backdrop-blur {
        backdrop-filter: blur(15px);
    }

    .active-tab {
        background-color: #ffc107 !important;
        color: #000 !important;
        border: none !important;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-control:focus {
        border-color: #ffc107 !important;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.3) !important;
    }
</style>

<script>
    (() => {
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        loginTab.addEventListener('click', () => {
            loginForm.classList.add('show');
            loginForm.classList.remove('d-none');
            registerForm.classList.remove('show');
            registerForm.classList.add('d-none');
            loginTab.classList.add('active-tab');
            registerTab.classList.remove('active-tab');
        });

        registerTab.addEventListener('click', () => {
            registerForm.classList.add('show');
            registerForm.classList.remove('d-none');
            loginForm.classList.remove('show');
            loginForm.classList.add('d-none');
            registerTab.classList.add('active-tab');
            loginTab.classList.remove('active-tab');
        });
    })();
</script>

<?php include 'includes/footer.php'; ?>