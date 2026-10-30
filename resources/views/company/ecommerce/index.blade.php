@extends('layouts.ecommerce')

@section('title', 'E-commerce')

@section('page-title', 'Tableau de bord')

@section('content')

<div class="ecommerce-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="ecommerce-header">

        <div class="ecommerce-header-content">

            <div>
                <h1>
                    E-commerce
                </h1>
            </div>

        </div>

    </div>


    {{-- =========================================================
         ALERTES
    ========================================================== --}}

    @if(session('success'))

        <div class="ecommerce-alert ecommerce-alert-success">

            <span class="ecommerce-alert-icon">
                ✓
            </span>

            <div>
                {{ session('success') }}
            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="ecommerce-alert ecommerce-alert-error">

            <span class="ecommerce-alert-icon">
                !
            </span>

            <div>
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         STATISTIQUES
    ========================================================== --}}

    <div class="ecommerce-stats">

        {{-- TOTAL PRODUITS --}}

        <div class="ecommerce-stat-card">

            <div class="ecommerce-stat-icon ecommerce-stat-blue">
                <span>▦</span>
            </div>

            <div class="ecommerce-stat-content">

                <span class="ecommerce-stat-label">
                    Produits du catalogue
                </span>

                <strong class="ecommerce-stat-value">
                    {{ number_format($stats['totalProducts'], 0, ',', ' ') }}
                </strong>

                <span class="ecommerce-stat-description">
                    Produits associés à la boutique
                </span>

            </div>

        </div>


        {{-- PRODUITS PUBLIÉS --}}

        <div class="ecommerce-stat-card">

            <div class="ecommerce-stat-icon ecommerce-stat-green">
                <span>✓</span>
            </div>

            <div class="ecommerce-stat-content">

                <span class="ecommerce-stat-label">
                    Produits publiés
                </span>

                <strong class="ecommerce-stat-value">
                    {{ number_format($stats['publishedProducts'], 0, ',', ' ') }}
                </strong>

                <span class="ecommerce-stat-description">
                    Visibles dans la boutique
                </span>

            </div>

        </div>


        {{-- PRODUITS VEDETTES --}}

        <div class="ecommerce-stat-card">

            <div class="ecommerce-stat-icon ecommerce-stat-orange">
                <span>★</span>
            </div>

            <div class="ecommerce-stat-content">

                <span class="ecommerce-stat-label">
                    Produits vedettes
                </span>

                <strong class="ecommerce-stat-value">
                    {{ number_format($stats['featuredProducts'], 0, ',', ' ') }}
                </strong>

                <span class="ecommerce-stat-description">
                    Mis en avant dans la boutique
                </span>

            </div>

        </div>


        {{-- NON PUBLIÉS --}}

        <div class="ecommerce-stat-card">

            <div class="ecommerce-stat-icon ecommerce-stat-gray">
                <span>○</span>
            </div>

            <div class="ecommerce-stat-content">

                <span class="ecommerce-stat-label">
                    Non publiés
                </span>

                <strong class="ecommerce-stat-value">
                    {{ number_format($stats['unpublishedProducts'], 0, ',', ' ') }}
                </strong>

                <span class="ecommerce-stat-description">
                    Produits encore hors boutique
                </span>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CONTENU PRINCIPAL
    ========================================================== --}}

    <div class="ecommerce-dashboard-grid">


        {{-- =====================================================
             CATALOGUE
        ====================================================== --}}

        <div class="ecommerce-panel ecommerce-catalog-panel">

            <div class="ecommerce-panel-header">

                <div>

                    <h2>
                        Catalogue e-commerce
                    </h2>

                    <p>
                        Gérez les produits disponibles dans votre boutique.
                    </p>

                </div>

                @if(Route::has('company.ecommerce.products.index'))

                    <a
                        href="{{ route('company.ecommerce.products.index') }}"
                        class="ecommerce-panel-link"
                    >
                        Voir le catalogue
                        <span>→</span>
                    </a>

                @endif

            </div>


            @if($recentProducts->count())

                <div class="ecommerce-products-list">

                    
                @foreach($recentProducts as $ecommerceProduct)

                    @php
                        $product = $ecommerceProduct->product;
                    @endphp

                    <div class="ecommerce-product-row">

                        {{-- VISUEL --}}
                        <div class="ecommerce-product-visual">
                            <div class="ecommerce-product-placeholder">
                                <span>▦</span>
                            </div>
                        </div>

                        {{-- NOM ET RÉFÉRENCE --}}
                        <div class="ecommerce-product-info">

                            <div class="ecommerce-product-name">
                                {{ $product?->name ?? 'Produit indisponible' }}
                            </div>

                            <div class="ecommerce-product-reference">
                                Réf. {{ $product?->sku ?? '—' }}
                            </div>

                        </div>

                        {{-- PRIX DE VENTE --}}
                        <div class="ecommerce-product-price">
                            {{ number_format((float) ($product?->selling_price ?? 0), 0, ',', ' ') }}
                            FCFA
                        </div>

                        {{-- STATUT --}}
                        <div class="ecommerce-product-status">

                            @if($ecommerceProduct->is_published)
                                <span class="ecommerce-badge ecommerce-badge-success">
                                    <span></span>
                                    Publié
                                </span>
                            @else
                                <span class="ecommerce-badge ecommerce-badge-muted">
                                    <span></span>
                                    Non publié
                                </span>
                            @endif

                        </div>

                        {{-- MODIFICATION --}}
                        @if(Route::has('company.ecommerce.products.edit'))

                            @canPermission('ecommerce.edit')
                                <a
                                    href="{{ route('company.ecommerce.products.edit', $ecommerceProduct) }}"
                                    class="ecommerce-product-action"
                                    title="Modifier le produit"
                                    aria-label="Modifier le produit"
                                >
                                    →
                                </a>
                            @endcanPermission

                        @endif

                    </div>

                @endforeach

                </div>

            @else

                <div class="ecommerce-empty">

                    <div class="ecommerce-empty-icon">
                        ▦
                    </div>

                    <h3>
                        Aucun produit dans le catalogue
                    </h3>

                    <p>
                        Commencez par ajouter un produit ERP à votre boutique.
                    </p>

                    @if(Route::has('company.ecommerce.products.create'))

                        @canPermission('ecommerce.create')

                            <a
                                href="{{ route('company.ecommerce.products.create') }}"
                                class="ecommerce-btn ecommerce-btn-primary"
                            >
                                Ajouter mon premier produit
                            </a>

                        @endcanPermission

                    @endif

                </div>

            @endif

        </div>


        {{-- =====================================================
             ACTIONS RAPIDES
        ====================================================== --}}

        <div class="ecommerce-panel ecommerce-actions-panel">

            <div class="ecommerce-panel-header">

                <div>

                    <h2>
                        Accès rapides
                    </h2>

                    <p>
                        Les principales fonctions de votre boutique.
                    </p>

                </div>

            </div>


            <div class="ecommerce-quick-actions">


                @if(Route::has('company.ecommerce.products.index'))

                    <a
                        href="{{ route('company.ecommerce.products.index') }}"
                        class="ecommerce-quick-action"
                    >

                        <span class="ecommerce-quick-icon ecommerce-quick-blue">
                            ▦
                        </span>

                        <span class="ecommerce-quick-content">

                            <strong>
                                Catalogue
                            </strong>

                            <small>
                                Gérer les produits en ligne
                            </small>

                        </span>

                        <span class="ecommerce-quick-arrow">
                            →
                        </span>

                    </a>

                @endif


                <div class="ecommerce-quick-action ecommerce-quick-disabled">

                    <span class="ecommerce-quick-icon ecommerce-quick-purple">
                        🛒
                    </span>

                    <span class="ecommerce-quick-content">

                        <strong>
                            Commandes
                        </strong>

                        <small>
                            Bientôt disponible
                        </small>

                    </span>

                    <span class="ecommerce-coming-soon">
                        Bientôt
                    </span>

                </div>


                <div class="ecommerce-quick-action ecommerce-quick-disabled">

                    <span class="ecommerce-quick-icon ecommerce-quick-orange">
                        🚚
                    </span>

                    <span class="ecommerce-quick-content">

                        <strong>
                            Livraison
                        </strong>

                        <small>
                            Zones et modes de livraison
                        </small>

                    </span>

                    <span class="ecommerce-coming-soon">
                        Bientôt
                    </span>

                </div>


                <div class="ecommerce-quick-action ecommerce-quick-disabled">

                    <span class="ecommerce-quick-icon ecommerce-quick-green">
                        %
                    </span>

                    <span class="ecommerce-quick-content">

                        <strong>
                            Promotions
                        </strong>

                        <small>
                            Réductions et codes promotionnels
                        </small>

                    </span>

                    <span class="ecommerce-coming-soon">
                        Bientôt
                    </span>

                </div>


                <div class="ecommerce-quick-action ecommerce-quick-disabled">

                    <span class="ecommerce-quick-icon ecommerce-quick-red">
                        ★
                    </span>

                    <span class="ecommerce-quick-content">

                        <strong>
                            Avis clients
                        </strong>

                        <small>
                            Gérer les avis et évaluations
                        </small>

                    </span>

                    <span class="ecommerce-coming-soon">
                        Bientôt
                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PROCHAINS BLOCS
    ========================================================== --}}

    <div class="ecommerce-roadmap">

        <div class="ecommerce-roadmap-header">

            <div>

                <h2>
                    Développement de la boutique
                </h2>

                <p>
                    Les prochaines fonctionnalités du module e-commerce.
                </p>

            </div>

        </div>


        <div class="ecommerce-roadmap-grid">

            <div class="ecommerce-roadmap-item active">

                <span class="ecommerce-roadmap-number">
                    01
                </span>

                <div>
                    <strong>
                        Catalogue
                    </strong>

                    <small>
                        En cours
                    </small>
                </div>

            </div>


            <div class="ecommerce-roadmap-item">

                <span class="ecommerce-roadmap-number">
                    02
                </span>

                <div>
                    <strong>
                        Commandes
                    </strong>

                    <small>
                        Prochaine étape
                    </small>
                </div>

            </div>


            <div class="ecommerce-roadmap-item">

                <span class="ecommerce-roadmap-number">
                    03
                </span>

                <div>
                    <strong>
                        Livraison
                    </strong>

                    <small>
                        À venir
                    </small>
                </div>

            </div>


            <div class="ecommerce-roadmap-item">

                <span class="ecommerce-roadmap-number">
                    04
                </span>

                <div>
                    <strong>
                        Paiements
                    </strong>

                    <small>
                        À venir
                    </small>
                </div>

            </div>


            <div class="ecommerce-roadmap-item">

                <span class="ecommerce-roadmap-number">
                    05
                </span>

                <div>
                    <strong>
                        Promotions
                    </strong>

                    <small>
                        À venir
                    </small>
                </div>

            </div>


            <div class="ecommerce-roadmap-item">

                <span class="ecommerce-roadmap-number">
                    06
                </span>

                <div>
                    <strong>
                        Avis clients
                    </strong>

                    <small>
                        À venir
                    </small>
                </div>

            </div>

        </div>

    </div>

