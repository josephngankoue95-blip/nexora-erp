<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">


    {{-- =====================================================
         PARAMÈTRES PLATEFORME
    ====================================================== --}}

    @php

        $platformName = \App\Models\PlatformSetting::get(
            'platform_name',
            'ERP SaaS'
        );

        $platformLogo = \App\Models\PlatformSetting::get(
            'platform_logo'
        );

        $platformSlogan = \App\Models\PlatformSetting::get(
            'platform_slogan',
            'Votre gestion simplifiée'
        );

        $favicon = \App\Models\PlatformSetting::get(
            'favicon'
        );

        $primaryColor = \App\Models\PlatformSetting::get(
            'primary_color',
            '#2563eb'
        );

        $secondaryColor = \App\Models\PlatformSetting::get(
            'secondary_color',
            '#111827'
        );

        $sidebarColor = \App\Models\PlatformSetting::get(
            'sidebar_color',
            '#111827'
        );

        $sidebarTextColor = \App\Models\PlatformSetting::get(
            'sidebar_text_color',
            '#ffffff'
        );

    @endphp


    {{-- =====================================================
         COULEURS DYNAMIQUES DE LA PLATEFORME
    ====================================================== --}}

    <style>

        :root {

            --sa-primary: {{ $primaryColor }};

            --sa-secondary: {{ $secondaryColor }};

            --sa-sidebar: {{ $sidebarColor }};

            --sa-sidebar-text: {{ $sidebarTextColor }};

        }

    </style>


    {{-- =====================================================
         FAVICON
    ====================================================== --}}

    @if($favicon)

        <link rel="icon"
              href="{{ asset('storage/' . $favicon) }}">

    @endif


    {{-- =====================================================
         CSS / JS
    ====================================================== --}}

    @vite([
        'resources/css/app.css',
        'resources/css/superadmin-dashboard.css',
        'resources/js/app.js',
        'resources/css/superadmin-users.css',
        'resources/css/superadmin-subscriptions.css',
        'resources/css/superadmin-payments.css',
        'resources/css/superadmin-settings.css'
    ])

    @stack('styles')

</head>


<body>

<div class="app">


    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <aside class="sidebar" id="sidebar">


        {{-- =================================================
             SIDEBAR HEADER
        ================================================== --}}

        <div class="sidebar-header">

            <div class="brand">


                {{-- LOGO --}}

                <div class="brand-logo">

                    @if($platformLogo)

                        <img
                            src="{{ asset('storage/' . $platformLogo) }}"
                            alt="{{ $platformName }}"
                            style="
                                width: 100%;
                                height: 100%;
                                object-fit: contain;
                                border-radius: inherit;
                            "
                        >

                    @else

                        ERP

                    @endif

                </div>


                {{-- NOM PLATEFORME --}}

                <div class="brand-text">

                    <h2>
                        {{ $platformName }}
                    </h2>

                    <span>
                        Super Admin
                    </span>

                </div>

            </div>

        </div>


        {{-- =================================================
             MENU
        ================================================== --}}

        <nav class="sidebar-menu">


            {{-- =================================================
                 PRINCIPAL
            ================================================== --}}

            <div class="menu-title">
                PRINCIPAL
            </div>


            {{-- Dashboard --}}

            <a
                href="{{ route('superadmin.dashboard') }}"
                class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"
            >

                <span class="menu-icon">⌂</span>

                <span>
                    Dashboard
                </span>

            </a>


            {{-- =================================================
                 GESTION
            ================================================== --}}

            <div class="menu-title">
                GESTION
            </div>


            {{-- Entreprises --}}

            <a
                href="{{ route('superadmin.companies.index') }}"
                class="{{ request()->routeIs('superadmin.companies.*') ? 'active' : '' }}"
            >

                <span class="menu-icon">▣</span>

                <span>
                    Entreprises
                </span>

            </a>


            <!-- {{-- Utilisateurs --}}

            <a
                href="{{ route('superadmin.users.index') }}"
                class="{{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}"
            >

                <span class="menu-icon">♙</span>

                <span>
                    Utilisateurs
                </span>

            </a> -->


            {{-- =================================================
                 FINANCES
            ================================================== --}}

            <div class="menu-title">
                FINANCES
            </div>


            {{-- Abonnements --}}

<a
    href="{{ route('superadmin.subscription-plans.index') }}"
    class="{{ request()->routeIs('superadmin.subscription-plans.*') ? 'active' : '' }}"
>
    <span class="menu-icon">P</span>
    <span>Plans d’abonnement</span>
</a>

<a
    href="{{ route('superadmin.subscriptions.index') }}"
    class="{{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}"
