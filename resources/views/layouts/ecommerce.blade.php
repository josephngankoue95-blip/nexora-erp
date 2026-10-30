<!DOCTYPE html>
<html lang="fr">
<head>



    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'E-commerce') — {{ config('app.name', 'ERP SaaS') }}
    </title>

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <style>
        :root {
            --ecom-primary: #315bea;
            --ecom-primary-dark: #2448c9;
            --ecom-primary-soft: #eef2ff;

            --ecom-bg: #f5f7fb;
            --ecom-card: #ffffff;

            --ecom-text: #172033;
            --ecom-muted: #6b7280;

            --ecom-border: #e5e7eb;

            --ecom-success: #16a34a;
            --ecom-warning: #d97706;
            --ecom-danger: #dc2626;

            --ecom-sidebar-width: 260px;
            --ecom-header-height: 70px;

            --ecom-radius: 14px;

            --ecom-shadow:
                0 4px 18px rgba(15, 23, 42, 0.06);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background: var(--ecom-bg);
            color: var(--ecom-text);
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* ============================================================
           APPLICATION
        ============================================================ */

        .ecommerce-app {
            min-height: 100vh;
            display: flex;
        }

        /* ============================================================
           SIDEBAR
        ============================================================ */

        .ecommerce-sidebar {
            position: fixed;
            inset: 0 auto 0 0;

            width: var(--ecom-sidebar-width);

            background: #ffffff;
            border-right: 1px solid var(--ecom-border);

            z-index: 1000;

            display: flex;
            flex-direction: column;

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease;
        }

        .ecommerce-sidebar-header {
            height: var(--ecom-header-height);

            display: flex;
            align-items: center;

            padding: 0 20px;

            border-bottom: 1px solid var(--ecom-border);
        }

        .ecommerce-brand {
            display: flex;
            align-items: center;
            gap: 12px;

            min-width: 0;
        }

        .ecommerce-brand-icon {
            width: 40px;
            height: 40px;

            flex: 0 0 40px;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    var(--ecom-primary),
                    var(--ecom-primary-dark)
                );

            color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 19px;

            box-shadow:
                0 5px 14px rgba(49, 91, 234, 0.25);
        }

        .ecommerce-brand-text {
            min-width: 0;
        }

        .ecommerce-brand-title {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.2;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ecommerce-brand-subtitle {
            margin-top: 3px;

            font-size: 11px;
            color: var(--ecom-muted);

            white-space: nowrap;
        }

        .ecommerce-sidebar-content {
            flex: 1;

            overflow-y: auto;

            padding: 18px 12px;
        }

        .ecommerce-menu-section {
            margin-bottom: 22px;
        }

        .ecommerce-menu-title {
            padding: 0 12px 8px;

            font-size: 10px;
            font-weight: 800;

            text-transform: uppercase;
            letter-spacing: 0.08em;

            color: #9ca3af;
        }

        .ecommerce-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .ecommerce-menu-link {
            min-height: 43px;

            display: flex;
            align-items: center;
            gap: 11px;

            padding: 10px 12px;

            border-radius: 10px;

            color: #4b5563;

            font-size: 13px;
            font-weight: 600;

            transition:
                background 0.18s ease,
                color 0.18s ease,
                transform 0.18s ease;
        }

        .ecommerce-menu-link:hover {
            background: #f3f5fa;
            color: var(--ecom-primary);
        }

        .ecommerce-menu-link.active {
            background: var(--ecom-primary-soft);
            color: var(--ecom-primary);
        }

        .ecommerce-menu-icon {
            width: 20px;
            min-width: 20px;

            text-align: center;

            font-size: 16px;
        }

        .ecommerce-menu-label {
            flex: 1;
        }

        .ecommerce-menu-badge {
            min-width: 20px;
            height: 20px;

            padding: 0 6px;

            border-radius: 999px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            background: #fef3c7;
            color: #92400e;

            font-size: 10px;
            font-weight: 800;
        }

        .ecommerce-sidebar-footer {
            padding: 12px;

            border-top: 1px solid var(--ecom-border);
        }

        .ecommerce-back-erp {
            width: 100%;

            display: flex;
            align-items: center;
            gap: 10px;

            padding: 11px 12px;

            border-radius: 10px;

            background: #f8fafc;
            border: 1px solid var(--ecom-border);

            color: #4b5563;

            font-size: 12px;
            font-weight: 700;

            transition: 0.18s ease;
        }

        .ecommerce-back-erp:hover {
            background: var(--ecom-primary-soft);
            border-color: #c7d2fe;
            color: var(--ecom-primary);
        }

        /* ============================================================
           MAIN
        ============================================================ */

        .ecommerce-main {
            width: calc(100% - var(--ecom-sidebar-width));
            min-height: 100vh;

            margin-left: var(--ecom-sidebar-width);

            display: flex;
            flex-direction: column;
        }

        /* ============================================================
           HEADER
        ============================================================ */

        .ecommerce-header {
            height: var(--ecom-header-height);

            position: sticky;
            top: 0;

            z-index: 900;

            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);

            border-bottom: 1px solid var(--ecom-border);

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 26px;
        }

        .ecommerce-header-left {
            display: flex;
            align-items: center;
            gap: 14px;

            min-width: 0;
        }

        .ecommerce-mobile-toggle {
            display: none;

            width: 40px;
            height: 40px;

            border: 1px solid var(--ecom-border);
            border-radius: 10px;

            background: #ffffff;
            color: #374151;

            align-items: center;
            justify-content: center;

            font-size: 18px;
        }

        .ecommerce-header-title {
            min-width: 0;
        }

        .ecommerce-header-title h1 {
            font-size: 17px;
            font-weight: 800;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ecommerce-header-title p {
            margin-top: 2px;

            font-size: 11px;
            color: var(--ecom-muted);
        }

        .ecommerce-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ecommerce-header-company {
            display: flex;
            align-items: center;
            gap: 9px;

            padding: 7px 10px;

            border: 1px solid var(--ecom-border);
            border-radius: 10px;

            background: #ffffff;
        }

        .ecommerce-company-avatar {
            width: 32px;
            height: 32px;

            border-radius: 9px;

            background: var(--ecom-primary-soft);
            color: var(--ecom-primary);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 14px;
            font-weight: 800;
        }

        .ecommerce-company-info {
            line-height: 1.2;
        }

        .ecommerce-company-name {
            max-width: 170px;

            font-size: 12px;
            font-weight: 800;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ecommerce-company-role {
            margin-top: 3px;

            font-size: 10px;
            color: var(--ecom-muted);
        }

        /* ============================================================
           CONTENT
        ============================================================ */

        .ecommerce-content {
            width: 100%;
            max-width: 1600px;

            margin: 0 auto;

            padding: 28px;
        }

        /* ============================================================
           FLASH MESSAGES
        ============================================================ */

        .ecommerce-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;

            padding: 13px 15px;

            margin-bottom: 20px;

            border-radius: 11px;

            font-size: 13px;
            line-height: 1.5;
        }

        .ecommerce-alert-success {
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .ecommerce-alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .ecommerce-alert-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .ecommerce-alert-close {
            margin-left: auto;

            border: 0;
            background: transparent;

            color: inherit;

            font-size: 18px;
            line-height: 1;
        }

        /* ============================================================
           MOBILE OVERLAY
        ============================================================ */

        .ecommerce-overlay {
            display: none;

            position: fixed;
            inset: 0;

            background: rgba(15, 23, 42, 0.42);

            z-index: 950;
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */

        @media (max-width: 1100px) {

            :root {
                --ecom-sidebar-width: 235px;
            }

            .ecommerce-content {
                padding: 22px;
            }

            .ecommerce-header {
                padding: 0 20px;
            }
        }

        @media (max-width: 850px) {

            .ecommerce-sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .ecommerce-sidebar.open {
                transform: translateX(0);
                box-shadow:
                    10px 0 30px rgba(15, 23, 42, 0.12);
            }

            .ecommerce-main {
                width: 100%;
                margin-left: 0;
            }

            .ecommerce-mobile-toggle {
                display: inline-flex;
            }

            .ecommerce-overlay.active {
                display: block;
            }

            .ecommerce-header {
                padding: 0 16px;
            }

            .ecommerce-header-title p {
                display: none;
            }

            .ecommerce-content {
                padding: 18px 15px;
            }

            .ecommerce-company-info {
                display: none;
            }

            .ecommerce-header-company {
                padding: 6px;
            }
        }

        @media (max-width: 560px) {

            .ecommerce-header {
                height: 62px;
            }

            .ecommerce-header-title h1 {
                font-size: 15px;
            }

            .ecommerce-content {
                padding: 15px 12px;
            }

            .ecommerce-header-company {
                display: none;
            }
        }

        /* ============================================================
           ACCESSIBILITY
        ============================================================ */

        .ecommerce-menu-link:focus-visible,
        .ecommerce-mobile-toggle:focus-visible,
        .ecommerce-back-erp:focus-visible,
        .ecommerce-alert-close:focus-visible {
            outline: 3px solid rgba(49, 91, 234, 0.22);
            outline-offset: 2px;
        }

        /* ============================================================
           PRINT
        ============================================================ */

        @media print {

            .ecommerce-sidebar,
            .ecommerce-header,
            .ecommerce-overlay {
                display: none !important;
            }

            .ecommerce-main {
                width: 100%;
                margin: 0;
            }

            .ecommerce-content {
                padding: 0;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>

<div class="ecommerce-app">

    @include('layouts.ecommerce-sidebar')

    <div
        class="ecommerce-overlay"
        id="ecommerceOverlay"
    ></div>

    <main class="ecommerce-main">

        @include('layouts.ecommerce-header')

        <div class="ecommerce-content">

            @if(session('success'))
                <div class="ecommerce-alert ecommerce-alert-success">
                    <span>✓</span>

                    <div>
                        {{ session('success') }}
                    </div>

                    <button
                        type="button"
                        class="ecommerce-alert-close"
                        onclick="this.parentElement.remove()"
                        aria-label="Fermer"
                    >
                        ×
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="ecommerce-alert ecommerce-alert-error">
                    <span>⚠</span>

                    <div>
                        {{ session('error') }}
                    </div>

                    <button
                        type="button"
                        class="ecommerce-alert-close"
                        onclick="this.parentElement.remove()"
                        aria-label="Fermer"
                    >
                        ×
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="ecommerce-alert ecommerce-alert-error">
                    <span>⚠</span>

                    <div>
                        <strong>
                            Vérifiez les informations saisies.
                        </strong>

                        <ul style="margin: 6px 0 0 18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <button
                        type="button"
                        class="ecommerce-alert-close"
                        onclick="this.parentElement.remove()"
                        aria-label="Fermer"
                    >
                        ×
                    </button>
                </div>
            @endif

            @yield('content')

        </div>

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('ecommerceSidebar');
    const overlay = document.getElementById('ecommerceOverlay');
    const toggle = document.getElementById('ecommerceMobileToggle');

    function openSidebar() {
        if (!sidebar) {
            return;
        }

        sidebar.classList.add('open');

        if (overlay) {
            overlay.classList.add('active');
        }

        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (!sidebar) {
            return;
        }

        sidebar.classList.remove('open');

        if (overlay) {
            overlay.classList.remove('active');
        }

        document.body.style.overflow = '';
    }

    if (toggle) {
        toggle.addEventListener('click', function () {

            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }

        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    document.querySelectorAll(
        '.ecommerce-menu-link'
    ).forEach(function (link) {

        link.addEventListener('click', function () {

            if (window.innerWidth <= 850) {
                closeSidebar();
            }

        });

    });

    window.addEventListener('resize', function () {

        if (window.innerWidth > 850) {
            closeSidebar();
        }

    });

});
</script>


    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

@stack('scripts')

</body>
</html>