</div>


<style>

/* ================================================================
   ERP SAAS — E-COMMERCE DASHBOARD
================================================================ */

.ecommerce-page {
    --ecom-primary: #315bea;
    --ecom-primary-dark: #2448c9;
    --ecom-primary-soft: #eff4ff;

    --ecom-text: #172033;
    --ecom-muted: #64748b;

    --ecom-border: #e5eaf2;
    --ecom-bg: #f7f9fc;
    --ecom-white: #ffffff;

    width: 100%;
}


/* ================================================================
   HEADER
================================================================ */

.ecommerce-header {
    margin-bottom: 24px;
}

.ecommerce-header-content {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
}

.ecommerce-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-bottom: 8px;

    color: #94a3b8;
    font-size: 13px;
}

.ecommerce-breadcrumb span {
    color: #cbd5e1;
}

.ecommerce-header h1 {
    margin: 0;

    color: var(--ecom-text);

    font-size: 28px;
    font-weight: 750;
    letter-spacing: -0.6px;
}

.ecommerce-header p {
    margin: 7px 0 0;

    color: var(--ecom-muted);

    font-size: 14px;
}


/* ================================================================
   BUTTONS
================================================================ */

.ecommerce-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    min-height: 42px;

    padding: 0 17px;

    border-radius: 9px;

    font-size: 13px;
    font-weight: 650;

    text-decoration: none;

    transition:
        background .18s ease,
        border-color .18s ease,
        box-shadow .18s ease,
        transform .18s ease;
}

