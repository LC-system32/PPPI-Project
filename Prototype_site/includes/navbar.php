<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="/public/css/navbar.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="/">
            <i class="bi bi-gear-fill me-2 text-warning"></i>
            AutoParts
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarGuest">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarGuest">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item mx-lg-2 mb-2 mb-lg-0">
                    <a href="/" class="nav-link d-flex align-items-center hover-underline">
                        <i class="bi bi-house-door me-1"></i>Головна
                    </a>
                </li>

                <li class="nav-item position-relative mb-2 mb-lg-0" style="margin-left:8px; margin-right:24px;">
                    <a href="/cart" class="nav-link d-flex align-items-center position-relative hover-underline">
                        <i class="bi bi-cart3 me-1"></i>Кошик
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                            0
                        </span>
                    </a>
                </li>

                <li class="nav-item mx-lg-2 mb-2 mb-lg-0">
                    <a href="/auth" class="btn btn-warning text-dark fw-semibold d-flex align-items-center px-3 py-1 rounded-pill shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Увійти
                    </a>
                </li>

                <li class="nav-item dropdown ms-lg-2 z-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-translate me-1 text-warning"></i>UA
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="languageDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="?lang=ua">
                                <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f1fa-1f1e6.svg" alt="UA" class="flag-icon me-2">
                                Українська
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="?lang=en">
                                <img src="https://twemoji.maxcdn.com/v/latest/svg/1f1ec-1f1e7.svg" alt="EN" class="flag-icon me-2">
                                English
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>