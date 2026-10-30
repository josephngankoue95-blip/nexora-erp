@extends('layouts.ecommerce')
@section('title', 'Nouvelle collection')
@section('page-title', 'Nouvelle collection')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/ecommerce-collections.css') }}">
@endpush
@section('content')
<div class="ecol-page">
    <header class="ecol-header"><div><h1>Créer une collection</h1><p>Définissez les informations et choisissez les produits à regrouper.</p></div>
        <a class="ecol-btn ecol-btn-outline" href="{{ route('company.ecommerce.collections.index') }}">← Retour</a>
    </header>
    @if($errors->any())<div class="ecol-alert ecol-alert-error"><strong>Veuillez corriger les erreurs :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('company.ecommerce.collections.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="ecol-form-layout"><main class="ecol-form-main">
            <section class="ecol-panel ecol-pad"><h2>Informations générales</h2>
                <div class="ecol-field"><label for="name">Nom de la collection <span>*</span></label><input class="ecol-input" id="name" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="Ex. : Offres de la semaine"></div>
                <div class="ecol-field"><label for="description">Description</label><textarea class="ecol-input" id="description" name="description" rows="5" placeholder="Décrivez cette sélection...">{{ old('description') }}</textarea></div>
                <div class="ecol-field"><label for="image">Image de couverture</label><input class="ecol-input" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG ou WEBP — 2 Mo maximum.</small></div>
            </section>
            <section class="ecol-panel ecol-pad"><div class="ecol-section-head"><div><h2>Produits de la collection</h2><p>Vous pouvez sélectionner plusieurs produits E-commerce.</p></div><span id="selectedCount" class="ecol-counter">0 sélectionné(s)</span></div>
                <div class="ecol-product-toolbar"><input type="search" id="productSearch" class="ecol-input" placeholder="Rechercher un produit par nom ou référence..."><button type="button" class="ecol-btn ecol-btn-outline" id="selectVisible">Tout sélectionner (visibles)</button><button type="button" class="ecol-btn ecol-btn-outline" id="clearProducts">Tout désélectionner</button></div>
                <div class="ecol-product-list" id="productList">
                    @forelse($products as $ecommerceProduct)
                        <label class="ecol-product-item" data-search="{{ mb_strtolower(($ecommerceProduct->product?->name ?? '') . ' ' . ($ecommerceProduct->product?->sku ?? '')) }}">
                            <input type="checkbox" name="products[]" value="{{ $ecommerceProduct->id }}" @checked(in_array($ecommerceProduct->id, old('products', [])))>
                            <span><strong>{{ $ecommerceProduct->product?->name ?? 'Produit ERP indisponible' }}</strong><small>Réf. {{ $ecommerceProduct->product?->sku ?? '—' }} · {{ number_format((float) $ecommerceProduct->sale_price, 0, ',', ' ') }} FCFA</small></span>
                            <span class="ecol-pill {{ $ecommerceProduct->is_published ? 'ecol-pill-green' : 'ecol-pill-gray' }}">{{ $ecommerceProduct->is_published ? 'Publié' : 'Brouillon' }}</span>
                        </label>
                    @empty <div class="ecol-empty">Aucun produit E-commerce disponible pour cette entreprise.</div>
                    @endforelse
                </div>
            </section>
            <section class="ecol-panel ecol-pad"><h2>Référencement SEO</h2>
                <div class="ecol-field"><label for="seo_title">Titre SEO</label><input class="ecol-input" id="seo_title" name="seo_title" maxlength="255" value="{{ old('seo_title') }}"></div>
                <div class="ecol-field"><label for="seo_description">Description SEO</label><textarea class="ecol-input" id="seo_description" name="seo_description" maxlength="2000" rows="4">{{ old('seo_description') }}</textarea></div>
            </section>
        </main><aside class="ecol-form-side">
            <section class="ecol-panel ecol-pad"><h2>Visibilité</h2>
                <input type="hidden" name="is_active" value="0"><label class="ecol-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', 1))> Collection active</label>
                <input type="hidden" name="is_featured" value="0"><label class="ecol-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', 0))> Mettre à la une</label>
                <div class="ecol-field"><label for="sort_order">Ordre d'affichage</label><input class="ecol-input" type="number" min="0" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"></div>
            </section>
            <div class="ecol-submit"><button class="ecol-btn ecol-btn-primary" type="submit">Enregistrer la collection</button><a class="ecol-btn ecol-btn-outline" href="{{ route('company.ecommerce.collections.index') }}">Annuler</a></div>
        </aside></div>
    </form>
</div>
@include('company.ecommerce.collections._product_script')
@endsection