.ecommerce-btn-primary {
    color: #fff;
    background: var(--ecom-primary);
    box-shadow: 0 5px 14px rgba(49, 91, 234, .16);
}

.ecommerce-btn-primary:hover {
    color: #fff;
    background: var(--ecom-primary-dark);
    transform: translateY(-1px);
}

.ecommerce-btn-icon {
    font-size: 19px;
    line-height: 1;
}


/* ================================================================
   ALERTS
================================================================ */

.ecommerce-alert {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 20px;
    padding: 12px 15px;

    border-radius: 9px;

    font-size: 13px;
}

.ecommerce-alert-success {
    color: #166534;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}

.ecommerce-alert-error {
    color: #991b1b;
    background: #fef2f2;
    border: 1px solid #fecaca;
}

.ecommerce-alert-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 24px;
    height: 24px;

    border-radius: 50%;

    font-weight: 800;
}


/* ================================================================
   STATS
================================================================ */

.ecommerce-stats {
    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 15px;

    margin-bottom: 20px;
}

.ecommerce-stat-card {
    display: flex;
    align-items: center;
    gap: 14px;

    min-width: 0;

    padding: 18px;

    background: var(--ecom-white);

    border: 1px solid var(--ecom-border);

    border-radius: 12px;

    box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
}

.ecommerce-stat-icon {
    flex: 0 0 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    width: 44px;
    height: 44px;

    border-radius: 10px;

    font-size: 19px;
    font-weight: 700;
}

