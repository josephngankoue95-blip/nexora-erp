@extends('layouts.ecommerce')

@section('title', 'Client - ' . $client->name)

@section('content')

<div class="ecommerce-client-show">

    {{-- HEADER --}}

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                CLIENT E-COMMERCE
            </div>

            <h1>
                {{ $client->name }}
            </h1>

            <p>
                Historique des commandes et activité du client.
            </p>

        </div>


        <a
            href="{{ route('company.ecommerce.clients.index') }}"
            class="btn btn-secondary"
        >
            ← Retour aux clients
        </a>

    </div>


    {{-- PROFIL --}}

    <div class="client-profile-card">

        <div class="customer-avatar profile-avatar">

            {{ strtoupper(
                substr(
                    $client->name,
                    0,
                    1
                )
            ) }}

        </div>


        <div class="client-profile-info">

            <h2>
                {{ $client->name }}
            </h2>

            <div class="client-contact">

                @if($client->email)

                    <span>
                        ✉ {{ $client->email }}
                    </span>

                @endif

                @if($client->phone)

                    <span>
                        ☎ {{ $client->phone }}
                    </span>

                @endif

            </div>

        </div>

    </div>


    {{-- STATISTIQUES --}}

    <div class="orders-stats">

        <div class="stat-card">

            <div class="stat-icon">
                🛒
            </div>

            <div>

                <span>
                    Commandes
                </span>

                <strong>
                    {{ number_format(
                        $stats['orders'],
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

                <span>
                    Commandes livrées
                </span>

                <strong>
                    {{ number_format(
                        $stats['completed_orders'],
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

                <span>
                    Total dépensé
                </span>

                <strong>
                    {{ number_format(
                        $stats['total_spent'],
                        0,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </div>


        <div class="stat-card info">

            <div class="stat-icon">
                ↗
            </div>

            <div>

                <span>
                    Panier moyen
                </span>

                <strong>
                    {{ number_format(
                        $stats['average_order'],
                        0,
                        ',',
                        ' '
                    ) }}

                    FCFA

                </strong>

            </div>

        </div>

    </div>


    {{-- COMMANDES --}}

    <div class="orders-panel">

        <div class="detail-card-header">

            <div>

                <h2>
                    Historique des commandes
                </h2>

                <span>
                    Commandes liées à ce client
                </span>

            </div>

        </div>


        <div class="table-wrapper">

            @if($orders->count())

                <table class="orders-table">

                    <thead>

                        <tr>

                            <th>
                                Commande
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Articles
                            </th>

                            <th>
                                Montant
                            </th>

                            <th>
                                Paiement
                            </th>

                            <th>
                                Statut
                            </th>

                            <th>
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($orders as $order)

                            <tr>

                                <td>

                                    <a
                                        href="{{ route(
                                            'company.ecommerce.orders.show',
                                            $order
                                        ) }}"
                                        class="order-reference"
                                    >
                                        {{ $order->reference }}
                                    </a>

                                </td>


                                <td>

                                    {{ $order->created_at->format(
                                        'd/m/Y'
                                    ) }}

                                    <small>
                                        {{ $order->created_at->format(
                                            'H:i'
                                        ) }}
                                    </small>

                                </td>


                                <td>

                                    {{ $order->items->sum('quantity') }}

                                </td>


                                <td>

                                    <strong class="order-total">

                                        {{ number_format(
                                            $order->total,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="payment-badge payment-{{ $order->payment_status }}"
                                    >

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

                                    <span
                                        class="status-badge status-{{ $order->status }}"
                                    >

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
                                        href="{{ route(
                                            'company.ecommerce.orders.show',
                                            $order
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
                        🛒
                    </div>

                    <h3>
                        Aucune commande
                    </h3>

                    <p>
                        Ce client n'a pas encore de commande.
                    </p>

                </div>

            @endif

        </div>


        @if($orders->hasPages())

            <div class="pagination-wrapper">
                {{ $orders->links() }}
            </div>

        @endif

    </div>

</div>

@endsection