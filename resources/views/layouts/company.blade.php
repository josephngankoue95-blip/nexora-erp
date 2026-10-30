<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Espace entreprise') - ERP SaaS
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')

</head>


<body>

@php

    $user = auth()->user();

    $company = $user?->company;

    /*
    |--------------------------------------------------------------------------
    | MODULES ACTIFS DE L'ENTREPRISE
    |--------------------------------------------------------------------------
    |
    | Les modules sont chargés par CompanyModuleService
    | via AppServiceProvider.
    |
    */

    $activeModuleSlugs = $activeModuleSlugs ?? [];

@endphp


<div class="company-layout">


    {{-- ==========================================================
        OVERLAY MOBILE
    =========================================================== --}}

    <div
        class="company-sidebar-overlay"
        id="companySidebarOverlay"
    ></div>



    {{-- ==========================================================
        SIDEBAR
    =========================================================== --}}

    <aside
        class="company-sidebar"
        id="companySidebar"
    >


        {{-- ======================================================
            FERMETURE MOBILE
        ======================================================= --}}

        <button
            type="button"
            class="company-sidebar-close"
            id="companySidebarClose"
            aria-label="Fermer le menu"
        >
            ×
        </button>



        {{-- ======================================================
            ENTREPRISE CONNECTÉE
        ======================================================= --}}

        <div class="company-sidebar-company">

            <div class="company-company-avatar">

                @if($company?->logo)

                    <img
                        src="{{ asset('storage/' . $company->logo) }}"
                        alt="Logo {{ $company->name }}"
                        class="company-company-logo"
                    >

                @else

                    {{ strtoupper(
                        substr(
                            $company?->name ?? 'E',
                            0,
                            1
                        )
                    ) }}

                @endif

            </div>


            <div class="company-company-info">

                <strong>
                    {{ $company?->name ?? 'Entreprise' }}
                </strong>

                <span>
                    {{ $user?->roleLabel() ?? 'Utilisateur' }}
                </span>

            </div>

        </div>

        {{-- ======================================================
    CHANGER DE MODULE
======================================================= --}}

@if(Route::has('company.modules.index'))

    <a
        href="{{ route('company.modules.index') }}"
        class="company-module-switcher"
    >

        <span class="company-module-switcher-icon">
            ▦
        </span>

        <span class="company-module-switcher-content">

            <strong>
                Applications
            </strong>

            <small>
                Retour
            </small>

        </span>

    </a>

