@extends('layouts.company')

@section('title', 'Finances')

@section('content')

<div class="module-page finance-page">

{{-- =========================================================
     EN-TÊTE
========================================================== --}}
<div class="module-header finance-header">

    <div class="finance-header-content">
        <div class="finance-title-area">

            <div class="finance-title-icon">
                <span>€</span>
            </div>

            <div>
                <h1>Finances</h1>

                <p>
                    Suivez les ventes, les encaissements et les créances
                    de votre entreprise.
                </p>
            </div>
        </div>

        {{-- FILTRE PÉRIODE --}}
        <div class="finance-period-filter">

            <form method="GET"
                  action="{{ route('company.finance.index') }}"
                  class="finance-period-form">

                <label for="finance-period">
                    Période
                </label>

                <div class="finance-select-wrapper">

                    <span class="finance-select-icon">
                        📅
                    </span>

                    <select
                        id="finance-period"
                        name="period"
                        class="module-input finance-period-select"
                        onchange="this.form.submit()"
                    >

                        <option value="today"
                            {{ $period === 'today' ? 'selected' : '' }}>
                            Aujourd'hui
                        </option>

                        <option value="week"
                            {{ $period === 'week' ? 'selected' : '' }}>
                            Cette semaine
                        </option>

                        <option value="month"
                            {{ $period === 'month' ? 'selected' : '' }}>
                            Ce mois
                        </option>

                        <option value="year"
                            {{ $period === 'year' ? 'selected' : '' }}>
                            Cette année
                        </option>

                    </select>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
     INDICATEURS PRINCIPAUX
