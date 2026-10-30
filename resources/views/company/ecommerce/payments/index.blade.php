@extends('layouts.ecommerce')

@section('title', 'Paiements')

@section('content')

<div class="ecommerce-payments-page">

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                E-COMMERCE
            </div>

            <h1>Paiements</h1>

            <p>
                Suivez les règlements associés aux commandes
                de votre boutique.
            </p>

        </div>

        @if(
            auth()->user()->hasPermission('ecommerce.create') &&
            Route::has('company.ecommerce.payments.create')
        )

            <a
                href="{{ route('company.ecommerce.payments.create') }}"
                class="btn btn-primary"
            >
                + Nouveau paiement
            </a>

        @endif

    </div>


    {{-- STATISTIQUES --}}

    <div class="orders-stats">

        <div class="stat-card">

            <div class="stat-icon">
                💳
            </div>

            <div>

                <span>Paiements</span>

                <strong>
                    {{ number_format(
                        $stats['total'],
                        0,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </div>


        <div class="stat-card success">

            <div class="stat-icon">
                ✓
            </div>

            <div>

                <span>Confirmés</span>

                <strong>
                    {{ number_format(
                        $stats['completed'],
                        0,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </div>


        <div class="stat-card info">

            <div class="stat-icon">
                …
            </div>

            <div>

                <span>En attente</span>

                <strong>
                    {{ number_format(
                        $stats['pending'],
                        0,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </div>


        <div class="stat-card revenue">

            <div class="stat-icon">
                FCFA
            </div>

            <div>

                <span>Montant encaissé</span>

                <strong>
                    {{ number_format(
                        $stats['amount'],
                        0,
                        ',',
                        ' '
                    ) }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>


    {{-- FILTRES --}}

    <div class="orders-panel">

        <form
            method="GET"
            action="{{ route('company.ecommerce.payments.index') }}"
            class="filters-form"
        >

            <div class="filter-search">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Référence, commande, client..."
                >

            </div>


            <select name="status">

                <option value="">
                    Tous les statuts
                </option>

                <option
                    value="completed"
                    @selected(request('status') === 'completed')
                >
                    Confirmés
                </option>

                <option
                    value="pending"
                    @selected(request('status') === 'pending')
                >
                    En attente
                </option>

                <option
                    value="failed"
                    @selected(request('status') === 'failed')
                >
                    Échecs
                </option>

                <option
                    value="refunded"
                    @selected(request('status') === 'refunded')
                >
                    Remboursés
                </option>

            </select>


            <select name="method">

                <option value="">
                    Toutes les méthodes
                </option>

                <option
                    value="cash"
                    @selected(request('method') === 'cash')
                >
                    Espèces
                </option>

                <option
                    value="mobile_money"
                    @selected(request('method') === 'mobile_money')
                >
                    Mobile Money
                </option>

                <option
                    value="card"
                    @selected(request('method') === 'card')
                >
                    Carte bancaire
                </option>

                <option
                    value="bank_transfer"
                    @selected(request('method') === 'bank_transfer')
                >
                    Virement
                </option>

                <option
                    value="other"
                    @selected(request('method') === 'other')
                >
                    Autre
                </option>

            </select>


            <button
                type="submit"
                class="btn btn-secondary"
            >
                Filtrer
            </button>

        </form>


        {{-- TABLE --}}

        <div class="table-wrapper">

            @if($payments->count())

                <table class="orders-table">

                    <thead>

                        <tr>

                            <th>Référence</th>

                            <th>Commande</th>

                            <th>Client</th>

                            <th>Méthode</th>

                            <th>Montant</th>

                            <th>Statut</th>

                            <th>Date</th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($payments as $payment)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $payment->reference }}
                                    </strong>

                                </td>


                                <td>

                                    @if($payment->order)

                                        <a
                                            href="{{ route(
                                                'company.ecommerce.orders.show',
                                                $payment->order
                                            ) }}"
                                            class="order-reference"
                                        >
                                            {{ $payment->order->reference }}
                                        </a>

                                    @else

                                        —

                                    @endif

                                </td>


                                <td>

                                    {{ $payment->order?->customer_name ?? '—' }}

                                </td>


                                <td>

                                    {{ $payment->method_label }}

                                    @if($payment->provider_label)

                                        <small class="payment-provider">
                                            {{ $payment->provider_label }}
                                        </small>

                                    @endif

                                </td>


                                <td>

                                    <strong class="order-total">

                                        {{ number_format(
                                            $payment->amount,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="payment-status-badge payment-status-{{ $payment->status }}"
                                    >

                                        @switch($payment->status)

                                            @case('completed')
                                                Confirmé
                                                @break

                                            @case('pending')
                                                En attente
                                                @break

                                            @case('failed')
                                                Échec
                                                @break

                                            @case('refunded')
                                                Remboursé
                                                @break

                                        @endswitch

                                    </span>

                                </td>


                                <td>

                                    {{ $payment->created_at->format(
                                        'd/m/Y H:i'
                                    ) }}

                                </td>


                                <td>

                                    <a
                                        href="{{ route(
                                            'company.ecommerce.payments.show',
                                            $payment
                                        ) }}"
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
                        💳
                    </div>

                    <h3>
                        Aucun paiement
                    </h3>

                    <p>
                        Les paiements e-commerce apparaîtront ici.
                    </p>

                </div>

            @endif

        </div>


        @if($payments->hasPages())

            <div class="pagination-wrapper">
                {{ $payments->links() }}
            </div>

        @endif

    </div>

</div>

@endsection