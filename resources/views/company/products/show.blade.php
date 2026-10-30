@extends('layouts.company')

@section('title', $product->name)
@section('page-title', $product->name)

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                PRODUIT
            </span>

            <h1>{{ $product->name }}</h1>

            <p>
                Référence : {{ $product->sku }}
            </p>

        </div>

        <div class="module-header-actions">

            <a
                href="{{ route('company.products.edit', $product->id) }}"
                class="module-btn module-btn-primary"
            >
                Modifier
            </a>

            <a
                href="{{ route('company.products.index') }}"
                class="module-btn module-btn-light"
            >
                ← Retour
            </a>

        </div>

    </div>


    <div class="module-card">

        <div class="module-card-header">

            <div>
                <h2>Informations du produit</h2>
            </div>

        </div>


        <div class="details-grid">

            <div>
                <span>Nom</span>
                <strong>{{ $product->name }}</strong>
            </div>

            <div>
                <span>SKU</span>
                <strong>{{ $product->sku }}</strong>
            </div>

            <div>
                <span>Code-barres</span>
                <strong>{{ $product->barcode ?? '—' }}</strong>
            </div>

            <div>
                <span>Catégorie</span>
                <strong>
                    {{ $product->category?->name ?? 'Sans catégorie' }}
                </strong>
            </div>

            <div>
                <span>Prix d'achat</span>
                <strong>
                    {{ number_format($product->purchase_price, 0, ',', ' ') }}
                    FCFA
                </strong>
            </div>

            <div>
                <span>Prix de vente</span>
                <strong>
                    {{ number_format($product->selling_price, 0, ',', ' ') }}
                    FCFA
                </strong>
            </div>

            <div>
                <span>Stock actuel</span>
                <strong>
                    {{ $product->stock_quantity }}
                </strong>
            </div>

            <div>
                <span>Seuil d'alerte</span>
                <strong>
                    {{ $product->min_stock }}
                </strong>
            </div>

            <div>
                <span>Statut</span>

                @if($product->is_active)

                    <strong class="status-active">
                        Actif
                    </strong>

                @else

                    <strong class="status-inactive">
                        Inactif
                    </strong>

                @endif

            </div>

        </div>

    </div>


    <div class="module-card">

        <div class="module-card-header">
            <h2>Description</h2>
        </div>

        <div class="product-description">

            @if($product->description)

                {!! nl2br(e($product->description)) !!}

            @else

                <span>
                    Aucune description.
                </span>

            @endif

        </div>

    </div>

</div>

@endsection