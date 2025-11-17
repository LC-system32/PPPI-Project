<?php
// Фейковий список авто
$vehicles = $savedVehicles ?? [
    ['brand' => 'BMW', 'model' => '530d xDrive', 'year' => 2022, 'vin' => 'WBAJA91040XXXXX', 'mileage' => '68 000 км'],
    ['brand' => 'Audi', 'model' => 'Q5 45 TFSI', 'year' => 2021, 'vin' => 'WAUZZZFY0PXXXXXX', 'mileage' => '52 500 км'],
    ['brand' => 'Toyota', 'model' => 'Camry Hybrid', 'year' => 2020, 'vin' => 'JTNB23HK500XXXXX', 'mileage' => '72 300 км'],
    ['brand' => 'Mercedes-Benz', 'model' => 'GLC 220d 4MATIC', 'year' => 2023, 'vin' => 'W1N2539051FXXXXX', 'mileage' => '28 450 км'],
    ['brand' => 'Volkswagen', 'model' => 'Passat B8 2.0 TDI', 'year' => 2019, 'vin' => 'WVWZZZ3CZKE0XXXXX', 'mileage' => '154 900 км'],
    ['brand' => 'Mazda', 'model' => 'CX-5 AWD', 'year' => 2021, 'vin' => 'JMZKF2WLA00XXXXX', 'mileage' => '41 800 км'],
    ['brand' => 'Skoda', 'model' => 'Superb 2.0 TSI', 'year' => 2020, 'vin' => 'TMBJH9NP0L0XXXXX', 'mileage' => '89 200 км'],
    ['brand' => 'Honda', 'model' => 'CR-V AWD', 'year' => 2018, 'vin' => 'JHLRW2840JCXXXXX', 'mileage' => '121 330 км']
];

// === ПАГІНАЦІЯ ===
$perPage = 4;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$totalVehicles = count($vehicles);
$totalPages = ceil($totalVehicles / $perPage);

$start = ($page - 1) * $perPage;
$vehiclesPage = array_slice($vehicles, $start, $perPage);
?>
<div class="card p-4 rounded-4 shadow-sm">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <p class="text-uppercase text-muted small mb-1">Автопарк</p>
            <h2 class="h5 mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-car-front text-warning"></i> Збережені авто
            </h2>
            <p class="text-muted small mb-0">Додавайте транспортні засоби, щоб швидко знаходити сумісні запчастини.</p>
        </div>
        <button class="btn btn-warning text-dark rounded-pill">
            <i class="bi bi-plus-circle me-2"></i>Додати авто
        </button>
    </div>

    <?php if (!empty($vehiclesPage)): ?>
        <div class="row g-3">
            <?php foreach ($vehiclesPage as $vehicle): ?>
                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="h6 mb-0">
                                    <?= htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model'], ENT_QUOTES, 'UTF-8') ?>
                                </h5>
                                <span class="badge bg-light text-dark">
                                    <?= htmlspecialchars($vehicle['year']) ?>
                                </span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger rounded-pill">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <p class="text-muted small mb-1">
                            <i class="bi bi-three-dots me-2 text-warning"></i>
                            VIN: <?= htmlspecialchars($vehicle['vin']) ?>
                        </p>

                        <p class="text-muted small mb-0">
                            <i class="bi bi-speedometer2 me-2 text-warning"></i>
                            Пробіг: <?= htmlspecialchars($vehicle['mileage']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination pagination-sm justify-content-center gap-1 ">

                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link border-0 bg-white text-dark rounded-pill px-3 py-2" href="?page=<?= $page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link border-0 rounded-pill px-3 py-2 
                        <?= $i == $page
                            ? 'bg-white text-warning border border-warning'
                            : 'bg-white text-dark' ?>"
                            href="?page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link border-0 bg-white text-dark rounded-pill px-3 py-2" href="?page=<?= $page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>

            </ul>
        </nav>
    <?php endif; ?>