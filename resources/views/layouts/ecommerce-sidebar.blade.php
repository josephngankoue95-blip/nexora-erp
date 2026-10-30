<aside
    class="ecommerce-sidebar"
    id="ecommerceSidebar"
>

    {{-- ============================================================
         BRAND
    ============================================================= --}}

    <div class="ecommerce-sidebar-header">

        <a
            href="{{ Route::has('company.ecommerce.index')
                ? route('company.ecommerce.index')
                : '#' }}"
            class="ecommerce-brand"
        >

            <div class="ecommerce-brand-icon">
                🛍️
            </div>

            <div class="ecommerce-brand-text">

                <div class="ecommerce-brand-title">
                    E-commerce
                </div>

                <div class="ecommerce-brand-subtitle">
                    Boutique en ligne
                </div>

            </div>

        </a>

    </div>


    {{-- ============================================================
         MENU
    ============================================================= --}}

    <div class="ecommerce-sidebar-content">

        {{-- PRINCIPAL --}}
        <div class="ecommerce-menu-section">

            <div class="ecommerce-menu-title">
                Principal
            </div>

            <nav class="ecommerce-menu">

                @if(Route::has('company.ecommerce.index'))

                    <a
                        href="{{ route('company.ecommerce.index') }}"
                        class="ecommerce-menu-link
                            {{ request()->routeIs('company.ecommerce.index')
                                ? 'active'
                                : '' }}"
                    >

                        <span class="ecommerce-menu-icon">
                            📊
                        </span>

                        <span class="ecommerce-menu-label">
                            Tableau de bord
                        </span>

                    </a>

                @endif

            </nav>

        </div>


        {{-- CATALOGUE --}}
        <div class="ecommerce-menu-section">

            <div class="ecommerce-menu-title">
                Catalogue
            </div>

            <nav class="ecommerce-menu">

                @if(
                    Route::has('company.ecommerce.products.index') &&
                    auth()->user()->hasPermission('ecommerce.view')
                )

                    <a
                        href="{{ route('company.ecommerce.products.index') }}"
                        class="ecommerce-menu-link
                            {{ request()->routeIs('company.ecommerce.products.*')
                                ? 'active'
                                : '' }}"
                    >

                        <span class="ecommerce-menu-icon">
                            📦
                        </span>

                        <span class="ecommerce-menu-label">
                            Produits
                        </span>

                    </a>

                @endif


                {{-- Catégories : future fonctionnalité --}}
               @if(
                    auth()->user()->hasPermission('ecommerce.view') &&
                    Route::has('company.ecommerce.categories.index')
                )

                    <a
                        href="{{ route('company.ecommerce.categories.index') }}"
                        class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.categories.*') ? 'active' : '' }}"
                    >
                        <span class="ecommerce-menu-icon">📂</span>
                        <span>Catégories</span>
                    </a>

                @endif

                {{-- Collections : future fonctionnalité --}}
                
                @if(
                    Route::has('company.ecommerce.collections.index')
                    && auth()->user()->hasPermission('ecommerce.view')
                )
                    <a
                        href="{{ route('company.ecommerce.collections.index') }}"
                        class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.collections.*') ? 'active' : '' }}"
                    >
                        <span class="ecommerce-menu-icon">
                            <i class="fas fa-layer-group"></i>
                        </span>

                        <span>Collections</span>
                    </a>
                @endif


            </nav>

        </div>


        {{-- COMMANDES --}}
        <div class="ecommerce-menu-section">

            <div class="ecommerce-menu-title">
                Ventes en ligne
            </div>

            <nav class="ecommerce-menu">

              @if(
                    Route::has('company.ecommerce.orders.index') &&
                    auth()->user()->hasPermission('ecommerce.view')
                )

                    <a
                        href="{{ route('company.ecommerce.orders.index') }}"
                        class="ecommerce-menu-link
                            {{ request()->routeIs('company.ecommerce.orders.*')
                                ? 'active'
                                : '' }}"
                    >

                        <span class="ecommerce-menu-icon">
                            🛒
                        </span>

                        <span class="ecommerce-menu-label">
                            Commandes
                        </span>

                    </a>

                @endif

                @if(
                    Route::has('company.ecommerce.orders.index') &&
                    auth()->user()->hasPermission('ecommerce.view')
                )

                    <a
                        href="{{ route('company.ecommerce.clients.index') }}"
                        class="ecommerce-menu-link
                            {{ request()->routeIs('company.ecommerce.clients.*')
                                ? 'active'
                                : '' }}"
                    >

                        <span class="ecommerce-menu-icon">
                            👥
                        </span>

                        <span class="ecommerce-menu-label">
                            Clients
                        </span>

                    </a>

                @endif



                @if(
                    auth()->user()->hasPermission('ecommerce.view') &&
                    Route::has('company.ecommerce.payments.index')
                )

                    <a
                        href="{{ route('company.ecommerce.payments.index') }}"
                        class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.payments.*') ? 'active' : '' }}"
                    >
                        <span class="ecommerce-menu-icon">💳</span>
                        <span>Paiements</span>
                    </a>

                @endif

            </nav>

        </div>


        {{-- LOGISTIQUE --}}
        <div class="ecommerce-menu-section">

            <div class="ecommerce-menu-title">
                Logistique
            </div>

            <nav class="ecommerce-menu">

                @if(
                    auth()->user()->hasPermission('ecommerce.view') &&
                    Route::has('company.ecommerce.deliveries.index')
                )

                    <a
                        href="{{ route('company.ecommerce.deliveries.index') }}"
                        class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.deliveries.*') ? 'active' : '' }}"
                    >
                        <span class="ecommerce-menu-icon">🚚</span>
                        <span>Livraison</span>
                    </a>

                @endif




            </nav>

        </div>


        {{-- MARKETING --}}
        <div class="ecommerce-menu-section">

            <div class="ecommerce-menu-title">
                Marketing
            </div>

            <nav class="ecommerce-menu">

                @if(
                    auth()->user()->hasPermission('ecommerce.view') &&
                    Route::has('company.ecommerce.promotions.index')
                )

                    <a
                        href="{{ route('company.ecommerce.promotions.index') }}"
                        class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.promotions.*') ? 'active' : '' }}"
                    >
                        <span class="ecommerce-menu-icon">🏷️</span>
                        <span>Promotions</span>
                    </a>

                @endif


                @if(
                    auth()->user()->hasPermission('ecommerce.view') &&
                    Route::has('company.ecommerce.reviews.index')
                )

                    <a
                        href="{{ route('company.ecommerce.reviews.index') }}"
                        class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.reviews.*') ? 'active' : '' }}"
                    >
                        <span class="ecommerce-menu-icon">⭐</span>
                        <span>Avis Clients</span>
                    </a>

                @endif

            </nav>

        </div>


        {{-- CONFIGURATION --}}
        <div class="ecommerce-menu-section">

            <div class="ecommerce-menu-title">
                Configuration
            </div>

            <nav class="ecommerce-menu">


            @if(
                auth()->user()->hasPermission('ecommerce.view') &&
                Route::has('company.ecommerce.settings.edit')
            )
            <a
                href="{{ route('company.ecommerce.settings.edit') }}"
                class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.settings.*') ? 'active' : '' }}"
            >
                <span class="ecommerce-menu-icon">⚙️</span>
                <span>Paramètres Boutique</span>
            </a>
            @endif



               
            @if(
                auth()->user()->hasPermission('ecommerce.view') &&
                Route::has('company.ecommerce.reports.index')
            )

                <a
                    href="{{ route('company.ecommerce.reports.index') }}"
                    class="ecommerce-menu-link {{ request()->routeIs('company.ecommerce.reports.*') ? 'active' : '' }}"
                >
                    <span class="ecommerce-menu-icon">📊</span>
                    <span>Rapports</span>
                </a>

            @endif


            </nav>

        </div>

    </div>


    {{-- ============================================================
     FOOTER
============================================================= --}}

<div class="ecommerce-sidebar-footer">

    @if(Route::has('company.modules.index'))

        <a
            href="{{ route('company.modules.index') }}"
            class="ecommerce-back-erp"
        >

            <span>
                ←
            </span>

            <span>
                Retour aux applications
            </span>

        </a>

    @endif

</div>

</aside>