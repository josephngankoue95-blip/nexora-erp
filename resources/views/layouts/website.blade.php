
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Gestion du site web') - ERP SaaS</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')

    <style>
        /* =====================================================
           WEBSITE ADMIN — VARIABLES
        ===================================================== */

        .website-admin {
            --wa-primary: #4f46e5;
            --wa-primary-dark: #3730a3;
            --wa-primary-light: #eef2ff;
            --wa-text: #172033;
            --wa-muted: #788399;
            --wa-border: #e7eaf1;
            --wa-surface: #ffffff;
            --wa-background: #f6f7fb;
            --wa-sidebar-width: 260px;

            min-height: 100vh;
            color: var(--wa-text);
            background: var(--wa-background);
            font-family: Inter, -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
        }

        .website-admin *,
        .website-admin *::before,
        .website-admin *::after {
            box-sizing: border-box;
        }

        .website-admin a {
            color: inherit;
            text-decoration: none;
        }

        .website-admin button {
            font: inherit;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */

        .wa-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            width: var(--wa-sidebar-width);
            height: 100dvh;
            overflow-y: auto;
            background: var(--wa-surface);
            border-right: 1px solid var(--wa-border);
            transition: transform .25s ease;
        }

        .wa-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 82px;
            padding: 20px;
            border-bottom: 1px solid var(--wa-border);
        }

        .wa-brand-icon {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 43px;
            height: 43px;
            color: white;
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #4338ca);
            border-radius: 13px;
            box-shadow: 0 5px 14px #4f46e52b;
        }

        .wa-brand-copy {
            min-width: 0;
        }

        .wa-brand-copy strong {
            display: block;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .wa-brand-copy span {
            display: block;
            margin-top: 4px;
            color: var(--wa-muted);
            font-size: 11px;
        }

        .wa-company {
            display: flex;
            align-items: center;
            gap: 11px;
            margin: 18px 14px 12px;
            padding: 12px;
            background: #f8f9fc;
            border: 1px solid var(--wa-border);
            border-radius: 13px;
        }

        .wa-company-avatar {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            overflow: hidden;
            color: var(--wa-primary);
            font-size: 17px;
            font-weight: 800;
            background: var(--wa-primary-light);
            border-radius: 11px;
        }

        .wa-company-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .wa-company-info {
            min-width: 0;
        }

        .wa-company-info strong,
        .wa-company-info span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .wa-company-info strong {
            font-size: 12px;
        }

        .wa-company-info span {
            margin-top: 5px;
            color: var(--wa-muted);
            font-size: 11px;
        }

        .wa-back-link {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 14px 20px;
            padding: 11px 12px;
            color: #475569 !important;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--wa-border);
            border-radius: 10px;
            transition: background .2s, color .2s;
        }

        .wa-back-link:hover {
            color: var(--wa-primary) !important;
            background: var(--wa-primary-light);
        }

        .wa-nav {
            flex: 1;
            padding: 0 14px 20px;
        }

        .wa-nav-heading {
            margin: 16px 10px 9px;
            color: #9aa3b5;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.1px;
        }

        .wa-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            margin: 4px 0;
            padding: 10px 12px;
            color: #657086 !important;
            font-size: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: color .2s, background .2s;
        }

        .wa-nav-link:hover {
            color: var(--wa-primary) !important;
            background: #f7f7ff;
        }

        .wa-nav-link.active {
            color: var(--wa-primary) !important;
            background: var(--wa-primary-light);
            font-weight: 750;
        }

        .wa-nav-icon {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 22px;
            font-size: 17px;
        }

        .wa-sidebar-footer {
            padding: 15px;
            border-top: 1px solid var(--wa-border);
        }

        .wa-user {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .wa-user-avatar {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            color: white;
            font-size: 14px;
            font-weight: 800;
            background: linear-gradient(135deg, #64748b, #334155);
            border-radius: 50%;
        }

        .wa-user-info {
            min-width: 0;
        }

        .wa-user-info strong,
        .wa-user-info span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .wa-user-info strong {
            font-size: 12px;
        }

        .wa-user-info span {
            margin-top: 4px;
            color: var(--wa-muted);
            font-size: 10px;
        }

        /* =====================================================
           CONTENU PRINCIPAL
        ===================================================== */

        .wa-main {
            min-height: 100vh;
            margin-left: var(--wa-sidebar-width);
        }

        .wa-topbar {
            position: sticky;
            top: 0;
            z-index: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            min-height: 82px;
            padding: 15px 30px;
            background: rgb(255 255 255 / 94%);
            border-bottom: 1px solid var(--wa-border);
            backdrop-filter: blur(12px);
        }

        .wa-topbar-left {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .wa-heading {
            min-width: 0;
        }

        .wa-heading h1 {
            margin: 0;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -.7px;
        }

        .wa-heading p {
            margin: 5px 0 0;
            color: var(--wa-muted);
            font-size: 12px;
        }

        .wa-topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .wa-preview-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 10px 15px;
            color: var(--wa-primary) !important;
            font-size: 12px;
            font-weight: 700;
            background: white;
            border: 1px solid #dfe3ff;
            border-radius: 10px;
            transition: background .2s;
        }

        .wa-preview-button:hover {
            background: var(--wa-primary-light);
        }

        .wa-topbar-profile {
            display: flex;
            align-items: center;
            gap: 9px;
            padding-left: 14px;
            border-left: 1px solid var(--wa-border);
        }

        .wa-topbar-avatar {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            color: white;
            font-size: 13px;
            font-weight: 800;
            background: var(--wa-primary);
            border-radius: 50%;
        }

        .wa-topbar-profile strong {
            display: block;
            font-size: 12px;
        }

        .wa-topbar-profile span {
            display: block;
            margin-top: 4px;
            color: var(--wa-muted);
            font-size: 10px;
        }

        .wa-menu-toggle,
        .wa-sidebar-close {
            display: none;
            place-items: center;
            width: 40px;
            height: 40px;
            color: var(--wa-text);
            font-size: 22px;
            background: white;
            border: 1px solid var(--wa-border);
            border-radius: 10px;
            cursor: pointer;
        }

        .wa-content {
            width: 100%;
            max-width: 1800px;
            min-width: 0;
            margin: 0 auto;
            padding: 28px 30px 40px;
        }

        /* =====================================================
           ALERTES LARAVEL
        ===================================================== */

        .wa-alert {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            margin-bottom: 20px;
            padding: 14px 16px;
            font-size: 13px;
            line-height: 1.6;
            border: 1px solid transparent;
            border-radius: 11px;
        }

        .wa-alert-success {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .wa-alert-error {
            color: #991b1b;
            background: #fef2f2;
            border-color: #fecaca;
        }

        .wa-alert ul {
            margin: 8px 0 0;
            padding-left: 20px;
        }

        .wa-overlay {
            position: fixed;
            inset: 0;
            z-index: 900;
            display: none;
            background: rgb(15 23 42 / 50%);
            backdrop-filter: blur(2px);
        }

        .wa-overlay.show {
            display: block;
        }

        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1100px) {
            .wa-topbar {
                padding-right: 22px;
                padding-left: 22px;
            }

            .wa-content {
                padding: 24px 22px 35px;
            }
        }

        @media (max-width: 991px) {
            .wa-sidebar {
                transform: translateX(-100%);
            }

            .wa-sidebar.open {
                transform: translateX(0);
            }

            .wa-sidebar-close {
                position: absolute;
                top: 20px;
                right: 14px;
                display: grid;
            }

            .wa-main {
                margin-left: 0;
            }

            .wa-menu-toggle {
                display: grid;
            }

            body.wa-sidebar-open {
                overflow: hidden;
            }
        }

        @media (max-width: 600px) {
            .wa-topbar {
                gap: 10px;
                min-height: 72px;
                padding: 12px 14px;
            }

            .wa-heading h1 {
                font-size: 17px;
            }

            .wa-heading p {
                max-width: 210px;
                font-size: 11px;
                line-height: 1.4;
            }

            .wa-topbar-profile {
                padding-left: 0;
                border-left: 0;
            }

            .wa-topbar-profile-info {
                display: none;
            }

            .wa-preview-button {
                min-height: 37px;
                padding: 8px 10px;
                font-size: 11px;
            }

            .wa-content {
                padding: 19px 14px 28px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .website-admin *,
            .website-admin *::before,
            .website-admin *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

@php
    $waUser = auth()->user();
    $waCompany = $waUser?->company;

    $waInitial = strtoupper(
        substr($waUser?->name ?? 'U', 0, 1)
    );

    $waCompanyInitial = strtoupper(
        substr($waCompany?->name ?? 'E', 0, 1)
    );
@endphp

<div class="website-admin">

    <div class="wa-overlay" id="waOverlay"></div>

    {{-- SIDEBAR SPÉCIFIQUE AU MODULE SITE WEB --}}
    <aside class="wa-sidebar" id="waSidebar">

        <button
            type="button"
            class="wa-sidebar-close"
            id="waSidebarClose"
            aria-label="Fermer le menu"
        >
            &times;
        </button>

        <div class="wa-brand">
            <div class="wa-brand-icon">W</div>

            <div class="wa-brand-copy">
                <strong>WebStudio</strong>
                <span>Gestion du site internet</span>
            </div>
        </div>

        <div class="wa-company">
            <div class="wa-company-avatar">
                @if($waCompany?->logo)
                    <img
                        src="{{ asset('storage/' . $waCompany->logo) }}"
                        alt="Logo de l'entreprise"
                    >
                @else
                    {{ $waCompanyInitial }}
                @endif
            </div>

            <div class="wa-company-info">
                <strong>{{ $waCompany?->name ?? 'Entreprise' }}</strong>
                <span>Espace professionnel</span>
            </div>
        </div>

        @if(Route::has('company.modules.index'))
            <a
                href="{{ route('company.modules.index') }}"
                class="wa-back-link"
            >
                <span>←</span>
                <span>Retour aux applications ERP</span>
            </a>
        @endif

        <nav class="wa-nav" aria-label="Navigation du site web">

            <div class="wa-nav-heading">GESTION DU SITE</div>

            @if(Route::has('company.website.index'))
                <a
                    href="{{ route('company.website.index') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.index') ? 'active' : '' }}"
                    @if(request()->routeIs('company.website.index')) aria-current="page" @endif
                >
                    <span class="wa-nav-icon">▦</span>
                    <span>Vue d'ensemble</span>
                </a>
            @endif


            @if(Route::has('company.website.media.index'))
                <a
                    href="{{ route('company.website.media.index') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.media.*') ? 'active' : '' }}"
                >
                    <span class="wa-nav-icon">🖼️</span>
                    <span>Médiathèque</span>
                </a>
            @endif
            

            @if(Route::has('company.website.templates.index'))

                <a
                    href="{{ route('company.website.templates.index') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.templates.*') ? 'active' : '' }}"
                    @if(request()->routeIs('company.website.templates.*'))
                        aria-current="page"
                    @endif
                >
                    <span class="wa-nav-icon">🎨</span>
                    <span>Modèles de sites</span>
                </a>

            @endif


            @if(Route::has('company.website.pages.index'))
                <a
                    href="{{ route('company.website.pages.index') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.pages.*') ? 'active' : '' }}"
                >
                    <span class="wa-nav-icon">📄</span>
                    <span>Pages</span>
                </a>
            @endif


            @if(Route::has('company.website.articles.index'))
                <a
                    href="{{ route('company.website.articles.index') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.articles.*') ? 'active' : '' }}"
                >
                    <span class="wa-nav-icon">✍️</span>
                    <span>Articles et actualités</span>
                </a>
            @endif


            <!-- @if(Route::has('company.website.menus.index'))
                <a
                    href="{{ route('company.website.menus.index') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.menus.*') ? 'active' : '' }}"
                >
                    <span class="wa-nav-icon">☷</span>
                    <span>Menus de navigation</span>
                </a>
            @endif -->

            <!-- <div class="wa-nav-heading">PERSONNALISATION</div>

            @if(Route::has('company.website.appearance.edit'))
                <a
                    href="{{ route('company.website.appearance.edit') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.appearance.*') ? 'active' : '' }}"
                >
                    <span class="wa-nav-icon">🎨</span>
                    <span>Apparence du site</span>
                </a>
            @endif

            @if(Route::has('company.website.seo.edit'))
                <a
                    href="{{ route('company.website.seo.edit') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.seo.*') ? 'active' : '' }}"
                >
                    <span class="wa-nav-icon">🔎</span>
                    <span>Référencement SEO</span>
                </a>
            @endif -->

            <div class="wa-nav-heading">CONFIGURATION</div>

            @if(Route::has('company.website.settings.edit'))
                <a
                    href="{{ route('company.website.settings.edit') }}"
                    class="wa-nav-link {{ request()->routeIs('company.website.settings.*') ? 'active' : '' }}"
                >
                    <span class="wa-nav-icon">⚙️</span>
                    <span>Paramètres du site</span>
                </a>
            @endif

        </nav>

        <div class="wa-sidebar-footer">
            <div class="wa-user">
                <div class="wa-user-avatar">{{ $waInitial }}</div>

                <div class="wa-user-info">
                    <strong>{{ $waUser?->name ?? 'Utilisateur' }}</strong>
                    <span>{{ $waUser?->roleLabel() ?? 'Utilisateur' }}</span>
                </div>
            </div>
        </div>

    </aside>

    {{-- CONTENU PRINCIPAL --}}
    <div class="wa-main">

        <header class="wa-topbar">

            <div class="wa-topbar-left">

                <button
                    type="button"
                    class="wa-menu-toggle"
                    id="waMenuToggle"
                    aria-label="Ouvrir le menu"
                    aria-controls="waSidebar"
                    aria-expanded="false"
                >
                    ☰
                </button>

                <div class="wa-heading">
                    <h1>@yield('page-title', 'Gestion du site web')</h1>

                    @hasSection('page-description')
                        <p>@yield('page-description')</p>
                    @else
                        <p>Gérez votre présence sur le web.</p>
                    @endif
                </div>

            </div>

            <div class="wa-topbar-actions">

                @hasSection('preview-url')
                    <a
                        href="@yield('preview-url')"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="wa-preview-button"
                    >
                        <span>↗</span>
                        <span>Prévisualiser</span>
                    </a>
                @endif

                <div class="wa-topbar-profile">
                    <div class="wa-topbar-avatar">{{ $waInitial }}</div>

                    <div class="wa-topbar-profile-info">
                        <strong>{{ $waUser?->name ?? 'Utilisateur' }}</strong>
                        <span>{{ $waUser?->roleLabel() ?? 'Utilisateur' }}</span>
                    </div>
                </div>

            </div>

        </header>

        <main class="wa-content">

            @if(session('success'))
                <div class="wa-alert wa-alert-success" role="status">
                    <strong>✓</strong>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="wa-alert wa-alert-error" role="alert">
                    <strong>!</strong>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="wa-alert wa-alert-error" role="alert">
                    <div>
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')

        </main>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('waSidebar');
    const overlay = document.getElementById('waOverlay');
    const openButton = document.getElementById('waMenuToggle');
    const closeButton = document.getElementById('waSidebarClose');

    function openSidebar() {
        sidebar?.classList.add('open');
        overlay?.classList.add('show');
        document.body.classList.add('wa-sidebar-open');
        openButton?.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.classList.remove('wa-sidebar-open');
        openButton?.setAttribute('aria-expanded', 'false');
    }

    openButton?.addEventListener('click', function () {
        if (sidebar?.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    closeButton?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    sidebar?.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 991) {
                closeSidebar();
            }
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) {
            closeSidebar();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
});
</script>

@stack('scripts')

</body>
</html>
