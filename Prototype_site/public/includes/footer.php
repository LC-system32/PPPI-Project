<?php
// If admin layout was opened in navbar, close the grid columns and container BEFORE rendering footer
if (isset($isAdmin) && $isAdmin) {
    // close main, row and container-fluid
    echo "</main></div></div>\n";
}
?>

<footer class="footer bg-dark text-light mt-auto">
    <div class="container">
        <h4 class="fw-bold mb-4 text-warning text-center">AutoParts — сервіс для професіоналів та автолюбителів</h4>

        <div class="row text-start gy-3">
            <div class="col-12 col-md-4">
                <h5 class="fw-semibold mb-3 text-warning">Покупцям</h5>
                <ul class="list-unstyled">
                    <li><a href="/faq" class="footer-link text-light">FAQ</a></li>
                    <li><a href="/returns" class="footer-link text-light">Повернення товару</a></li>
                    <li><a href="/delivery-payment" class="footer-link text-light">Доставка та оплата</a></li>
                    <li><a href="/support" class="footer-link text-light">Підтримка</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-4">
                <h5 class="fw-semibold mb-3 text-warning">Документи</h5>
                <ul class="list-unstyled">
                    <li><a href="/privacy-policy" class="footer-link text-light">Політика конфіденційності</a></li>
                    <li><a href="/about" class="footer-link text-light">Про компанію</a></li>
                    <li><a href="/information" class="footer-link text-light">Корисна інформація</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-4">
                <h5 class="fw-semibold mb-3 text-warning">Контакти</h5>
                <ul class="list-unstyled">
                    <li>Email: <a href="mailto:support@autoparts.ua" class="text-light">support@autoparts.ua</a></li>
                    <li>Телефон: <a href="tel:+380800300200" class="text-light">0 800 300 200</a></li>
                    <li>Адреса: м. Київ, просп. Перемоги, 16</li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider my-2 border-light">

        <p class="mb-1 text-center">&copy; <?= date('Y') ?> <span class="text-warning fw-semibold">AutoParts</span></p>
    </div>
</footer>

<!-- Global confirm modal (used by admin actions) -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Підтвердження</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Ви впевнені, що хочете виконати цю дію?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-danger" id="confirmModalAction">Підтвердити</button>
            </div>
        </div>
    </div>
</div>

<!-- Toasts container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="toasts"></div>
</div>

<script>
// small helper to show Bootstrap toast messages
function showToast(message, title = '', variant = 'bg-dark text-white') {
    const container = document.getElementById('toasts');
    if (!container) return;
    const toastId = 'toast-' + Date.now();
    container.insertAdjacentHTML('beforeend', `
        <div id="${toastId}" class="toast align-items-center ${variant} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body py-2">${title ? '<strong class="me-2">' + title + '</strong>' : ''}${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрити"></button>
            </div>
        </div>
    `);
    const toastEl = document.getElementById(toastId);
    const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 4000 });
    bsToast.show();
}

// Confirm modal helper: attach to links/forms with data-confirm attribute
document.addEventListener('click', function(e) {
    const el = e.target.closest('[data-confirm]');
    if (!el) return;
    e.preventDefault();
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const confirmBtn = document.getElementById('confirmModalAction');
    const action = function() {
        if (el.tagName === 'A') {
            window.location.href = el.href;
        } else if (el.tagName === 'BUTTON' || el.tagName === 'INPUT') {
            el.closest('form')?.submit();
        } else if (el.tagName === 'FORM') {
            el.submit();
        }
        modal.hide();
    };
    // remove previous handlers
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    document.getElementById('confirmModalAction').addEventListener('click', action, { once: true });
    modal.show();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
