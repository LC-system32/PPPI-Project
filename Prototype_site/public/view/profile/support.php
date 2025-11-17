<div class="card p-4 rounded-4 shadow-sm">
    <div class="mb-4">
        <p class="text-uppercase text-muted small mb-1">Служба підтримки</p>
        <h2 class="h5 mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-life-preserver text-warning"></i> Ми поруч, щоб допомогти
        </h2>
        <p class="text-muted small mb-0">Зверніться до нас будь-яким зручним способом або залиште запит онлайн.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="border rounded-4 p-3 h-100">
                <h6 class="mb-2 d-flex align-items-center gap-2"><i class="bi bi-telephone text-warning"></i> Гаряча лінія</h6>
                <p class="text-muted small mb-1">+380 77 777 777 (безкоштовно)</p>
                <p class="text-muted small mb-0">Графік: 08:00 — 22:00, щодня</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded-4 p-3 h-100">
                <h6 class="mb-2 d-flex align-items-center gap-2"><i class="bi bi-envelope-open text-warning"></i> Email</h6>
                <p class="text-muted small mb-1">support@autoparts.ua</p>
                <p class="text-muted small mb-0">Відповідь протягом 24 годин</p>
            </div>
        </div>
    </div>

    <form class="row g-3" enctype="multipart/form-data">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Тема звернення</label>
            <input type="text" class="form-control rounded-3" placeholder="Наприклад, питання по замовленню #12345">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Email для відповіді</label>
            <input type="email" class="form-control rounded-3" value="<?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Повідомлення</label>
            <textarea class="form-control rounded-3" rows="4" placeholder="Опишіть ситуацію або питання"></textarea>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-warning text-dark rounded-pill">
                <i class="bi bi-send me-2"></i>Відправити запит
            </button>
        </div>
    </form>
</div>
