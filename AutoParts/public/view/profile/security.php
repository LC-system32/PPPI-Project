<div class="card p-4 rounded-4 shadow-sm h-100 mt-4">

    <!-- Заголовок -->
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <p class="text-uppercase small text-secondary fw-semibold mb-1">Безпека акаунта</p>

            <h2 class="h4 fw-bold mb-1 d-flex gap-2 align-items-center text-dark">
                <i class="bi bi-shield-lock-fill text-warning fs-3"></i>
                Захист акаунта
            </h2>

            <p class="text-muted small">Регулярно оновлюйте пароль для безпеки.</p>
        </div>
    </div>

    <!-- Security Banner -->
    <div class="p-4 rounded-4 bg-dark text-light shadow-sm mb-4 security-banner">
        <div class="d-flex align-items-center mb-3">
            <div class="security-icon d-flex align-items-center justify-content-center me-3">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
            </div>

            <h5 class="fw-bold mb-0">Важливі рекомендації</h5>
        </div>

        <ul class="ps-3 mb-0 small fw-semibold text-white-50">
            <li class="mb-1">Не використовуйте один пароль для різних сервісів.</li>
            <li class="mb-1">Додавайте великі літери, цифри та спецсимволи.</li>
            <li class="mb-1">Не передавайте пароль іншим людям.</li>
        </ul>
    </div>

    <!-- Password Form -->
    <form action="/profile/password" method="POST" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="col-12">
            <label class="form-label fw-semibold">Поточний пароль</label>
            <div class="position-relative">
                <input type="password"
                    id="currentPasswordInput"
                    name="current_password"
                    class="form-control form-control-lg rounded-3 pe-5"
                    required
                    minlength="8">
                <button type="button"
                    class="btn btn-outline-secondary btn-sm position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                    data-target="currentPasswordInput"
                    aria-label="Показати пароль"
                    aria-pressed="false"
                    title="Показати пароль">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Новий пароль</label>
            <div class="position-relative">
                <input type="password"
                    id="newPasswordInput"
                    name="new_password"
                    class="form-control form-control-lg rounded-3 pe-5"
                    placeholder="Мінімум 8 символів"
                    required
                    minlength="8">
                <button type="button"
                    class="btn btn-outline-secondary btn-sm position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                    data-target="newPasswordInput"
                    aria-label="Показати пароль"
                    aria-pressed="false"
                    title="Показати пароль">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Підтвердження пароля</label>
            <div class="position-relative">
                <input type="password"
                    id="confirmPasswordInput"
                    name="confirm_password"
                    class="form-control form-control-lg rounded-3 pe-5"
                    required
                    minlength="8">
                <button type="button"
                    class="btn btn-outline-secondary btn-sm position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                    data-target="confirmPasswordInput"
                    aria-label="Показати пароль"
                    aria-pressed="false"
                    title="Показати пароль">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow">
                <i class="bi bi-save me-2"></i> Зберегти зміни
            </button>
        </div>
    </form>
</div>
<script src="/public/js/security.js"></script>
