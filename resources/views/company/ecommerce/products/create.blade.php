

@extends('layouts.ecommerce')

@section('title', 'Ajouter un produit e-commerce')

@section('page-title', 'Ajouter au catalogue')

@section('content')

<div class="ecommerce-product-form-page">

    {{-- HEADER --}}

    <div class="product-form-header">

        <div>

            <div class="product-form-breadcrumb">

                <a href="{{ route('company.ecommerce.index') }}">E-commerce</a>

                <span>/</span>

                <a href="{{ route('company.ecommerce.products.index') }}">Catalogue</a>

                <span>/</span>

                <span>Ajouter</span>

            </div>

            <h1>Ajouter un produit au catalogue</h1>

            <p>

                Sélectionnez un produit ERP et préparez sa présentation en ligne.

            </p>

        </div>

        <a

            href="{{ route('company.ecommerce.products.index') }}"

            class="product-form-back"

        >

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

    {{-- MESSAGES --}}

    @if(session('error'))

        <div class="product-form-alert product-form-alert-error">

            {{ session('error') }}

        </div>

    @endif

    <form

        method="POST"

        action="{{ route('company.ecommerce.products.store') }}"

        id="ecommerceProductCreateForm" enctype="multipart/form-data"

    >

        @csrf

        <div class="product-form-layout">

            {{-- COLONNE PRINCIPALE --}}

            <div class="product-form-main">

                {{-- PRODUIT ERP ET PRIX --}}

                <div class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Produit ERP</h2>

                            <p>

                                Choisissez un produit ERP pour récupérer son prix de vente.

                            </p>

                        </div>

                        <span class="product-form-card-number">01</span>

                    </div>

                    <div class="product-form-field">

                        <label for="product_id">

                            Produit <span>*</span>

                        </label>

                        <select

                            name="product_id"

                            id="product_id"

                            required

                        >

                            <option value="">

                                -- Sélectionner un produit --

                            </option>

                            @foreach($products as $product)

                                <option

                                    value="{{ $product->id }}"

                                    data-price="{{ $product->selling_price }}"

                                    data-stock="{{ $product->stock_quantity }}"

                                    @selected(old('product_id') == $product->id)

                                >

                                    {{ $product->name }}

                                    @if($product->sku)

                                        — Réf. {{ $product->sku }}

                                    @endif

                                </option>

                            @endforeach

                        </select>

                        <small></small>

                            {{-- CATEGORIE E-COMMERCE --}}

                            <div class="product-form-field">

                                <label for="ecommerce_category_id">

                                    Catégorie E-commerce

                                </label>

                                <select

                                    name="ecommerce_category_id"

                                    id="ecommerce_category_id"

                                >

                                    <option value="">-- Sans catégorie --</option>

                                    @foreach($categories as $category)

                                        <option

                                            value="{{ $category->id }}"

                                            @selected(old('ecommerce_category_id') == $category->id)

                                        >

                                            {{ $category->name }}

                                        </option>

                                    @endforeach

                                </select>

                                <small>

                                    Sélectionnez la catégorie dans laquelle

                                    ce produit apparaîtra sur la boutique.

                                </small>

                                @error('ecommerce_category_id')

                                    <small style="color: #dc2626;">

                                        {{ $message }}

                                    </small>

                                @enderror

                            </div>

                        <!-- <small>

                            Seuls les produits actifs qui ne sont pas encore

                            présents dans le catalogue sont affichés.

                        </small> -->

                    </div>

                    {{-- PRIX DE VENTE BOUTIQUE --}}

                    <div class="product-form-field">

                        <label for="sale_price">

                            Prix de vente boutique (FCFA) <span>*</span>

                        </label>

                        <div class="product-price-input-wrapper">

                            <input

                                type="number"

                                name="sale_price"

                                id="sale_price"

                                min="0"

                                max="9999999999999.99"

                                step="0.01"

                                value="{{ old('sale_price', '') }}"

                                placeholder="Ex. : 15000"

                                class="product-price-readonly"

                                readonly

                            >

                            <span class="product-price-currency">FCFA</span>

                        </div>

                        <small>

                            Le prix de vente est récupéré depuis le catalogue ERP

                            et ne peut pas être modifié depuis cette page.

                        </small>

                    </div>

                    {{-- APERÇU --}}

                    <div

                        class="selected-product-preview"

                        id="selectedProductPreview"

                    >

                        <div class="selected-product-icon">📦</div>

                        <div class="selected-product-content">

                            <strong id="previewProductName">

                                Aucun produit sélectionné

                            </strong>

                            <div class="selected-product-details">

                                <span>

                                    Prix boutique :

                                    <strong id="previewProductPrice">—</strong>

                                </span>

                                <span>

                                    Stock :

                                    <strong id="previewProductStock">—</strong>

                                </span>

                            </div>

                        </div>

                    </div>

                    @if($products->isEmpty())

                        <div class="product-form-empty">

                            <strong>Aucun produit disponible</strong>

                            <p>

                                Tous les produits actifs sont déjà dans le

                                catalogue ou aucun produit actif n'existe.

                            </p>

                        </div>

                    @endif

                </div>

                {{-- DESCRIPTION --}}

                <div class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Présentation du produit</h2>

                            <p>

                                Préparez les informations affichées dans

                                votre boutique en ligne.

                            </p>

                        </div>

                        <span class="product-form-card-number">02</span>

                    </div>

                    <div class="product-form-field">

                        <label for="short_description">

                            Description courte

                        </label>

                        <textarea

                            name="short_description"

                            id="short_description"

                            rows="4"

                            maxlength="1000"

                            placeholder="Présentez rapidement les avantages du produit..."

                        >{{ old('short_description') }}</textarea>

                        <div class="product-form-counter">

                            <span id="shortDescriptionCount">0</span>/1000

                        </div>

                    </div>

                    <div class="product-form-field">

                        <label for="description">

                            Description complète

                        </label>

                        <textarea

                            name="description"

                            id="description"

                            rows="9"

                            placeholder="Décrivez en détail le produit, ses caractéristiques et ses avantages..."

                        >{{ old('description') }}</textarea>

                    </div>

                </div>

                {{-- SEO --}}

                <div class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Référencement SEO</h2>

                            <p>

                                Optimisez l'affichage du produit dans

                                les moteurs de recherche.

                            </p>

                        </div>

                        <span class="product-form-card-number">03</span>

                    </div>

                    <div class="product-form-field">

                        <label for="seo_title">Titre SEO</label>

                        <input

                            type="text"

                            name="seo_title"

                            id="seo_title"

                            value="{{ old('seo_title') }}"

                            maxlength="255"

                            placeholder="Titre affiché dans Google..."

                        >

                    </div>

                    <div class="product-form-field">

                        <label for="seo_description">

                            Description SEO

                        </label>

                        <textarea

                            name="seo_description"

                            id="seo_description"

                            rows="4"

                            maxlength="1000"

                            placeholder="Courte description destinée aux moteurs de recherche..."

                        >{{ old('seo_description') }}</textarea>

                    </div>

                </div>

            </div>

            {{-- SIDEBAR --}}

            <aside class="product-form-sidebar">

                {{-- PUBLICATION --}}

                <div class="product-form-card">

                    <div class="product-form-card-header">

                        <div>

                            <h2>Publication</h2>

                            <p>Visibilité du produit</p>

                        </div>

                    </div>

                    <label class="product-toggle">

                        <input

                            type="checkbox"

                            name="is_published"

                            value="1"

                            @checked(old('is_published'))

                        >

                        <span class="product-toggle-slider"></span>

                        <span class="product-toggle-content">

                            <strong>Publier le produit</strong>

                            <small>

                                Le produit sera visible dans la boutique.

                            </small>

                        </span>

                    </label>

                    <label class="product-toggle">

                        <input

                            type="checkbox"

                            name="is_featured"

                            value="1"

                            @checked(old('is_featured'))

                        >

                        <span class="product-toggle-slider"></span>

                        <span class="product-toggle-content">

                            <strong>Produit à la une</strong>

                            <small>

                                Mettre en avant ce produit.

                            </small>

                        </span>

                    </label>

                </div>

                {{-- CONSEIL --}}

                <div class="product-form-tip">

                    <div class="product-form-tip-icon">💡</div>

                    <div>

                        <strong>Conseil</strong>

                        <p>

                            Vérifiez le prix de vente et le stock dans votre

                            ERP avant de publier le produit. Le prix boutique

                            peut être différent du prix ERP.

                        </p>

                    </div>

                </div>

                @include('company.ecommerce.products.partials.images')

                {{-- ACTIONS --}}

                <div class="product-form-actions">

                    <button

                        type="submit"

                        class="product-form-submit"

                        @disabled($products->isEmpty())

                    >

                        Ajouter au catalogue

                    </button>

                    <a

                        href="{{ route('company.ecommerce.products.index') }}"

                        class="product-form-cancel"

                    >

                        Annuler

                    </a>

                </div>

            </aside>

        </div>

    </form>

