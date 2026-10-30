@extends('layouts.company')

@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                RELATION CLIENT
            </span>

            <h1>Clients</h1>

            <p>
                Gérez les clients de votre entreprise.
            </p>

        </div>


        <a
            href="{{ route('company.clients.create') }}"
            class="module-btn module-btn-primary"
        >
            + Nouveau client
        </a>

    </div>


    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- STATISTIQUES --}}

    <div class="stock-stats">

        <div class="stock-stat-card">

            <span class="stock-stat-label">
                Total clients
            </span>

            <strong>
                {{ $totalClients }}
            </strong>

        </div>


        <div class="stock-stat-card">

            <span class="stock-stat-label">
                Clients actifs
            </span>

            <strong>
                {{ $activeClients }}
            </strong>

        </div>


        <div class="stock-stat-card">

            <span class="stock-stat-label">
                Clients inactifs
            </span>

            <strong>
                {{ $inactiveClients }}
            </strong>

        </div>

    </div>


    {{-- FILTRES --}}

    <div class="module-card">

        <form
            method="GET"
            action="{{ route('company.clients.index') }}"
            class="module-filters"
        >

            <div class="filter-group">

                <label>
                    Recherche
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nom, téléphone, email..."
                >

            </div>


            <div class="filter-group">

                <label>
                    Statut
                </label>

                <select name="status">

                    <option value="">
                        Tous
                    </option>

                    <option
                        value="active"
                        @selected(request('status') === 'active')
                    >
                        Actifs
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status') === 'inactive')
                    >
                        Inactifs
                    </option>

                </select>

            </div>


            <div class="filter-actions">

                <button
                    type="submit"
                    class="module-btn module-btn-primary"
                >
                    Rechercher
                </button>

                <a
                    href="{{ route('company.clients.index') }}"
                    class="module-btn module-btn-light"
                >
                    Réinitialiser
                </a>

            </div>

        </form>

    </div>


    {{-- TABLE --}}

    <div class="module-card">

        <div class="module-card-header">

            <h2>
                Liste des clients
            </h2>

            <span>
                {{ $clients->total() }} client(s)
            </span>

        </div>


        <div class="module-table-wrapper">

            <table class="module-table">

                <thead>

                    <tr>

                        <th>Client</th>

                        <th>Téléphone</th>

                        <th>Email</th>

                        <th>Ville</th>

                        <th>Statut</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($clients as $client)

                    <tr>

                        <td>

                            <div class="product-name">

                                <strong>
                                    {{ $client->name }}
                                </strong>

                                @if($client->tax_number)

                                    <small>
                                        N° contribuable :
                                        {{ $client->tax_number }}
                                    </small>

                                @endif

                            </div>

                        </td>


                        <td>
                            {{ $client->phone ?? '—' }}
                        </td>


                        <td>
                            {{ $client->email ?? '—' }}
                        </td>


                        <td>
                            {{ $client->city ?? '—' }}
                        </td>


                        <td>

                            @if($client->is_active)

                                <span class="status-badge status-active">
                                    Actif
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Inactif
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="table-actions">

                                <a
                                    href="{{ route('company.clients.show', $client->id) }}"
                                    class="action-btn"
                                >
                                    Voir
                                </a>

                                <a
                                    href="{{ route('company.clients.edit', $client->id) }}"
                                    class="action-btn"
                                >
                                    Modifier
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('company.clients.destroy', $client->id) }}"
                                    onsubmit="return confirm('Supprimer ce client ?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="action-btn action-danger"
                                    >
                                        Supprimer
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="empty-state"
                        >

                            <strong>
                                Aucun client
                            </strong>

                            <p>
                                Commencez par ajouter votre premier client.
                            </p>

                            <a
                                href="{{ route('company.clients.create') }}"
                                class="module-btn module-btn-primary"
                            >
                                Ajouter un client
                            </a>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($clients->hasPages())

            <div class="module-pagination">

                {{ $clients->links() }}

            </div>

        @endif

    </div>

</div>

@endsection