@extends('layouts.ecommerce')

@section('title', 'Commandes')

@section('content')

<div class="ecommerce-orders-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="page-header">

        <div>
            <div class="page-eyebrow">
                E-COMMERCE
            </div>

            <h1>Commandes</h1>

            <p>
                Gérez les commandes reçues depuis votre boutique en ligne.
            </p>
        </div>

        @if(auth()->user()->hasPermission('ecommerce.create'))
            <a
                href="{{ route('company.ecommerce.orders.create') }}"
                class="btn btn-primary"
            >
                <span>＋</span>
                Nouvelle commande
            </a>
        @endif

    </div>


    {{-- =========================================================
         STATISTIQUES
    ========================================================== --}}

    <div class="orders-stats">

        <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div>
                <span>Total</span>
                <strong>{{ number_format($stats['total'], 0, ',', ' ') }}</strong>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">⏳</div>
            <div>
                <span>En attente</span>
                <strong>{{ number_format($stats['pending'], 0, ',', ' ') }}</strong>
            </div>
        </div>

        <div class="stat-card info">
            <div class="stat-icon">✓</div>
            <div>
                <span>Confirmées</span>
                <strong>{{ number_format($stats['confirmed'], 0, ',', ' ') }}</strong>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">📦</div>
            <div>
                <span>Préparation</span>
                <strong>{{ number_format($stats['preparing'], 0, ',', ' ') }}</strong>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon">🚚</div>
            <div>
                <span>Livrées</span>
                <strong>{{ number_format($stats['delivered'], 0, ',', ' ') }}</strong>
            </div>
        </div>

        <div class="stat-card revenue">
            <div class="stat-icon">FCFA</div>
            <div>
                <span>Chiffre d'affaires</span>
                <strong>
                    {{ number_format($stats['revenue'], 0, ',', ' ') }}
                </strong>
            </div>
        </div>

    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}

    <div class="orders-panel">

        <form
            method="GET"
            action="{{ route('company.ecommerce.orders.index') }}"
            class="filters-form"
        >

            <div class="filter-search">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher une commande, un client..."
                >

            </div>


            <div>
                <select name="status">

                    <option value="">
                        Tous les statuts
                    </option>

                    <option value="pending"
                        @selected(request('status') === 'pending')>
                        En attente
                    </option>

                    <option value="confirmed"
                        @selected(request('status') === 'confirmed')>
                        Confirmée
                    </option>

                    <option value="preparing"
                        @selected(request('status') === 'preparing')>
                        En préparation
                    </option>

                    <option value="shipped"
                        @selected(request('status') === 'shipped')>
                        Expédiée
                    </option>

                    <option value="delivered"
                        @selected(request('status') === 'delivered')>
                        Livrée
                    </option>

                    <option value="cancelled"
                        @selected(request('status') === 'cancelled')>
                        Annulée
                    </option>

                </select>
            </div>


            <div>
                <select name="payment_status">

                    <option value="">
                        Tous les paiements
                    </option>

                    <option value="pending"
                        @selected(request('payment_status') === 'pending')>
                        En attente
                    </option>

                    <option value="paid"
                        @selected(request('payment_status') === 'paid')>
                        Payé
                    </option>

                    <option value="partially_paid"
                        @selected(request('payment_status') === 'partially_paid')>
                        Partiellement payé
                    </option>

                    <option value="failed"
                        @selected(request('payment_status') === 'failed')>
                        Échec
                    </option>

                    <option value="refunded"
                        @selected(request('payment_status') === 'refunded')>
                        Remboursé
                    </option>

                </select>
            </div>


            <button
                type="submit"
                class="btn btn-secondary"
            >
                Filtrer
            </button>

            @if(request()->hasAny(['search', 'status', 'payment_status']))
                <a
                    href="{{ route('company.ecommerce.orders.index') }}"
                    class="filter-reset"
                >
                    Réinitialiser
                </a>
            @endif

        </form>


        {{-- =====================================================
             TABLE
        ====================================================== --}}

        <div class="table-wrapper">

            @if($orders->count())

                <table class="orders-table">

                    <thead>

                        <tr>
                            <th>Commande</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Articles</th>
                            <th>Montant</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($orders as $order)

                            <tr>

                                <td>

                                    <a
                                        href="{{ route('company.ecommerce.orders.show', $order) }}"
                                        class="order-reference"
                                    >
                                        {{ $order->reference }}
                                    </a>

                                    <small>
                                        #{{ $order->id }}
                                    </small>

                                </td>


                                <td>

                                    <div class="customer-cell">

                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>

                                        <div>

                                            <strong>
                                                {{ $order->customer_name }}
                                            </strong>

                                            @if($order->customer_phone)
                                                <small>
                                                    {{ $order->customer_phone }}
                                                </small>
                                            @endif

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <strong>
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </strong>

                                    <small>
                                        {{ $order->created_at->format('H:i') }}
                                    </small>

                                </td>


                                <td>
                                    {{ $order->items->sum('quantity') }}
                                </td>


                                <td>

                                    <strong class="order-total">
                                        {{ number_format($order->total, 0, ',', ' ') }}
                                        FCFA
                                    </strong>

                                </td>


                                <td>

                                    <span class="payment-badge payment-{{ $order->payment_status }}">
                                        @switch($order->payment_status)
                                            @case('paid')
                                                Payé
                                                @break

                                            @case('partially_paid')
                                                Partiel
                                                @break

                                            @case('failed')
                                                Échec
                                                @break

                                            @case('refunded')
                                                Remboursé
                                                @break

                                            @default
                                                En attente
                                        @endswitch
                                    </span>

                                </td>


                                <td>

                                    <span class="status-badge status-{{ $order->status }}">

                                        @switch($order->status)

                                            @case('pending')
                                                En attente
                                                @break

                                            @case('confirmed')
                                                Confirmée
                                                @break

                                            @case('preparing')
                                                Préparation
                                                @break

                                            @case('shipped')
                                                Expédiée
                                                @break

                                            @case('delivered')
                                                Livrée
                                                @break

                                            @case('cancelled')
                                                Annulée
                                                @break

                                        @endswitch

                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="{{ route('company.ecommerce.orders.show', $order) }}"
                                        class="table-action"
                                    >
                                        Voir
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        🛒
                    </div>

                    <h3>
                        Aucune commande
                    </h3>

                    <p>
                        Les commandes de votre boutique apparaîtront ici.
                    </p>

                    @if(auth()->user()->hasPermission('ecommerce.create'))

                        <a
                            href="{{ route('company.ecommerce.orders.create') }}"
                            class="btn btn-primary"
                        >
                            Créer une commande
                        </a>

                    @endif

                </div>

            @endif

        </div>


        {{-- =====================================================
             PAGINATION
        ====================================================== --}}

        @if($orders->hasPages())

            <div class="pagination-wrapper">
                {{ $orders->links() }}
            </div>

        @endif

    </div>

</div>

<style>

@endsection