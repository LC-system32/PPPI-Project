<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$csrfToken = $_SESSION['csrf_token'] ?? '';
$old = $old ?? [];
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<section class="auth-page min-vh-100 d-flex align-items-center justify-content-center text-white position-relative overflow-hidden">
    <img src="https://abrakadabra.fun/uploads/posts/2022-03/thumbs/1647661616_13-abrakadabra-fun-p-fon-remont-avto-15.jpg"
        alt="Auth Background"
        class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>

    <div class="container position-relative" style="max-width: 420px; z-index: 2;">
        <div class="card bg-transparent border border-light border-opacity-25 rounded-5 shadow-lg backdrop-blur text-center p-4 p-md-5 text-white">

            <?php if (!empty($errors)): ?>
                <div class="message-box message-error mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="message-box message-success mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-center mb-4 gap-2">
                <button id="loginTab" class="btn btn-outline-light rounded-pill fw-semibold px-4 py-2 <?= $activeTab === 'login' ? 'active-tab' : '' ?>">Вхід</button>
                <button id="registerTab" class="btn btn-outline-light rounded-pill fw-semibold px-4 py-2 <?= $activeTab === 'register' ? 'active-tab' : '' ?>">Реєстрація</button>
            </div>

            <form id="loginForm" action="/login" method="POST" class="needs-validation <?= $activeTab === 'login' ? 'show' : 'd-none' ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <h3 class="fw-bold mb-4 text-warning">Увійдіть до акаунту</h3>
                <div class="mb-3 text-start">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control bg-transparent text-white border-white border-opacity-50"
                        value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required placeholder="example@gmail.com">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control bg-transparent text-white border-white border-opacity-50"
                        required minlength="6" placeholder="Ваш пароль">
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-semibold py-2 mt-2">Увійти</button>
            </form>

            <form id="registerForm" action="/register" method="POST" class="needs-validation <?= $activeTab === 'register' ? 'show' : 'd-none' ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <h3 class="fw-bold mb-4 text-warning">Створіть акаунт</h3>
                <div class="mb-3 text-start">
                    <label class="form-label">Логін</label>
                    <input type="text" name="login" class="form-control bg-transparent text-white border-white border-opacity-50"
                        value="<?= htmlspecialchars($old['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required minlength="3" maxlength="32" placeholder="Ваш логін">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control bg-transparent text-white border-white border-opacity-50"
                        value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required placeholder="example@gmail.com">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control bg-transparent text-white border-white border-opacity-50"
                        required minlength="6" placeholder="Щонайменше 6 символів">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Підтвердіть пароль</label>
                    <input type="password" name="confirm_password" class="form-control bg-transparent text-white border-white border-opacity-50"
                        required placeholder="Повторіть пароль">
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-semibold py-2 mt-2">Зареєструватися</button>
            </form>
        </div>
    </div>
</section>

<style>
    .backdrop-blur { backdrop-filter: blur(15px); }
    .active-tab { background-color: #ffc107 !important; color: #000 !important; border: none !important; }
    .btn-outline-light:hover { background: rgba(255, 255, 255, 0.3); }
    .form-control::placeholder { color: rgba(255, 255, 255, 0.6); }
    .form-control:focus { border-color: #ffc107 !important; box-shadow: 0 0 10px rgba(255, 193, 7, 0.3) !important; }
    .message-box { padding: 1rem 1.4rem; border-radius: 14px; color: #fff; font-weight: 500; display: flex; align-items: center; gap: 0.6rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25); backdrop-filter: blur(8px); }
    .message-error { background: linear-gradient(135deg, #e53935, #ef5350); }
    .message-success { background: linear-gradient(135deg, #43a047, #66bb6a); }
</style>

<script>
(() => {
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    function showTab(tab) {
        const isLogin = tab === 'login';
        loginForm.classList.toggle('show', isLogin);
        loginForm.classList.toggle('d-none', !isLogin);
        registerForm.classList.toggle('show', !isLogin);
        registerForm.classList.toggle('d-none', isLogin);
        loginTab.classList.toggle('active-tab', isLogin);
        registerTab.classList.toggle('active-tab', !isLogin);
        localStorage.setItem('activeTab', tab);
    }

    const savedTab = localStorage.getItem('activeTab') || 'login';
    showTab(savedTab);

    loginTab.addEventListener('click', () => showTab('login'));
    registerTab.addEventListener('click', () => showTab('register'));
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
