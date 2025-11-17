<?php
$regions = [
    'Київська',
    'Львівська',
    'Харківська',
    'Дніпропетровська',
    'Одеська',
];

$citiesByRegion = [
    'Київська' => ['Київ', 'Біла Церква', 'Бориспіль'],
    'Львівська' => ['Львів', 'Дрогобич', 'Стрий'],
    'Харківська' => ['Харків', 'Чугуїв', 'Лозова'],
    'Дніпропетровська' => ['Дніпро', 'Кривий Ріг', 'Павлоград'],
    'Одеська' => ['Одеса', 'Чорноморськ', 'Ізмаїл'],
];

$novaPoshtaByCity = [
    'Київ' => ['Відділення №1', 'Відділення №5', 'Відділення №12', 'Поштомат №103'],
    'Біла Церква' => ['Відділення №3', 'Відділення №8'],
    'Бориспіль' => ['Відділення №2', 'Поштомат №17'],
    'Львів' => ['Відділення №1', 'Відділення №6', 'Поштомат №45'],
    'Дрогобич' => ['Відділення №4'],
    'Стрий' => ['Відділення №2'],
    'Харків' => ['Відділення №9', 'Відділення №22'],
    'Чугуїв' => ['Відділення №1'],
    'Лозова' => ['Відділення №3'],
    'Дніпро' => ['Відділення №1', 'Поштомат №58'],
    'Кривий Ріг' => ['Відділення №7'],
    'Павлоград' => ['Відділення №5'],
    'Одеса' => ['Відділення №3', 'Відділення №15'],
    'Чорноморськ' => ['Відділення №1'],
    'Ізмаїл' => ['Поштомат №11'],
];

$selectedRegion = $formData['region'] ?? $user->region ?? '';
$selectedCity = $formData['city'] ?? $user->city ?? '';
$selectedBranch = $formData['nova_poshta'] ?? $user->nova_poshta ?? '';
?>

<div class="card p-4 rounded-4 shadow-sm h-100">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4 h-100">
        <div>
            <p class="text-uppercase text-muted small mb-1">Профіль</p>
            <h2 class="h5 mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-warning"></i> Особиста інформація
            </h2>
            <p class="text-muted small mb-0">Оновіть контактні дані для кращого сервісу.</p>
        </div>
        <span class="badge bg-light text-dark fw-semibold">
            Дата реєстрації: <?= date('d.m.Y', strtotime($user->created_at ?? 'now')) ?>
        </span>
    </div>

    <form action="/profile/details" method="POST" class="row gx-4 gy-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <!-- ЛОГІН -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Логін</label>
            <input type="text"
                name="login"
                class="form-control rounded-3"
                value="<?= htmlspecialchars($formData['login'] ?? $user->login, ENT_QUOTES, 'UTF-8') ?>"
                required minlength="3">
        </div>

        <!-- EMAIL -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Email</label>
            <input type="email"
                name="email"
                class="form-control rounded-3"
                value="<?= htmlspecialchars($formData['email'] ?? $user->email, ENT_QUOTES, 'UTF-8') ?>"
                required>
        </div>

        <!-- ПІБ -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">ПІБ</label>
            <input type="text"
                name="full_name"
                class="form-control rounded-3"
                value="<?= htmlspecialchars($formData['full_name'] ?? ($user->full_name ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Прізвище Ім’я По батькові">
        </div>

        <!-- ТЕЛЕФОН -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Телефон</label>
            <input type="tel"
                name="phone"
                id="phoneInput"
                class="form-control rounded-3"
                value="<?= htmlspecialchars($formData['phone'] ?? ($user->phone ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                placeholder="+380 __ ___ ____"
                maxlength="17">
        </div>

        <!-- ОБЛАСТЬ -->
        <div class="col-md-4">
            <label class="form-label fw-semibold">Область</label>
            <select class="form-select rounded-3" name="region" id="profileRegionSelect">
                <option value="" disabled <?= empty($selectedRegion) ? 'selected' : '' ?>>Оберіть область</option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= $region ?>" <?= $selectedRegion === $region ? 'selected' : '' ?>>
                        <?= $region ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- МІСТО -->
        <div class="col-md-4">
            <label class="form-label fw-semibold">Місто</label>
            <select class="form-select rounded-3" name="city" id="profileCitySelect" <?= empty($selectedRegion) ? 'disabled' : '' ?>>
                <option value="" disabled <?= empty($selectedCity) ? 'selected' : '' ?>>Оберіть місто</option>
                <?php foreach ($citiesByRegion[$selectedRegion] ?? [] as $city): ?>
                    <option value="<?= $city ?>" <?= $selectedCity === $city ? 'selected' : '' ?>>
                        <?= $city ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- НОВА ПОШТА -->
        <div class="col-md-4">
            <label class="form-label fw-semibold">Відділення Нової пошти</label>
            <select class="form-select rounded-3" name="nova_poshta" id="profileNovaSelect" <?= empty($selectedCity) ? 'disabled' : '' ?>>
                <option value="" disabled <?= empty($selectedBranch) ? 'selected' : '' ?>>Оберіть відділення</option>
                <?php foreach ($novaPoshtaByCity[$selectedCity] ?? [] as $branch): ?>
                    <option value="<?= $branch ?>" <?= $selectedBranch === $branch ? 'selected' : '' ?>>
                        <?= $branch ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Зберегти -->
        <div class="col-12 d-flex justify-content-between flex-wrap gap-3 mt-4">
            <button type="button" class="btn btn-danger rounded-pill px-4">
                <i class="bi bi-trash me-2"></i>Видалити акаунт
            </button>
            <button class="btn btn-warning text-dark fw-semibold rounded-pill px-4">
                <i class="bi bi-save me-2"></i> Зберегти зміни
            </button>
        </div>

    </form>
</div>
<div id="profileData"
    data-cities='<?= htmlspecialchars(json_encode($citiesByRegion, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'
    data-branches='<?= htmlspecialchars(json_encode($novaPoshtaByCity, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'
    class="d-none"></div>
<script src="/public/js/profile-info.js"></script>