
@extends('layouts.storefront')

@section('title', ($collection->seo_title ?: $collection->name) . ' — ' . ($settings->store_name ?: $company->name))

@section('meta_description', $collection->seo_description ?: ($collection->description ?: 'Découvrez notre collection'))

@section('content')

@php
    $storeRoute = ['company' => $company->slug];
    $currency = $settings->currency ?: 'FCFA';
@endphp

<div class="nc-page">

    {{-- ============================================
         EN-TETE COLLECTION
    ============================================ --}}

    <section class="nc-detail-hero">

        <div class="store-container">

            <nav class="nc-breadcrumb" aria-label="Fil d'Ariane">

                <a href="{{ route('storefront.index', $storeRoute) }}">
                    Accueil
                </a>

                <span>/</span>

                <a href="{{ route('storefront.collections.index', $storeRoute) }}">
                    Collections
                </a>

                <span>/</span>

                <span aria-current="page">
                    {{ $collection->name }}
                </span>

            </nav>

            <div class="nc-detail-layout">

                <div class="nc-detail-content">

                    <span class="nc-detail-tag">
                        ✦ COLLECTION
                    </span>

                    <h1>{{ $collection->name }}</h1>

                    <p>
                        {{ $collection->description
                            ?: 'Découvrez les produits disponibles dans cette collection.' }}
                    </p>

                    <div class="nc-detail-stats">

                        <div class="nc-detail-stat">
                            <strong>{{ $products->total() }}</strong>
                            <span>Produit(s) disponible(s)</span>
                        </div>

                    </div>

                    <a href="#nc-collection-products"
                       class="nc-primary-button">

                        Explorer les produits
                        <span aria-hidden="true">↓</span>

                    </a>

                </div>

                <div class="nc-detail-visual">

                    @if($collection->image)

                        <img
                            src="{{ asset('storage/' . $collection->image) }}"
                            alt="{{ $collection->name }}"
                        >

                    @else

                        <div class="nc-detail-placeholder">
                            <span aria-hidden="true">✦</span>
                            <strong>{{ $collection->name }}</strong>
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================
         PRODUITS DE LA COLLECTION
    ============================================ --}}

    <section class="nc-section" id="nc-collection-products">

        <div class="store-container">

            <div class="nc-section-header">

                <div>

                    <span class="nc-section-label">
                        NOTRE CATALOGUE
                    </span>

                    <h2>Produits de la collection</h2>

                    <p>
                        Retrouvez les articles disponibles dans
                        la collection {{ $collection->name }}.
                    </p>

                </div>

                <div class="nc-count-badge">
                    {{ $products->total() }} produit(s)
                </div>

            </div>


            @if($products->count())

                <div class="nc-products-grid">

                    @foreach($products as $item)

                        @php
                            $product = $item->product;

                            $price = $item->sale_price !== null
                                && (float) $item->sale_price > 0
                                ? (float) $item->sale_price
                                : (float) ($product?->selling_price ?? 0);

                            $image = $item->primaryImage?->image_path;
                        @endphp

                        @if($product)

                            <article class="nc-product-card">

                                <a href="{{ route('storefront.products.show', [
                                    'company' => $company->slug,
                                    'slug' => $item->slug
                                ]) }}">

                                    <div class="nc-product-image">

                                        @if($image)

                                            <img
                                                src="{{ asset('storage/' . $image) }}"
                                                alt="{{ $product->name }}"
                                                loading="lazy"
                                            >

                                        @else

                                            <div class="nc-product-placeholder">
                                                <span aria-hidden="true">📦</span>
                                            </div>

                                        @endif

                                        @if($item->is_featured)

                                            <span class="nc-featured-badge">
                                                ✦ Vedette
                                            </span>

                                        @endif

                                    </div>

                                    <div class="nc-product-body">

                                        @if($item->category)

                                            <span class="nc-product-category">
                                                {{ $item->category->name }}
                                            </span>

                                        @endif

                                        <h3>{{ $product->name }}</h3>

                                        <p>
                                            {{ \Illuminate\Support\Str::limit(
                                                strip_tags(
                                                    $item->short_description
                                                    ?: $product->description
                                                    ?: 'Découvrez ce produit.'
                                                ),
                                                90
                                            ) }}
                                        </p>

                                        <div class="nc-product-price">
                                            {{ number_format($price, 0, ',', ' ') }}
                                            <span>{{ $currency }}</span>
                                        </div>

                                        <div class="nc-product-footer">

                                            <span>Voir le produit</span>

                                            <span class="nc-card-next" aria-hidden="true">
                                                →
                                            </span>

                                        </div>

                                    </div>

                                </a>

                            </article>

                        @endif

                    @endforeach

                </div>

                @if($products->hasPages())

                    <div class="nc-pagination">
                        {{ $products->links() }}
                    </div>

                @endif

            @else

                <div class="nc-empty">

                    <div class="nc-empty-icon" aria-hidden="true">
                        📦
                    </div>

                    <h3>Aucun produit publié</h3>

                    <p>
                        Cette collection ne contient pas encore
                        de produits visibles.
                    </p>

                    <a href="{{ route('storefront.collections.index', $storeRoute) }}"
                       class="nc-primary-button">

                        Explorer les autres collections
                        <span aria-hidden="true">→</span>

                    </a>

                </div>

            @endif

        </div>

    </section>


    {{-- ============================================
         LIEN RETOUR CATALOGUE
    ============================================ --}}

    <section class="nc-bottom-section">

        <div class="store-container">

            <div class="nc-bottom-banner">

                <div>

                    <span class="nc-bottom-tag">
                        DECOUVREZ DAVANTAGE
                    </span>

                    <h2>Encore plus de produits à découvrir</h2>

                    <p>
                        Parcourez l'ensemble de notre catalogue
                        et trouvez les articles qui vous intéressent.
                    </p>

                </div>

                <a href="{{ route('storefront.products.index', $storeRoute) }}"
                   class="nc-bottom-button">

                    Tous les produits
                    <span aria-hidden="true">→</span>

                </a>

            </div>

        </div>

    </section>

</div>

@endsection
