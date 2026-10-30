@extends('layouts.ecommerce')

@section('title', 'Clients')

@section('content')

<div class="ecommerce-clients-page">

    {{-- HEADER --}}

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                E-COMMERCE
            </div>

            <h1>Clients</h1>

            <p>
                Retrouvez les clients ayant effectué des commandes
                dans votre boutique.
            </p>

        </div>

    </div>


    {{-- STATISTIQUES --}}

    <div class="orders-stats">

        <div class="stat-card">

            <div class="stat-icon">
                👥
            </div>

            <div>
                <span>Clients</span>

                <strong>
                    {{ number_format($stats['total'], 0, ',', ' ') }}
                </strong>
            </div>

        </div>


        <div class="stat-card info">

            <div class="stat-icon">
                🛒
            </div>

            <div>
                <span>Commandes</span>

                <strong>
                    {{ number_format($stats['orders'], 0, ',', ' ') }}
                </strong>
            </div>

        </div>


        <div class="stat-card success">

            <div class="stat-icon">
                FCFA
            </div>

            <div>
                <span>Chiffre d'affaires</span>

                <strong>
                    {{ number_format($stats['revenue'], 0, ',', ' ') }}
                </strong>
            </div>

        </div>


        <div class="stat-card revenue">

            <div class="stat-icon">
                ↗
            </div>

            <div>
                <span>Panier moyen</span>

                <strong>
                    {{ number_format($stats['average'], 0, ',', ' ') }}
                    FCFA
                </strong>
            </div>

        </div>

    </div>


    {{-- PANEL --}}

    <div class="orders-panel">

        {{-- RECHERCHE --}}

        <form
            method="GET"
            action="{{ route('company.ecommerce.clients.index') }}"
            class="filters-form"
        >

            <div class="filter-search">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher un client..."
                >

            </div>

            <button
                type="submit"
                class="btn btn-secondary"
            >
                Rechercher
            </button>

            @if(request('search'))

                <a
                    href="{{ route('company.ecommerce.clients.index') }}"
                    class="filter-reset"
                >
                    Réinitialiser
                </a>

            @endif

        </form>


        {{-- TABLE --}}

        <div class="table-wrapper">

            @if($clients->count())

                <table class="orders-table">

                    <thead>

                        <tr>

                            <th>Client</th>

                            <th>Téléphone</th>

                            <th>Commandes</th>

                            <th>Total dépensé</th>

                            <th>Dernière commande</th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($clients as $client)

                            <tr>

                                <td>

                                    <div class="customer-cell">

                                        <div class="customer-avatar">

                                            {{ strtoupper(
                                                substr(
                                                    $client->name,
                                                    0,
                                                    1
                                                )
                                            ) }}

                                        </div>

                                        <div>

                                            <strong>
                                                {{ $client->name }}
                                            </strong>

                                            @if($client->email)

                                                <small>
                                                    {{ $client->email }}
                                                </small>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    {{ $client->phone ?? '—' }}

                                </td>


                                <td>

                                    <strong>
                                        {{ $client->ecommerce_orders_count }}
                                    </strong>

                                </td>


                                <td>

                                    <strong class="order-total">

                                        {{ number_format(
                                            $client->ecommerce_orders_sum_total ?? 0,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </strong>

                                </td>


                                <td>

                                    @if($client->ecommerce_orders_max_created_at)

                                        {{ \Carbon\Carbon::parse(
                                            $client->ecommerce_orders_max_created_at
                                        )->format('d/m/Y') }}

                                    @else

                                        —

                                    @endif

                                </td>


                                <td>

                                    <a
                                        href="{{ route(
                                            'company.ecommerce.clients.show',
                                            $client
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
                        👥
                    </div>

                    <h3>
                        Aucun client e-commerce
                    </h3>

                    <p>
                        Les clients apparaîtront automatiquement
                        après leurs commandes.
                    </p>

                </div>

            @endif

        </div>


        {{-- PAGINATION --}}

        @if($clients->hasPages())

            <div class="pagination-wrapper">
                {{ $clients->links() }}
            </div>

        @endif

    </div>

</div>


@endsection