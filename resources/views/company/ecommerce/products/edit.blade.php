

@extends('layouts.ecommerce')



@section('title', 'Modifier le produit e-commerce')

@section('page-title', 'Modifier le produit')



@section('content')



@php

    $product = $ecommerceProduct->product;

@endphp



<div class="ecommerce-product-form-page">



    {{-- HEADER --}}

    <div class="product-form-header">

        <div>

            <div class="product-form-breadcrumb">

                <a href="{{ route('company.ecommerce.index') }}">E-commerce</a>

                <span>/</span>

                <a href="{{ route('company.ecommerce.products.index') }}">Catalogue</a>

                <span>/</span>

                <span>Modifier</span>

            </div>



            <h1>Modifier le produit e-commerce</h1>

            <p>

                Modifiez la présentation, la visibilité et les informations SEO du produit.

            </p>

        </div>



        <a href="{{ route('company.ecommerce.products.index') }}"

           class="product-form-back">

            ← Retour au catalogue

        </a>

    </div>



    {{-- ERREURS --}}

    @if($errors->any())

        <div class="product-form-alert product-form-alert-error">

            <strong>Vérifiez les informations saisies.</strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif



    @if(session('success'))

        <div class="product-form-alert product-form-alert-success">

            {{ session('success') }}

        </div>

    @endif



    @if(!$product)

        <div class="product-form-alert product-form-alert-error">

            Le produit ERP associé à cette fiche n'existe plus.

            Vous pouvez revenir au catalogue pour vérifier les produits disponibles.

        </div>

    @endif



    <form method="POST"

          action="{{ route('company.ecommerce.products.update', $ecommerceProduct) }}"

          id="ecommerceProductEditForm" enctype="multipart/form-data">



        @csrf

        @method('PUT')



        <div class="product-form-layout">



            {{-- COLONNE PRINCIPALE --}}

            <div class="product-form-main">



                {{-- INFORMATIONS ERP --}}

                <section class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Produit ERP</h2>

                            <p>

                                Informations provenant du catalogue principal de votre ERP.

                            </p>

                        </div>

                        <span class="product-form-card-number">01</span>

                    </div>



                    @if($product)

                        <div class="edit-product-summary">

                            <div class="edit-product-image">📦</div>



                            <div class="edit-product-info">

                                <strong>{{ $product->name }}</strong>



                                @if($product->sku)

                                    <span>Référence : {{ $product->sku }}</span>

                                @endif



                                <div class="edit-product-meta">

                                    <span>

                                        Prix de vente :

                                        <strong>

                                            {{ number_format(

                                                (float) $product->selling_price,

                                                0,

                                                ',',

                                                ' '

                                            ) }} FCFA

                                        </strong>

                                    </span>



                                    <span>

                                        Stock :

                                        <strong>{{ $product->stock_quantity }}</strong>

                                    </span>



                                    <span>

                                        Prix d'achat :

                                        <strong>

                                            {{ number_format(

                                                (float) $product->purchase_price,

                                                0,

                                                ',',

                                                ' '

                                            ) }} FCFA

                                        </strong>

                                    </span>

                                </div>

                            </div>

                        </div>

                    @else

                        <p class="product-form-empty">

                            Impossible d'afficher les informations du produit ERP.

                        </p>

                    @endif



                    <div class="edit-product-notice">

                        <span>ℹ</span>

                        <p>

                            Le nom, la référence, le prix d'achat, le prix de vente et

                            le stock sont gérés depuis le module Produits de votre ERP.

                            Ici, vous gérez la présentation et la publication dans la boutique.

                        </p>

                    </div>

                </section>





                {{-- CATEGORIE E-COMMERCE --}}

                <section class="product-form-card">



                    <div class="product-form-card-header">

                        <div>

                            <h2>Catégorie E-commerce</h2>

                            <p>

                                Définissez la catégorie du produit

                                dans votre boutique en ligne.

                            </p>

                        </div>

                    </div>



                    <div class="product-form-field">



                        <label for="ecommerce_category_id">

                            Catégorie du produit

                        </label>



                        <select

                            name="ecommerce_category_id"

                            id="ecommerce_category_id"

                            class="product-category-select"

                        >

                            <option value="">-- Sans catégorie --</option>



                            @foreach($categories as $category)

                                <option

                                    value="{{ $category->id }}"

                                    @selected(

                                        old(

                                            'ecommerce_category_id',

                                            $ecommerceProduct->ecommerce_category_id

                                        ) == $category->id

                                    )

                                >

                                    {{ $category->name }}

                                </option>

                            @endforeach



                        </select>



                        @error('ecommerce_category_id')

                            <small class="product-form-error">

                                {{ $message }}

                            </small>

                        @enderror



                    </div>



                </section>





                {{-- PRÉSENTATION --}}

                <section class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Présentation du produit</h2>

                            <p>Contenu affiché sur votre boutique en ligne.</p>

                        </div>

                        <span class="product-form-card-number">02</span>

                    </div>



                    <div class="product-form-field">

                        <label for="short_description">Description courte</label>



                        <textarea

                            name="short_description"

                            id="short_description"

                            rows="4"

                            maxlength="1000"

                            placeholder="Courte présentation du produit..."

                        >{{ old('short_description', $ecommerceProduct->short_description) }}</textarea>



                        <div class="product-form-counter">

                            <span id="shortDescriptionCount">0</span>/1000

                        </div>



                        @error('short_description')

                            <small class="product-form-error">{{ $message }}</small>

                        @enderror

                    </div>



                    <div class="product-form-field">

                        <label for="description">Description complète</label>



                        <textarea

                            name="description"

                            id="description"

                            rows="9"

                            placeholder="Description détaillée du produit..."

                        >{{ old('description', $ecommerceProduct->description) }}</textarea>



                        @error('description')

                            <small class="product-form-error">{{ $message }}</small>

                        @enderror

                    </div>

                </section>



                {{-- SEO --}}

                <section class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Référencement SEO</h2>

                            <p>Optimisez la présentation du produit dans les moteurs de recherche.</p>

                        </div>

                        <span class="product-form-card-number">03</span>

                    </div>



                    <div class="product-form-field">

                        <label for="seo_title">Titre SEO</label>



                        <input

                            type="text"

                            name="seo_title"

                            id="seo_title"

                            maxlength="255"

                            value="{{ old('seo_title', $ecommerceProduct->seo_title) }}"

                            placeholder="Titre SEO du produit..."

                        >



                        @error('seo_title')

                            <small class="product-form-error">{{ $message }}</small>

                        @enderror

                    </div>



                    <div class="product-form-field">

                        <label for="seo_description">Description SEO</label>



                        <textarea

                            name="seo_description"

                            id="seo_description"

                            rows="4"

                            maxlength="1000"

                            placeholder="Description utilisée par les moteurs de recherche..."

                        >{{ old('seo_description', $ecommerceProduct->seo_description) }}</textarea>



                        @error('seo_description')

                            <small class="product-form-error">{{ $message }}</small>

                        @enderror

                    </div>

                </section>



            </div>



            {{-- COLONNE LATÉRALE --}}

            <aside class="product-form-sidebar">



                {{-- PUBLICATION --}}

                <section class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Publication</h2>

                            <p>Visibilité dans la boutique</p>

                        </div>

                    </div>



                    <label class="product-toggle">

                        <input

                            type="checkbox"

                            name="is_published"

                            value="1"

                            @checked((bool) old(

                                'is_published',

                                $ecommerceProduct->is_published

                            ))

                        >



                        <span class="product-toggle-slider"></span>



                        <span class="product-toggle-content">

                            <strong>Produit publié</strong>

                            <small>Visible dans la boutique en ligne.</small>

                        </span>

                    </label>



                    <label class="product-toggle">

                        <input

                            type="checkbox"

                            name="is_featured"

                            value="1"

                            @checked((bool) old(

                                'is_featured',

                                $ecommerceProduct->is_featured

                            ))

                        >



                        <span class="product-toggle-slider"></span>



                        <span class="product-toggle-content">

                            <strong>Produit à la une</strong>

                            <small>Mettre ce produit en avant.</small>

                        </span>

                    </label>

                </section>



                {{-- INFORMATIONS --}}

                <section class="product-form-info-card">

                    <div class="product-form-info-title">Informations</div>



                    <div class="product-form-info-row">

                        <span>Statut actuel</span>

                        <strong>

                            {{ $ecommerceProduct->is_published ? 'Publié' : 'Non publié' }}

                        </strong>

                    </div>



                    <div class="product-form-info-row">

                        <span>À la une</span>

                        <strong>

                            {{ $ecommerceProduct->is_featured ? 'Oui' : 'Non' }}

                        </strong>

                    </div>



                    <div class="product-form-info-row">

                        <span>Ajouté le</span>

                        <strong>

                            {{ $ecommerceProduct->created_at?->format('d/m/Y') ?? '—' }}

                        </strong>

                    </div>



                    <div class="product-form-info-row">

                        <span>Dernière modification</span>

                        <strong>

                            {{ $ecommerceProduct->updated_at?->format('d/m/Y') ?? '—' }}

                        </strong>

                    </div>



                    @if($ecommerceProduct->published_at)

                        <div class="product-form-info-row">

                            <span>Publié le</span>

                            <strong>{{ $ecommerceProduct->published_at->format('d/m/Y') }}</strong>

                        </div>

                    @endif

                </section>



                @include('company.ecommerce.products.partials.images')



                {{-- ACTIONS --}}

                <div class="product-form-actions">

                    <button type="submit" class="product-form-submit">

                        Enregistrer les modifications

                    </button>



                    <a href="{{ route('company.ecommerce.products.index') }}"

                       class="product-form-cancel">

                        Annuler

                    </a>

                </div>



            </aside>

        </div>

    </form>

