@extends('layouts.superadmin')
@section('title', 'Entreprises')
@section('content')

<div class="page-header">

    <div>

        <h1>Entreprises</h1>

        <p>
            Gérez toutes les entreprises de votre plateforme.
        </p>

    </div>

    <a
        href="{{ route('superadmin.companies.create') }}"
        class="btn btn-primary"
    >
        + Ajouter une entreprise
    </a>

</div>


{{-- ALERTES --}}

@if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

    </div>

@endif


{{-- FILTRES --}}

<div class="dashboard-section">

    <form
        method="GET"
        action="{{ route('superadmin.companies.index') }}"
        class="company-filters"
    >

        <div class="filter-group">

            <label>
                Rechercher
            </label>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nom, email, téléphone..."
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
                    {{ request('status') === 'active' ? 'selected' : '' }}
                >
                    Actives
                </option>

                <option
                    value="inactive"
                    {{ request('status') === 'inactive' ? 'selected' : '' }}
                >
                    Inactives
                </option>

            </select>

        </div>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Rechercher
        </button>


        <a
            href="{{ route('superadmin.companies.index') }}"
            class="btn btn-secondary"
        >
            Réinitialiser
        </a>

    </form>

</div>


{{-- TABLE --}}

<div class="dashboard-section">

    <div class="dashboard-section-header">

        <div>

            <h2>
                Liste des entreprises
            </h2>

            <p>
                {{ $companies->total() }}
                entreprise(s)
            </p>

        </div>

    </div>


    <div class="table-container">

        <table class="dashboard-table">

            <thead>

                <tr>

                    <th>
                        Entreprise
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Téléphone
                    </th>

                    <th>
                        Statut
                    </th>

                    <th>
                        Création
                    </th>

                    <th>
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

            @forelse($companies as $company)

                <tr>

                    {{-- ENTREPRISE --}}

                    <td>

                        <strong>
                            {{ $company->name }}
                        </strong>

                    </td>


                    {{-- EMAIL --}}

                    <td>
                        {{ $company->email ?? '—' }}
                    </td>


                    {{-- TELEPHONE --}}

                    <td>
                        {{ $company->phone ?? '—' }}
                    </td>


                    {{-- STATUT --}}

                    <td>

                        @if($company->is_active)

                            <span class="badge badge-success">
                                Active
                            </span>

                        @else

                            <span class="badge badge-danger">
                                Inactive
                            </span>

                        @endif

                    </td>


                    {{-- DATE DE CREATION --}}

                    <td>
                        {{ $company->created_at->format('d/m/Y') }}
                    </td>


                    {{-- ACTIONS --}}

                    <td>

                        <div class="table-actions">

                            {{-- VOIR --}}

                            <a
                                href="{{ route('superadmin.companies.show', $company) }}"
                                class="action-button"
                            >
                                Voir
                            </a>


                            {{-- MODIFIER --}}

                            <a
                                href="{{ route('superadmin.companies.edit', $company) }}"
                                class="action-button"
                            >
                                Modifier
                            </a>

                            <a
                                href="{{ route('superadmin.companies.modules.edit',$company) }}"
                                class="action-button"
                            >
                                ⚙️ Modules
                            </a>


                            {{-- ACTIVER / DESACTIVER --}}

                            <form
                                method="POST"
                                action="{{ route('superadmin.companies.toggle-status', $company) }}"
                            >

                                @csrf

                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="action-button"
                                >
                                    {{ $company->is_active
                                        ? 'Désactiver'
                                        : 'Activer'
                                    }}
                                </button>

                            </form>


                            {{-- SUPPRIMER --}}

                            <form
                                method="POST"
                                action="{{ route('superadmin.companies.destroy', $company) }}"
                                onsubmit="return confirm('Voulez-vous vraiment supprimer cette entreprise ?');"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="action-button action-danger"
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
                        Aucune entreprise trouvée.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}

    <div class="pagination-container">

        {{ $companies->links() }}

    </div>

</div>

@endsection