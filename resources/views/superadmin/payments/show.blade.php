@extends('layouts.superadmin')

@section('title', 'Détail du paiement')

@section('content')

<div class="payment-page">

    {{-- ==========================================================
         EN-TÊTE
    =========================================================== --}}

    <div class="payment-page-header">

        <div>

            <div class="payment-breadcrumb">

                <a href="{{ route('superadmin.payments.index') }}">
                    Paiements
                </a>

                <i data-lucide="chevron-right"></i>

                <strong>
                    {{ $payment->reference }}
                </strong>

            </div>

            <h1>
                <i data-lucide="receipt"></i>
                Détail du paiement
            </h1>

            <p>
                Consultez les informations de la transaction.
            </p>

        </div>


        <div class="payment-header-actions">

            <a
                href="{{ route('superadmin.payments.receipt', $payment) }}"
                target="_blank"
                class="payment-secondary-btn"
            >
                <i data-lucide="printer"></i>
                Imprimer le reçu
            </a>

            <a
                href="{{ route('superadmin.payments.edit', $payment) }}"
                class="payment-primary-btn"
            >
                <i data-lucide="pencil"></i>
                Modifier
            </a>

        </div>

    </div>


    {{-- ==========================================================
         SUCCESS
    =========================================================== --}}

    @if(session('success'))

        <div class="payment-alert payment-alert-success">

            <div class="payment-alert-icon">
                <i data-lucide="check-circle"></i>
            </div>

            <div>
                <strong>Opération réussie</strong>
                <span>{{ session('success') }}</span>
            </div>

        </div>

    @endif


    {{-- ==========================================================
         HERO PAIEMENT
    =========================================================== --}}

    <div class="payment-detail-hero">

        <div class="payment-detail-hero-left">

            <div class="payment-detail-main-icon">
                <i data-lucide="wallet-cards"></i>
            </div>

            <div>

                <span class="payment-detail-label">
                    Référence de paiement
                </span>

                <h2>
                    {{ $payment->reference }}
                </h2>

                <p>
                    Créé le
                    {{ $payment->created_at->format('d/m/Y à H:i') }}
                </p>

            </div>

        </div>


        <div>

            @switch($payment->status)

                @case('completed')

                    <span class="payment-status payment-status-success payment-status-large">
                        <i data-lucide="check-circle"></i>
                        Terminé
                    </span>

                    @break

                @case('pending')

                    <span class="payment-status payment-status-warning payment-status-large">
                        <i data-lucide="clock-3"></i>
                        En attente
                    </span>

                    @break

                @case('failed')

                    <span class="payment-status payment-status-danger payment-status-large">
                        <i data-lucide="x-circle"></i>
                        Échoué
                    </span>

                    @break

                @case('refunded')

                    <span class="payment-status payment-status-info payment-status-large">
                        <i data-lucide="rotate-ccw"></i>
                        Remboursé
                    </span>

                    @break

            @endswitch

        </div>

    </div>


    {{-- ==========================================================
         CONTENU
    =========================================================== --}}

    <div class="payment-detail-layout">

        <div class="payment-detail-main">


            {{-- ==================================================
                 INFORMATIONS TRANSACTION
            =================================================== --}}

            <div class="payment-detail-card">

                <div class="payment-detail-card-header">

                    <div>

                        <h2>
                            Informations de transaction
                        </h2>

                        <p>
                            Données principales du paiement.
                        </p>

                    </div>

                    <div class="payment-detail-card-icon">
                        <i data-lucide="credit-card"></i>
                    </div>

                </div>


                <div class="payment-detail-grid">

                    <div class="payment-detail-item">

                        <span>Montant</span>

                        <strong class="payment-detail-amount">
                            {{ number_format($payment->amount, 0, ',', ' ') }}
                            FCFA
                        </strong>

                    </div>


                    <div class="payment-detail-item">

                        <span>Méthode</span>

                        <strong>
                            {{ $payment->method_label }}
                        </strong>

                    </div>


                    <div class="payment-detail-item">

                        <span>Fournisseur</span>

                        <strong>
                            {{ $payment->provider ?: '—' }}
                        </strong>

                    </div>


                    <div class="payment-detail-item">

                        <span>Téléphone</span>

                        <strong>
                            {{ $payment->phone ?: '—' }}
                        </strong>

                    </div>


                    <div class="payment-detail-item payment-detail-full">

                        <span>Référence de transaction</span>

                        <strong class="payment-mono">
                            {{ $payment->transaction_reference ?: '—' }}
                        </strong>

                    </div>


                    <div class="payment-detail-item">

                        <span>Date du paiement</span>

                        <strong>
                            @if($payment->paid_at)
                                {{ $payment->paid_at->format('d/m/Y à H:i') }}
                            @else
                                —
                            @endif
                        </strong>

                    </div>

                </div>

            </div>


            {{-- ==================================================
                 ENTREPRISE
            =================================================== --}}

            <div class="payment-detail-card">

                <div class="payment-detail-card-header">

                    <div>

                        <h2>
                            Entreprise
                        </h2>

                        <p>
                            Entreprise ayant effectué le paiement.
                        </p>

                    </div>

                    <div class="payment-detail-card-icon">
                        <i data-lucide="building-2"></i>
                    </div>

                </div>


                @if($payment->company)

                    <div class="payment-company-detail">

                        <div class="payment-company-detail-logo">

                            @if($payment->company->logo)

                                <img
                                    src="{{ asset('storage/' . $payment->company->logo) }}"
                                    alt="{{ $payment->company->name }}"
                                >

                            @else

                                <i data-lucide="building-2"></i>

                            @endif

                        </div>


                        <div class="payment-company-detail-info">

                            <h3>
                                {{ $payment->company->name }}
                            </h3>

                            @if($payment->company->email)
                                <p>
                                    <i data-lucide="mail"></i>
                                    {{ $payment->company->email }}
                                </p>
                            @endif

                            @if($payment->company->phone)
                                <p>
                                    <i data-lucide="phone"></i>
                                    {{ $payment->company->phone }}
                                </p>
                            @endif

                            @if($payment->company->city)
                                <p>
                                    <i data-lucide="map-pin"></i>
                                    {{ $payment->company->city }}
                                    @if($payment->company->country)
                                        — {{ $payment->company->country }}
                                    @endif
                                </p>
                            @endif

                        </div>

                    </div>

                @else

                    <div class="payment-detail-empty">
                        <i data-lucide="building-2"></i>
                        Entreprise supprimée
                    </div>

                @endif

            </div>


            {{-- ==================================================
                 ABONNEMENT
            =================================================== --}}

            <div class="payment-detail-card">

                <div class="payment-detail-card-header">

                    <div>

                        <h2>
                            Abonnement associé
                        </h2>

                        <p>
                            Abonnement concerné par cette transaction.
                        </p>

                    </div>

                    <div class="payment-detail-card-icon">
                        <i data-lucide="layers-3"></i>
                    </div>

                </div>


                @if($payment->subscription)

                    <div class="payment-subscription-detail">

                        <div class="payment-subscription-detail-icon">
                            <i data-lucide="package"></i>
                        </div>

                        <div class="payment-subscription-detail-content">

                            <h3>
                                {{ $payment->subscription->name }}
                            </h3>

                            @if($payment->subscription->plan)

                                <span>
                                    Plan :
                                    <strong>
                                        {{ $payment->subscription->plan->name }}
                                    </strong>
                                </span>

                            @endif

                            <div class="payment-subscription-meta">

                                <div>
                                    <small>Début</small>

                                    <strong>
                                        {{ $payment->subscription->starts_at?->format('d/m/Y') ?? '—' }}
                                    </strong>
                                </div>

                                <div>
                                    <small>Fin</small>

                                    <strong>
                                        {{ $payment->subscription->ends_at?->format('d/m/Y') ?? '—' }}
                                    </strong>
                                </div>

                                <div>
                                    <small>Prix</small>

                                    <strong>
                                        {{ number_format($payment->subscription->price, 0, ',', ' ') }}
                                        FCFA
                                    </strong>
                                </div>

                                <div>
                                    <small>Statut</small>

                                    <strong>
                                        {{ $payment->subscription->status_label }}
                                    </strong>
                                </div>

                            </div>

                        </div>

                    </div>

                @else

                    <div class="payment-detail-empty">
                        <i data-lucide="layers-3"></i>
                        Aucun abonnement associé à ce paiement.
                    </div>

                @endif

            </div>


            {{-- ==================================================
                 NOTES
            =================================================== --}}

            @if($payment->notes)

                <div class="payment-detail-card">

                    <div class="payment-detail-card-header">

                        <div>

                            <h2>
                                Notes
                            </h2>

                            <p>
                                Informations complémentaires.
                            </p>

                        </div>

                        <div class="payment-detail-card-icon">
                            <i data-lucide="file-text"></i>
                        </div>

                    </div>


                    <div class="payment-notes">

                        {{ $payment->notes }}

                    </div>

                </div>

            @endif

        </div>


        {{-- ==================================================
             SIDEBAR
        =================================================== --}}

        <aside class="payment-detail-sidebar">

            <div class="payment-detail-summary">

                <div class="payment-detail-summary-icon">
                    <i data-lucide="banknote"></i>
                </div>

                <span>
                    Montant encaissé
                </span>

                <strong>
                    {{ number_format($payment->amount, 0, ',', ' ') }}
                </strong>

                <small>
                    FCFA
                </small>

            </div>


            <div class="payment-detail-side-card">

                <h3>
                    Informations système
                </h3>

                <div class="payment-side-row">

                    <span>ID</span>

                    <strong>
                        #{{ $payment->id }}
                    </strong>

                </div>


                <div class="payment-side-row">

                    <span>Référence</span>

                    <strong class="payment-mono">
                        {{ $payment->reference }}
                    </strong>

                </div>


                <div class="payment-side-row">

                    <span>Créé le</span>

                    <strong>
                        {{ $payment->created_at->format('d/m/Y H:i') }}
                    </strong>

                </div>


                <div class="payment-side-row">

                    <span>Mis à jour</span>

                    <strong>
                        {{ $payment->updated_at->format('d/m/Y H:i') }}
                    </strong>

                </div>

            </div>


            <a
                href="{{ route('superadmin.payments.receipt', $payment) }}"
                target="_blank"
                class="payment-receipt-card"
            >

                <div>
                    <i data-lucide="printer"></i>
                </div>

                <span>
                    Imprimer le reçu
                </span>

                <i
                    data-lucide="arrow-up-right"
                    class="payment-receipt-arrow"
                ></i>

            </a>

        </aside>

    </div>

</div>

@endsection