</div>



<style>

.ecommerce-product-form-page {

    --form-primary: #315bea;

    --form-primary-dark: #2448c9;

    --form-text: #172033;

    --form-muted: #64748b;

    --form-border: #e2e8f0;

    color: var(--form-text);

}



.product-form-header {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 25px;

    margin-bottom: 25px;

}



.product-form-breadcrumb {

    display: flex;

    flex-wrap: wrap;

    gap: 8px;

    margin-bottom: 9px;

    color: #94a3b8;

    font-size: 12px;

}



.product-form-breadcrumb a {

    color: var(--form-primary);

    text-decoration: none;

    font-weight: 700;

}



.product-form-header h1 {

    margin: 0;

    font-size: 27px;

    font-weight: 750;

}



.product-form-header p {

    margin: 8px 0 0;

    color: var(--form-muted);

    font-size: 14px;

    line-height: 1.5;

}



.product-form-back,

.product-form-cancel {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 42px;

    padding: 0 15px;

    border: 1px solid var(--form-border);

    border-radius: 9px;

    color: #475569;

    background: #fff;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

}



.product-form-alert {

    margin-bottom: 20px;

    padding: 14px 16px;

    border-radius: 10px;

    font-size: 13px;

}



.product-form-alert ul {

    margin: 8px 0 0 18px;

}



