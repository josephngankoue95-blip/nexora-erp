
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $storeName = $settings->store_name ?: $company->name;

        $storeDescription = $settings->description
            ?: 'Découvrez notre boutique en ligne.';

        $storeRoute = [
            'company' => $company->slug
        ];
    @endphp

    <meta name="description"
          content="@yield('meta_description', $storeDescription)">

    <meta name="theme-color" content="#2563eb">

    <title>@yield('title', $storeName)</title>

    @vite([
        'resources/css/storefront.css',
        'resources/js/storefront.js'
    ])

    @stack('styles')
</head>

<body class="store-body">

    {{-- =========================================
        TOP BAR
    ========================================= --}}
    <div class="store-topbar">
        <div class="store-container store-topbar-inner">

            <div class="topbar-message">
                <span class="topbar-dot"></span>
                Bienvenue dans notre boutique en ligne
            </div>

            <div class="topbar-contact">

                @if($settings->support_phone)
                    <a href="tel:{{ preg_replace('/[^+0-9]/', '', $settings->support_phone) }}">
                        {{ $settings->support_phone }}
                    </a>
                @endif

                @if($settings->support_email)
                    <a href="mailto:{{ $settings->support_email }}">
                        {{ $settings->support_email }}
                    </a>
                @endif

            </div>

        </div>
    </div>


    {{-- =========================================
        HEADER PRINCIPAL
    ========================================= --}}
    <header class="store-header">

        <div class="store-container store-header-inner">

            {{-- LOGO / NOM BOUTIQUE --}}
            <a href="{{ route('storefront.index', $storeRoute) }}"
               class="store-brand">

                <span class="store-brand-icon">
                    {{ mb_strtoupper(mb_substr($storeName, 0, 1)) }}
                </span>

                <span class="store-brand-content">
                    <strong>{{ $storeName }}</strong>
                    <small>Boutique officielle</small>
                </span>

            </a>


            {{-- NAVIGATION --}}
            <nav class="store-nav"
                 id="store-nav"
                 aria-label="Navigation principale">

                <a href="{{ route('storefront.index', $storeRoute) }}"
                   @class([
                       'store-nav-link',
                       'active' => request()->routeIs('storefront.index')
                   ])>
                    Accueil
                </a>

                <a href="{{ route('storefront.products.index', $storeRoute) }}"
                   @class([
                       'store-nav-link',
                       'active' => request()->routeIs('storefront.products.*')
                   ])>
                    Produits
                </a>

                <a href="{{ route('storefront.collections.index', $storeRoute) }}"
                   @class([
                       'store-nav-link',
                       'active' => request()->routeIs('storefront.collections.*')
                   ])>
                    Collections
                </a>

            </nav>


            {{-- ACTIONS HEADER --}}
            <div class="store-header-actions">

                <a href="{{ route('storefront.products.index', $storeRoute) }}"
                   class="store-header-shop"
                   aria-label="Découvrir les produits">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                </a>

                {{-- Activer après vérification du nom de route panier --}}
                @if(Route::has('storefront.cart.index'))
                    <a href="{{ route('storefront.cart.index', $storeRoute) }}"
                       class="store-cart-button"
                       aria-label="Voir mon panier">

                        <svg viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             aria-hidden="true">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.5 13h13l2-9H6"></path>
                        </svg>

                        <span>Panier</span>

                    </a>
                @endif

                {{-- MENU MOBILE --}}
                <button class="store-menu-button"
                        type="button"
                        aria-label="Ouvrir le menu"
                        aria-controls="store-nav"
                        aria-expanded="false"
                        data-menu-toggle>

                    <span></span>
                    <span></span>
                    <span></span>

                </button>

            </div>

        </div>

    </header>


    {{-- =========================================
        CONTENU PRINCIPAL
    ========================================= --}}
    <main id="main-content" class="store-main">

        @yield('content')

    </main>


    {{-- =========================================
        FOOTER
    ========================================= --}}
    <footer class="store-footer">

        <div class="store-container">

            <div class="store-footer-grid">

                {{-- PRESENTATION --}}
                <div class="store-footer-about">

                    <div class="store-footer-brand">
                        <span class="store-footer-logo">
                            {{ mb_strtoupper(mb_substr($storeName, 0, 1)) }}
                        </span>

                        <strong>{{ $storeName }}</strong>
                    </div>

                    <p>
                        {{ $storeDescription }}
                    </p>

                    <div class="store-footer-trust">
                        <span>Qualité</span>
                        <span>Confiance</span>
                        <span>Service</span>
                    </div>

                </div>


                {{-- NAVIGATION --}}
                <div class="store-footer-column">

                    <h3>Navigation</h3>

                    <ul>
                        <li>
                            <a href="{{ route('storefront.index', $storeRoute) }}">
                                Accueil
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('storefront.products.index', $storeRoute) }}">
                                Nos produits
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('storefront.collections.index', $storeRoute) }}">
                                Collections
                            </a>
                        </li>
                    </ul>

                </div>


                {{-- INFORMATIONS --}}
                <div class="store-footer-column">

                    <h3>Informations</h3>

                    <p>
                        Découvrez nos produits et profitez
                        d'une expérience d'achat simple
                        et agréable.
                    </p>

                    <p>
                        Notre équipe reste disponible
                        pour vous accompagner.
                    </p>

                </div>


                {{-- CONTACT --}}
                <div class="store-footer-column">

                    <h3>Contactez-nous</h3>

                    @if($settings->support_email)
                        <a class="store-footer-contact"
                           href="mailto:{{ $settings->support_email }}">
                            {{ $settings->support_email }}
                        </a>
                    @endif

                    @if($settings->support_phone)
                        <a class="store-footer-contact"
                           href="tel:{{ preg_replace('/[^+0-9]/', '', $settings->support_phone) }}">
                            {{ $settings->support_phone }}
                        </a>
                    @endif

                    @if(!$settings->support_email && !$settings->support_phone)
                        <p>
                            Bienvenue dans notre boutique.
                        </p>
                    @endif

                </div>

            </div>


            {{-- COPYRIGHT --}}
            <div class="store-footer-bottom">

                <p>
                    &copy; {{ date('Y') }}
                    {{ $storeName }}.
                    Tous droits réservés.
                </p>

                <p>
                    Boutique propulsée par
                    <strong>Nexora</strong>
                </p>

            </div>

        </div>

    </footer>

    @stack('scripts')

</body>
</html>
