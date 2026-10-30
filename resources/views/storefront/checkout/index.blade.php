
@extends('layouts.storefront')

@section('title', 'Finaliser ma commande — ' . ($settings->store_name ?: $company->name))

@section('content')

@php
    $storeRoute = ['company' => $company->slug];
    $currency = $settings->currency ?: 'FCFA';
@endphp

<div class="nx-checkout-page">

    <div class="store-container">

        {{-- FIL D'ARIANE --}}
        <nav class="nx-checkout-breadcrumb" aria-label="Fil d'Ariane">

            <a href="{{ route('storefront.index', $storeRoute) }}">
                Accueil
            </a>

            <span>/</span>

            <a href="{{ route('storefront.cart.index', $storeRoute) }}">
                Panier
            </a>

            <span>/</span>

            <span aria-current="page">
                Finaliser la commande
            </span>

        </nav>

        {{-- EN-TETE --}}
        <div class="nx-checkout-heading">

            <div>

                <span class="nx-checkout-eyebrow">
                    FINALISATION DE VOTRE ACHAT
                </span>

                <h1>Finaliser ma commande</h1>

                <p>
                    Renseignez vos coordonnées, choisissez votre
                    mode de paiement et confirmez votre commande.
                </p>

            </div>

            <a href="{{ route('storefront.cart.index', $storeRoute) }}"
               class="nx-checkout-back">
                ← Retour au panier
            </a>

        </div>

        {{-- ETAPES --}}
        <div class="nx-checkout-steps">

            <div class="nx-checkout-step nx-step-completed">
                <span class="nx-step-number">✓</span>
                <span>Mon panier</span>
            </div>

            <span class="nx-step-separator"></span>

            <div class="nx-checkout-step nx-step-active">
                <span class="nx-step-number">2</span>
                <span>Livraison et paiement</span>
            </div>

            <span class="nx-step-separator"></span>

            <div class="nx-checkout-step">
                <span class="nx-step-number">3</span>
                <span>Confirmation</span>
            </div>

        </div>

        {{-- ERREURS DE VALIDATION --}}
        @if($errors->any())

            <div class="nx-checkout-errors" role="alert">

                <strong>
                    Veuillez corriger les erreurs suivantes :
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif

        {{-- GRILLE PRINCIPALE --}}
        <div class="nx-checkout-grid">

            {{-- COLONNE FORMULAIRE --}}
            <div class="nx-checkout-main">

                <form
                    id="nx-checkout-form"
                    method="POST"
                    action="{{ route('storefront.checkout.store', $storeRoute) }}"
                >

                    @csrf

                    {{-- COORDONNEES --}}
                    <section class="nx-checkout-panel">

                        <div class="nx-panel-heading">

                            <div class="nx-panel-icon">1</div>

                            <div>
                                <h2>Vos coordonnées</h2>
                                <p>
                                    Ces informations permettront à la boutique
                                    de vous contacter.
                                </p>
                            </div>

                        </div>

                        <div class="nx-checkout-field">

                            <label for="customer_name">
                                Nom complet <span>*</span>
                            </label>

                            <input
                                type="text"
                                id="customer_name"
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                placeholder="Votre nom et prénom"
                                autocomplete="name"
                                maxlength="255"
                                required
                            >

                        </div>

                        <div class="nx-checkout-two">

                            <div class="nx-checkout-field">

                                <label for="customer_phone">
                                    Téléphone <span>*</span>
                                </label>

                                <input
                                    type="tel"
                                    id="customer_phone"
                                    name="customer_phone"
                                    value="{{ old('customer_phone') }}"
                                    placeholder="Ex. 6XXXXXXXX"
                                    autocomplete="tel"
                                    maxlength="40"
                                    required
                                >

                            </div>

                            <div class="nx-checkout-field">

                                <label for="customer_email">
                                    Adresse e-mail
                                </label>

                                <input
                                    type="email"
                                    id="customer_email"
                                    name="customer_email"
                                    value="{{ old('customer_email') }}"
                                    placeholder="exemple@email.com"
                                    autocomplete="email"
                                >

                            </div>

                        </div>

                    </section>

                    {{-- LIVRAISON --}}
                    <section class="nx-checkout-panel">

                        <div class="nx-panel-heading">

                            <div class="nx-panel-icon">2</div>

                            <div>
                                <h2>Adresse de livraison</h2>
                                <p>
                                    Indiquez l'adresse à laquelle
                                    vous souhaitez recevoir vos articles.
                                </p>
                            </div>

                        </div>

                        <div class="nx-checkout-field">

                            <label for="shipping_address">
                                Adresse complète <span>*</span>
                            </label>

                            <input
                                type="text"
                                id="shipping_address"
                                name="shipping_address"
                                value="{{ old('shipping_address') }}"
                                placeholder="Quartier, rue, point de repère..."
                                autocomplete="street-address"
                                required
                            >

                        </div>

                        <div class="nx-checkout-two">

                            <div class="nx-checkout-field">

                                <label for="shipping_city">
                                    Ville <span>*</span>
                                </label>

                                <input
                                    type="text"
                                    id="shipping_city"
                                    name="shipping_city"
                                    value="{{ old('shipping_city') }}"
                                    placeholder="Ex. Douala"
                                    autocomplete="address-level2"
                                    required
                                >

                            </div>

                            <div class="nx-checkout-field">

                                <label for="shipping_region">
                                    Région
                                </label>

                                <input
                                    type="text"
                                    id="shipping_region"
                                    name="shipping_region"
                                    value="{{ old('shipping_region') }}"
                                    placeholder="Ex. Littoral"
                                    autocomplete="address-level1"
                                >

                            </div>

                        </div>

                        <div class="nx-checkout-field">

                            <label for="shipping_country">
                                Pays <span>*</span>
                            </label>

                            <input
                                type="text"
                                id="shipping_country"
                                name="shipping_country"
                                value="{{ old('shipping_country', 'Cameroun') }}"
                                autocomplete="country-name"
                                required
                            >

                        </div>

                    </section>

                    {{-- PAIEMENT --}}
                    <section class="nx-checkout-panel">

                        <div class="nx-panel-heading">

                            <div class="nx-panel-icon">3</div>

                            <div>
                                <h2>Mode de paiement</h2>
                                <p>
                                    Sélectionnez votre moyen de paiement préféré.
                                </p>
                            </div>

                        </div>

                        <div class="nx-checkout-field">

                            <label for="payment_method">
                                Moyen de paiement <span>*</span>
                            </label>

                            <select
                                id="payment_method"
                                name="payment_method"
                                required
                            >

                                <option
                                    value="cash_on_delivery"
                                    @selected(old('payment_method', 'cash_on_delivery') === 'cash_on_delivery')
                                >
                                    Paiement à la livraison
                                </option>

                                <option
                                    value="bank_transfer"
                                    @selected(old('payment_method') === 'bank_transfer')
                                >
                                    Virement bancaire (à confirmer)
                                </option>

                                <option
                                    value="orange_money"
                                    @selected(old('payment_method') === 'orange_money')
                                >
                                    Orange Money (en attente d'intégration)
                                </option>

                                <option
                                    value="mtn_momo"
                                    @selected(old('payment_method') === 'mtn_momo')
                                >
                                    MTN MoMo (en attente d'intégration)
                                </option>

                            </select>

                        </div>

                        <div class="nx-payment-notice">

                            <span aria-hidden="true">ⓘ</span>

                            <p>
                                Aucun paiement en ligne n'est prélevé
                                lors de l'enregistrement de cette commande.
                                Les modalités de règlement seront confirmées
                                par la boutique.
                            </p>

                        </div>

                    </section>

                    {{-- NOTES --}}
                    <section class="nx-checkout-panel">

                        <div class="nx-panel-heading">

                            <div class="nx-panel-icon">4</div>

                            <div>
                                <h2>Instructions complémentaires</h2>
                                <p>
                                    Ajoutez des précisions si nécessaire.
                                </p>
                            </div>

                        </div>

                        <div class="nx-checkout-field">

                            <label for="customer_note">
                                Message pour la boutique
                            </label>

                            <textarea
                                id="customer_note"
                                name="customer_note"
                                rows="4"
                                placeholder="Précisions sur votre commande ou la livraison..."
                            >{{ old('customer_note') }}</textarea>

                        </div>

                    </section>

                </form>

            </div>

            {{-- COLONNE RESUME --}}
            <aside class="nx-checkout-sidebar">

                <div class="nx-checkout-summary">

                    <div class="nx-summary-heading">

                        <h2>Résumé de commande</h2>

                        <span>{{ count($items) }} ligne(s)</span>

                    </div>

                    <div class="nx-summary-products">

                        @foreach($items as $line)

                            <div class="nx-summary-product">

                                <div class="nx-summary-product-icon" aria-hidden="true">
                                    📦
                                </div>

                                <div class="nx-summary-product-info">

                                    <h3>
                                        {{ $line['entry']->product?->name ?? 'Produit' }}
                                    </h3>

                                    <p>
                                        Quantité : {{ $line['quantity'] }}
                                    </p>

                                </div>

                                <strong>
                                    {{ number_format((float) $line['total'], 0, ',', ' ') }}
                                    {{ $currency }}
                                </strong>

                            </div>

                        @endforeach

                    </div>

                    <div class="nx-summary-totals">

                        <div class="nx-summary-row">

                            <span>Sous-total</span>

                            <strong>
                                {{ number_format((float) $subtotal, 0, ',', ' ') }}
                                {{ $currency }}
                            </strong>

                        </div>

                        <div class="nx-summary-row">

                            <span>Livraison (non calculée)</span>

                            <strong>0 {{ $currency }}</strong>

                        </div>

                        <div class="nx-summary-row">

                            <span>Taxes (non calculées)</span>

                            <strong>0 {{ $currency }}</strong>

                        </div>

                    </div>

                    <div class="nx-summary-total">

                        <span>Total provisoire</span>

                        <strong>
                            {{ number_format((float) $subtotal, 0, ',', ' ') }}
                            <small>{{ $currency }}</small>
                        </strong>

                    </div>

                    <p class="nx-summary-disclaimer">
                        Les éventuels frais de livraison et taxes
                        seront confirmés ultérieurement par la boutique.
                    </p>

                    <button
                        type="submit"
                        form="nx-checkout-form"
                        class="nx-checkout-submit"
                    >
                        Confirmer ma commande
                        <span aria-hidden="true">→</span>
                    </button>

                    <div class="nx-summary-security">
                        <span aria-hidden="true">✓</span>
                        Votre commande sera enregistrée pour confirmation.
                    </div>

                </div>

            </aside>

        </div>

    </div>

</div>

@endsection
