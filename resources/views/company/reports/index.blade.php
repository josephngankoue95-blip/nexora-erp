@extends('layouts.company')

@section('title', 'Rapports')

@section('content')

<div class="reports-page">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}

    <div class="reports-header">

        <div class="reports-header-content">

            <div class="reports-title-area">

                <div class="reports-title-icon">
                    Ra
                </div>

                <div>
                    <h1>Rapports</h1>

                    <p>
                        Analysez les performances de votre entreprise
                        et consultez vos principales statistiques.
                    </p>
                </div>

            </div>

            <div class="reports-actions">

                <a
                    href="{{ route('company.reports.export.excel', ['period' => $period]) }}"
                    class="report-action-btn excel"
                >
                    <i data-lucide="file-spreadsheet"></i>
                    <span>Exporter Excel</span>
                </a>

                <a
                    href="{{ route('company.reports.export.pdf', ['period' => $period]) }}"
                    class="report-action-btn pdf"
                    target="_blank"
                >
                    <i data-lucide="file-text"></i>
                    <span>Exporter PDF</span>
                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRE PÉRIODE
    ========================================================== --}}

    <div class="reports-filter-card">

        <form
            action="{{ route('company.reports.index') }}"
            method="GET"
            class="reports-filter-form"
        >

            <div class="filter-heading">

                <div class="filter-icon">
                    Pe
                </div>

                <div>
                    <strong>Période du rapport</strong>

                    <span>
                        Sélectionnez la période à analyser.
                    </span>
                </div>

            </div>

            <div class="filter-controls">

                <div class="filter-select-wrapper">

                    <label for="period">
                        Période
                    </label>

                    <select
                        name="period"
                        id="period"
                    >

                        <option
                            value="today"
                            {{ $period === 'today' ? 'selected' : '' }}
                        >
                            Aujourd'hui
                        </option>

                        <option
                            value="week"
                            {{ $period === 'week' ? 'selected' : '' }}
                        >
                            Cette semaine
                        </option>

                        <option
                            value="month"
                            {{ $period === 'month' ? 'selected' : '' }}
                        >
                            Ce mois
                        </option>

                        <option
                            value="year"
                            {{ $period === 'year' ? 'selected' : '' }}
                        >
                            Cette année
                        </option>

                    </select>

                </div>

                <button
                    type="submit"
                    class="report-filter-btn"
                >
                    <i data-lucide="refresh-cw"></i>
                    Actualiser
                </button>

            </div>

        </form>

        <div class="report-period-info">

            <i data-lucide="calendar-days"></i>

            <span>
                Du
                <strong>
                    {{ $startDate->format('d/m/Y') }}
                </strong>

                au

                <strong>
                    {{ $endDate->format('d/m/Y') }}
                </strong>
            </span>

        </div>

    </div>


    {{-- =========================================================
         STATISTIQUES PRINCIPALES
    ========================================================== --}}

    <div class="reports-stats-grid">

        {{-- VENTES --}}

        <div class="report-stat-card sales">

            <div class="report-stat-top">

                <div class="report-stat-icon">
                    Ve
                </div>

                <span class="report-stat-label">
                    Ventes
                </span>

            </div>

            <div class="report-stat-value">
                {{ number_format($salesTotal, 0, ',', ' ') }}
                <small>FCFA</small>
            </div>

            <div class="report-stat-footer">

                <span>
                    {{ $salesCount }}
                    vente{{ $salesCount > 1 ? 's' : '' }}
                </span>

                <span>
                    <i data-lucide="trending-up"></i>
                </span>

            </div>

        </div>


        {{-- PAIEMENTS --}}

        <div class="report-stat-card payments">

            <div class="report-stat-top">

                <div class="report-stat-icon">
                    Pe
                </div>

                <span class="report-stat-label">
                    Paiements encaissés
                </span>

            </div>

            <div class="report-stat-value">
                {{ number_format($paymentsTotal, 0, ',', ' ') }}
                <small>FCFA</small>
            </div>

            <div class="report-stat-footer">

                <span>
                    Paiements confirmés
                </span>

                <span>
                    <i data-lucide="check-circle"></i>
                </span>

            </div>

        </div>


        {{-- STOCK --}}

        <div class="report-stat-card stock">

            <div class="report-stat-top">

                <div class="report-stat-icon">
                    Vs
                </div>

                <span class="report-stat-label">
                    Valeur du stock
                </span>

            </div>

            <div class="report-stat-value">
                {{ number_format($stockValuePurchase, 0, ',', ' ') }}
                <small>FCFA</small>
            </div>

            <div class="report-stat-footer">

                <span>
                    {{ number_format($stockQuantity, 0, ',', ' ') }}
                    unités
                </span>

                <span>
                    <i data-lucide="package"></i>
                </span>

            </div>

        </div>


        {{-- CRÉANCES --}}

        <div class="report-stat-card receivables">

            <div class="report-stat-top">

                <div class="report-stat-icon">
                    Cr
                </div>

                <span class="report-stat-label">
                    Créances
                </span>

            </div>

            <div class="report-stat-value">
                {{ number_format($receivablesTotal, 0, ',', ' ') }}
                <small>FCFA</small>
            </div>

            <div class="report-stat-footer">

                <span>
                    Montant restant à recouvrer
                </span>

                <span>
                    <i data-lucide="clock-3"></i>
                </span>

            </div>

        </div>

    </div>

    <div class="reports-two-columns">

    {{-- ACHATS --}}
    <div class="report-panel">

        <div class="report-panel-header">

            <div>
                <h2>Résumé des achats</h2>

                <p>
                    Détail des achats de la période.
                </p>
            </div>

            <div class="panel-icon purple">
                Ra
            </div>

        </div>


        <div class="report-summary-list">

            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot blue"></span>
                    <span>Nombre d'achats</span>
                </div>

                <strong>
                    {{ $purchasesCount }}
                </strong>

            </div>


            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot blue"></span>
                    <span>Sous-total</span>
                </div>

                <strong>
                    {{ number_format($purchasesSubtotal, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot orange"></span>
                    <span>Remises</span>
                </div>

                <strong>
                    {{ number_format($purchasesDiscount, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot purple"></span>
                    <span>Taxes</span>
                </div>

                <strong>
                    {{ number_format($purchasesTax, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="report-summary-total">

                <span>
                    Total des achats
                </span>

                <strong>
                    {{ number_format($purchasesTotal, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>


    {{-- PAIEMENTS FOURNISSEURS --}}
    <div class="report-panel">

        <div class="report-panel-header">

            <div>
                <h2>Paiements fournisseurs</h2>

                <p>
                    Décaissements effectués durant la période.
                </p>
            </div>

            <div class="panel-icon orange">
                Pf
            </div>

        </div>


        <div class="report-summary-list">

            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot blue"></span>
                    <span>Total payé</span>
                </div>

                <strong>
                    {{ number_format($purchasePaymentsTotal, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot orange"></span>
                    <span>Espèces</span>
                </div>

                <strong>
                    {{ number_format($purchaseCashTotal, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot purple"></span>
                    <span>Mobile Money</span>
                </div>

                <strong>
                    {{ number_format($purchaseMobileMoneyTotal, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="report-summary-item">

                <div class="summary-left">
                    <span class="summary-dot blue"></span>
                    <span>Virement</span>
                </div>

                <strong>
                    {{ number_format($purchaseBankTransferTotal, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            <div class="report-summary-total">

                <span>
                    Dettes fournisseurs
                </span>

                <strong>
                    {{ number_format($payablesTotal, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>

</div>

{{--=============================================================
       TRESORERIE NETTE
=========================================================== --}}
<div class="report-panel">

    <div class="report-panel-header">

        <div>

            <h2>Trésorerie nette</h2>

            <p>
                Comparaison des encaissements clients et des paiements fournisseurs.
            </p>

        </div>

        <div class="panel-icon green">
            Tn
        </div>

    </div>


    <div class="report-summary-list">

        <div class="report-summary-item">

            <div class="summary-left">
                <span class="summary-dot blue"></span>
                <span>Encaissements clients</span>
            </div>

            <strong>
                {{ number_format($paymentsTotal, 0, ',', ' ') }}
                FCFA
            </strong>

        </div>


        <div class="report-summary-item">

            <div class="summary-left">
                <span class="summary-dot orange"></span>
                <span>Décaissements fournisseurs</span>
            </div>

            <strong>
                {{ number_format($purchasePaymentsTotal, 0, ',', ' ') }}
                FCFA
            </strong>

        </div>


        <div class="report-summary-total">

            <span>
                Solde net
            </span>

            <strong>
                {{ number_format($netCashFlow, 0, ',', ' ') }}
                FCFA
            </strong>

        </div>

    </div>

</div>


    {{-- =========================================================
         VENTES ET PAIEMENTS
    ========================================================== --}}

    <div class="reports-two-columns">

        {{-- RÉSUMÉ DES VENTES --}}

        <div class="report-panel">

            <div class="report-panel-header">

                <div>
                    <h2>Résumé des ventes</h2>

                    <p>
                        Détail du chiffre d'affaires de la période.
                    </p>
                </div>

                <div class="panel-icon blue">
                    <i data-lucide="receipt"></i>
                </div>

            </div>

            <div class="report-summary-list">

                <div class="report-summary-item">

                    <div class="summary-left">
                        <span class="summary-dot blue"></span>
                        <span>Sous-total</span>
                    </div>

                    <strong>
                        {{ number_format($salesSubtotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>


                <div class="report-summary-item">

                    <div class="summary-left">
                        <span class="summary-dot orange"></span>
                        <span>Remises</span>
                    </div>

                    <strong>
                        {{ number_format($salesDiscount, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>


                <div class="report-summary-item">

                    <div class="summary-left">
                        <span class="summary-dot purple"></span>
                        <span>Taxes</span>
                    </div>

                    <strong>
                        {{ number_format($salesTax, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>


                <div class="report-summary-total">

                    <span>Total des ventes</span>

                    <strong>
                        {{ number_format($salesTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>

            </div>

        </div>


        {{-- PAIEMENTS PAR MODE --}}

        <div class="report-panel">

            <div class="report-panel-header">

                <div>
                    <h2>Modes de paiement</h2>

                    <p>
                        Répartition des encaissements.
                    </p>
                </div>

                <div class="panel-icon green">
                    <i data-lucide="credit-card"></i>
                </div>

            </div>

            <div class="payment-method-list">

                <div class="payment-method-item">

                    <div class="payment-method-name">

                        <span class="payment-method-icon cash">
                            <i data-lucide="banknote"></i>
                        </span>

                        <span>Espèces</span>

                    </div>

                    <strong>
                        {{ number_format($cashTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>


                <!-- <div class="payment-method-item">

                    <div class="payment-method-name">

                        <span class="payment-method-icon mobile">
                            <i data-lucide="smartphone"></i>
                        </span>

                        <span>Mobile Money</span>

                    </div>

                    <strong>
                        {{ number_format($mobileMoneyTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>
 -->

                <div class="payment-method-item">

                    <div class="payment-method-name">

                        <span class="payment-method-icon card">
                            <i data-lucide="credit-card"></i>
                        </span>

                        <span>Carte bancaire</span>

                    </div>

                    <strong>
                        {{ number_format($cardTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>


                <div class="payment-method-item">

                    <div class="payment-method-name">

                        <span class="payment-method-icon bank">
                            <i data-lucide="landmark"></i>
                        </span>

                        <span>Virement bancaire</span>

                    </div>

                    <strong>
                        {{ number_format($bankTransferTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>


                <div class="payment-method-item">

                    <div class="payment-method-name">

                        <span class="payment-method-icon other">
                            <i data-lucide="more-horizontal"></i>
                        </span>

                        <span>Autres</span>

                    </div>

                    <strong>
                        {{ number_format($otherPaymentsTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>

            </div>

        </div>

    </div>


    <!-- {{-- =========================================================
         MOBILE MONEY
    ========================================================== --}}

    <div class="report-panel mobile-money-panel">

        <div class="report-panel-header">

            <div>
                <h2>Mobile Money</h2>

                <p>
                    Détail des paiements Mobile Money.
                </p>
            </div>

            <div class="panel-icon orange">
                <i data-lucide="smartphone"></i>
            </div>

        </div>

        <div class="mobile-money-grid">

            <div class="mobile-money-card orange-money">

                <div class="mobile-money-logo">
                    OM
                </div>

                <div>

                    <span>Orange Money</span>

                    <strong>
                        {{ number_format($orangeMoneyTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>

            </div>


            <div class="mobile-money-card mtn-money">

                <div class="mobile-money-logo">
                    MTN
                </div>

                <div>

                    <span>MTN Mobile Money</span>

                    <strong>
                        {{ number_format($mtnMomoTotal, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>

            </div>


            <div class="mobile-money-card pending">

                <div class="mobile-money-logo">
                    <i data-lucide="clock"></i>
                </div>

                <div>

                    <span>Paiements en attente</span>

                    <strong>
                        {{ number_format($pendingPayments, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>

            </div>

        </div>

    </div> -->


    {{-- =========================================================
         STOCK
    ========================================================== --}}

    <div class="reports-two-columns">

        <div class="report-panel">

            <div class="report-panel-header">

                <div>
                    <h2>Situation du stock</h2>

                    <p>
                        État actuel de votre inventaire.
                    </p>
                </div>

                <div class="panel-icon purple">
                    <i data-lucide="warehouse"></i>
                </div>

            </div>

            <div class="stock-overview-grid">

                <div class="stock-overview-item">

                    <span class="stock-number">
                        {{ $productsCount }}
                    </span>

                    <span class="stock-label">
                        Produits
                    </span>

                </div>


                <div class="stock-overview-item">

                    <span class="stock-number">
                        {{ number_format($stockQuantity, 0, ',', ' ') }}
                    </span>

                    <span class="stock-label">
                        Quantité
                    </span>

                </div>


                <div class="stock-overview-item warning">

                    <span class="stock-number">
                        {{ $lowStock }}
                    </span>

                    <span class="stock-label">
                        Stock faible
                    </span>

                </div>


                <div class="stock-overview-item danger">

                    <span class="stock-number">
                        {{ $outOfStock }}
                    </span>

                    <span class="stock-label">
                        Ruptures
                    </span>

                </div>

            </div>


            <div class="stock-values">

                <div>

                    <span>Valeur d'achat</span>

                    <strong>
                        {{ number_format($stockValuePurchase, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>

                <div>

                    <span>Valeur de vente</span>

                    <strong>
                        {{ number_format($stockValueSelling, 0, ',', ' ') }}
                        FCFA
                    </strong>

                </div>

            </div>

        </div>


        <div class="report-panel">

            <div class="report-panel-header">

                <div>
                    <h2>Mouvements de stock</h2>

                    <p>
                        Mouvements enregistrés durant la période.
                    </p>
                </div>

                <div class="panel-icon blue">
                    <i data-lucide="arrow-left-right"></i>
                </div>

            </div>

            <div class="stock-movements">

                <div class="stock-movement-item entry">

                    <div class="movement-icon">
                        <i data-lucide="arrow-down-to-line"></i>
                    </div>

                    <div class="movement-content">

                        <span>
                            Entrées
                        </span>

                        <strong>
                            {{ number_format($stockEntries, 0, ',', ' ') }}
                        </strong>

                    </div>

                </div>


                <div class="stock-movement-item exit">

                    <div class="movement-icon">
                        <i data-lucide="arrow-up-from-line"></i>
                    </div>

                    <div class="movement-content">

                        <span>
                            Sorties
                        </span>

                        <strong>
                            {{ number_format($stockExits, 0, ',', ' ') }}
                        </strong>

                    </div>

                </div>


                <div class="stock-movement-item adjustment">

                    <div class="movement-icon">
                        <i data-lucide="refresh-cw"></i>
                    </div>

                    <div class="movement-content">

                        <span>
                            Ajustements
                        </span>

                        <strong>
                            {{ number_format($stockAdjustments, 0, ',', ' ') }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MEILLEURS PRODUITS
    ========================================================== --}}

    <div class="report-panel">

        <div class="report-panel-header">

            <div>
                <h2>Meilleurs produits vendus</h2>

                <p>
                    Les produits ayant généré le plus de ventes
                    durant la période.
                </p>
            </div>

            <div class="panel-icon blue">
                <i data-lucide="trophy"></i>
            </div>

        </div>


        @if($topProducts->count())

            <div class="report-table-wrapper">

                <table class="report-table">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th>Quantité vendue</th>
                            <th>Chiffre d'affaires</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($topProducts as $index => $product)

                            <tr>

                                <td>

                                    <span class="ranking-number">
                                        {{ $index + 1 }}
                                    </span>

                                </td>

                                <td>

                                    <div class="product-report-name">

                                        <div class="product-report-icon">
                                            <i data-lucide="package"></i>
                                        </div>

                                        <strong>
                                            {{ $product->name }}
                                        </strong>

                                    </div>

                                </td>

                                <td>
                                    {{ number_format($product->quantity, 0, ',', ' ') }}
                                </td>

                                <td>

                                    <strong>
                                        {{ number_format($product->total, 0, ',', ' ') }}
                                        FCFA
                                    </strong>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="report-empty">

                <i data-lucide="package-open"></i>

                <h3>Aucune vente</h3>

                <p>
                    Aucun produit vendu durant cette période.
                </p>

            </div>

        @endif

    </div>


    {{-- =========================================================
         VENTES JOURNALIÈRES
    ========================================================== --}}

    <div class="report-panel">

        <div class="report-panel-header">

            <div>
                <h2>Ventes par jour</h2>

                <p>
                    Évolution du chiffre d'affaires durant la période.
                </p>
            </div>

            <div class="panel-icon green">
                <i data-lucide="chart-no-axes-combined"></i>
            </div>

        </div>


        @if($dailySales->count())

            <div class="report-table-wrapper">

                <table class="report-table">

                    <thead>

                        <tr>
                            <th>Date</th>
                            <th>Nombre de ventes</th>
                            <th>Chiffre d'affaires</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($dailySales as $day)

                            <tr>

                                <td>
                                    <strong>
                                        {{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $day->count }}
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($day->total, 0, ',', ' ') }}
                                        FCFA
                                    </strong>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="report-empty">

                <i data-lucide="calendar-x"></i>

                <h3>Aucune donnée</h3>

                <p>
                    Aucune vente enregistrée durant cette période.
                </p>

            </div>

        @endif

    </div>


    {{-- =========================================================
         PAIEMENTS RÉCENTS
    ========================================================== --}}

    <div class="report-panel">

        <div class="report-panel-header">

            <div>
                <h2>Paiements récents</h2>

                <p>
                    Les derniers paiements enregistrés.
                </p>
            </div>

            <div class="panel-icon green">
                <i data-lucide="history"></i>
            </div>

        </div>


        @if($recentPayments->count())

            <div class="report-table-wrapper">

                <table class="report-table">

                    <thead>

                        <tr>
                            <th>Référence</th>
                            <th>Vente</th>
                            <th>Mode</th>
                            <th>Montant</th>
                            <th>Date</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($recentPayments as $payment)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $payment->reference ?? '—' }}
                                    </strong>

                                </td>

                                <td>

                                    @if($payment->sale)

                                        {{ $payment->sale->reference }}

                                    @else

                                        —

                                    @endif

                                </td>

                                <td>
                                    {{ $payment->method_label ?? ucfirst($payment->method) }}
                                </td>

                                <td>

                                    <strong>
                                        {{ number_format($payment->amount, 0, ',', ' ') }}
                                        FCFA
                                    </strong>

                                </td>

                                <td>

                                    {{ optional($payment->created_at)->format('d/m/Y H:i') }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="report-empty">

                <i data-lucide="wallet-cards"></i>

                <h3>Aucun paiement</h3>

                <p>
                    Aucun paiement récent à afficher.
                </p>

            </div>

        @endif

    </div>

</div>

@endsection