
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('principalSidebar');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            sidebar.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const classFilter = document.getElementById('classFilter');

    if (!classFilter) return;

    classFilter.addEventListener('change', () => {
        const form = classFilter.closest('form');

        if (!form) return;

        const subject = form.querySelector('[name="subject_id"]');

        if (subject) {
            subject.required = false;
            subject.value = '';
        }

        const period = form.querySelector('[name="period_id"]');

        if (period) {
            period.required = false;
        }

        form.submit();
    });
});

