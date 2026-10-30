
@extends('layouts.ecommerce')

@section('title', 'Produits e-commerce')
@section('page-title', 'Catalogue produits')

@section('content')

<div class="ecommerce-catalog-page">

    <div class="catalog-header">
        <div>
            <div class="catalog-breadcrumb">
                <a href="{{ route('company.ecommerce.index') }}">E-commerce</a>
                <span>/</span>
                <span>Catalogue</span>
            </div>

            <h1>Catalogue produits</h1>
            <p>Gérez les produits disponibles dans votre boutique en ligne.</p>
        </div>

        @canPermission('ecommerce.create')
            <a href="{{ route('company.ecommerce.products.create') }}"
               class="catalog-primary-button">
                ＋ Ajouter au catalogue
            </a>
        @endcanPermission
    </div>

    @if(session('success'))
        <div class="catalog-alert catalog-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="catalog-alert catalog-alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="catalog-alert catalog-alert-error">
            <strong>Une erreur est survenue :</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTRES --}}
    <div class="catalog-filter-card">
        <form method="GET"
              action="{{ route('company.ecommerce.products.index') }}"
              class="catalog-filter-form">

            <div>
                <label for="search">Rechercher un produit</label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Nom du produit ou référence...">
            </div>

            <div>
                <label for="status">Statut</label>
                <select id="status" name="status">
                    <option value="">Tous les produits</option>
                    <option value="published"
                        @selected(request('status') === 'published')>
                        Publiés
                    </option>
                    <option value="unpublished"
                        @selected(request('status') === 'unpublished')>
                        Non publiés
                    </option>
                    <option value="featured"
                        @selected(request('status') === 'featured')>
                        À la une
                    </option>
                </select>
            </div>

            <div class="catalog-filter-actions">
                <button type="submit" class="catalog-filter-button">
                    Rechercher
                </button>

                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('company.ecommerce.products.index') }}"
                       class="catalog-reset-button">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- RÉSULTATS --}}
    <div class="catalog-result-bar">
        <div>
            <strong>{{ $ecommerceProducts->total() }}</strong>
            produit(s) dans le catalogue
        </div>

        @if(request('search'))
            <span>
                Recherche :
                <strong>{{ request('search') }}</strong>
            </span>
        @endif
    </div>

    {{-- TABLEAU --}}
    <div class="catalog-table-card">

        @if($ecommerceProducts->count())

            <div class="catalog-table-wrapper">
                <table class="catalog-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix de vente</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th>À la une</th>
                            <th class="catalog-action-column">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($ecommerceProducts as $ecommerceProduct)

                            @php
                                $product = $ecommerceProduct->product;
                            @endphp

                            <tr>
                                {{-- PRODUIT --}}
                                <td>
                                    <div class="catalog-product">
                                        <div class="catalog-product-placeholder">
                                            📦
                                        </div>

                                        <div class="catalog-product-info">
                                            <strong>
                                                {{ $product?->name ?? 'Produit supprimé' }}
                                            </strong>

                                            @if($product?->sku)
                                                <span>
                                                    Réf. {{ $product->sku }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- PRIX DE VENTE ERP --}}
                                <td>
                                    @if($product)
                                        <span class="catalog-price">
                                            {{ number_format(
                                                (float) $product->selling_price,
                                                0,
                                                ',',
                                                ' '
                                            ) }} FCFA
                                        </span>
                                    @else
                                        <span class="catalog-muted">—</span>
                                    @endif
                                </td>

                                {{-- STOCK ERP --}}
                                <td>
                                    @if($product)
                                        @if($product->stock_quantity > 0)
                                            <span class="catalog-stock catalog-stock-positive">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        @else
                                            <span class="catalog-stock catalog-stock-negative">
                                                Rupture
                                            </span>
                                        @endif
                                    @else
                                        <span class="catalog-muted">—</span>
                                    @endif
                                </td>

                                {{-- PUBLICATION --}}
                                <td>
                                    @if($ecommerceProduct->is_published)
                                        <span class="catalog-badge catalog-badge-success">
                                            Publié
                                        </span>
                                    @else
                                        <span class="catalog-badge catalog-badge-muted">
                                            Non publié
                                        </span>
                                    @endif
                                </td>

                                {{-- VEDETTE --}}
                                <td>
                                    @if($ecommerceProduct->is_featured)
                                        <span class="catalog-featured">★ À la une</span>
                                    @else
                                        <span class="catalog-muted">—</span>
                                    @endif
                                </td>

                                {{-- ACTIONS --}}
                                <td>
                                    <div class="catalog-actions">

                                        @canPermission('ecommerce.edit')
                                            <a href="{{ route(
                                                'company.ecommerce.products.edit',
                                                $ecommerceProduct
                                            ) }}"
                                               class="catalog-action catalog-action-edit"
                                               title="Modifier"
                                               aria-label="Modifier le produit">
                                                ✎
                                            </a>
                                        @endcanPermission

                                        @canPermission('ecommerce.delete')
                                            <form method="POST"
                                                  action="{{ route(
                                                    'company.ecommerce.products.destroy',
                                                    $ecommerceProduct
                                                  ) }}"
                                                  class="catalog-delete-form"
                                                  onsubmit="return confirm('Voulez-vous vraiment retirer ce produit du catalogue e-commerce ?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="catalog-action catalog-action-delete"
                                                        title="Retirer du catalogue"
                                                        aria-label="Retirer du catalogue">
                                                    🗑
                                                </button>
                                            </form>
                                        @endcanPermission

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($ecommerceProducts->hasPages())
                <div class="catalog-pagination">
                    {{ $ecommerceProducts->withQueryString()->links() }}
                </div>
            @endif

        @else

            <div class="catalog-empty">
                <div class="catalog-empty-icon">🛍️</div>

                <h3>Aucun produit dans le catalogue</h3>

                <p>
                    Ajoutez vos produits ERP à votre boutique e-commerce
                    pour commencer à construire votre catalogue.
                </p>

                @canPermission('ecommerce.create')
                    <a href="{{ route('company.ecommerce.products.create') }}"
                       class="catalog-primary-button">
                        ＋ Ajouter un produit
                    </a>
                @endcanPermission
            </div>

        @endif
    </div>
