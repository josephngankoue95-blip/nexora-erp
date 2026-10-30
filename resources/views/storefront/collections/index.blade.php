
@extends('layouts.storefront')

@section('title', 'Collections — ' . ($settings->store_name ?: $company->name))

@section('content')

<div class="nc-page">

    {{-- ============================================
         BANNIERE PRINCIPALE
    ============================================ --}}

    <section class="nc-hero">

        <div class="store-container">

            <nav class="nc-breadcrumb nc-breadcrumb-light" aria-label="Fil d'Ariane">
                <a href="{{ route('storefront.index', ['company' => $company->slug]) }}">
                    Accueil
                </a>
                <span>/</span>
                <span aria-current="page">Collections</span>
            </nav>

            <div class="nc-hero-content">

                <span class="nc-eyebrow">
                    ✦ NOTRE SELECTION
                </span>

                <h1>
                    Explorez nos
                    <span>collections</span>
                </h1>

                <p>
                    Découvrez nos différents univers et trouvez les
                    produits qui correspondent à vos envies.
                </p>

                <a href="{{ route('storefront.products.index', ['company' => $company->slug]) }}"
                   class="nc-hero-button">
                    Voir tous les produits
                    <span aria-hidden="true">→</span>
                </a>

            </div>

        </div>

        <div class="nc-hero-decoration" aria-hidden="true"></div>

    </section>


    {{-- ============================================
         LISTE DES COLLECTIONS
    ============================================ --}}

    <section class="nc-section">

        <div class="store-container">

            <div class="nc-section-header">

                <div>
                    <span class="nc-section-label">
                        DECOUVREZ NOS UNIVERS
                    </span>

                    <h2>Toutes les collections</h2>

                    <p>
                        Parcourez nos sélections et découvrez
                        les articles disponibles dans notre boutique.
                    </p>
                </div>

                <div class="nc-count-badge">
                    {{ $collections->total() }} collection(s)
                </div>

            </div>


            @if($collections->count())

                <div class="nc-collections-grid">

                    @foreach($collections as $collection)

                        <a class="nc-collection-card"
                           href="{{ route('storefront.collections.show', [
                               'company' => $company->slug,
                               'slug' => $collection->slug
                           ]) }}">

                            <div class="nc-collection-image">

                                @if($collection->image)

                                    <img
                                        src="{{ asset('storage/' . $collection->image) }}"
                                        alt="{{ $collection->name }}"
                                        loading="lazy"
                                    >

                                @else

                                    <div class="nc-image-placeholder">
                                        <span aria-hidden="true">✦</span>
                                    </div>

                                @endif

                                <span class="nc-image-badge">
                                    Collection
                                </span>

                                <span class="nc-image-arrow" aria-hidden="true">
                                    ↗
                                </span>

                            </div>

                            <div class="nc-collection-body">

                                <div class="nc-collection-meta">

                                    <span class="nc-product-count">
                                        {{ $collection->published_products_count ?? 0 }}
                                        produit(s)
                                    </span>

                                </div>

                                <h3>{{ $collection->name }}</h3>

                                <p>
                                    {{ \Illuminate\Support\Str::limit(
                                        strip_tags($collection->description ?? ''),
                                        115
                                    ) ?: 'Découvrez les produits de cette collection.' }}
                                </p>

                                <div class="nc-card-footer">

                                    <span>Explorer la collection</span>

                                    <span class="nc-card-next" aria-hidden="true">
                                        →
                                    </span>

                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>

                @if($collections->hasPages())

                    <div class="nc-pagination">
                        {{ $collections->links() }}
                    </div>

                @endif

            @else

                <div class="nc-empty">

                    <div class="nc-empty-icon" aria-hidden="true">
                        ✦
                    </div>

                    <h3>Aucune collection disponible</h3>

                    <p>
                        Les collections apparaîtront ici dès leur publication.
                        Vous pouvez déjà consulter notre catalogue.
                    </p>

                    <a href="{{ route('storefront.products.index', [
                        'company' => $company->slug
                    ]) }}" class="nc-primary-button">

                        Voir les produits
                        <span aria-hidden="true">→</span>

                    </a>

                </div>

            @endif

        </div>

    </section>

</div>

@endsection
