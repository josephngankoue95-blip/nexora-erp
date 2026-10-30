@extends('layouts.storefront')
@section('title', 'Nos produits — ' . ($settings->store_name ?: $company->name))
@section('content')
@php
    $storeRoute = ['company' => $company->slug];
    $currentSort = $sort ?? request('sort', 'recent');
@endphp
<div class="store-products-page store-catalog">
    <nav class="store-product-breadcrumb" aria-label="Fil d’Ariane">
        <a href="{{ route('storefront.index', $storeRoute) }}">Accueil</a><span aria-hidden="true">/</span><span>Produits</span>
    </nav>
    <header class="store-products-header">
        <span class="store-products-eyebrow">NOTRE CATALOGUE</span>
        <h1>Découvrez nos produits</h1>
        <p>Explorez les articles disponibles chez {{ $settings->store_name ?: $company->name }}.</p>
    </header>
    <form method="GET" action="{{ route('storefront.products.index', $storeRoute) }}" class="store-products-filters" role="search">
        <label class="store-filter-field store-filter-search">
            <span>Rechercher</span>
            <input type="search" name="search" value="{{ $search ?? request('search', '') }}" placeholder="Nom d’un produit…">
        </label>
        <label class="store-filter-field">
            <span>Trier par</span>
            <select name="sort">
                <option value="recent" @selected($currentSort === 'recent')>Plus récents</option>
                <option value="price_asc" @selected($currentSort === 'price_asc')>Prix croissant</option>
                <option value="price_desc" @selected($currentSort === 'price_desc')>Prix décroissant</option>
                <option value="name" @selected($currentSort === 'name')>Nom du produit</option>
            </select>
        </label>
        <button type="submit">Rechercher <span aria-hidden="true">→</span></button>
        @if(request()->filled('search') || request()->filled('sort'))
            <a class="store-filter-reset" href="{{ route('storefront.products.index', $storeRoute) }}">Réinitialiser</a>
        @endif
    </form>
    <div class="store-catalog-toolbar"><p class="store-products-count"><strong>{{ $products->total() }}</strong> produit(s) trouvé(s)</p><span>Notre sélection</span></div>
    <div class="store-products-grid">
        @forelse($products as $item)
            @php
                $product = $item->product;
                $price = $item->sale_price !== null && (float) $item->sale_price > 0
                    ? (float) $item->sale_price : (float) ($product?->selling_price ?? 0);
                $image = $item->primaryImage?->image_path;
            @endphp
            @if($product)
                <article class="store-product-card">
                    <a href="{{ route('storefront.products.show', ['company' => $company->slug, 'slug' => $item->slug]) }}">
                        <div class="store-product-image">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <span class="store-product-placeholder" aria-hidden="true">📦</span>
                            @endif
                            @if($item->is_featured)<span class="store-featured-badge">✦ Vedette</span>@endif
                        </div>
                        <div class="store-product-info">
                            <h3>{{ $product->name }}</h3>
                            <p class="store-product-description">{{ \Illuminate\Support\Str::limit(strip_tags($item->short_description ?: $product->description ?: 'Découvrez ce produit.'), 90) }}</p>
                            <strong class="store-product-price">{{ number_format($price, 0, ',', ' ') }} {{ $settings->currency ?: 'FCFA' }}</strong>
                            <span class="store-product-link">Voir le produit <span aria-hidden="true">→</span></span>
                        </div>
                    </a>
                </article>
            @endif
        @empty
            <div class="store-products-empty">
                <div class="store-empty-icon" aria-hidden="true">⌕</div>
                <h3>Aucun produit trouvé</h3>
                <p>Essayez une autre recherche ou revenez prochainement.</p>
                <a href="{{ route('storefront.products.index', $storeRoute) }}">Voir tout le catalogue →</a>
            </div>
        @endforelse
    </div>
    @if($products->hasPages())
        <div class="store-products-pagination">{{ $products->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