.ecommerce-stat-blue {
    color: #315bea;
    background: #eff4ff;
}

.ecommerce-stat-green {
    color: #15803d;
    background: #f0fdf4;
}

.ecommerce-stat-orange {
    color: #c2410c;
    background: #fff7ed;
}

.ecommerce-stat-gray {
    color: #475569;
    background: #f1f5f9;
}

.ecommerce-stat-content {
    min-width: 0;
}

.ecommerce-stat-label {
    display: block;

    color: #64748b;

    font-size: 12px;
    font-weight: 600;
}

.ecommerce-stat-value {
    display: block;

    margin-top: 3px;

    color: var(--ecom-text);

    font-size: 23px;
    font-weight: 750;
}

.ecommerce-stat-description {
    display: block;

    margin-top: 2px;

    color: #94a3b8;

    font-size: 11px;
}


/* ================================================================
   DASHBOARD GRID
================================================================ */

.ecommerce-dashboard-grid {
    display: grid;

    grid-template-columns:
        minmax(0, 1.65fr)
        minmax(300px, .85fr);

    gap: 20px;

    margin-bottom: 20px;
}


/* ================================================================
   PANELS
================================================================ */

.ecommerce-panel {
    min-width: 0;

    background: #fff;

    border: 1px solid var(--ecom-border);

    border-radius: 12px;

    box-shadow: 0 3px 12px rgba(15, 23, 42, .035);

    overflow: hidden;
}

.ecommerce-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 19px 20px;

    border-bottom: 1px solid #edf1f6;
}

.ecommerce-panel-header h2 {
    margin: 0;

    color: var(--ecom-text);

    font-size: 15px;
    font-weight: 700;
}

.ecommerce-panel-header p {
    margin: 4px 0 0;

    color: #94a3b8;

    font-size: 12px;
}

.ecommerce-panel-link {
    flex-shrink: 0;

    color: var(--ecom-primary);

    font-size: 12px;
    font-weight: 650;

    text-decoration: none;
}

