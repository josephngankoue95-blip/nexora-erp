
@extends('layouts.storefront')

@section('title', 'Commande enregistrée — ' . $company->name)

@section('content')

@php
    $storeRoute = ['company' => $company->slug];
    $currency = $settings->currency ?: 'FCFA';
@endphp

<div class="nx-success-page">

    <div class="store-container">

        <div class="nx-success-wrapper">

            {{-- ICON CONFIRMATION --}}
            <div class="nx-success-icon" aria-hidden="true">
                ✓
            </div>

            <span class="nx-success-eyebrow">
                COMMANDE ENREGISTREE
            </span>

            <h1>Merci pour votre commande !</h1>

            <p class="nx-success-intro">
                Merci <strong>{{ $order->customer_name }}</strong>.
                Votre commande a bien été reçue et reste
                en attente de confirmation par notre boutique.
            </p>

            {{-- REFERENCE --}}
            <div class="nx-success-reference">

                <span>Référence de votre commande</span>

                <strong>{{ $order->reference }}</strong>

                <p>
                    Conservez cette référence pour faciliter
                    le suivi de votre commande.
                </p>

            </div>

            {{-- DETAILS --}}
            <div class="nx-success-card">

                <div class="nx-success-card-header">
                    <h2>Récapitulatif de commande</h2>
                    <span>Commande reçue</span>
                </div>

                <div class="nx-success-items">

                    @foreach($order->items as $item)

                        <div class="nx-success-item">

                            <div class="nx-success-item-icon" aria-hidden="true">
                                📦
                            </div>

                            <div class="nx-success-item-info">

                                <h3>{{ $item->product_name }}</h3>

                                <p>
                                    Quantité :
                                    {{ number_format((float) $item->quantity, 0, ',', ' ') }}
                                </p>

                            </div>

                            <strong>
                                {{ number_format((float) $item->total, 0, ',', ' ') }}
                                {{ $currency }}
                            </strong>

                        </div>

                    @endforeach

                </div>

                <div class="nx-success-total">

                    <span>Total enregistré</span>

                    <strong>
                        {{ number_format((float) $order->total, 0, ',', ' ') }}
                        {{ $currency }}
                    </strong>

                </div>

                <div class="nx-success-payment">

                    <span>Statut du paiement</span>

                    <span class="nx-payment-pending">
                        ● En attente
                    </span>

                </div>

            </div>

            {{-- INFORMATIONS --}}
            <div class="nx-success-notice">

                <div class="nx-success-notice-icon">
                    ⓘ
                </div>

                <div>

                    <h3>Informations importantes</h3>

                    <p>
                        Votre commande est enregistrée, mais elle
                        n'est pas encore confirmée par la boutique.
                        Les éventuels frais de livraison seront communiqués
                        ultérieurement.
                    </p>

                    <p>
                        Ne procédez à aucun paiement sans avoir reçu
                        des instructions vérifiées de l'entreprise.
                    </p>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="nx-success-actions">

                <a href="{{ route('storefront.index', $storeRoute) }}"
                   class="nx-success-home">
                    Retour à la boutique
                    <span aria-hidden="true">→</span>
                </a>

                <a href="{{ route('storefront.products.index', $storeRoute) }}"
                   class="nx-success-products">
                    Continuer mes achats
                </a>

            </div>

        </div>

    </div>

</div>

@endsection
