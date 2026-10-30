@extends('layouts.company')

@section('title', 'Produits')
@section('page-title', 'Produits')

@section('content')

<div class="company-module">

    {{-- HEADER --}}
    <div class="module-header">

        <div>
            <span class="module-kicker">
                GESTION COMMERCIALE
            </span>

            <h1>Produits</h1>

            <p>
                Gérez les produits, prix et niveaux de stock de votre entreprise.
            </p>
        </div>

        <a
            href="{{ route('company.products.create') }}"
            class="module-btn module-btn-primary"
        >
            + Nouveau produit
        </a>

    </div>


    {{-- MESSAGE --}}
    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- FILTRES --}}
    <div class="module-card">

        <form
            method="GET"
            action="{{ route('company.products.index') }}"
            class="module-filters"
        >

            <div class="filter-group">

                <label>Recherche</label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nom, SKU ou code-barres..."
                >

            </div>


            <div class="filter-group">

                <label>Catégorie</label>

                <select name="category_id">

                    <option value="">
                        Toutes les catégories
                    </option>

                    @foreach($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            @selected(request('category_id') == $category->id)
                        >
                            {{ $category->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="filter-group">

                <label>Statut</label>

                <select name="status">

                    <option value="">
                        Tous
                    </option>

                    <option
                        value="active"
                        @selected(request('status') === 'active')
                    >
                        Actifs
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status') === 'inactive')
                    >
                        Inactifs
                    </option>

                    <option
                        value="low_stock"
                        @selected(request('status') === 'low_stock')
                    >
                        Stock faible
                    </option>

                    <option
                        value="out_of_stock"
                        @selected(request('status') === 'out_of_stock')
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
                    Rechercher
                </button>

                <a
                    href="{{ route('company.products.index') }}"
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
                <h2>Liste des produits</h2>

                <span>
                    {{ $products->total() }}
                    produit(s)
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

                        <th>Prix achat</th>

                        <th>Prix vente</th>

                        <th>Stock</th>

                        <th>Statut</th>

                        <th>Actions</th>

                    </tr>
                </thead>

                <tbody>

                @forelse($products as $product)

                    <tr>

                        <td>

                            <div class="product-name">

                                <strong>
                                    {{ $product->name }}
                                </strong>

                                @if($product->barcode)

                                    <small>
                                        {{ $product->barcode }}
                                    </small>

                                @endif

                            </div>

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
                            {{ number_format($product->purchase_price, 0, ',', ' ') }}
                            FCFA
                        </td>


                        <td>
                            <strong>
                                {{ number_format($product->selling_price, 0, ',', ' ') }}
                                FCFA
                            </strong>
                        </td>


                        <td>

                            @if($product->isOutOfStock())

                                <span class="stock-badge stock-danger">
                                    Rupture
                                </span>

                            @elseif($product->isLowStock())

                                <span class="stock-badge stock-warning">
                                    {{ $product->stock_quantity }}
                                </span>

                            @else

                                <span class="stock-badge stock-success">
                                    {{ $product->stock_quantity }}
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($product->is_active)

                                <span class="status-badge status-active">
                                    Actif
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Inactif
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="table-actions">

                                <a
                                    href="{{ route('company.products.show', $product->id) }}"
                                    class="action-btn"
                                    title="Voir"
                                >
                                    Voir
                                </a>

                                <a
                                    href="{{ route('company.products.edit', $product->id) }}"
                                    class="action-btn"
                                    title="Modifier"
                                >
                                    Modifier
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('company.products.destroy', $product->id) }}"
                                    onsubmit="return confirm('Supprimer ce produit ?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="action-btn action-danger"
                                    >
                                        Supprimer
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="empty-state"
                        >

                            <div>

                                <strong>
                                    Aucun produit trouvé
                                </strong>

                                <p>
                                    Commencez par ajouter votre premier produit.
                                </p>

                                <a
                                    href="{{ route('company.products.create') }}"
                                    class="module-btn module-btn-primary"
                                >
                                    Ajouter un produit
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($products->hasPages())

            <div class="module-pagination">

                {{ $products->links() }}

            </div>

        @endif

    </div>

</div>

@endsection