.ecommerce-panel-link:hover {
    color: var(--ecom-primary-dark);
}


/* ================================================================
   PRODUCT LIST
================================================================ */

.ecommerce-products-list {
    width: 100%;
}

.ecommerce-product-row {
    display: grid;

    grid-template-columns:
        48px
        minmax(150px, 1fr)
        auto
        auto
        30px;

    align-items: center;

    gap: 13px;

    min-height: 76px;

    padding: 12px 20px;

    border-bottom: 1px solid #f0f2f6;
}

.ecommerce-product-row:last-child {
    border-bottom: 0;
}

.ecommerce-product-visual {
    width: 48px;
    height: 48px;

    overflow: hidden;

    border-radius: 9px;

    background: #f4f6fa;
}

.ecommerce-product-visual img {
    display: block;

    width: 100%;
    height: 100%;

    object-fit: cover;
}

.ecommerce-product-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 100%;
    height: 100%;

    color: #94a3b8;

    font-size: 18px;
}

.ecommerce-product-info {
    min-width: 0;
}

.ecommerce-product-name {
    overflow: hidden;

    color: #1e293b;

    font-size: 13px;
    font-weight: 650;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.ecommerce-product-reference {
    margin-top: 4px;

    color: #94a3b8;

    font-size: 11px;
}

.ecommerce-product-price {
    color: #1e293b;

    font-size: 12px;
    font-weight: 650;

    white-space: nowrap;
}

.ecommerce-product-status {
    white-space: nowrap;
}

.ecommerce-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 5px 8px;

    border-radius: 999px;

    font-size: 10px;
    font-weight: 650;
}

.ecommerce-badge > span {
    width: 5px;
    height: 5px;

    border-radius: 50%;
}

.ecommerce-badge-success {
    color: #166534;
    background: #f0fdf4;
}

.ecommerce-badge-success > span {
    background: #22c55e;
}

.ecommerce-badge-muted {
    color: #64748b;
    background: #f1f5f9;
}

.ecommerce-badge-muted > span {
    background: #94a3b8;
}

.ecommerce-product-action {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 30px;
    height: 30px;

    color: #64748b;

    border-radius: 7px;

    text-decoration: none;

    transition: .18s ease;
}

.ecommerce-product-action:hover {
    color: var(--ecom-primary);
    background: var(--ecom-primary-soft);
}


/* ================================================================
   QUICK ACTIONS
================================================================ */

.ecommerce-quick-actions {
    padding: 8px 0;
}

.ecommerce-quick-action {
    display: flex;
    align-items: center;

    gap: 12px;

    padding: 13px 18px;

    text-decoration: none;

    border-bottom: 1px solid #f0f2f6;

    transition: background .18s ease;
}

.ecommerce-quick-action:last-child {
    border-bottom: 0;
}

a.ecommerce-quick-action:hover {
    background: #fafbff;
}

.ecommerce-quick-icon {
    flex: 0 0 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    border-radius: 9px;

    font-size: 16px;
    font-weight: 700;
}

.ecommerce-quick-blue {
    color: #315bea;
    background: #eff4ff;
}

.ecommerce-quick-purple {
    color: #7c3aed;
    background: #f5f3ff;
}

.ecommerce-quick-orange {
    color: #ea580c;
    background: #fff7ed;
}

.ecommerce-quick-green {
    color: #15803d;
    background: #f0fdf4;
}

.ecommerce-quick-red {
    color: #dc2626;
    background: #fef2f2;
}

.ecommerce-quick-content {
    display: flex;
    flex-direction: column;

    min-width: 0;

    flex: 1;
}

.ecommerce-quick-content strong {
    color: #1e293b;

    font-size: 12px;
}

.ecommerce-quick-content small {
    margin-top: 3px;

    color: #94a3b8;

    font-size: 10px;
}

.ecommerce-quick-arrow {
    color: #94a3b8;

    font-size: 16px;
}

.ecommerce-quick-disabled {
    opacity: .7;
}

