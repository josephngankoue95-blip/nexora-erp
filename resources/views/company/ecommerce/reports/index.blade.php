
@extends('layouts.ecommerce')

@section('title', 'Rapports E-commerce')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ecommerce-reports.css') }}">
@endpush

@section('content')

<div class="ecommerce-reports-page">

    {{-- EN-TÊTE --}}
    <div class="reports-header">
        <div>
            <h1>Rapports E-commerce</h1>
            <p>Analysez les commandes, les ventes et les paiements de votre boutique.</p>
        </div>

        <form method="GET"
              action="{{ route('company.ecommerce.reports.index') }}"
              class="reports-filter">

            <label for="report-period">Période</label>

            <select id="report-period"
                    name="period"
                    onchange="this.form.submit()">

                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>
                    Aujourd'hui
                </option>

                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>
                    Cette semaine
                </option>

                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>
                    Ce mois
                </option>

                <option value="year" {{ $period === 'year' ? 'selected' : '' }}>
                    Cette année
                </option>
            </select>
        </form>
        
    <div class="reports-export-actions">

        <a
            href="{{ route('company.ecommerce.reports.export.pdf', ['period' => $period]) }}"
            class="reports-export-btn reports-export-pdf"
        >
            📄 Exporter PDF
        </a>

        <a
            href="{{ route('company.ecommerce.reports.export.excel', ['period' => $period]) }}"
            class="reports-export-btn reports-export-excel"
        >
            📊 Exporter Excel
        </a>

    </div>

    </div>

    <div class="reports-period-info">
        Du {{ $startDate->format('d/m/Y') }}
        au {{ $endDate->format('d/m/Y') }}
    </div>

    {{-- STATISTIQUES PRINCIPALES --}}
    <div class="reports-stats-grid">

        <div class="reports-stat-card">
            <span class="reports-stat-icon">🛒</span>
            <span class="reports-stat-label">Commandes</span>
            <strong>{{ number_format($totalOrders, 0, ',', ' ') }}</strong>
            <small>Toutes les commandes de la période</small>
        </div>

        <div class="reports-stat-card">
            <span class="reports-stat-icon">💰</span>
            <span class="reports-stat-label">Valeur des commandes</span>
            <strong>{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</strong>
            <small>Hors commandes annulées</small>
        </div>

        <div class="reports-stat-card">
            <span class="reports-stat-icon">✅</span>
            <span class="reports-stat-label">Paiements encaissés</span>
            <strong>{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</strong>
            <small>{{ $completedPayments }} transactions complétées</small>
        </div>

        <div class="reports-stat-card">
            <span class="reports-stat-icon">📋</span>
            <span class="reports-stat-label">Reste à encaisser</span>
            <strong>{{ number_format($remainingAmount, 0, ',', ' ') }} FCFA</strong>
            <small>Sur les commandes de la période</small>
        </div>

    </div>

    {{-- STATUTS DES COMMANDES --}}
    <section class="reports-section">

        <div class="reports-section-heading">
            <h2>Suivi des commandes</h2>
            <p>Répartition selon leur statut actuel</p>
        </div>

        <div class="reports-status-grid">
            @foreach([
                ['label' => 'En attente', 'value' => $pendingOrders, 'type' => 'pending'],
                ['label' => 'Confirmées', 'value' => $confirmedOrders, 'type' => 'confirmed'],
                ['label' => 'En préparation', 'value' => $preparingOrders, 'type' => 'preparing'],
                ['label' => 'Expédiées', 'value' => $shippedOrders, 'type' => 'shipped'],
                ['label' => 'Livrées', 'value' => $deliveredOrders, 'type' => 'delivered'],
                ['label' => 'Annulées', 'value' => $cancelledOrders, 'type' => 'cancelled'],
            ] as $status)
                <div class="reports-status-card status-{{ $status['type'] }}">
                    <span>{{ $status['label'] }}</span>
                    <strong>{{ number_format($status['value'], 0, ',', ' ') }}</strong>
                </div>
            @endforeach
        </div>

    </section>

    {{-- RÉSUMÉ FINANCIER --}}
    <section class="reports-section">

        <div class="reports-section-heading">
            <h2>Résumé financier</h2>
            <p>Composition de la valeur des commandes non annulées</p>
        </div>

        <div class="reports-financial-grid">
            @foreach([
                ['label' => 'Sous-total', 'value' => $totalSubtotal],
                ['label' => 'Remises', 'value' => $totalDiscount],
                ['label' => 'Livraison facturée', 'value' => $totalShipping],
                ['label' => 'Taxes', 'value' => $totalTax],
                ['label' => 'Total des commandes', 'value' => $totalRevenue],
                ['label' => 'Paiements sur ces commandes', 'value' => $paidOnPeriodOrders],
            ] as $financial)
                <div class="reports-financial-item">
                    <span>{{ $financial['label'] }}</span>
                    <strong>{{ number_format($financial['value'], 0, ',', ' ') }} FCFA</strong>
                </div>
            @endforeach
        </div>

    </section>

    {{-- GRAPHIQUE --}}
    <section class="reports-section">

        <div class="reports-section-heading">
            <h2>Évolution des commandes</h2>
            <p>Montants regroupés par jour ou par mois</p>
        </div>

        @php
            $maxSales = max(1, (float) $salesEvolution->max('total'));
        @endphp

        @if($salesEvolution->isEmpty())
            <div class="reports-empty">Aucune vente sur cette période.</div>
        @else
            <div class="reports-chart">
                @foreach($salesEvolution as $sale)
                    @php
                        $height = max(
                            3,
                            ((float) $sale->total / $maxSales) * 100
                        );
                    @endphp

                    <div class="reports-chart-column">
                        <span class="reports-chart-value">
                            {{ number_format($sale->total, 0, ',', ' ') }}
                        </span>

                        <div class="reports-chart-track">
                            <div class="reports-chart-bar"
                                 style="height: {{ $height }}%;">
                            </div>
                        </div>

                        <span class="reports-chart-label">
                            {{ $sale->period_label }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

    </section>

    {{-- PRODUITS ET PAIEMENTS --}}
    <div class="reports-two-columns">

        <section class="reports-section">
            <div class="reports-section-heading">
                <h2>Top 10 produits</h2>
                <p>{{ number_format($totalItemsSold, 3, ',', ' ') }} unités vendues</p>
            </div>

            <div class="reports-table-wrapper">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                            <tr>
                                <td>
                                    <strong>{{ $product->product_name }}</strong>
                                    <small>{{ $product->product_reference ?: 'Sans référence' }}</small>
                                </td>
                                <td>{{ number_format($product->total_quantity, 3, ',', ' ') }}</td>
                                <td>{{ number_format($product->total_sales, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="reports-empty-cell">
                                    Aucun produit vendu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="reports-section">
            <div class="reports-section-heading">
                <h2>Modes de paiement</h2>
                <p>Répartition des encaissements de la période</p>
            </div>

            <div class="reports-table-wrapper">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>Méthode</th>
                            <th>Transactions</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentsByMethod as $payment)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->method ?? 'Autre')) }}</td>
                                <td>{{ $payment->transactions }}</td>
                                <td>{{ number_format($payment->total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="reports-empty-cell">
                                    Aucun encaissement enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    {{-- DERNIÈRES COMMANDES --}}
    <section class="reports-section">
        <div class="reports-section-heading">
            <h2>Dernières commandes</h2>
            <p>Les dix commandes les plus récentes</p>
        </div>

        <div class="reports-table-wrapper">
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->reference }}</strong>
                            </td>
                            <td>{{ $order->customer_name ?: 'Client non renseigné' }}</td>
                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="reports-badge status-{{ $order->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="reports-empty-cell">
                                Aucune commande pour cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- DERNIERS PAIEMENTS --}}
    <section class="reports-section">
        <div class="reports-section-heading">
            <h2>Derniers paiements encaissés</h2>
            <p>Les dix transactions complétées les plus récentes</p>
        </div>

        <div class="reports-table-wrapper">
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Commande</th>
                        <th>Méthode</th>
                        <th>Date</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->reference }}</td>
                            <td>{{ $payment->order?->reference ?? '—' }}</td>
                            <td>{{ $payment->method_label }}</td>
                            <td>{{ $payment->paid_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="reports-empty-cell">
                                Aucun paiement encaissé sur cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>

@endsection
