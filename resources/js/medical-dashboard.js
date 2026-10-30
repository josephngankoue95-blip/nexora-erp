document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('medicalMenuToggle');
    const sidebar = document.getElementById('medicalSidebar');

    if (!toggle || !sidebar) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('open');

        toggle.setAttribute(
            'aria-expanded',
            String(isOpen)
        );
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('medicalSidebar');
    const toggle = document.getElementById('medicalMenuToggle');
    const overlay = document.getElementById('medicalOverlay');

    if (!sidebar || !toggle || !overlay) {
        return;
    }

    const closeSidebar = () => {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-visible');

        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Ouvrir le menu');

        document.body.classList.remove('medical-menu-open');
    };

    const openSidebar = () => {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-visible');

        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Fermer le menu');

        document.body.classList.add('medical-menu-open');
    };

    toggle.addEventListener('click', () => {
        if (sidebar.classList.contains('is-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 800) {
            closeSidebar();
        }
    });
});