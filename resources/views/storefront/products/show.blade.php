
@extends('layouts.storefront')

@section('title', ($ecommerceProduct->product?->name ?? 'Produit') . ' — ' . ($settings->store_name ?: $company->name))

@section('content')

@php
    $product = $ecommerceProduct->product;

    $price = $ecommerceProduct->sale_price !== null
        && (float) $ecommerceProduct->sale_price > 0
        ? (float) $ecommerceProduct->sale_price
        : (float) ($product?->selling_price ?? 0);

    $stock = (int) ($product?->stock_quantity ?? 0);
    $available = $stock > 0;
    $image = $ecommerceProduct->primaryImage?->image_path;
    $storeRoute = ['company' => $company->slug];
    $currency = $settings->currency ?: 'FCFA';
@endphp

<div class="nexora-product-page">

    <div class="store-container">

        {{-- FIL D'ARIANE --}}
        <nav class="np-breadcrumb" aria-label="Fil d'Ariane">
            <a href="{{ route('storefront.index', $storeRoute) }}">Accueil</a>
            <span>/</span>
            <a href="{{ route('storefront.products.index', $storeRoute) }}">Produits</a>
            <span>/</span>
            <span aria-current="page">{{ $product?->name ?? 'Produit' }}</span>
        </nav>

        {{-- PRESENTATION DU PRODUIT --}}
        <div class="np-product-layout">

            {{-- IMAGE --}}
            <div class="np-gallery">

                <div class="np-main-image">

                    @if($image)
                        <img
                            src="{{ asset('storage/' . $image) }}"
                            alt="{{ $product?->name ?? 'Produit' }}"
                            class="np-product-photo"
                        >
                    @else
                        <div class="np-image-placeholder">
                            <span aria-hidden="true">📦</span>
                            <p>Image non disponible</p>
                        </div>
                    @endif

                    @if($ecommerceProduct->is_featured)
                        <span class="np-featured-badge">
                            ✦ Produit vedette
                        </span>
                    @endif

                </div>

                <div class="np-gallery-caption">
                    <span>✦</span>
                    {{ $settings->store_name ?: $company->name }}
                </div>

            </div>

            {{-- INFORMATIONS ET ACHAT --}}
            <div class="np-product-content">

                @if($ecommerceProduct->category)
                    <span class="np-category">
                        {{ $ecommerceProduct->category->name }}
                    </span>
                @endif

                <h1 class="np-product-title">
                    {{ $product?->name ?? 'Produit' }}
                </h1>

                <p class="np-short-description">
                    {{ $ecommerceProduct->short_description
                        ?: $product?->description
                        ?: 'Découvrez ce produit dans notre boutique.' }}
                </p>

                {{-- CARTE D'ACHAT --}}
                <div class="np-purchase-card">

                    <div class="np-price-heading">
                        Prix de vente
                    </div>

                    <div class="np-price">
                        {{ number_format($price, 0, ',', ' ') }}
                        <span>{{ $currency }}</span>
                    </div>

                    <div class="np-divider"></div>

                    <div class="np-availability">

                        @if($available)
                            <span class="np-stock np-stock-available">
                                <span class="np-status-dot"></span>
                                En stock
                            </span>

                            <span class="np-stock-count">
                                {{ $stock }} unité(s) disponible(s)
                            </span>
                        @else
                            <span class="np-stock np-stock-unavailable">
                                <span class="np-status-dot"></span>
                                Rupture de stock
                            </span>
                        @endif

                    </div>

                    <div class="np-reference">
                        <span>Référence du produit</span>
                        <strong>{{ $product?->sku ?: 'Non renseignée' }}</strong>
                    </div>

                    <div class="np-divider"></div>

                    {{-- NOTIFICATIONS --}}
                    @if(session('success'))
                        <div class="np-alert np-alert-success" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="np-alert np-alert-error" role="alert">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- PANIER --}}
                    @if($available)

                        <form
                            action="{{ route('storefront.cart.store', $storeRoute) }}"
                            method="POST"
                            class="np-cart-form"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="ecommerce_product_id"
                                value="{{ $ecommerceProduct->id }}"
                            >

                            <label class="np-quantity-label" for="cart-quantity">
                                Quantité souhaitée
                            </label>

                            <div class="np-quantity-row">

                                <div class="store-quantity-control np-quantity-control">

                                    <button
                                        type="button"
                                        class="store-quantity-minus"
                                        aria-label="Diminuer la quantité"
                                    >−</button>

                                    <input
                                        type="number"
                                        id="cart-quantity"
                                        name="quantity"
                                        value="{{ old('quantity', 1) }}"
                                        min="1"
                                        max="{{ $stock }}"
                                        inputmode="numeric"
                                        required
                                    >

                                    <button
                                        type="button"
                                        class="store-quantity-plus"
                                        aria-label="Augmenter la quantité"
                                    >+</button>

                                </div>

                                <span class="np-quantity-hint">
                                    Maximum : {{ $stock }}
                                </span>

                            </div>

                            <button type="submit" class="np-add-cart">
                                <span aria-hidden="true">🛒</span>
                                Ajouter au panier
                                <span aria-hidden="true">→</span>
                            </button>

                        </form>

                    @else

                        <button class="np-add-cart" type="button" disabled>
                            Produit indisponible
                        </button>

                    @endif

                    <a
                        href="{{ route('storefront.cart.index', $storeRoute) }}"
                        class="np-view-cart"
                    >
                        Voir mon panier
                        <span aria-hidden="true">→</span>
                    </a>

                </div>

                <div class="np-assurance">
                    <span aria-hidden="true">✓</span>
                    Commande depuis notre boutique en ligne
                </div>

            </div>

        </div>

        {{-- DESCRIPTION --}}
        <section class="np-description-section">

            <div class="np-section-heading">
                <span class="np-section-eyebrow">INFORMATIONS</span>
                <h2>Description du produit</h2>
            </div>

            <div class="np-description-card">
                <p>{{ $ecommerceProduct->description
                    ?: $product?->description
                    ?: 'Aucune description disponible.' }}</p>
            </div>

        </section>

        {{-- AVIS CLIENTS --}}
        <section class="np-reviews-section" id="avis-clients">
            <div class="np-section-heading">
                <span class="np-section-eyebrow">VOS RETOURS</span>
                <h2>Avis des clients</h2>
            </div>

            <div class="np-reviews-summary">
                @if($reviewCount > 0)
                    <strong>{{ number_format($averageRating, 1, ',', ' ') }}/5</strong>
                    <span aria-label="Note moyenne : {{ $averageRating }} sur 5">
                        @for($star = 1; $star <= 5; $star++)
                            <span class="{{ $star <= round($averageRating) ? 'np-star-active' : 'np-star-muted' }}">★</span>
                        @endfor
                    </span>
                    <span>{{ $reviewCount }} avis publié(s)</span>
                @else
                    <p>Aucun avis pour ce produit. Soyez le premier à donner votre opinion.</p>
                @endif
            </div>

            @if(session('review_success'))
                <div class="np-review-success" role="status">{{ session('review_success') }}</div>
            @endif

            @if($errors->any())
                <div class="np-review-error" role="alert">
                    <strong>Votre avis n'a pas été envoyé :</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="np-reviews-grid">
                <div class="np-reviews-list">
                    @forelse($reviews as $review)
                        <article class="np-review-card">
                            <div class="np-review-head">
                                <strong>{{ $review->customer_name }}</strong>
                                <time datetime="{{ $review->published_at?->toDateString() ?? $review->created_at?->toDateString() }}">
                                    {{ ($review->published_at ?? $review->created_at)?->format('d/m/Y') }}
                                </time>
                            </div>
                            <div class="np-review-stars" aria-label="{{ $review->rating }} étoiles sur 5">
                                @for($star = 1; $star <= 5; $star++)
                                    <span class="{{ $star <= $review->rating ? 'np-star-active' : 'np-star-muted' }}">★</span>
                                @endfor
                            </div>
                            @if($review->title)<h3>{{ $review->title }}</h3>@endif
                            <p>{{ $review->comment }}</p>
                        </article>
                    @empty
                        <p class="np-review-empty">Les avis approuvés apparaîtront ici.</p>
                    @endforelse
                    {{ $reviews->fragment('avis-clients')->links() }}
                </div>

                <div class="np-review-form-card">
                    <h3>Donner mon avis</h3>
                    <p>Votre avis sera publié après validation par la boutique.</p>
                    <form method="POST" action="{{ route('storefront.reviews.store', ['company' => $company->slug, 'slug' => $ecommerceProduct->slug]) }}">
                        @csrf
                        <label for="review-name">Votre nom *</label>
                        <input id="review-name" type="text" name="customer_name" value="{{ old('customer_name') }}" maxlength="100" required>

                        <label for="review-email">E-mail (facultatif)</label>
                        <input id="review-email" type="email" name="customer_email" value="{{ old('customer_email') }}" maxlength="100">

                        <label for="review-rating">Votre note *</label>
                        <select id="review-rating" name="rating" required>
                            <option value="">Choisir une note</option>
                            @for($star = 5; $star >= 1; $star--)
                                <option value="{{ $star }}" @selected(old('rating') == $star)>{{ $star }} / 5</option>
                            @endfor
                        </select>

                        <label for="review-title">Titre (facultatif)</label>
                        <input id="review-title" type="text" name="title" value="{{ old('title') }}" maxlength="100">

                        <label for="review-comment">Votre commentaire *</label>
                        <textarea id="review-comment" name="comment" rows="5" minlength="10" maxlength="3000" required>{{ old('comment') }}</textarea>

                        <button type="submit">Envoyer mon avis →</button>
                    </form>
                </div>
            </div>
        </section>

        {{-- PRODUITS SIMILAIRES --}}
        @if($relatedProducts->isNotEmpty())

            <section class="np-related-section">

                <div class="np-related-heading">

                    <div>
                        <span class="np-section-eyebrow">
                            VOUS AIMEREZ AUSSI
                        </span>

                        <h2>Produits similaires</h2>
                    </div>

                    <a href="{{ route('storefront.products.index', $storeRoute) }}">
                        Voir tous les produits →
                    </a>

                </div>

                <div class="np-related-grid">

                    @foreach($relatedProducts as $related)

                        @php
                            $relatedProduct = $related->product;

                            $relatedPrice = $related->sale_price !== null
                                && (float) $related->sale_price > 0
                                ? (float) $related->sale_price
                                : (float) ($relatedProduct?->selling_price ?? 0);

                            $relatedImage = $related->primaryImage?->image_path;
                        @endphp

                        @if($relatedProduct)

                            <article class="np-related-card">

                                <a href="{{ route('storefront.products.show', [
                                    'company' => $company->slug,
                                    'slug' => $related->slug
                                ]) }}">

                                    <div class="np-related-image">

                                        @if($relatedImage)
                                            <img
                                                src="{{ asset('storage/' . $relatedImage) }}"
                                                alt="{{ $relatedProduct->name }}"
                                                loading="lazy"
                                            >
                                        @else
                                            <span aria-hidden="true">📦</span>
                                        @endif

                                    </div>

                                    <div class="np-related-info">

                                        <h3>{{ $relatedProduct->name }}</h3>

                                        <strong>
                                            {{ number_format($relatedPrice, 0, ',', ' ') }}
                                            {{ $currency }}
                                        </strong>

                                        <span>Voir le produit →</span>

                                    </div>

                                </a>

                            </article>

                        @endif

                    @endforeach

                </div>

            </section>

        @endif

    </div>

</div>

@endsection
