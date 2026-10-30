
@extends('layouts.storefront')

@section('title', ($settings->store_name ?: $company->name) . ' — Boutique')

@section('content')

@php
    $storeName = $settings->store_name ?: $company->name;

    $storeDescription = $settings->description
        ?: 'Découvrez notre sélection de produits et nos collections.';

    $storeRoute = ['company' => $company->slug];
@endphp

<div class="store-home">

    {{-- ============================================
        HERO - BANNIERE PRINCIPALE
    ============================================ --}}
    <section class="store-hero">

        <div class="store-hero-decoration store-hero-circle-1"></div>
        <div class="store-hero-decoration store-hero-circle-2"></div>

        <div class="store-container">

            <div class="store-hero-layout">

                {{-- CONTENU PRINCIPAL --}}
                <div class="store-hero-content">

                    <div class="store-hero-badge">
                        <span class="store-hero-badge-dot"></span>
                        Votre boutique en ligne
                    </div>

                    <h1>
                        Bienvenue chez
                        <span>{{ $storeName }}</span>
                    </h1>

                    <p class="store-hero-description">
                        {{ $storeDescription }}
                    </p>

                    <div class="store-hero-actions">

                        <a href="{{ route('storefront.products.index', $storeRoute) }}"
                           class="store-button store-button-primary">

                            <span>Découvrir nos produits</span>
                            <span aria-hidden="true">→</span>

                        </a>

                        <a href="{{ route('storefront.collections.index', $storeRoute) }}"
                           class="store-button store-button-outline">

                            Explorer les collections

                        </a>

                    </div>

                    <div class="store-hero-highlights">

                        <div class="store-hero-highlight">
                            <span class="store-highlight-icon">✓</span>
                            <span>Produits sélectionnés</span>
                        </div>

                        <div class="store-hero-highlight">
                            <span class="store-highlight-icon">✓</span>
                            <span>Achat simplifié</span>
                        </div>

                    </div>

                </div>


                {{-- CARTE VISUELLE --}}
                <div class="store-hero-visual">

                    <div class="store-hero-showcase">

                        <div class="store-showcase-top">
                            <span class="store-showcase-label">
                                NOTRE UNIVERS
                            </span>

                            <span class="store-showcase-star">
                                ✦
                            </span>
                        </div>

                        <div class="store-showcase-center">

                            <div class="store-showcase-symbol">
                                🛍️
                            </div>

                            <h2>{{ $storeName }}</h2>

                            <p>
                                Découvrez notre univers
                                et trouvez vos prochains coups de cœur.
                            </p>

                        </div>

                        <div class="store-showcase-bottom">

                            <span class="store-showcase-indicator">
                                ● Collection
                            </span>

                            <span>Découvrir ↗</span>

                        </div>

                    </div>

                    <div class="store-hero-floating">
                        <span>✦</span>
                        Des découvertes à chaque visite
                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================
        AVANTAGES BOUTIQUE
    ============================================ --}}
    <section class="store-benefits">

        <div class="store-container">

            <div class="store-benefits-grid">

                <div class="store-benefit-item">

                    <div class="store-benefit-icon">
                        ✦
                    </div>

                    <div>
                        <h3>Notre sélection</h3>
                        <p>Des produits à découvrir</p>
                    </div>

                </div>

                <div class="store-benefit-item">

                    <div class="store-benefit-icon">
                        🛒
                    </div>

                    <div>
                        <h3>Achat facile</h3>
                        <p>Une expérience simplifiée</p>
                    </div>

                </div>

                <div class="store-benefit-item">

                    <div class="store-benefit-icon">
                        ♡
                    </div>

                    <div>
                        <h3>À votre écoute</h3>
                        <p>Un service à votre disposition</p>
                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================
        COLLECTIONS A LA UNE
    ============================================ --}}
    <section class="store-section store-featured-section">

        <div class="store-container">

            <div class="store-section-heading">

                <div class="store-section-heading-content">

                    <span class="store-section-tag">
                        NOS COLLECTIONS
                    </span>

                    <h2>
                        Collections
                        <span>à la une</span>
                    </h2>

                    <p>
                        Explorez nos différentes collections
                        et trouvez les produits qui vous correspondent.
                    </p>

                </div>

                <a href="{{ route('storefront.collections.index', $storeRoute) }}"
                   class="store-section-more">

                    Toutes les collections
                    <span aria-hidden="true">→</span>

                </a>

            </div>


            @if($collections->isNotEmpty())

                <div class="store-grid store-featured-grid">

                    @foreach($collections as $collection)

                        <a class="store-card store-featured-card"
                           href="{{ route('storefront.collections.show', [
                               'company' => $company->slug,
                               'slug' => $collection->slug
                           ]) }}">

                            {{-- IMAGE --}}
                            <div class="store-card-media">

                                @if($collection->image)

                                    <img
                                        src="{{ asset('storage/' . $collection->image) }}"
                                        alt="{{ $collection->name }}"
                                        loading="lazy"
                                    >

                                @else

                                    <div class="store-placeholder">
                                        <span aria-hidden="true">✦</span>
                                    </div>

                                @endif

                                <span class="store-card-badge">
                                    Collection
                                </span>

                                <span class="store-card-arrow"
                                      aria-hidden="true">
                                    ↗
                                </span>

                            </div>


                            {{-- INFORMATIONS --}}
                            <div class="store-card-body">

                                <div class="store-card-category">
                                    COLLECTION À DÉCOUVRIR
                                </div>

                                <h3>
                                    {{ $collection->name }}
                                </h3>

                                <p>
                                    {{ \Illuminate\Support\Str::limit(
                                        strip_tags($collection->description ?? ''),
                                        110
                                    ) ?: 'Découvrez les produits de cette collection.' }}
                                </p>

                                <div class="store-card-bottom">

                                    <span class="store-card-explore">
                                        Explorer la collection
                                    </span>

                                    <span class="store-card-next"
                                          aria-hidden="true">
                                        →
                                    </span>

                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>

            @else

                {{-- ETAT VIDE --}}
                <div class="store-empty store-featured-empty">

                    <div class="store-empty-icon">
                        ✦
                    </div>

                    <h3>
                        De belles collections arrivent bientôt !
                    </h3>

                    <p>
                        Nous préparons de nouvelles sélections.
                        Revenez prochainement pour les découvrir.
                    </p>

                    <a href="{{ route('storefront.products.index', $storeRoute) }}"
                       class="store-empty-button">

                        Découvrir nos produits
                        <span aria-hidden="true">→</span>

                    </a>

                </div>

            @endif

        </div>

    </section>

    
{{-- AVIS CLIENTS --}}
@if($reviews->isNotEmpty())
<section class="store-reviews-section">
    <div class="store-container">

        <div class="store-section-heading">
            <div class="store-section-heading-content">
                <span class="store-section-tag">
                    TÉMOIGNAGES
                </span>

                <h2>
                    Ce que disent <span>nos clients</span>
                </h2>

                <p>
                    Découvrez les expériences partagées
                    par les clients de notre boutique.
                </p>
            </div>
        </div>

        <div class="store-reviews-grid">
            @foreach($reviews as $review)
                <article class="store-review-card">

                    <div class="store-review-stars"
                         aria-label="{{ $review->rating }} étoiles sur 5">
                        @for($i = 1; $i <= 5; $i++)
                            <span>
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            </span>
                        @endfor
                    </div>

                    @if($review->title)
                        <h3>{{ $review->title }}</h3>
                    @endif

                    <p class="store-review-comment">
                        {{ $review->comment }}
                    </p>

                    <div class="store-review-footer">
                        <span class="store-review-avatar">
                            {{ mb_strtoupper(
                                mb_substr($review->customer_name, 0, 1)
                            ) }}
                        </span>

                        <div>
                            <strong>
                                {{ $review->customer_name }}
                            </strong>

                            <small>
                                {{ $review->published_at?->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>

                </article>
            @endforeach
        </div>

    </div>
</section>
@endif



    {{-- ============================================
        BANNIERE PROMOTIONNELLE
    ============================================ --}}
    <section class="store-discover-section">

        <div class="store-container">

            <div class="store-discover-banner">

                <div class="store-discover-content">

                    <span class="store-discover-tag">
                        ENVIE DE DÉCOUVRIR ?
                    </span>

                    <h2>
                        Trouvez votre prochain
                        coup de cœur.
                    </h2>

                    <p>
                        Parcourez notre catalogue et découvrez
                        les produits disponibles dans notre boutique.
                    </p>

                    <a href="{{ route('storefront.products.index', $storeRoute) }}"
                       class="store-discover-button">

                        Voir tous les produits
                        <span aria-hidden="true">→</span>

                    </a>

                </div>

                <div class="store-discover-visual"
                     aria-hidden="true">
                    🛍️
                </div>

            </div>

        </div>

    </section>

</div>

@endsection
