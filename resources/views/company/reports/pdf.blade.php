<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title>Rapport financier</title>

    <style>
        @page {
            margin: 30px 30px 35px 30px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #315bea;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 65%;
            vertical-align: top;
        }

        .header-right {
            width: 35%;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .report-title {
            font-size: 15px;
            font-weight: bold;
            color: #315bea;
            margin-bottom: 4px;
        }

        .company-info {
            color: #6b7280;
            font-size: 9px;
        }

        .period-box {
            display: inline-block;
            border: 1px solid #dbe3f0;
            background: #f8fafc;
            padding: 8px 10px;
            border-radius: 5px;
            text-align: left;
            min-width: 145px;
        }

        .period-label {
            color: #6b7280;
            font-size: 8px;
            text-transform: uppercase;
        }

        .period-value {
            color: #111827;
            font-weight: bold;
            font-size: 10px;
            margin-top: 2px;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            border-left: 4px solid #315bea;
            padding-left: 7px;
            margin-bottom: 9px;
        }

        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-left: -6px;
            margin-right: -6px;
        }

        .card {
            width: 25%;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            padding: 10px;
            vertical-align: top;
        }

        .card-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .card-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        .card-value.blue {
            color: #315bea;
        }

        .card-value.green {
            color: #15803d;
        }

        .card-value.red {
            color: #dc2626;
        }

        .card-value.orange {
            color: #c2410c;
        }

        .two-columns {
            width: 100%;
            border-collapse: collapse;
        }

        .two-columns td {
            width: 50%;
            vertical-align: top;
        }

        .two-columns td:first-child {
            padding-right: 7px;
        }

        .two-columns td:last-child {
            padding-left: 7px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
        }

        .info-table th {
            background: #f1f5f9;
            color: #374151;
            font-size: 8px;
            text-align: left;
            padding: 7px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-table td {
            padding: 7px;
            border-bottom: 1px solid #eef2f7;
            font-size: 8.5px;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .summary-row td {
            font-weight: bold;
            background: #f8fafc;
        }

        .positive {
            color: #15803d;
            font-weight: bold;
        }

        .negative {
            color: #dc2626;
            font-weight: bold;
        }

        .warning {
            color: #c2410c;
            font-weight: bold;
        }

        .small-note {
            color: #6b7280;
            font-size: 8px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 25px;
            padding-top: 9px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 8px;
        }

        .page-break {
            page-break-before: always;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            background: #eff6ff;
            color: #315bea;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="page">

    {{-- ========================================================= --}}
    {{-- EN-TÊTE --}}
    {{-- ========================================================= --}}

    <div class="header">

        <table class="header-table">
            <tr>

                <td class="header-left">

                    <div class="company-name">
                        {{ $company->name ?? 'Entreprise' }}
                    </div>

                    <div class="report-title">
                        Rapport financier et d'activité
                    </div>

                    <div class="company-info">

                        @if(!empty($company->email))
                            {{ $company->email }}
                        @endif

                        @if(!empty($company->phone))
                            @if(!empty($company->email)) · @endif
                            {{ $company->phone }}
                        @endif

                        @if(!empty($company->city))
                            @if(!empty($company->email) || !empty($company->phone)) · @endif
                            {{ $company->city }}
                        @endif

                    </div>

                </td>

                <td class="header-right">

                    <div class="period-box">

                        <div class="period-label">
                            Période
                        </div>

                        <div class="period-value">

                            @if($period['period'] === 'today')
                                Aujourd'hui
                            @elseif($period['period'] === 'week')
                                Cette semaine
                            @elseif($period['period'] === 'year')
                                Cette année
                            @else
                                Ce mois
                            @endif

                        </div>

                        <div class="small-note">
                            Du {{ $period['startDate']->format('d/m/Y') }}
                            au {{ $period['endDate']->format('d/m/Y') }}
                        </div>

                    </div>

                </td>

            </tr>
        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- SYNTHÈSE FINANCIÈRE --}}
    {{-- ========================================================= --}}

    <div class="section">

        <div class="section-title">
            Synthèse financière
        </div>

        <table class="cards">

            <tr>

                <td class="card">

                    <div class="card-label">
                        Chiffre d'affaires
                    </div>

                    <div class="card-value blue">
                        {{ number_format($salesTotal, 0, ',', ' ') }} FCFA
                    </div>

                </td>

                <td class="card">

                    <div class="card-label">
                        Encaissements
                    </div>

                    <div class="card-value green">
                        {{ number_format($paymentsTotal, 0, ',', ' ') }} FCFA
                    </div>

                </td>

                <td class="card">

                    <div class="card-label">
                        Paiements achats
                    </div>

                    <div class="card-value red">
                        {{ number_format($purchasePaymentsTotal, 0, ',', ' ') }} FCFA
                    </div>

                </td>

                <td class="card">

                    <div class="card-label">
                        Solde net
                    </div>

                    <div class="card-value {{ $netCashFlow >= 0 ? 'green' : 'red' }}">
                        {{ number_format($netCashFlow, 0, ',', ' ') }} FCFA
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- VENTES / ACHATS --}}
    {{-- ========================================================= --}}

    <div class="section">

        <table class="two-columns">

            <tr>

                <td>

                    <div class="section-title">
                        Ventes
                    </div>

                    <table class="info-table">

                        <tr>
                            <td>Nombre de ventes</td>
                            <td class="text-right">
                                {{ $salesCount }}
                            </td>
                        </tr>

                        <tr>
                            <td>Sous-total</td>
                            <td class="text-right">
                                {{ number_format($salesSubtotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr>
                            <td>Remises</td>
                            <td class="text-right">
                                {{ number_format($salesDiscount, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr>
                            <td>Taxes</td>
                            <td class="text-right">
                                {{ number_format($salesTax, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr class="summary-row">
                            <td>Total ventes</td>
                            <td class="text-right">
                                {{ number_format($salesTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                    </table>

                </td>


                <td>

                    <div class="section-title">
                        Achats
                    </div>

                    <table class="info-table">

                        <tr>
                            <td>Nombre d'achats</td>
                            <td class="text-right">
                                {{ $purchasesCount }}
                            </td>
                        </tr>

                        <tr>
                            <td>Sous-total</td>
                            <td class="text-right">
                                {{ number_format($purchasesSubtotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr>
                            <td>Remises</td>
                            <td class="text-right">
                                {{ number_format($purchasesDiscount, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr>
                            <td>Taxes</td>
                            <td class="text-right">
                                {{ number_format($purchasesTax, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr class="summary-row">
                            <td>Total achats</td>
                            <td class="text-right">
                                {{ number_format($purchasesTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                    </table>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- ENCAISSEMENTS --}}
    {{-- ========================================================= --}}

    <div class="section">

        <div class="section-title">
            Encaissements clients
        </div>

        <table class="info-table">

            <thead>
                <tr>
                    <th>Mode de paiement</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>Espèces</td>
                    <td class="text-right">
                        {{ number_format($cashTotal, 0, ',', ' ') }} FCFA
                    </td>
                </tr>

                <tr>
                    <td>Mobile Money</td>
                    <td class="text-right">
                        {{ number_format($mobileMoneyTotal, 0, ',', ' ') }} FCFA
                    </td>
                </tr>

                <tr>
                    <td>Carte bancaire</td>
                    <td class="text-right">
                        {{ number_format($cardTotal, 0, ',', ' ') }} FCFA
                    </td>
                </tr>

                <tr>
                    <td>Virement bancaire</td>
                    <td class="text-right">
                        {{ number_format($bankTransferTotal, 0, ',', ' ') }} FCFA
                    </td>
                </tr>

                <tr>
                    <td>Autres</td>
                    <td class="text-right">
                        {{ number_format($otherPaymentsTotal, 0, ',', ' ') }} FCFA
                    </td>
                </tr>

                <tr class="summary-row">
                    <td>Total encaissé</td>
                    <td class="text-right">
                        {{ number_format($paymentsTotal, 0, ',', ' ') }} FCFA
                    </td>
                </tr>

            </tbody>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- MOBILE MONEY --}}
    {{-- ========================================================= --}}

    <div class="section">

        <table class="two-columns">

            <tr>

                <td>

                    <div class="section-title">
                        Mobile Money — Clients
                    </div>

                    <table class="info-table">

                        <tr>
                            <td>Orange Money</td>
                            <td class="text-right">
                                {{ number_format($orangeMoneyTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr>
                            <td>MTN MoMo</td>
                            <td class="text-right">
                                {{ number_format($mtnMomoTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr class="summary-row">
                            <td>Total Mobile Money</td>
                            <td class="text-right">
                                {{ number_format($mobileMoneyTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                    </table>

                </td>


                <td>

                    <div class="section-title">
                        Mobile Money — Fournisseurs
                    </div>

                    <table class="info-table">

                        <tr>
                            <td>Orange Money</td>
                            <td class="text-right">
                                {{ number_format($purchaseOrangeMoneyTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr>
                            <td>MTN MoMo</td>
                            <td class="text-right">
                                {{ number_format($purchaseMtnMomoTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                        <tr class="summary-row">
                            <td>Total Mobile Money</td>
                            <td class="text-right">
                                {{ number_format($purchaseMobileMoneyTotal, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>

                    </table>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- CRÉANCES / DETTES --}}
    {{-- ========================================================= --}}

    <div class="section">

        <div class="section-title">
            Situation financière
        </div>

        <table class="cards">

            <tr>

                <td class="card">

                    <div class="card-label">
                        Créances clients
                    </div>

                    <div class="card-value orange">
                        {{ number_format($receivablesTotal, 0, ',', ' ') }} FCFA
                    </div>

                </td>

                <td class="card">

                    <div class="card-label">
                        Dettes fournisseurs
                    </div>

                    <div class="card-value red">
                        {{ number_format($payablesTotal, 0, ',', ' ') }} FCFA
                    </div>

                </td>

                <td class="card">

                    <div class="card-label">
                        Paiements en attente
                    </div>

                    <div class="card-value orange">
                        {{ number_format($pendingPayments, 0, ',', ' ') }} FCFA
                    </div>

                </td>

                <td class="card">

                    <div class="card-label">
                        Solde net
                    </div>

                    <div class="card-value {{ $netCashFlow >= 0 ? 'green' : 'red' }}">
                        {{ number_format($netCashFlow, 0, ',', ' ') }} FCFA
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- STOCK --}}
    {{-- ========================================================= --}}

    <div class="section">

        <div class="section-title">
            Situation du stock
        </div>

        <table class="info-table">

            <tr>
                <td>Nombre de produits</td>
                <td class="text-right">
                    {{ $productsCount }}
                </td>
            </tr>

            <tr>
                <td>Quantité en stock</td>
                <td class="text-right">
                    {{ number_format($stockQuantity, 0, ',', ' ') }}
                </td>
            </tr>

            <tr>
                <td>Valeur d'achat du stock</td>
                <td class="text-right">
                    {{ number_format($stockValuePurchase, 0, ',', ' ') }} FCFA
                </td>
            </tr>

            <tr>
                <td>Valeur de vente du stock</td>
                <td class="text-right">
                    {{ number_format($stockValueSelling, 0, ',', ' ') }} FCFA
                </td>
            </tr>

            <tr>
                <td>Produits en stock faible</td>
                <td class="text-right warning">
                    {{ $lowStock }}
                </td>
            </tr>

            <tr>
                <td>Produits en rupture</td>
                <td class="text-right negative">
                    {{ $outOfStock }}
                </td>
            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- TOP PRODUITS --}}
    {{-- ========================================================= --}}

    @if($topProducts->count())

        <div class="section">

            <div class="section-title">
                Produits les plus vendus
            </div>

            <table class="info-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th class="text-right">Quantité</th>
                        <th class="text-right">Chiffre d'affaires</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($topProducts as $index => $product)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $product->name }}
                            </td>

                            <td class="text-right">
                                {{ number_format($product->quantity, 0, ',', ' ') }}
                            </td>

                            <td class="text-right">
                                {{ number_format($product->total, 0, ',', ' ') }} FCFA
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- DÉTAIL DES VENTES --}}
    {{-- ========================================================= --}}

    @if($sales->count())

        <div class="section page-break">

            <div class="section-title">
                Détail des ventes
            </div>

            <table class="info-table">

                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Vendeur</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($sales as $sale)

                        <tr>

                            <td>
                                {{ $sale->reference ?? '—' }}
                            </td>

                            <td>
                                {{ optional($sale->created_at)->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $sale->client?->name ?? 'Client comptoir' }}
                            </td>

                            <td>
                                {{ $sale->user?->name ?? '—' }}
                            </td>

                            <td class="text-right">
                                {{ number_format((float) $sale->total, 0, ',', ' ') }} FCFA
                            </td>

                        </tr>

                    @endforeach

                </tbody>

                <tfoot>

                    <tr class="summary-row">

                        <td colspan="4">
                            TOTAL
                        </td>

                        <td class="text-right">
                            {{ number_format($salesTotal, 0, ',', ' ') }} FCFA
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- DÉTAIL DES ACHATS --}}
    {{-- ========================================================= --}}

    @if($purchases->count())

        <div class="section page-break">

            <div class="section-title">
                Détail des achats
            </div>

            <table class="info-table">

                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Fournisseur</th>
                        <th>Utilisateur</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($purchases as $purchase)

                        <tr>

                            <td>
                                {{ $purchase->reference ?? '—' }}
                            </td>

                            <td>
                                {{ optional($purchase->created_at)->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $purchase->supplier?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $purchase->user?->name ?? '—' }}
                            </td>

                            <td class="text-right">
                                {{ number_format((float) $purchase->total, 0, ',', ' ') }} FCFA
                            </td>

                        </tr>

                    @endforeach

                </tbody>

                <tfoot>

                    <tr class="summary-row">

                        <td colspan="4">
                            TOTAL
                        </td>

                        <td class="text-right">
                            {{ number_format($purchasesTotal, 0, ',', ' ') }} FCFA
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PAIEMENTS RÉCENTS --}}
    {{-- ========================================================= --}}

    @if($payments->count())

        <div class="section page-break">

            <div class="section-title">
                Encaissements clients
            </div>

            <table class="info-table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Vente</th>
                        <th>Mode</th>
                        <th>Utilisateur</th>
                        <th class="text-right">Montant</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($payments as $payment)

                        <tr>

                            <td>
                                {{ optional($payment->created_at)->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $payment->sale?->reference ?? '—' }}
                            </td>

                            <td>
                                @switch($payment->method)

                                    @case('cash')
                                        Espèces
                                        @break

                                    @case('mobile_money')
                                        Mobile Money
                                        @break

                                    @case('card')
                                        Carte
                                        @break

                                    @case('bank_transfer')
                                        Virement
                                        @break

                                    @default
                                        Autre

                                @endswitch
                            </td>

                            <td>
                                {{ $payment->user?->name ?? '—' }}
                            </td>

                            <td class="text-right">
                                {{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA
                            </td>

                        </tr>

                    @endforeach

                </tbody>

                <tfoot>

                    <tr class="summary-row">

                        <td colspan="4">
                            TOTAL ENCAISSÉ
                        </td>

                        <td class="text-right">
                            {{ number_format($paymentsTotal, 0, ',', ' ') }} FCFA
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PIED DE PAGE --}}
    {{-- ========================================================= --}}

    <div class="footer">

        Rapport généré le
        {{ now()->format('d/m/Y à H:i') }}

        — ERP SaaS

    </div>

</div>

</body>
</html>