.product-form-alert-error {

    color: #991b1b;

    background: #fef2f2;

    border: 1px solid #fecaca;

}



.product-form-alert-success {

    color: #166534;

    background: #f0fdf4;

    border: 1px solid #bbf7d0;

}



.product-form-layout {

    display: grid;

    grid-template-columns: minmax(0, 1fr) 330px;

    gap: 20px;

    align-items: start;

}



.product-form-main,

.product-form-sidebar {

    display: flex;

    flex-direction: column;

    gap: 20px;

}



.product-form-sidebar {

    gap: 18px;

}



.product-form-card,

.product-form-info-card {

    padding: 22px;

    background: #fff;

    border: 1px solid var(--form-border);

    border-radius: 13px;

    box-shadow: 0 2px 8px rgba(15,23,42,.03);

}



.product-form-card-header {

    display: flex;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 22px;

}



.product-form-card-header h2 {

    margin: 0;

    font-size: 16px;

    font-weight: 750;

}



.product-form-card-header p {

    margin: 5px 0 0;

    color: var(--form-muted);

    font-size: 12px;

    line-height: 1.5;

}



.product-form-card-number {

    color: var(--form-primary);

    font-size: 12px;

    font-weight: 800;

}



.edit-product-summary {

    display: flex;

    align-items: center;

    gap: 14px;

    padding: 15px;

    border: 1px solid #edf2f7;

    border-radius: 11px;

    background: #f8fafc;

}



.edit-product-image {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 68px;

    height: 68px;

    flex: 0 0 68px;

    border: 1px solid #dbeafe;

    border-radius: 11px;

    background: #eff6ff;

    font-size: 25px;

}



.edit-product-info {

    display: flex;

    flex-direction: column;

    min-width: 0;

}



.edit-product-info > strong {

    font-size: 14px;

}



.edit-product-info > span {

    margin-top: 5px;

    color: var(--form-muted);

    font-size: 11px;

}



.edit-product-meta {

    display: flex;

    flex-wrap: wrap;

    gap: 10px 16px;

    margin-top: 8px;

    color: var(--form-muted);

    font-size: 11px;

}



.edit-product-meta strong {

    color: var(--form-text);

}



.edit-product-notice {

    display: flex;

    gap: 10px;

    margin-top: 15px;

    padding: 12px 13px;

    border: 1px solid #dbeafe;

    border-radius: 9px;

    background: #eff6ff;

}



.edit-product-notice > span {

    color: var(--form-primary);

    font-weight: 800;

}



.edit-product-notice p {

    margin: 0;

    color: #475569;

    font-size: 11px;

    line-height: 1.55;

}



.product-form-empty {

    padding: 15px;

    color: #991b1b;

    background: #fef2f2;

    border-radius: 9px;

    font-size: 13px;

}



.product-form-field {

    position: relative;

    margin-bottom: 18px;

}



.product-form-field:last-child {

    margin-bottom: 0;

}



.product-form-field label {

    display: block;

    margin-bottom: 7px;

    color: #334155;

    font-size: 12px;

    font-weight: 750;

}



