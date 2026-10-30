@extends('layouts.company')

@section('title', 'Catégories')

@section('content')

<div class="page-header">

    <div>

        <h1>
            Catégories
        </h1>

        <p>
            Gérez les catégories de vos produits.
        </p>

    </div>


    <a
        href="{{ route('company.categories.create') }}"
        class="btn btn-primary"
    >
        + Ajouter une catégorie
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
        action="{{ route('company.categories.index') }}"
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
                placeholder="Nom de la catégorie..."
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
            href="{{ route('company.categories.index') }}"
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
                Liste des catégories
            </h2>

            <p>
                {{ $categories->total() }}
                catégorie(s)
            </p>

        </div>

    </div>


    <div class="table-container">

        <table class="dashboard-table">

            <thead>

                <tr>

                    <th>
                        Catégorie
                    </th>

                    <th>
                        Description
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

            @forelse($categories as $category)

                <tr>

                    <td>

                        <strong>
                            {{ $category->name }}
                        </strong>

                    </td>


                    <td>

                        {{ $category->description ?? '—' }}

                    </td>


                    <td>

                        @if($category->is_active)

                            <span class="badge badge-success">
                                Active
                            </span>

                        @else

                            <span class="badge badge-danger">
                                Inactive
                            </span>

                        @endif

                    </td>


                    <td>

                        {{ $category->created_at->format('d/m/Y') }}

                    </td>


                    <td>

                        <div class="table-actions">


                            <a
                                href="{{ route('company.categories.show', $category) }}"
                                class="action-button"
                            >
                                Voir
                            </a>


                            <a
                                href="{{ route('company.categories.edit', $category) }}"
                                class="action-button"
                            >
                                Modifier
                            </a>


                            <form
                                method="POST"
                                action="{{ route('company.categories.toggle-status', $category) }}"
                            >

                                @csrf

                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="action-button"
                                >
                                    {{ $category->is_active
                                        ? 'Désactiver'
                                        : 'Activer'
                                    }}
                                </button>

                            </form>


                            <form
                                method="POST"
                                action="{{ route('company.categories.destroy', $category) }}"
                                onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');"
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
                        colspan="5"
                        class="empty-state"
                    >
                        Aucune catégorie trouvée.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}

    <div class="pagination-container">

        {{ $categories->links() }}

    </div>

</div>

@endsection