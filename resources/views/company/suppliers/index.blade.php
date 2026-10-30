@extends('layouts.company')

@section('title', 'Fournisseurs | Gestion')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>
            <span class="module-kicker">
                GESTION
            </span>

            <h1 class="module-title">
                Fournisseurs
            </h1>

            <p class="module-description">
                Gérez vos fournisseurs et partenaires commerciaux.
            </p>
        </div>

        <a href="{{ route('company.suppliers.create') }}"
           class="module-btn module-btn-primary">
            + Nouveau fournisseur
        </a>

    </div>


    {{-- STATISTIQUES --}}

    <div class="module-stats">

        <div class="module-stat-card">
            <span>Total fournisseurs</span>
            <strong>{{ $totalSuppliers }}</strong>
        </div>

        <div class="module-stat-card">
            <span>Actifs</span>
            <strong>{{ $activeSuppliers }}</strong>
        </div>

        <div class="module-stat-card">
            <span>Inactifs</span>
            <strong>{{ $inactiveSuppliers }}</strong>
        </div>

    </div>


    {{-- MESSAGE --}}

    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- FILTRES --}}

    <div class="module-card">

        <form method="GET"
              action="{{ route('company.suppliers.index') }}"
              class="module-filters">

            <div class="module-field">

                <label for="search">
                    Recherche
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nom, téléphone, email..."
                >

            </div>


            <div class="module-field">

                <label for="status">
                    Statut
                </label>

                <select id="status" name="status">

                    <option value="">
                        Tous
                    </option>

                    <option value="active"
                        @selected(request('status') === 'active')>
                        Actifs
                    </option>

                    <option value="inactive"
                        @selected(request('status') === 'inactive')>
                        Inactifs
                    </option>

                </select>

            </div>


            <div class="module-filter-actions">

                <button type="submit"
                        class="module-btn module-btn-primary">
                    Filtrer
                </button>

                <a href="{{ route('company.suppliers.index') }}"
                   class="module-btn module-btn-secondary">
                    Réinitialiser
                </a>

            </div>

        </form>

    </div>


    {{-- TABLE --}}

    <div class="module-card">

        <div class="module-table-wrapper">

            <table class="module-table">

                <thead>

                    <tr>
                        <th>Fournisseur</th>
                        <th>Contact</th>
                        <th>Localisation</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($suppliers as $supplier)

                        <tr>

                            <td>

                                <strong>
                                    {{ $supplier->name }}
                                </strong>

                                @if($supplier->tax_number)
                                    <small>
                                        N° contribuable :
                                        {{ $supplier->tax_number }}
                                    </small>
                                @endif

                            </td>


                            <td>

                                @if($supplier->phone)
                                    <div>
                                        {{ $supplier->phone }}
                                    </div>
                                @endif

                                @if($supplier->email)

                                    <small>
                                        {{ $supplier->email }}
                                    </small>

                                @endif

                                @if(!$supplier->phone && !$supplier->email)
                                    —
                                @endif

                            </td>


                            <td>

                                {{ $supplier->city ?: '—' }}

                                @if($supplier->country)
                                    <small>
                                        {{ $supplier->country }}
                                    </small>
                                @endif

                            </td>


                            <td>

                                @if($supplier->is_active)

                                    <span class="module-badge module-badge-success">
                                        Actif
                                    </span>

                                @else

                                    <span class="module-badge module-badge-danger">
                                        Inactif
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="module-actions">

                                    <a href="{{ route('company.suppliers.show', $supplier) }}"
                                       class="module-action">
                                        Voir
                                    </a>

                                    <a href="{{ route('company.suppliers.edit', $supplier) }}"
                                       class="module-action">
                                        Modifier
                                    </a>

                                    <form
                                        action="{{ route('company.suppliers.destroy', $supplier) }}"
                                        method="POST"
                                        onsubmit="return confirm('Supprimer ce fournisseur ?');"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="module-action module-action-danger">
                                            Supprimer
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5">

                                <div class="module-empty">

                                    <strong>
                                        Aucun fournisseur
                                    </strong>

                                    <p>
                                        Commencez par ajouter votre premier fournisseur.
                                    </p>

                                    <a href="{{ route('company.suppliers.create') }}"
                                       class="module-btn module-btn-primary">
                                        Ajouter un fournisseur
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($suppliers->hasPages())

            <div class="module-pagination">
                {{ $suppliers->links() }}
            </div>

        @endif

    </div>

</div>

@endsection