.product-form-field input,

.product-form-field textarea {

    display: block;

    width: 100%;

    box-sizing: border-box;

    padding: 12px 13px;

    border: 1px solid #d8e0eb;

    border-radius: 9px;

    outline: none;

    background: #fff;

    color: var(--form-text);

    font-family: inherit;

    font-size: 13px;

}



.product-form-field input {

    height: 44px;

    padding-top: 0;

    padding-bottom: 0;

}



.product-form-field textarea {

    resize: vertical;

    line-height: 1.55;

}



.product-form-field input:focus,

.product-form-field textarea:focus {

    border-color: var(--form-primary);

    box-shadow: 0 0 0 3px rgba(49,91,234,.1);

}



.product-form-counter {

    margin-top: 5px;

    color: #94a3b8;

    text-align: right;

    font-size: 11px;

}



.product-form-error {

    display: block;

    margin-top: 6px;

    color: #dc2626;

    font-size: 12px;

}



.product-toggle {

    position: relative;

    display: flex;

    align-items: flex-start;

    gap: 11px;

    padding: 13px 0;

    border-bottom: 1px solid #eef2f7;

    cursor: pointer;

}



.product-toggle:last-child {

    border-bottom: 0;

}



.product-toggle input {

    position: absolute;

    width: 1px;

    height: 1px;

    opacity: 0;

}



.product-toggle-slider {

    position: relative;

    width: 38px;

    height: 21px;

    flex: 0 0 38px;

    margin-top: 1px;

    border-radius: 20px;

    background: #cbd5e1;

    transition: .2s ease;

}



.product-toggle-slider::after {

    content: "";

    position: absolute;

    top: 3px;

    left: 3px;

    width: 15px;

    height: 15px;

    border-radius: 50%;

    background: #fff;

    transition: .2s ease;

}



.product-toggle input:checked + .product-toggle-slider {

    background: var(--form-primary);

}



.product-toggle input:checked + .product-toggle-slider::after {

    transform: translateX(17px);

}



.product-toggle input:focus-visible + .product-toggle-slider {

    outline: 3px solid rgba(49,91,234,.2);

}



.product-toggle-content {

    display: flex;

    flex-direction: column;

    gap: 3px;

}



.product-toggle-content strong {

    font-size: 12px;

}



.product-toggle-content small {

    color: var(--form-muted);

    font-size: 11px;

    line-height: 1.4;

}



.product-form-info-title {

    margin-bottom: 13px;

    font-size: 13px;

    font-weight: 800;

}



.product-form-info-row {

    display: flex;

    justify-content: space-between;

    gap: 15px;

    padding: 10px 0;

    border-bottom: 1px solid #eef2f7;

    font-size: 11px;

}



.product-form-info-row:last-child {

    border-bottom: 0;

}



.product-form-info-row span {

    color: var(--form-muted);

}



.product-form-info-row strong {

    text-align: right;

}



.product-form-actions {

    display: flex;

    flex-direction: column;

    gap: 9px;

}



.product-form-submit {

    min-height: 44px;

    padding: 0 15px;

    border: 1px solid var(--form-primary);

    border-radius: 9px;

    background: var(--form-primary);

    color: #fff;

    font-family: inherit;

    font-size: 13px;

    font-weight: 750;

    cursor: pointer;

}



.product-form-submit:hover {

    background: var(--form-primary-dark);

    border-color: var(--form-primary-dark);

}



.product-form-submit:disabled {

    opacity: .6;

    cursor: not-allowed;

}



@media (max-width: 950px) {

    .product-form-layout {

        grid-template-columns: 1fr;

    }



    .product-form-sidebar {

        display: grid;

        grid-template-columns: 1fr 1fr;

        align-items: start;

    }



    .product-form-actions {

        grid-column: 1 / -1;

    }

}



@media (max-width: 650px) {

    .product-form-header {

        align-items: flex-start;

        flex-direction: column;

    }



    .product-form-back {

        width: 100%;

        box-sizing: border-box;

    }



    .product-form-card,

    .product-form-info-card {

        padding: 17px;

    }



    .product-form-sidebar {

        display: flex;

    }



    .product-form-header h1 {

        font-size: 22px;

    }



    .edit-product-summary {

        align-items: flex-start;

    }



    .edit-product-meta {

        flex-direction: column;

        gap: 5px;

    }

}

</style>



<script>

document.addEventListener('DOMContentLoaded', function () {

    const description = document.getElementById('short_description');

    const counter = document.getElementById('shortDescriptionCount');



    function updateCounter() {

        if (description && counter) {

            counter.textContent = description.value.length;

        }

    }



    if (description) {

        description.addEventListener('input', updateCounter);

        updateCounter();

    }

});

</script>



@endsection