========================================================== --}}
<div class="finance-stats">

    {{-- VENTES --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon sales-icon">
                🛒
            </div>

            <span class="finance-stat-label">
                Ventes
            </span>
        </div>

        <div class="finance-stat-value">
            {{ number_format($totalSales, 0, ',', ' ') }}
        </div>

        <div class="finance-stat-description">
            {{ $totalSales }} vente(s) sur la période
        </div>

    </div>


    {{-- CHIFFRE D'AFFAIRES --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon revenue-icon">
                💰
            </div>

            <span class="finance-stat-label">
                Chiffre d'affaires
            </span>
        </div>

        <div class="finance-stat-value finance-value-primary">
            {{ number_format($totalRevenue, 0, ',', ' ') }}
            <small>FCFA</small>
        </div>

        <div class="finance-stat-description">
            Total des ventes terminées
        </div>

    </div>


    {{-- ACHATS --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon">
                📦
            </div>

            <span class="finance-stat-label">
                Achats
            </span>
        </div>

        <div class="finance-stat-value">
            {{ number_format($totalPurchasesAmount, 0, ',', ' ') }}
            <small>FCFA</small>
        </div>

        <div class="finance-stat-description">
            {{ $totalPurchases }} achat(s) sur la période
        </div>

    </div>


    {{-- ENCAISSÉ --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon paid-icon">
                ✓
            </div>

            <span class="finance-stat-label">
                Encaissé
            </span>
        </div>

        <div class="finance-stat-value finance-value-success">
            {{ number_format($totalPaid, 0, ',', ' ') }}
            <small>FCFA</small>
        </div>

        <div class="finance-stat-description">
            Paiements clients confirmés
        </div>

    </div>


    {{-- DÉCAISSÉ --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon receivable-icon">
                ↓
            </div>

            <span class="finance-stat-label">
                Décaissements
            </span>
        </div>

        <div class="finance-stat-value finance-value-danger">
            {{ number_format($totalPurchasePaid, 0, ',', ' ') }}
            <small>FCFA</small>
        </div>

        <div class="finance-stat-description">
            Paiements fournisseurs confirmés
        </div>

    </div>


    {{-- SOLDE NET --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon">
                📊
            </div>

            <span class="finance-stat-label">
                Solde net
            </span>
        </div>

        <div class="finance-stat-value {{ $netCashFlow >= 0 ? 'finance-value-success' : 'finance-value-danger' }}">
            {{ number_format($netCashFlow, 0, ',', ' ') }}
            <small>FCFA</small>
        </div>

        <div class="finance-stat-description">
            Encaissements − décaissements
        </div>

    </div>


    {{-- CRÉANCES --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon receivable-icon">
                !
            </div>

            <span class="finance-stat-label">
                Créances clients
            </span>
        </div>

        <div class="finance-stat-value finance-value-danger">
            {{ number_format($totalReceivables, 0, ',', ' ') }}
            <small>FCFA</small>
        </div>

        <div class="finance-stat-description">
            Montant restant à encaisser
        </div>

    </div>


    {{-- DETTES FOURNISSEURS --}}
    <div class="finance-stat-card">

        <div class="finance-stat-top">
            <div class="finance-stat-icon">
                ⚠
            </div>

            <span class="finance-stat-label">
                Dettes fournisseurs
            </span>
        </div>

        <div class="finance-stat-value finance-value-danger">
            {{ number_format($totalPayables, 0, ',', ' ') }}
            <small>FCFA</small>
        </div>

        <div class="finance-stat-description">
            Montant restant à payer
        </div>

    </div>

</div>

{{-- =========================================================
     RÉPARTITION DES PAIEMENTS
========================================================== --}}
<div class="finance-section-grid">

    {{-- PAIEMENTS CLIENTS --}}
    <div class="module-card finance-card">

        <div class="finance-card-header">

            <div class="finance-card-title">

                <div class="finance-section-icon">
                    💰
                </div>

                <div>
                    <h2>Encaissements clients</h2>

                    <p>
                        Paiements reçus sur les ventes.
                    </p>
                </div>

            </div>

        </div>

        <div class="payment-breakdown">

            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💵</div>

                    <div>
                        <strong>Espèces</strong>
                        <small>Paiements clients en espèces</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($cashPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">📱</div>

                    <div>
                        <strong>Mobile Money</strong>
                        <small>Orange Money + MTN MoMo</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($mobileMoneyPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💳</div>

                    <div>
                        <strong>Carte bancaire</strong>
                        <small>Paiements par carte</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($cardPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">🏦</div>

                    <div>
                        <strong>Virement</strong>
                        <small>Virements reçus</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($bankPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>


    {{-- PAIEMENTS FOURNISSEURS --}}
    <div class="module-card finance-card">

        <div class="finance-card-header">

            <div class="finance-card-title">

                <div class="finance-section-icon">
                    📦
                </div>

                <div>
                    <h2>Décaissements fournisseurs</h2>

                    <p>
                        Paiements effectués sur les achats.
                    </p>
                </div>

            </div>

        </div>

        <div class="payment-breakdown">

            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💵</div>

                    <div>
                        <strong>Espèces</strong>
                        <small>Paiements fournisseurs</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseCashPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">📱</div>

                    <div>
                        <strong>Mobile Money</strong>
                        <small>Orange Money + MTN MoMo</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseMobileMoneyPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💳</div>

                    <div>
                        <strong>Carte bancaire</strong>
                        <small>Paiements fournisseurs</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseCardPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">🏦</div>

                    <div>
                        <strong>Virement</strong>
                        <small>Virements fournisseurs</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseBankPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>

</div>


    <!-- {{-- =====================================================
         RÉSUMÉ FINANCIER
    ====================================================== --}}
    <div class="finance-section-grid">

    {{-- PAIEMENTS CLIENTS --}}
    <div class="module-card finance-card">

        <div class="finance-card-header">

            <div class="finance-card-title">

                <div class="finance-section-icon">
                    💰
                </div>

                <div>
                    <h2>Encaissements clients</h2>

                    <p>
                        Paiements reçus sur les ventes.
                    </p>
                </div>

            </div>

        </div>

        <div class="payment-breakdown">

            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💵</div>

                    <div>
                        <strong>Espèces</strong>
                        <small>Paiements clients en espèces</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($cashPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">📱</div>

                    <div>
                        <strong>Mobile Money</strong>
                        <small>Orange Money + MTN MoMo</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($mobileMoneyPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💳</div>

                    <div>
                        <strong>Carte bancaire</strong>
                        <small>Paiements par carte</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($cardPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">🏦</div>

                    <div>
                        <strong>Virement</strong>
                        <small>Virements reçus</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($bankPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>


    {{-- PAIEMENTS FOURNISSEURS --}}
    <div class="module-card finance-card">

        <div class="finance-card-header">

            <div class="finance-card-title">

                <div class="finance-section-icon">
                    📦
                </div>

                <div>
                    <h2>Décaissements fournisseurs</h2>

                    <p>
                        Paiements effectués sur les achats.
                    </p>
                </div>

            </div>

        </div>

        <div class="payment-breakdown">

            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💵</div>

                    <div>
                        <strong>Espèces</strong>
                        <small>Paiements fournisseurs</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseCashPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">📱</div>

                    <div>
                        <strong>Mobile Money</strong>
                        <small>Orange Money + MTN MoMo</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseMobileMoneyPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">💳</div>

                    <div>
                        <strong>Carte bancaire</strong>
                        <small>Paiements fournisseurs</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseCardPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="payment-row">

                <div class="payment-info">
                    <div class="payment-icon">🏦</div>

                    <div>
                        <strong>Virement</strong>
                        <small>Virements fournisseurs</small>
                    </div>
                </div>

                <strong class="payment-amount">
                    {{ number_format($purchaseBankPayments, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>

</div>
</div> -->


{{-- =========================================================
     ÉVOLUTION DES VENTES
========================================================== --}}
<div class="module-card finance-card finance-chart-card">

    <div class="finance-card-header">

        <div class="finance-card-title">

            <div class="finance-section-icon">
                📈
            </div>

            <div>

                <h2>Évolution des ventes</h2>

                <p>
                    Chiffre d'affaires réalisé jour par jour.
                </p>

            </div>

        </div>

    </div>


    <div class="finance-chart-container">

        @if($dailySales->count())

            @php
                $maxDaily = max(1, $dailySales->max('total'));
            @endphp

            <div class="finance-bars">

                @foreach($dailySales as $day)

                    @php
                        $height = ($day->total / $maxDaily) * 100;
                    @endphp

                    <div class="finance-bar-column">

                        <div class="finance-bar-value">
                            {{ number_format($day->total, 0, ',', ' ') }}
                        </div>

                        <div class="finance-bar-wrapper">

                            <div
                                class="finance-bar"
                                style="height: {{ max(5, $height) }}%;"
                                title="{{ number_format($day->total, 0, ',', ' ') }} FCFA"
                            ></div>

                        </div>

                        <div class="finance-bar-label">
                            {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}
                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="module-empty finance-empty">

                <div class="finance-empty-icon">
                    📊
                </div>

                <strong>
                    Aucune vente enregistrée
                </strong>

                <span>
                    Aucune donnée disponible pour la période sélectionnée.
                </span>

            </div>

        @endif

    </div>

</div>


{{-- =========================================================
     VENTES ET PAIEMENTS RÉCENTS
========================================================== --}}
<div class="finance-section-grid">


    {{-- =====================================================
         VENTES RÉCENTES
    ====================================================== --}}
    <div class="module-card finance-card">

        <div class="finance-card-header">

            <div class="finance-card-title">

                <div class="finance-section-icon">
                    🛒
                </div>

                <div>

                    <h2>Ventes récentes</h2>

                    <p>
                        Les dernières ventes enregistrées.
                    </p>

                </div>

            </div>


            <a
                href="{{ route('company.sales.index') }}"
                class="module-btn module-btn-secondary"
            >
                Voir tout
            </a>

        </div>


        <div class="finance-list">

            @forelse($recentSales as $sale)

                <a
                    href="{{ route('company.sales.show', $sale) }}"
                    class="finance-list-item"
                >

                    <div class="finance-list-left">

                        <div class="finance-list-icon">
                            🧾
                        </div>

                        <div>

                            <strong>
                                {{ $sale->reference }}
                            </strong>

                            <small>
                                {{ $sale->client?->name ?? 'Client comptant' }}
                            </small>

                        </div>

                    </div>


                    <div class="finance-list-right">

                        <strong>
                            {{ number_format($sale->total, 0, ',', ' ') }}
                            FCFA
                        </strong>

                        <span>
                            Voir le détail →
                        </span>

                    </div>

                </a>

            @empty

                <div class="module-empty">

                    <div class="finance-empty-icon">
                        🧾
                    </div>

                    <strong>
                        Aucune vente
                    </strong>

                    <span>
                        Aucune vente récente à afficher.
                    </span>

                </div>

            @endforelse

        </div>

    </div>


    {{-- =====================================================
         PAIEMENTS RÉCENTS
    ====================================================== --}}
    <div class="module-card finance-card">

        <div class="finance-card-header">

            <div class="finance-card-title">

                <div class="finance-section-icon">
                    💰
                </div>

                <div>

                    <h2>Paiements récents</h2>

                    <p>
                        Les derniers encaissements.
                    </p>

                </div>

            </div>

        </div>


        <div class="finance-list">

            @forelse($recentPayments as $payment)

                <div class="finance-list-item">

                    <div class="finance-list-left">

                        <div class="finance-list-icon">
                            💳
                        </div>

                        <div>

                            <strong>
                                {{ $payment->sale?->reference ?? '—' }}
                            </strong>

                            <small>

                                {{ $payment->method_label }}

                                @if($payment->provider_label)
                                    · {{ $payment->provider_label }}
                                @endif

                            </small>

                        </div>

                    </div>


                    <div class="finance-list-right">

                        <strong>
                            {{ number_format($payment->amount, 0, ',', ' ') }}
                            FCFA
                        </strong>

                        <span
                            class="finance-payment-status finance-status-{{ $payment->status }}"
                        >
                            {{ $payment->status_label }}
                        </span>

                    </div>

                </div>

            @empty

                <div class="module-empty">

                    <div class="finance-empty-icon">
                        💳
                    </div>

                    <strong>
                        Aucun paiement
                    </strong>

                    <span>
                        Aucun encaissement récent à afficher.
                    </span>

                </div>

            @endforelse

        </div>

    </div>

</div>

</div>

@endsection