@endif



        {{-- ======================================================
            NAVIGATION
        ======================================================= --}}

        <nav class="company-navigation">


            {{-- ==================================================
                PRINCIPAL
            =================================================== --}}

            <div class="company-nav-section">

                <div class="company-nav-title">
                    PRINCIPAL
                </div>



                {{-- ==================================================
                    TABLEAU DE BORD
                =================================================== --}}

                @if(Route::has('company.dashboard'))

                    <a
                        href="{{ route('company.dashboard') }}"
                        class="company-nav-link
                        {{ request()->routeIs('company.dashboard') ? 'active' : '' }}"
                    >

                        <span class="company-nav-icon">
                            🏠
                        </span>

                        <span>
                            Tableau de bord
                        </span>

                    </a>

                @endif

                

                {{-- ==================================================
                    CATÉGORIES
                =================================================== --}}

                @if(in_array('categories', $activeModuleSlugs, true))

                    @canPermission('categories.view')

                        @if(Route::has('company.categories.index'))

                            <a
                                href="{{ route('company.categories.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.categories.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    🗂️
                                </span>

                                <span>
                                    Catégories
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif




                {{-- ==================================================
                    PRODUITS
                =================================================== --}}

                @if(in_array('products', $activeModuleSlugs, true))

                    @canPermission('products.view')

                        @if(Route::has('company.products.index'))

                            <a
                                href="{{ route('company.products.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.products.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    📦
                                </span>

                                <span>
                                    Produits
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    PRESTATIONS
                =================================================== --}}

                @if(in_array('services', $activeModuleSlugs, true))

                    @canPermission('services.view')

                        @if(Route::has('company.services.index'))

                            <a
                                href="{{ route('company.services.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.services.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    🔧
                                </span>

                                <span>
                                    Prestations
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    STOCK
                =================================================== --}}

                @if(in_array('stock', $activeModuleSlugs, true))

                    @canPermission('stock.view')

                        @if(Route::has('company.stock.index'))

                            <a
                                href="{{ route('company.stock.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.stock.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    🏬
                                </span>

                                <span>
                                    Stock
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    ENTRÉES STOCK
                =================================================== --}}

                @if(in_array('stock', $activeModuleSlugs, true))

                    @canPermission('stock.entry')

                        @if(Route::has('company.stock.entries'))

                            <a
                                href="{{ route('company.stock.entries') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.stock.entries') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    ⬇️
                                </span>

                                <span>
                                    Entrées
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    SORTIES STOCK
                =================================================== --}}

                @if(in_array('stock', $activeModuleSlugs, true))

                    @canPermission('stock.exit')

                        @if(Route::has('company.stock.exits'))

                            <a
                                href="{{ route('company.stock.exits') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.stock.exits') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    ⬆️
                                </span>

                                <span>
                                    Sorties
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    CLIENTS
                =================================================== --}}

                @if(in_array('clients', $activeModuleSlugs, true))

                    @canPermission('clients.view')

                        @if(Route::has('company.clients.index'))

                            <a
                                href="{{ route('company.clients.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.clients.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    👥
                                </span>

                                <span>
                                    Clients
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    VENTES
                =================================================== --}}

                @if(in_array('sales', $activeModuleSlugs, true))

                    @canPermission('sales.view')

                        @if(Route::has('company.sales.index'))

                            <a
                                href="{{ route('company.sales.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.sales.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    🛒
                                </span>

                                <span>
                                    Ventes
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif

                


                {{-- ==================================================
                    ACHATS
                =================================================== --}}

                @if(in_array('purchases', $activeModuleSlugs, true))

                    @canPermission('purchases.view')

                        @if(Route::has('company.purchases.index'))

                            <a
                                href="{{ route('company.purchases.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.purchases.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    🛍️
                                </span>

                                <span>
                                    Achats
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    FOURNISSEURS
                =================================================== --}}

                @if(in_array('suppliers', $activeModuleSlugs, true))

                    @canPermission('suppliers.view')

                        @if(Route::has('company.suppliers.index'))

                            <a
                                href="{{ route('company.suppliers.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.suppliers.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    🚚
                                </span>

                                <span>
                                    Fournisseurs
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                <!-- {{-- ==================================================
                    PAIEMENTS
                =================================================== --}}

                @if(in_array('payments', $activeModuleSlugs, true))

                    @canPermission('payments.view')

                        @if(Route::has('company.settings.payments'))

                            <a
                                href="{{ route('company.settings.payments') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.settings.payments') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    💳
                                </span>

                                <span>
                                    Paiements
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif -->



                {{-- ==================================================
                    FINANCES
                =================================================== --}}

                @if(in_array('finance', $activeModuleSlugs, true))

                    @canPermission('finance.view')

                        @if(Route::has('company.finance.index'))

                            <a
                                href="{{ route('company.finance.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.finance.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    💰
                                </span>

                                <span>
                                    Finances
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    RAPPORTS
                =================================================== --}}

                @if(in_array('reports', $activeModuleSlugs, true))

                    @canPermission('reports.view')

                        @if(Route::has('company.reports.index'))

                            <a
                                href="{{ route('company.reports.index') }}"
                                class="company-nav-link
                                {{ request()->routeIs('company.reports.*') ? 'active' : '' }}"
                            >

                                <span class="company-nav-icon">
                                    📊
                                </span>

                                <span>
                                    Rapports
                                </span>

                            </a>

                        @endif

                    @endcanPermission

                @endif



                {{-- ==================================================
                    UTILISATEURS
                =================================================== --}}

                @canPermission('users.view')

                    @if(Route::has('company.users.index'))

                        <a
                            href="{{ route('company.users.index') }}"
                            class="company-nav-link
                            {{ request()->routeIs('company.users.*') ? 'active' : '' }}"
                        >

                            <span class="company-nav-icon">
                                👤
                            </span>

                            <span>
                                Utilisateurs
                            </span>

                        </a>

                    @endif

                @endcanPermission



                {{-- ==================================================
                    PARAMÈTRES
                =================================================== --}}

                @canPermission('settings.view')

                    @if(Route::has('company.settings.index'))

                        <a
                            href="{{ route('company.settings.index') }}"
                            class="company-nav-link
                            {{ request()->routeIs('company.settings.*') ? 'active' : '' }}"
                        >

                            <span class="company-nav-icon">
                                ⚙️
                            </span>

                            <span>
                                Paramètres
                            </span>

                        </a>

                    @endif

                @endcanPermission


            </div>

        </nav>



        {{-- ======================================================
            BAS SIDEBAR
        ======================================================= --}}

        <div class="company-sidebar-footer">


            {{-- ==================================================
                UTILISATEUR CONNECTÉ
            =================================================== --}}

            <div class="company-user-mini">

                <div class="company-user-avatar">

                    {{ strtoupper(
                        substr(
                            $user?->name ?? 'U',
                            0,
                            1
                        )
                    ) }}

                </div>


                <div class="company-user-info">

                    <strong>
                        {{ $user?->name }}
                    </strong>

                    <span>
                        {{ $user?->email }}
                    </span>

                </div>

            </div>



            


        </div>

    </aside>



    {{-- ==========================================================
        CONTENU PRINCIPAL
    =========================================================== --}}

    <div class="company-main">


        {{-- ======================================================
            TOPBAR
        ======================================================= --}}

        <header class="company-topbar">


            <div class="company-topbar-left">


                {{-- ==================================================
                    HAMBURGER MOBILE
                =================================================== --}}

                <button
                    type="button"
                    class="company-menu-toggle"
                    id="companyMenuToggle"
                    aria-label="Ouvrir le menu"
                >
                    ☰
                </button>



                {{-- ==================================================
                    TITRE DE LA PAGE
                =================================================== --}}

                <div class="company-page-heading">

                    <h1>
                        @yield('page-title', 'Tableau de bord')
                    </h1>


                    @hasSection('page-description')

                        <p>
                            @yield('page-description')
                        </p>

                    @endif

                </div>

            </div>



            {{-- ==================================================
                PROFIL UTILISATEUR
            =================================================== --}}

            <div class="company-topbar-user">

                <div class="company-topbar-avatar">

                    {{ strtoupper(
                        substr(
                            $user?->name ?? 'U',
                            0,
                            1
                        )
                    ) }}

                </div>


                <div class="company-topbar-user-info">

                    <strong>
                        {{ $user?->name }}
                    </strong>

                    <span>
                        {{ $user?->roleLabel() ?? 'Utilisateur' }}
                    </span>

                </div>

            </div>

        </header>



        {{-- ======================================================
            ALERTES + CONTENU
        ======================================================= --}}

        <main class="company-content">


            {{-- ==================================================
                MESSAGE SUCCESS
            =================================================== --}}

            @if(session('success'))

                <div class="company-alert company-alert-success">

                    <span>
                        ✓
                    </span>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            @endif



            {{-- ==================================================
                MESSAGE ERROR
            =================================================== --}}

            @if(session('error'))

                <div class="company-alert company-alert-error">

                    <span>
                        !
                    </span>

                    <span>
                        {{ session('error') }}
                    </span>

                </div>

            @endif



            {{-- ==================================================
                ERREURS DE VALIDATION
            =================================================== --}}

            @if($errors->any())

                <div class="company-alert company-alert-error">

                    <div>

                        <strong>
                            Une erreur est survenue.
                        </strong>

                        <ul>

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            @endif



            {{-- ==================================================
                CONTENU DE LA PAGE
            =================================================== --}}

            @yield('content')


        </main>

    </div>