</div>

<style>
.ecommerce-catalog-page {
    --primary: #315bea;
    --primary-dark: #2448c9;
    --text: #172033;
    --muted: #64748b;
    --border: #e2e8f0;
    width: 100%;
}

.catalog-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 24px;
}

.catalog-breadcrumb {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
    color: var(--muted);
    font-size: 13px;
}

.catalog-breadcrumb a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

.catalog-header h1 {
    margin: 0;
    color: var(--text);
    font-size: 28px;
    font-weight: 750;
}

.catalog-header p {
    margin: 7px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.catalog-primary-button,
.catalog-filter-button,
.catalog-reset-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0 15px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.catalog-primary-button,
.catalog-filter-button {
    color: #fff;
    background: var(--primary);
    border: 1px solid var(--primary);
}

.catalog-primary-button:hover,
.catalog-filter-button:hover {
    background: var(--primary-dark);
}

.catalog-alert {
    padding: 13px 16px;
    margin-bottom: 18px;
    border-radius: 10px;
    font-size: 14px;
}

.catalog-alert-success {
    color: #166534;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}

.catalog-alert-error {
    color: #991b1b;
    background: #fef2f2;
    border: 1px solid #fecaca;
}

.catalog-filter-card,
.catalog-table-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 13px;
    box-shadow: 0 2px 8px rgba(15,23,42,.03);
}

.catalog-filter-card {
    padding: 18px;
    margin-bottom: 15px;
}

.catalog-filter-form {
    display: grid;
    grid-template-columns: minmax(220px, 1fr) 220px auto;
    align-items: end;
    gap: 14px;
}

.catalog-filter-form label {
    display: block;
    margin-bottom: 7px;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
}

.catalog-filter-form input,
.catalog-filter-form select {
    width: 100%;
    height: 43px;
    padding: 0 12px;
    border: 1px solid #d8e0eb;
    border-radius: 9px;
    box-sizing: border-box;
    background: #fff;
    color: var(--text);
    font: inherit;
    font-size: 13px;
}

.catalog-filter-form input:focus,
.catalog-filter-form select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(49,91,234,.1);
}

