@extends('layouts.company')

@section('title', 'Ventes | Gestion')

@section('content')

<div class="module-page">

    {{-- ============================================================
         EN-TÊTE
    ============================================================ --}}

    <div class="module-header">

        <div>

            <span class="module-kicker">
                COMMERCE
            </span>

            <h1 class="module-title">
                Ventes
            </h1>

            <p class="module-description">
                Consultez et gérez toutes les ventes de votre entreprise.
            </p>

        </div>

        <a href="{{ route('company.sales.create') }}"
           class="module-btn module-btn-primary">

            + Nouvelle vente

        </a>

    </div>


    {{-- ============================================================
         STATISTIQUES
    ============================================================ --}}

    <div class="sale-stats">

        <div class="sale-stat-card">

            <div class="sale-stat-icon">
                🧾
            </div>

            <div>

                <span class="sale-stat-label">
                    Total des ventes
                </span>

                <strong>
                    {{ $totalSales }}
                </strong>

            </div>

        </div>


        <div class="sale-stat-card">

            <div class="sale-stat-icon">
                ✓
            </div>

            <div>

                <span class="sale-stat-label">
                    Ventes terminées
                </span>

                <strong>
                    {{ $completedSales }}
                </strong>

            </div>

        </div>


        <div class="sale-stat-card">

            <div class="sale-stat-icon">
                ↩
            </div>

            <div>

                <span class="sale-stat-label">
                    Ventes annulées
                </span>

                <strong>
                    {{ $cancelledSales }}
                </strong>

            </div>

        </div>


        <div class="sale-stat-card sale-stat-revenue">

            <div class="sale-stat-icon">
                💰
            </div>

            <div>

                <span class="sale-stat-label">
                    Chiffre d'affaires
                </span>

                <strong>
                    {{ number_format($totalRevenue, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>


        <div class="sale-stat-card">

            <div class="sale-stat-icon">
                💵
            </div>

            <div>

                <span class="sale-stat-label">
                    Total encaissé
                </span>

                <strong>
                    {{ number_format($totalPaid, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>


        <div class="sale-stat-card">

            <div class="sale-stat-icon">
                ⏳
            </div>

            <div>

                <span class="sale-stat-label">
                    Total restant
                </span>

                <strong>
                    {{ number_format($totalRemaining, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>

        </div>

    </div>


    {{-- ============================================================
         MESSAGE SUCCÈS
    ============================================================ --}}

    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ============================================================
         ERREURS
    ============================================================ --}}

    @if($errors->has('sale'))

        <div class="module-alert module-alert-danger">
            {{ $errors->first('sale') }}
        </div>

    @endif


    {{-- ============================================================
         FILTRES
    ============================================================ --}}

    <div class="module-card sale-filter-card">

        <form
            method="GET"
            action="{{ route('company.sales.index') }}"
            class="sale-filters"
        >

            <div class="module-field">

                <label for="search">
                    Recherche
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Référence ou nom du client..."
                >

            </div>


            <div class="module-field">

                <label for="status">
                    Statut de la vente
                </label>

                <select
                    name="status"
                    id="status"
                >

                    <option value="">
                        Toutes les ventes
                    </option>

                    <option
                        value="completed"
                        @selected(request('status') === 'completed')
                    >
                        Terminées
                    </option>

                    <option
                        value="cancelled"
                        @selected(request('status') === 'cancelled')
                    >
                        Annulées
                    </option>

                </select>

            </div>


            <div class="module-field">

                <label for="payment_status">
                    Paiement
                </label>

                <select
                    name="payment_status"
                    id="payment_status"
                >

                    <option value="">
                        Tous les paiements
                    </option>

                    <option
                        value="paid"
                        @selected(request('payment_status') === 'paid')
                    >
                        Payées
                    </option>

                    <option
                        value="partial"
                        @selected(request('payment_status') === 'partial')
                    >
                        Partiellement payées
                    </option>

                    <option
                        value="unpaid"
                        @selected(request('payment_status') === 'unpaid')
                    >
                        Impayées
                    </option>

                </select>

            </div>


            <div class="sale-filter-actions">

                <button
                    type="submit"
                    class="module-btn module-btn-primary"
                >
                    Rechercher
                </button>


                <a
                    href="{{ route('company.sales.index') }}"
                    class="module-btn module-btn-secondary"
                >
                    Réinitialiser
                </a>

            </div>

        </form>

    </div>


    {{-- ============================================================
         TABLEAU
    ============================================================ --}}

    <div class="module-card">

        <div class="sale-table-header">

            <div>

                <h2>
                    Historique des ventes
                </h2>

                <p>
                    {{ $sales->total() }}
                    vente(s) trouvée(s)
                </p>

            </div>

        </div>


        <div class="module-table-wrapper">

            <table class="module-table sale-list-table">

                <thead>

                    <tr>

                        <th>
                            Référence
                        </th>

                        <th>
                            Client
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Vendeur
                        </th>

                        <th>
                            Montant
                        </th>

                        <th>
                            Payé
                        </th>

                        <th>
                            Reste
                        </th>

                        <th>
                            Paiement
                        </th>

                        <th>
                            Statut
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($sales as $sale)

                        <tr>

                            {{-- RÉFÉRENCE --}}

                            <td>

                                <strong class="sale-reference">
                                    {{ $sale->reference }}
                                </strong>

                            </td>


                            {{-- CLIENT --}}

                            <td>

                                @if($sale->client)

                                    <strong>
                                        {{ $sale->client->name }}
                                    </strong>

                                    @if($sale->client->phone)

                                        <small>
                                            {{ $sale->client->phone }}
                                        </small>

                                    @endif

                                @else

                                    <span class="sale-counter-client">
                                        Client comptoir
                                    </span>

                                @endif

                            </td>


                            {{-- DATE --}}

                            <td>

                                <strong>
                                    {{ $sale->created_at->format('d/m/Y') }}
                                </strong>

                                <small>
                                    {{ $sale->created_at->format('H:i') }}
                                </small>

                            </td>


                            {{-- VENDEUR --}}

                            <td>

                                @if($sale->user)

                                    {{ $sale->user->name }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- MONTANT --}}

                            <td>

                                <strong class="sale-list-total">

                                    {{ number_format(
                                        $sale->total,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    FCFA

                                </strong>

                            </td>


                            {{-- PAYÉ --}}

                            <td>

                                <strong class="sale-list-paid">

                                    {{ number_format(
                                        $sale->paid_amount,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    FCFA

                                </strong>

                            </td>


                            {{-- RESTE --}}

                            <td>

                                @if($sale->remaining_amount > 0)

                                    <strong class="sale-list-remaining">

                                        {{ number_format(
                                            $sale->remaining_amount,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </strong>

                                @else

                                    <strong>
                                        0 FCFA
                                    </strong>

                                @endif

                            </td>


                            {{-- STATUT PAIEMENT --}}

                            <td>

                                @if($sale->payment_status === 'paid')

                                    <span class="module-badge module-badge-success">
                                        Payée
                                    </span>

                                @elseif($sale->payment_status === 'partial')

                                    <span class="module-badge">
                                        Partielle
                                    </span>

                                @else

                                    <span class="module-badge module-badge-danger">
                                        Impayée
                                    </span>

                                @endif

                            </td>


                            {{-- STATUT VENTE --}}

                            <td>

                                @if($sale->status === 'completed')

                                    <span class="module-badge module-badge-success">
                                        Terminée
                                    </span>

                                @else

                                    <span class="module-badge module-badge-danger">
                                        Annulée
                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}

                            <td>

                                <div class="module-actions">

                                    <a
                                        href="{{ route(
                                            'company.sales.show',
                                            $sale
                                        ) }}"
                                        class="module-action"
                                    >
                                        Voir
                                    </a>


                                    <!-- @if($sale->status === 'completed')

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'company.sales.destroy',
                                                $sale
                                            ) }}"
                                            onsubmit="return confirm(
                                                'Voulez-vous vraiment annuler cette vente ? Le stock sera automatiquement restauré.'
                                            );"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="module-action module-action-danger"
                                            >
                                                Annuler
                                            </button>

                                        </form>

                                    @endif -->

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10">

                                <div class="module-empty">

                                    <strong>
                                        Aucune vente
                                    </strong>

                                    <p>
                                        Aucune vente ne correspond aux critères sélectionnés.
                                    </p>

                                    <a
                                        href="{{ route('company.sales.create') }}"
                                        class="module-btn module-btn-primary"
                                    >
                                        Créer une vente
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        @if($sales->hasPages())

            <div class="module-pagination">
                {{ $sales->links() }}
            </div>

        @endif

    </div>

</div>

@endsection