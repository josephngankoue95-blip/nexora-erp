
/* ============================================================
   NEXORA ERP SAAS — STOREFRONT
   Navigation responsive / Menu mobile
   JavaScript natif
============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ========================================================
       ELEMENTS DU MENU
    ======================================================== */

    const toggle = document.querySelector('[data-menu-toggle]');
    const nav = document.getElementById('store-nav');

    if (!toggle || !nav) {
        return;
    }

    /* ========================================================
       FERMETURE DU MENU
    ======================================================== */

    function closeMenu() {

        nav.classList.remove('is-open');

        toggle.classList.remove('is-active');

        toggle.setAttribute('aria-expanded', 'false');

        toggle.setAttribute('aria-label', 'Ouvrir le menu');

    }

    /* ========================================================
       OUVERTURE / FERMETURE DU MENU
    ======================================================== */

    toggle.addEventListener('click', function () {

        const isOpen = nav.classList.toggle('is-open');

        toggle.classList.toggle('is-active', isOpen);

        toggle.setAttribute('aria-expanded', String(isOpen));

        toggle.setAttribute(
            'aria-label',
            isOpen ? 'Fermer le menu' : 'Ouvrir le menu'
        );

    });

    /* ========================================================
       FERMER LE MENU APRES UN CLIC SUR UN LIEN
    ======================================================== */

    const navLinks = nav.querySelectorAll('a');

    navLinks.forEach(function (link) {

        link.addEventListener('click', function () {

            closeMenu();

        });

    });

    /* ========================================================
       FERMER LE MENU EN CLIQUANT A L'EXTERIEUR
    ======================================================== */

    document.addEventListener('click', function (event) {

        const clickedInsideNav = nav.contains(event.target);

        const clickedToggle = toggle.contains(event.target);

        if (!clickedInsideNav && !clickedToggle) {

            closeMenu();

        }

    });

    /* ========================================================
       FERMER LE MENU AVEC LA TOUCHE ECHAP
    ======================================================== */

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            closeMenu();

        }

    });

    /* ========================================================
       REINITIALISER LE MENU AU REDIMENSIONNEMENT
    ======================================================== */

    window.addEventListener('resize', function () {

        if (window.innerWidth > 767) {

            closeMenu();

        }

    });

});


/* ============================================================
   NEXORA ERP SAAS — STOREFRONT
   GESTION DES QUANTITES PRODUITS
   JavaScript natif
============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ========================================================
       ELEMENTS DU SELECTEUR DE QUANTITE
    ======================================================== */

    const quantityInput = document.getElementById('cart-quantity');

    const minusButton = document.querySelector('.store-quantity-minus');

    const plusButton = document.querySelector('.store-quantity-plus');

    if (!quantityInput) {
        return;
    }

    /* ========================================================
       RECUPERATION DES LIMITES
    ======================================================== */

    function getQuantityLimits() {

        const min = parseInt(quantityInput.min, 10) || 1;

        const maxValue = parseInt(quantityInput.max, 10);

        const max = Number.isFinite(maxValue)
            ? Math.max(min, maxValue)
            : Infinity;

        return { min, max };
    }

    /* ========================================================
       NORMALISATION DE LA QUANTITE
    ======================================================== */

    function normalizeQuantity(value) {

        const { min, max } = getQuantityLimits();

        const parsedValue = parseInt(value, 10);

        const quantity = Number.isFinite(parsedValue)
            ? parsedValue
            : min;

        return Math.min(max, Math.max(min, quantity));
    }

    /* ========================================================
       METTRE A JOUR LA QUANTITE
    ======================================================== */

    function updateQuantity(value) {

        const quantity = normalizeQuantity(value);

        quantityInput.value = quantity;

        const { min, max } = getQuantityLimits();

        if (minusButton) {
            minusButton.disabled = quantity <= min;
        }

        if (plusButton) {
            plusButton.disabled = quantity >= max;
        }

    }

    /* ========================================================
       DIMINUER LA QUANTITE
    ======================================================== */

    if (minusButton) {

        minusButton.addEventListener('click', function () {

            const current = normalizeQuantity(quantityInput.value);

            updateQuantity(current - 1);

        });

    }

    /* ========================================================
       AUGMENTER LA QUANTITE
    ======================================================== */

    if (plusButton) {

        plusButton.addEventListener('click', function () {

            const current = normalizeQuantity(quantityInput.value);

            updateQuantity(current + 1);

        });

    }

    /* ========================================================
       SAISIE MANUELLE
    ======================================================== */

    quantityInput.addEventListener('change', function () {

        updateQuantity(quantityInput.value);

    });

    quantityInput.addEventListener('blur', function () {

        updateQuantity(quantityInput.value);

    });

    /* ========================================================
       VERIFICATION AVANT ENVOI DU FORMULAIRE
    ======================================================== */

    const cartForm = quantityInput.closest('form');

    if (cartForm) {

        cartForm.addEventListener('submit', function () {

            updateQuantity(quantityInput.value);

        });

    }

    /* ========================================================
       INITIALISATION
    ======================================================== */

    updateQuantity(quantityInput.value);

});