</div>

<style>

/* ================================================================

   E-COMMERCE — PRODUCT FORM

\================================================================ */

.ecommerce-product-form-page {

    --form-primary: #315bea;

    --form-primary-dark: #2448c9;

    --form-text: #172033;

    --form-muted: #64748b;

    --form-border: #e2e8f0;

    --form-background: #f8fafc;

    --form-white: #ffffff;

}

/* HEADER */

.product-form-header {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 25px;

    margin-bottom: 25px;

}

.product-form-breadcrumb {

    display: flex;

    align-items: center;

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

    color: var(--form-text);

    font-size: 27px;

    font-weight: 750;

}

.product-form-header p {

    margin: 8px 0 0;

    color: var(--form-muted);

    font-size: 14px;

    line-height: 1.5;

}

.product-form-back {

    min-height: 42px;

    padding: 0 15px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border: 1px solid var(--form-border);

    border-radius: 9px;

    color: #475569;

    background: #fff;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

    white-space: nowrap;

}

/* ALERTES */

.product-form-alert {

    margin-bottom: 20px;

    padding: 14px 16px;

    border-radius: 10px;

    font-size: 13px;

}

.product-form-alert-error {

    color: #991b1b;

    background: #fef2f2;

    border: 1px solid #fecaca;

}

.product-form-alert ul {

    margin: 8px 0 0 18px;

}

