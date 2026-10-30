@extends('layouts.company')

@section('title', 'Gestion du stock')
@section('page-title', 'Stock')

@section('content')

<div class="company-module">

    {{-- HEADER --}}
    <div class="module-header">

        <div>

            <span class="module-kicker">
                GESTION DES STOCKS
            </span>

            <h1>Stock</h1>

            <p>
                Consultez et gérez les niveaux de stock de vos produits.
            </p>

        </div>


        <div class="module-header-actions">

            <a
                href="{{ route('company.stock.entry') }}"
                class="module-btn module-btn-primary"
            >
                + Entrée
            </a>

            <a
                href="{{ route('company.stock.exit') }}"
                class="module-btn module-btn-light"
            >
                − Sortie
            </a>

        </div>

    </div>


    {{-- MESSAGE --}}
    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- STATISTIQUES --}}
    <div class="stock-stats">

        <div class="stock-stat-card">

            <span class="stock-stat-label">
                Produits
            </span>

            <strong>
                {{ $totalProducts }}
            </strong>

        </div>


        <div class="stock-stat-card">

            <span class="stock-stat-label">
                Quantité totale
            </span>

            <strong>
                {{ number_format($totalQuantity, 0, ',', ' ') }}
            </strong>

        </div>


        <div class="stock-stat-card stock-stat-warning">

            <span class="stock-stat-label">
                Stock faible
            </span>

            <strong>
                {{ $lowStock }}
            </strong>

        </div>


        <div class="stock-stat-card stock-stat-danger">

            <span class="stock-stat-label">
                Ruptures
            </span>

            <strong>
                {{ $outOfStock }}
            </strong>

        </div>

    </div>


    {{-- FILTRES --}}
    <div class="module-card">

        <form
            method="GET"
            action="{{ route('company.stock.index') }}"
            class="module-filters"
        >

            <div class="filter-group">

                <label>Recherche</label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Produit, SKU..."
                >

            </div>


            <div class="filter-group">

                <label>État du stock</label>

                <select name="status">

                    <option value="">
                        Tous
                    </option>

                    <option
                        value="available"
                        @selected(request('status') === 'available')
                    >
                        Disponible
                    </option>

                    <option
                        value="low"
                        @selected(request('status') === 'low')
                    >
                        Stock faible
                    </option>

                    <option
                        value="out"
                        @selected(request('status') === 'out')
                    >
                        Rupture
                    </option>

                </select>

            </div>


            <div class="filter-actions">

                <button
                    type="submit"
                    class="module-btn module-btn-primary"
                >
                    Filtrer
                </button>

                <a
                    href="{{ route('company.stock.index') }}"
                    class="module-btn module-btn-light"
                >
                    Réinitialiser
                </a>

            </div>

        </form>

    </div>


    {{-- TABLE --}}
    <div class="module-card">

        <div class="module-card-header">

            <div>

                <h2>État du stock</h2>

                <span>
                    {{ $products->total() }} produit(s)
                </span>

            </div>

        </div>


        <div class="module-table-wrapper">

            <table class="module-table">

                <thead>

                    <tr>

                        <th>Produit</th>

                        <th>SKU</th>

                        <th>Catégorie</th>

                        <th>Stock</th>

                        <th>Seuil</th>

                        <th>État</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($products as $product)

                    <tr>

                        <td>

                            <strong>
                                {{ $product->name }}
                            </strong>

                        </td>


                        <td>

                            <span class="product-sku">
                                {{ $product->sku }}
                            </span>

                        </td>


                        <td>

                            {{ $product->category?->name ?? 'Sans catégorie' }}

                        </td>


                        <td>

                            <strong>
                                {{ $product->stock_quantity }}
                            </strong>

                        </td>


                        <td>

                            {{ $product->min_stock }}

                        </td>


                        <td>

                            @if($product->isOutOfStock())

                                <span class="stock-badge stock-danger">
                                    Rupture
                                </span>

                            @elseif($product->isLowStock())

                                <span class="stock-badge stock-warning">
                                    Stock faible
                                </span>

                            @else

                                <span class="stock-badge stock-success">
                                    Disponible
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="table-actions">

                                <a
                                    href="{{ route('company.stock.entry') }}"
                                    class="action-btn"
                                >
                                    + Entrée
                                </a>

                                <a
                                    href="{{ route('company.stock.exit') }}"
                                    class="action-btn"
                                >
                                    − Sortie
                                </a>

                                <a
                                    href="{{ route('company.products.show', $product->id) }}"
                                    class="action-btn"
                                >
                                    Voir
                                </a>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="empty-state"
                        >

                            <strong>
                                Aucun produit trouvé
                            </strong>

                            <p>
                                Ajoutez d'abord des produits à votre catalogue.
                            </p>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($products->hasPages())

            <div class="module-pagination">
                {{ $products->links() }}
            </div>

        @endif

    </div>

</div>

@endsection