
@extends('layouts.storefront')

@section('title', 'Mon panier — ' . ($settings->store_name ?: $company->name))

@section('content')

@php
    $storeRoute = ['company' => $company->slug];
    $currency = $settings->currency ?: 'FCFA';
@endphp

<div class="nx-cart-page">

    <div class="store-container">

        {{-- ==========================================
             FIL D'ARIANE
        ========================================== --}}

        <nav class="nx-cart-breadcrumb" aria-label="Fil d'Ariane">

            <a href="{{ route('storefront.index', $storeRoute) }}">
                Accueil
            </a>

            <span>/</span>

            <span aria-current="page">Mon panier</span>

        </nav>


        {{-- ==========================================
             EN-TETE
        ========================================== --}}

        <div class="nx-cart-heading">

            <div>

                <span class="nx-cart-eyebrow">
                    VOTRE SELECTION
                </span>

                <h1>Mon panier</h1>

                <p>
                    Vérifiez vos articles, ajustez les quantités
                    et finalisez votre commande.
                </p>

            </div>

            <a href="{{ route('storefront.products.index', $storeRoute) }}"
               class="nx-cart-back">

                ← Continuer mes achats

            </a>

        </div>


        {{-- ==========================================
             NOTIFICATIONS
        ========================================== --}}

        @if(session('success'))

            <div class="nx-cart-alert nx-cart-alert-success" role="status">

                <span aria-hidden="true">✓</span>

                <p>{{ session('success') }}</p>

            </div>

        @endif


        @if($errors->any())

            <div class="nx-cart-alert nx-cart-alert-error" role="alert">

                <span aria-hidden="true">!</span>

                <div>

                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach

                </div>

            </div>

        @endif


        {{-- ==========================================
             PANIER NON VIDE
        ========================================== --}}

        @if($items->isNotEmpty())

            <div class="nx-cart-layout">

                {{-- ==================================
                     ARTICLES
                ================================== --}}

                <section class="nx-cart-products">

                    <div class="nx-cart-products-header">

                        <h2>Articles sélectionnés</h2>

                        <span>
                            {{ $items->count() }} ligne(s)
                        </span>

                    </div>


                    @foreach($items as $item)

                        @php
                            $entry = $item['ecommerce_product'];
                            $product = $entry->product;

                            $unitPrice = $entry->sale_price !== null
                                && (float) $entry->sale_price > 0
                                ? (float) $entry->sale_price
                                : (float) ($product?->selling_price ?? 0);

                            $quantity = (int) $item['quantity'];

                            $lineSubtotal = $unitPrice * $quantity;

                            $image = $entry->primaryImage?->image_path;
                        @endphp

                        <article class="nx-cart-item">

                            {{-- IMAGE PRODUIT --}}
                            <a
                                href="{{ route('storefront.products.show', [
                                    'company' => $company->slug,
                                    'slug' => $entry->slug
                                ]) }}"
                                class="nx-cart-image"
                                aria-label="Voir le produit"
                            >

                                @if($image)

                                    <img
                                        src="{{ asset('storage/' . $image) }}"
                                        alt="{{ $product?->name ?? 'Produit' }}"
                                        loading="lazy"
                                    >

                                @else

                                    <span class="nx-cart-image-placeholder" aria-hidden="true">
                                        📦
                                    </span>

                                @endif

                            </a>


                            {{-- INFORMATIONS --}}
                            <div class="nx-cart-item-content">

                                <a
                                    href="{{ route('storefront.products.show', [
                                        'company' => $company->slug,
                                        'slug' => $entry->slug
                                    ]) }}"
                                    class="nx-cart-product-name"
                                >
                                    {{ $entry->seo_title ?: ($product?->name ?? $entry->slug) }}
                                </a>

                                <div class="nx-cart-item-prices">

                                    <div class="nx-cart-price-row">

                                        <span>Prix unitaire</span>

                                        <strong>
                                            {{ number_format($unitPrice, 0, ',', ' ') }}
                                            {{ $currency }}
                                        </strong>

                                    </div>

                                    <div class="nx-cart-price-row">

                                        <span>Sous-total</span>

                                        <strong class="nx-cart-line-total">
                                            {{ number_format($lineSubtotal, 0, ',', ' ') }}
                                            {{ $currency }}
                                        </strong>

                                    </div>

                                </div>


                                {{-- ACTIONS --}}
                                <div class="nx-cart-item-actions">

                                    {{-- MODIFIER QUANTITE --}}
                                    <form
                                        method="POST"
                                        action="{{ route('storefront.cart.update', [
                                            'company' => $company->slug,
                                            'productId' => $entry->id
                                        ]) }}"
                                        class="nx-cart-update-form"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <label for="cart-qty-{{ $entry->id }}">
                                            Quantité
                                        </label>

                                        <input
                                            id="cart-qty-{{ $entry->id }}"
                                            type="number"
                                            name="quantity"
                                            min="1"
                                            max="100"
                                            value="{{ $quantity }}"
                                            required
                                        >

                                        <button
                                            type="submit"
                                            class="nx-cart-update-btn"
                                        >
                                            Actualiser
                                        </button>

                                    </form>


                                    {{-- SUPPRIMER ARTICLE --}}
                                    <form
                                        method="POST"
                                        action="{{ route('storefront.cart.destroy', [
                                            'company' => $company->slug,
                                            'productId' => $entry->id
                                        ]) }}"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="nx-cart-remove-btn"
                                            aria-label="Supprimer cet article du panier"
                                        >
                                            <span aria-hidden="true">×</span>
                                            Supprimer
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </section>


                {{-- ==================================
                     RESUME DE COMMANDE
                ================================== --}}

                <aside class="nx-cart-sidebar">

                    <div class="nx-cart-summary">

                        <div class="nx-cart-summary-heading">

                            <h2>Résumé du panier</h2>

                            <span>
                                {{ $items->count() }} article(s)
                            </span>

                        </div>


                        <div class="nx-cart-summary-lines">

                            <div class="nx-cart-summary-row">

                                <span>Sous-total</span>

                                <strong>
                                    {{ number_format((float) $total, 0, ',', ' ') }}
                                    {{ $currency }}
                                </strong>

                            </div>

                            <div class="nx-cart-summary-row">

                                <span>Livraison</span>

                                <span class="nx-cart-muted">
                                    À confirmer
                                </span>

                            </div>

                            <div class="nx-cart-summary-row">

                                <span>Taxes</span>

                                <span class="nx-cart-muted">
                                    Non calculées
                                </span>

                            </div>

                        </div>


                        <div class="nx-cart-total">

                            <span>Total provisoire</span>

                            <strong>
                                {{ number_format((float) $total, 0, ',', ' ') }}
                                <small>{{ $currency }}</small>
                            </strong>

                        </div>


                        <p class="nx-cart-summary-note">
                            Les prix et disponibilités seront revérifiés
                            lors de la confirmation de votre commande.
                            Les éventuels frais de livraison ne sont
                            pas inclus.
                        </p>


                        {{-- PASSER COMMANDE --}}
                        <a
                            href="{{ route('storefront.checkout.index', $storeRoute) }}"
                            class="nx-cart-checkout-btn"
                        >
                            Passer commande
                            <span aria-hidden="true">→</span>
                        </a>


                        <div class="nx-cart-summary-assurance">

                            <span aria-hidden="true">✓</span>

                            Commande enregistrée avant paiement

                        </div>


                        {{-- VIDER PANIER --}}
                        <form
                            method="POST"
                            action="{{ route('storefront.cart.clear', $storeRoute) }}"
                            class="nx-cart-clear-form"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="nx-cart-clear-btn"
                                onclick="return confirm('Voulez-vous vraiment vider votre panier ?')"
                            >
                                Vider le panier
                            </button>

                        </form>

                    </div>

                </aside>

            </div>

        @else

            {{-- ==========================================
                 PANIER VIDE
            ========================================== --}}

            <div class="nx-cart-empty">

                <div class="nx-cart-empty-icon" aria-hidden="true">
                    🛒
                </div>

                <h2>Votre panier est vide</h2>

                <p>
                    Vous n'avez pas encore ajouté de produits.
                    Explorez notre catalogue pour découvrir
                    les articles disponibles.
                </p>

                <a
                    href="{{ route('storefront.products.index', $storeRoute) }}"
                    class="nx-cart-empty-btn"
                >
                    Découvrir nos produits
                    <span aria-hidden="true">→</span>
                </a>

            </div>

        @endif

    </div>

</div>

@endsection
