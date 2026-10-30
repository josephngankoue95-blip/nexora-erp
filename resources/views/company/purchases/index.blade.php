@extends('layouts.company')

@section('title', 'Achats | Gestion')

@section('content')

<div class="module-page purchase-page">

    {{-- HEADER --}}
    <div class="module-header">

        <div>

            <span class="module-kicker">
                ACHATS
            </span>

            <h1 class="module-title">
                Achats
            </h1>

            <p class="module-description">
                Gérez vos achats fournisseurs, vos dépenses et les entrées de stock.
            </p>

        </div>

        @canPermission('purchases.create')

            <a
                href="{{ route('company.purchases.create') }}"
                class="module-btn module-btn-primary"
            >
                + Nouvel achat
            </a>

        @endcanPermission

    </div>


    {{-- ALERT SUCCESS --}}
    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ALERT ERROR --}}
    @if($errors->any())

        <div class="module-alert module-alert-danger">

            <strong>
                Une erreur est survenue :
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- STATISTIQUES --}}
    <div class="module-stats-grid">

        <div class="module-stat-card">

            <div class="module-stat-icon">
                🧾
            </div>

            <div>

                <span>
                    Total achats
                </span>

                <strong>
                    {{ $totalPurchases }}
                </strong>

            </div>

        </div>


        <div class="module-stat-card">

            <div class="module-stat-icon">
                ✓
            </div>

            <div>

                <span>
                    Achats terminés
                </span>

                <strong>
                    {{ $completedPurchases }}
                </strong>

            </div>

        </div>


        <div class="module-stat-card">

            <div class="module-stat-icon">
                ⚠️
            </div>

            <div>

                <span>
                    Achats annulés
                </span>

                <strong>
                    {{ $cancelledPurchases }}
                </strong>

            </div>

        </div>


        <div class="module-stat-card">

            <div class="module-stat-icon">
                💰
            </div>

            <div>

                <span>
                    Dépenses
                </span>

                <strong>
                    {{ number_format(
                        $totalExpenses,
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
    <div class="module-card purchase-filter-card">

        <form
            method="GET"
            action="{{ route('company.purchases.index') }}"
            class="purchase-filters"
        >

            <div class="purchase-search">

                <label for="search">
                    Recherche
                </label>

                <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Référence, fournisseur, téléphone..."
                >

            </div>


            <div>

                <label for="status">
                    Statut
                </label>

                <select
                    name="status"
                    id="status"
                >

                    <option value="">
                        Tous les statuts
                    </option>

                    <option
                        value="completed"
                        @selected(request('status') === 'completed')
                    >
                        Terminés
                    </option>

                    <option
                        value="cancelled"
                        @selected(request('status') === 'cancelled')
                    >
                        Annulés
                    </option>

                </select>

            </div>


            <div class="purchase-filter-actions">

                <button
                    type="submit"
                    class="module-btn module-btn-primary"
                >
                    Rechercher
                </button>

                <a
                    href="{{ route('company.purchases.index') }}"
                    class="module-btn module-btn-secondary"
                >
                    Réinitialiser
                </a>

            </div>

        </form>

    </div>


    {{-- TABLE --}}
    <div class="module-card purchase-list-card">

        <div class="purchase-section-header">

            <div>

                <h2>
                    Liste des achats
                </h2>

                <p>
                    Retrouvez l'ensemble des achats enregistrés dans votre entreprise.
                </p>

            </div>

            <span class="module-badge">
                {{ $purchases->total() }} achat(s)
            </span>

        </div>


        <div class="module-table-wrapper">

            <table class="module-table">

                <thead>

                    <tr>

                        <th>
                            Référence
                        </th>

                        <th>
                            Fournisseur
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Responsable
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Paiement
                        </th>

                        <th>
                            Statut
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($purchases as $purchase)

                        <tr>

                            <td>

                                <strong>
                                    {{ $purchase->reference }}
                                </strong>

                            </td>


                            <td>

                                <strong>
                                    {{ $purchase->supplier?->name ?? '—' }}
                                </strong>

                                @if($purchase->supplier?->phone)

                                    <small>
                                        {{ $purchase->supplier->phone }}
                                    </small>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ \Carbon\Carbon::parse(
                                        $purchase->purchase_date
                                    )->format('d/m/Y') }}
                                </strong>

                            </td>


                            <td>
                                {{ $purchase->user?->name ?? '—' }}
                            </td>


                            <td>

                                <strong>

                                    {{ number_format(
                                        $purchase->total,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    FCFA

                                </strong>

                            </td>


                            <td>

                                @if($purchase->payment_status === 'paid')

                                    <span class="module-badge module-badge-success">
                                        Payée
                                    </span>

                                @elseif($purchase->payment_status === 'partial')

                                    <span class="module-badge">
                                        Partielle
                                    </span>

                                @else

                                    <span class="module-badge module-badge-danger">
                                        Impayée
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($purchase->status === 'completed')

                                    <span class="module-badge module-badge-success">
                                        Terminée
                                    </span>

                                @else

                                    <span class="module-badge module-badge-danger">
                                        Annulée
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'company.purchases.show',
                                        $purchase
                                    ) }}"
                                    class="module-action"
                                >
                                    Voir
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8">

                                <div class="module-empty">

                                    <strong>
                                        Aucun achat trouvé
                                    </strong>

                                    <p>
                                        Aucun achat ne correspond aux critères sélectionnés.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($purchases->hasPages())

            <div class="module-pagination">

                {{ $purchases->links() }}

            </div>

        @endif

    </div>

</div>

@endsection