.catalog-filter-actions {
    display: flex;
    gap: 8px;
}

.catalog-reset-button {
    color: #475569;
    border: 1px solid var(--border);
    background: #fff;
}

.catalog-result-bar {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 12px;
    color: var(--muted);
    font-size: 13px;
}

.catalog-result-bar strong {
    color: var(--text);
}

.catalog-table-card {
    overflow: hidden;
}

.catalog-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.catalog-table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse;
}

.catalog-table th {
    padding: 14px 16px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border);
    color: #64748b;
    text-align: left;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}

.catalog-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #edf1f5;
    color: #334155;
    font-size: 13px;
    vertical-align: middle;
}

.catalog-table tbody tr:last-child td {
    border-bottom: 0;
}

.catalog-table tbody tr:hover {
    background: #fbfdff;
}

.catalog-product {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 220px;
}

.catalog-product-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: #f8fafc;
    font-size: 22px;
}

.catalog-product-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.catalog-product-info strong {
    color: var(--text);
    font-size: 13px;
}

.catalog-product-info span {
    color: #94a3b8;
    font-size: 11px;
}

.catalog-price {
    color: var(--text);
    font-weight: 750;
    white-space: nowrap;
}

.catalog-stock,
.catalog-badge,
.catalog-featured {
    display: inline-flex;
    align-items: center;
    padding: 6px 9px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 750;
    white-space: nowrap;
}

.catalog-stock-positive,
.catalog-badge-success {
    color: #166534;
    background: #f0fdf4;
}

.catalog-stock-negative {
    color: #b91c1c;
    background: #fef2f2;
}

.catalog-badge-muted {
    color: #64748b;
    background: #f1f5f9;
}

.catalog-featured {
    color: #a16207;
    background: #fef9c3;
}

.catalog-muted {
    color: #94a3b8;
}

.catalog-action-column {
    text-align: right !important;
}

.catalog-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 7px;
}

.catalog-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 1px solid;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
}

.catalog-action-edit {
    color: var(--primary);
    background: #eff6ff;
    border-color: #dbeafe;
}

.catalog-action-delete {
    color: #dc2626;
    background: #fef2f2;
    border-color: #fecaca;
}

.catalog-delete-form {
    margin: 0;
}

.catalog-pagination {
    padding: 15px 18px;
    border-top: 1px solid var(--border);
}

.catalog-empty {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    min-height: 320px;
    padding: 35px 20px;
    text-align: center;
}

.catalog-empty-icon {
    margin-bottom: 15px;
    font-size: 36px;
}

.catalog-empty h3 {
    margin: 0;
    color: var(--text);
    font-size: 18px;
}

.catalog-empty p {
    max-width: 520px;
    margin: 9px 0 20px;
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
}

@media (max-width: 850px) {
    .catalog-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .catalog-filter-form {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 600px) {
    .catalog-header h1 {
        font-size: 23px;
    }

    .catalog-header > div,
    .catalog-header .catalog-primary-button {
        width: 100%;
        box-sizing: border-box;
    }

    .catalog-filter-card {
        padding: 14px;
    }

    .catalog-filter-actions {
        width: 100%;
    }

    .catalog-filter-button,
    .catalog-reset-button {
        flex: 1;
    }

    .catalog-result-bar {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>

@endsection