</div>



{{-- ============================================================
    JAVASCRIPT SIDEBAR
============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    const sidebar = document.getElementById(
        'companySidebar'
    );


    const overlay = document.getElementById(
        'companySidebarOverlay'
    );


    const menuToggle = document.getElementById(
        'companyMenuToggle'
    );


    const sidebarClose = document.getElementById(
        'companySidebarClose'
    );



    /*
    |--------------------------------------------------------------------------
    | OUVRIR SIDEBAR
    |--------------------------------------------------------------------------
    */

    if (
        menuToggle
        && sidebar
        && overlay
    ) {

        menuToggle.addEventListener(
            'click',
            function () {

                sidebar.classList.add('open');

                overlay.classList.add('show');

                document.body.classList.add(
                    'company-sidebar-open'
                );

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | FERMER SIDEBAR
    |--------------------------------------------------------------------------
    */

    function closeSidebar() {

        if (sidebar) {

            sidebar.classList.remove('open');

        }


        if (overlay) {

            overlay.classList.remove('show');

        }


        document.body.classList.remove(
            'company-sidebar-open'
        );

    }



    /*
    |--------------------------------------------------------------------------
    | BOUTON FERMER
    |--------------------------------------------------------------------------
    */

    if (sidebarClose) {

        sidebarClose.addEventListener(
            'click',
            closeSidebar
        );

    }



    /*
    |--------------------------------------------------------------------------
    | OVERLAY
    |--------------------------------------------------------------------------
    */

    if (overlay) {

        overlay.addEventListener(
            'click',
            closeSidebar
        );

    }



    /*
    |--------------------------------------------------------------------------
    | FERMER APRÈS CLIC SUR UN LIEN MOBILE
    |--------------------------------------------------------------------------
    */

    const navigationLinks =
        document.querySelectorAll(
            '.company-nav-link'
        );


    navigationLinks.forEach(
        function (link) {

            link.addEventListener(
                'click',
                function () {

                    if (
                        window.innerWidth <= 991
                    ) {

                        closeSidebar();

                    }

                }
            );

        }
    );



    /*
    |--------------------------------------------------------------------------
    | FERMER AVANT RETOUR DESKTOP
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'resize',
        function () {

            if (
                window.innerWidth > 991
            ) {

                closeSidebar();

            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | ESCAPE POUR FERMER
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
            ) {

                closeSidebar();

            }

        }
    );


});

</script>


@stack('scripts')

</body>

</html>