/* LAYOUT */

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

/* CARTES */

.product-form-card {

    min-width: 0;

    padding: 22px;

    background: var(--form-white);

    border: 1px solid var(--form-border);

    border-radius: 13px;

    box-shadow: 0 2px 8px rgba(15, 23, 42, .03);

}

.product-form-card-header {

    display: flex;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 22px;

}

.product-form-card-header h2 {

    margin: 0;

    color: var(--form-text);

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

/* CHAMPS */

.product-form-field {

    position: relative;

    margin-bottom: 20px;

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

.product-form-field label span {

    color: #dc2626;

}

.product-form-field input,

.product-form-field select,

.product-form-field textarea {

    width: 100%;

    box-sizing: border-box;

    border: 1px solid #d8e0eb;

    border-radius: 9px;

    outline: none;

    background: #fff;

    color: var(--form-text);

    font-family: inherit;

    font-size: 13px;

    transition: border-color .2s, box-shadow .2s;

}

.product-form-field input,

.product-form-field select {

    height: 44px;

    padding: 0 13px;

}

.product-form-field textarea {

    padding: 12px 13px;

    resize: vertical;

    line-height: 1.55;

}

.product-form-field input:focus,

.product-form-field select:focus,

.product-form-field textarea:focus {

    border-color: var(--form-primary);

    box-shadow: 0 0 0 3px rgba(49, 91, 234, .10);

}

.product-form-field small {

    display: block;

    margin-top: 7px;

    color: #94a3b8;

    font-size: 11px;

    line-height: 1.5;

}

/* PRIX */

.product-price-input-wrapper {

    position: relative;

}

.product-price-input-wrapper input {

    padding-right: 75px;

    font-weight: 700;

}

.product-price-currency {

    position: absolute;

    top: 50%;

    right: 13px;

    transform: translateY(-50%);

    color: #64748b;

    font-size: 11px;

    font-weight: 800;

    pointer-events: none;

}

/* COMPTEUR */

.product-form-counter {

    margin-top: 5px;

    color: #94a3b8;

    text-align: right;

    font-size: 11px;

}

/* APERÇU PRODUIT */

.selected-product-preview {

    display: flex;

    align-items: center;

    gap: 13px;

    margin-top: 18px;

    padding: 14px;

    border: 1px solid #dbeafe;

    border-radius: 10px;

    background: #f8fbff;

}

.selected-product-icon {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 44px;

    height: 44px;

    flex: 0 0 44px;

    border-radius: 10px;

    background: #eff6ff;

    font-size: 20px;

}

.selected-product-content {

    min-width: 0;

}

.selected-product-preview > div:last-child > strong {

    display: block;

    overflow-wrap: anywhere;

    color: var(--form-text);

    font-size: 13px;

}

.selected-product-details {

    display: flex;

    flex-wrap: wrap;

    gap: 8px 15px;

    margin-top: 5px;

    color: var(--form-muted);

    font-size: 11px;

}

.selected-product-details strong {

    color: var(--form-text);

    font-size: 11px;

}

/* AUCUN PRODUIT */

.product-form-empty {

    margin-top: 15px;

    padding: 14px;

    border: 1px solid #e2e8f0;

    border-radius: 9px;

    background: #f8fafc;

}

.product-form-empty strong {

    color: var(--form-text);

    font-size: 12px;

}

.product-form-empty p {

    margin: 6px 0 0;

    color: var(--form-muted);

    font-size: 12px;

    line-height: 1.5;

}

/* TOGGLES */

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

.product-toggle input:focus-visible + .product-toggle-slider {

    outline: 3px solid rgba(49, 91, 234, .25);

    outline-offset: 3px;

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

.product-toggle-content {

    display: flex;

    flex-direction: column;

    gap: 3px;

}

.product-toggle-content strong {

    color: var(--form-text);

    font-size: 12px;

}

.product-toggle-content small {

    color: var(--form-muted);

    font-size: 11px;

    line-height: 1.4;

}

/* CONSEIL */

.product-form-tip {

    display: flex;

    gap: 11px;

    padding: 15px;

    border: 1px solid #dbeafe;

    border-radius: 11px;

    background: #eff6ff;

}

.product-form-tip-icon {

    font-size: 18px;

}

.product-form-tip strong {

    color: #1e3a8a;

    font-size: 12px;

}

.product-form-tip p {

    margin: 5px 0 0;

    color: #475569;

    font-size: 11px;

    line-height: 1.5;

}

/* ACTIONS */

.product-form-actions {

    display: flex;

    flex-direction: column;

    gap: 9px;

}

.product-form-submit,

.product-form-cancel {

    box-sizing: border-box;

    min-height: 44px;

    padding: 10px 14px;

    border-radius: 9px;

    font-family: inherit;

    font-size: 13px;

    font-weight: 750;

    text-decoration: none;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    text-align: center;

    transition: .2s ease;

}

.product-form-submit {

    border: 1px solid var(--form-primary);

    background: var(--form-primary);

    color: #fff;

}

.product-form-submit:hover:not(:disabled) {

    background: var(--form-primary-dark);

    border-color: var(--form-primary-dark);

}

.product-form-submit:disabled {

    opacity: .5;

    cursor: not-allowed;

}

.product-form-cancel {

    border: 1px solid var(--form-border);

    background: #fff;

    color: #475569;

}

.product-form-cancel:hover {

    background: #f8fafc;

}

/* RESPONSIVE */

@media (max-width: 950px) {

    .product-form-layout {

        grid-template-columns: minmax(0, 1fr);

    }

    .product-form-sidebar {

        display: grid;

        grid-template-columns: repeat(2, minmax(0, 1fr));

        align-items: start;

    }

    .product-form-actions {

        grid-column: 1 / -1;

    }

}

@media (max-width: 650px) {

    .product-form-header {

        align-items: stretch;

        flex-direction: column;

    }

    .product-form-back {

        width: 100%;

        box-sizing: border-box;

    }

    .product-form-card {

        padding: 17px;

    }

    .product-form-sidebar {

        display: flex;

    }

    .product-form-header h1 {

        font-size: 22px;

    }

    .selected-product-details {

        flex-direction: column;

        gap: 5px;

    }

}

/* Champ du prix de vente en lecture seule */

.product-price-readonly {

    background-color: #e9ecef !important;

    color: #6b7280 !important;

    border-color: #d1d5db !important;

    cursor: not-allowed;

    opacity: 1;

}

/* Empêcher les changements visuels au survol */

.product-price-readonly:hover,

.product-price-readonly:focus {

    background-color: #e9ecef !important;

    border-color: #d1d5db !important;

    box-shadow: none !important;

    outline: none;

}

</style>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const productSelect = document.getElementById('product_id');

    const productName = document.getElementById('previewProductName');

    const productPrice = document.getElementById('previewProductPrice');

    const productStock = document.getElementById('previewProductStock');

    const salePriceInput = document.getElementById('sale_price');

    const shortDescription = document.getElementById('short_description');

    const shortDescriptionCount = document.getElementById('shortDescriptionCount');

    /*

    |--------------------------------------------------------------------------

    | FORMATAGE DU PRIX

    |--------------------------------------------------------------------------

    */

    function formatPrice(value) {

        const amount = Number(value);

        if (!Number.isFinite(amount)) {

            return '—';

        }

        return new Intl.NumberFormat('fr-FR', {

            minimumFractionDigits: 0,

            maximumFractionDigits: 2

        }).format(amount) + ' FCFA';

    }

    /*

    |--------------------------------------------------------------------------

    | APERÇU DU PRODUIT

    |--------------------------------------------------------------------------

    */

    function updateProductPreview() {

        const option = productSelect.options[

            productSelect.selectedIndex

        ];

        if (!option || !option.value) {

            productName.textContent = 'Aucun produit sélectionné';

            productPrice.textContent = '—';

            productStock.textContent = '—';

            return;

        }

        const erpPrice = Number(option.dataset.price || 0);

        const stock = Number(option.dataset.stock || 0);

        productName.textContent = option.textContent.trim();

        /*

         * Le prix ERP est utilisé par défaut.

         * Un prix déjà saisi par l'utilisateur est conservé.

         */

        if (salePriceInput.value === '') {

            salePriceInput.value = erpPrice;

        }

        const boutiquePrice = salePriceInput.value !== ''

            ? Number(salePriceInput.value)

            : erpPrice;

        productPrice.textContent = formatPrice(boutiquePrice);

        productStock.textContent = stock + ' unité(s)';

    }

    /*

    |--------------------------------------------------------------------------

    | COMPTEUR DE DESCRIPTION

    |--------------------------------------------------------------------------

    */

    function updateDescriptionCounter() {

        shortDescriptionCount.textContent =

            shortDescription.value.length;

    }

    /*

    |--------------------------------------------------------------------------

    | ÉVÉNEMENTS

    |--------------------------------------------------------------------------

    */

    productSelect.addEventListener('change', function () {

        const option = productSelect.options[

            productSelect.selectedIndex

        ];

        /*

         * Lorsqu'un nouveau produit est sélectionné,

         * proposer automatiquement son prix ERP.

         */

        if (option && option.value) {

            salePriceInput.value = option.dataset.price || '0';

        } else {

            salePriceInput.value = '';

        }

        updateProductPreview();

    });

    salePriceInput.addEventListener('input', function () {

        updateProductPreview();

    });

    shortDescription.addEventListener(

        'input',

        updateDescriptionCounter

    );

    /*

    |--------------------------------------------------------------------------

    | INITIALISATION

    |--------------------------------------------------------------------------

    */

    updateProductPreview();

    updateDescriptionCounter();

});

</script>

@endsection