>
    <span class="menu-icon">A</span>
    <span>Abonnements</span>
</a>


            {{-- Paiements --}}

            <a
                href="{{ route('superadmin.payments.index') }}"
                class="{{ request()->routeIs('superadmin.payments.*') ? 'active' : '' }}"
            >

                <span class="menu-icon">▰</span>

                <span>
                    Paiements
                </span>

            </a>


            {{-- =================================================
                 SYSTÈME
            ================================================== --}}

            <div class="menu-title">
                SYSTÈME
            </div>


            {{-- Paramètres plateforme --}}

            <a
                href="{{ route('superadmin.settings.index') }}"
                class="{{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}"
            >

                <span class="menu-icon">⚙</span>

                <span>
                    Paramètres
                </span>

            </a>

        </nav>


        {{-- =====================================================
             SIDEBAR FOOTER
        ====================================================== --}}

        <div class="sidebar-footer">


            {{-- UTILISATEUR CONNECTÉ --}}

            <div class="sidebar-user">


                <div class="user-avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'SA',
                            0,
                            1
                        )
                    ) }}

                </div>


                <div class="sidebar-user-info">

                    <strong>

                        {{ auth()->user()->name }}

                    </strong>


                    <span>

                        Super Admin

                    </span>

                </div>

            </div>


            {{-- DÉCONNEXION --}}

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >

                    <span>
                        ⇥
                    </span>

                    Déconnexion

                </button>

            </form>

        </div>

    </aside>


    {{-- =====================================================
         CONTENU PRINCIPAL
    ====================================================== --}}

    <main class="main-content">


        {{-- =================================================
             TOPBAR
        ================================================== --}}

        <header class="topbar">


            <div class="topbar-left">


                {{-- MOBILE MENU --}}

                <button
                    type="button"
                    id="sidebarToggle"
                    class="sidebar-toggle"
                >

                    ☰

                </button>


                <div>

                    <span class="topbar-label">

                        Administration

                    </span>


                    <h3>

                        @yield('title', 'Dashboard')

                    </h3>

                </div>

            </div>


            {{-- =================================================
                 TOPBAR DROITE
            ================================================== --}}

            <div class="topbar-right">


                {{-- NOTIFICATIONS --}}

                <button
                    type="button"
                    class="notification-button"
                >

                    🔔

                    <span class="notification-badge">
                        0
                    </span>

                </button>


                {{-- UTILISATEUR --}}

                <div class="topbar-user">


                    <div class="topbar-avatar">

                        {{ strtoupper(
                            substr(
                                auth()->user()->name ?? 'SA',
                                0,
                                1
                            )
                        ) }}

                    </div>


                    <div class="topbar-user-info">

                        <strong>

                            {{ auth()->user()->name }}

                        </strong>


                        <span>

                            Super Admin

                        </span>

                    </div>

                </div>

            </div>

        </header>


        {{-- =================================================
             CONTENU DES PAGES
        ================================================== --}}

        <section class="content">


            {{-- SUCCESS --}}

            @if (session('success'))

                <div class="alert alert-success">

                    {{ session('success') }}

                </div>

            @endif


            {{-- ERROR --}}

            @if (session('error'))

                <div class="alert alert-error">

                    {{ session('error') }}

                </div>

            @endif


            @yield('content')

        </section>

    </main>

</div>


{{-- =====================================================
     OVERLAY MOBILE
====================================================== --}}

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
>
</div>


{{-- =====================================================
     SIDEBAR JAVASCRIPT
====================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    const toggle = document.getElementById('sidebarToggle');

    const sidebar = document.getElementById('sidebar');

    const overlay = document.getElementById('sidebarOverlay');


    /* =====================================================
       OUVERTURE / FERMETURE MOBILE
    ====================================================== */

    if (toggle && sidebar) {

        toggle.addEventListener('click', function () {

            sidebar.classList.toggle('active');


            if (overlay) {

                overlay.classList.toggle('active');

            }

        });

    }


    /* =====================================================
       OVERLAY
    ====================================================== */

    if (overlay) {

        overlay.addEventListener('click', function () {

            sidebar.classList.remove('active');

            overlay.classList.remove('active');

        });

    }


    /* =====================================================
       FERMER LE MENU APRÈS CLIC SUR UN LIEN
    ====================================================== */

    document
        .querySelectorAll('.sidebar-menu a')
        .forEach(function (link) {


            link.addEventListener('click', function () {


                if (window.innerWidth <= 768) {


                    sidebar.classList.remove('active');


                    if (overlay) {

                        overlay.classList.remove('active');

                    }

                }

            });

        });

});

</script>


@stack('scripts')


</body>

</html>