.ecommerce-coming-soon {
    flex-shrink: 0;

    padding: 4px 7px;

    color: #64748b;
    background: #f1f5f9;

    border-radius: 5px;

    font-size: 9px;
    font-weight: 650;
}


/* ================================================================
   EMPTY
================================================================ */

.ecommerce-empty {
    display: flex;
    align-items: center;
    flex-direction: column;

    padding: 45px 20px;

    text-align: center;
}

.ecommerce-empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 50px;
    height: 50px;

    margin-bottom: 12px;

    color: var(--ecom-primary);

    background: var(--ecom-primary-soft);

    border-radius: 12px;

    font-size: 21px;
}

.ecommerce-empty h3 {
    margin: 0;

    color: #1e293b;

    font-size: 14px;
}

.ecommerce-empty p {
    max-width: 380px;

    margin: 7px 0 17px;

    color: #94a3b8;

    font-size: 12px;
}


/* ================================================================
   ROADMAP
================================================================ */

.ecommerce-roadmap {
    padding: 20px;

    background: #fff;

    border: 1px solid var(--ecom-border);

    border-radius: 12px;

    box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
}

.ecommerce-roadmap-header {
    margin-bottom: 18px;
}

.ecommerce-roadmap-header h2 {
    margin: 0;

    color: var(--ecom-text);

    font-size: 15px;
}

.ecommerce-roadmap-header p {
    margin: 4px 0 0;

    color: #94a3b8;

    font-size: 12px;
}

.ecommerce-roadmap-grid {
    display: grid;

    grid-template-columns:
        repeat(6, minmax(0, 1fr));

    gap: 10px;
}

.ecommerce-roadmap-item {
    display: flex;
    align-items: center;

    gap: 10px;

    padding: 12px;

    background: #f8fafc;

    border: 1px solid #edf1f6;

    border-radius: 9px;
}

.ecommerce-roadmap-item.active {
    background: var(--ecom-primary-soft);

    border-color: #d7e1ff;
}

.ecommerce-roadmap-number {
    flex: 0 0 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    width: 30px;
    height: 30px;

    color: #64748b;

    background: #fff;

    border: 1px solid #e2e8f0;

    border-radius: 7px;

    font-size: 10px;
    font-weight: 750;
}

.ecommerce-roadmap-item.active
.ecommerce-roadmap-number {
    color: #fff;

    background: var(--ecom-primary);

    border-color: var(--ecom-primary);
}

.ecommerce-roadmap-item strong {
    display: block;

    color: #334155;

    font-size: 11px;
}

.ecommerce-roadmap-item small {
    display: block;

    margin-top: 2px;

    color: #94a3b8;

    font-size: 9px;
}


/* ================================================================
   RESPONSIVE
================================================================ */

@media (max-width: 1200px) {

    .ecommerce-stats {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .ecommerce-dashboard-grid {
        grid-template-columns: 1fr;
    }

    .ecommerce-roadmap-grid {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }
}


@media (max-width: 700px) {

    .ecommerce-header-content {
        align-items: stretch;
        flex-direction: column;
    }

    .ecommerce-header-actions {
        width: 100%;
    }

    .ecommerce-header-actions .ecommerce-btn {
        width: 100%;
    }

    .ecommerce-header h1 {
        font-size: 24px;
    }

    .ecommerce-stats {
        grid-template-columns: 1fr;
    }

    .ecommerce-panel-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .ecommerce-product-row {
        grid-template-columns:
            42px
            minmax(0, 1fr)
            30px;

        gap: 10px;

        padding: 12px 14px;
    }

    .ecommerce-product-visual {
        width: 42px;
        height: 42px;
    }

    .ecommerce-product-price,
    .ecommerce-product-status {
        display: none;
    }

    .ecommerce-roadmap {
        padding: 15px;
    }

    .ecommerce-roadmap-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


@media (max-width: 420px) {

    .ecommerce-roadmap-grid {
        grid-template-columns: 1fr;
    }

    .ecommerce-stat-card {
        padding: 15px;
    }

}

</style>

@endsection