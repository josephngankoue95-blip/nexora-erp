@extends('layouts.superadmin')

@section('title', 'Utilisateurs')

@section('content')

<div class="superadmin-module">

    {{-- ============================================================
         HEADER
    ============================================================ --}}
    <div class="module-header">

        <div>
            <span class="module-kicker">
                Administration
            </span>

            <h1>
                Utilisateurs
            </h1>

            <p>
                Gérez les comptes utilisateurs de toutes les entreprises.
            </p>
        </div>

        <a
            href="{{ route('superadmin.users.create') }}"
            class="module-primary-btn"
        >
            + Nouvel utilisateur
        </a>

    </div>


    {{-- ============================================================
         MESSAGES
    ============================================================ --}}
    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="module-alert module-alert-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- ============================================================
         RECHERCHE / FILTRES
    ============================================================ --}}
    <div class="module-panel filter-panel">

        <form
            action="{{ route('superadmin.users.index') }}"
            method="GET"
            class="users-filter-form"
        >

            {{-- RECHERCHE --}}
            <div class="filter-field search-field">

                <label>
                    Recherche
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nom ou adresse email..."
                >

            </div>


            {{-- ENTREPRISE --}}
            <div class="filter-field">

                <label>
                    Entreprise
                </label>

                <select name="company_id">

                    <option value="">
                        Toutes les entreprises
                    </option>

                    @foreach($companies as $company)

                        <option
                            value="{{ $company->id }}"
                            @selected(request('company_id') == $company->id)
                        >
                            {{ $company->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- RÔLE --}}
            <div class="filter-field">

                <label>
                    Rôle
                </label>

                <select name="role">

                    <option value="">
                        Tous les rôles
                    </option>

                    @foreach($roles as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(request('role') === $value)
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- STATUT --}}
            <div class="filter-field">

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


            {{-- ACTIONS FILTRES --}}
            <div class="filter-actions">

                <button
                    type="submit"
                    class="filter-btn"
                >
                    Rechercher
                </button>

                <a
                    href="{{ route('superadmin.users.index') }}"
                    class="reset-btn"
                >
                    Réinitialiser
                </a>

            </div>

        </form>

    </div>


    {{-- ============================================================
         TABLEAU
    ============================================================ --}}
    <div class="module-panel">

        <div class="table-header">

            <div>

                <h2>
                    Liste des utilisateurs
                </h2>

                <p>
                    {{ $users->total() }}
                    utilisateur(s)
                </p>

            </div>

        </div>


        <div class="responsive-table">

            <table>

                <thead>

                    <tr>

                        <th>
                            Utilisateur
                        </th>

                        <th>
                            Entreprise
                        </th>

                        <th>
                            Rôle
                        </th>

                        <th>
                            Statut
                        </th>

                        <th>
                            Créé le
                        </th>

                        <th class="actions-column">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($users as $user)

                        <tr>

                            {{-- ====================================================
                                 UTILISATEUR
                            ==================================================== --}}
                            <td>

                                <div class="table-user">

                                    <div class="table-avatar">

                                        {{ strtoupper(
                                            substr($user->name, 0, 2)
                                        ) }}

                                    </div>

                                    <div>

                                        <strong>
                                            {{ $user->name }}
                                        </strong>

                                        <span>
                                            {{ $user->email }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- ====================================================
                                 ENTREPRISE
                            ==================================================== --}}
                            <td>

                                @if($user->company)

                                    <span class="company-name">
                                        {{ $user->company->name }}
                                    </span>

                                @else

                                    <span class="text-muted">
                                        Aucune entreprise
                                    </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                 RÔLE
                            ==================================================== --}}
                            <td>

                                @php

                                    $roleLabels = [

                                        'companyadmin' =>
                                            'Administrateur entreprise',

                                        'director' =>
                                            'Directeur',

                                        'manager' =>
                                            'Manager',

                                        'seller' =>
                                            'Vendeur',

                                        'cashier' =>
                                            'Caissier',

                                        'stock_manager' =>
                                            'Gestionnaire de stock',

                                        'accountant' =>
                                            'Comptable',

                                        'employee' =>
                                            'Employé',

                                    ];

                                @endphp


                                <span class="role-pill">

                                    {{ $roleLabels[$user->role]
                                        ?? $user->role }}

                                </span>

                            </td>


                            {{-- ====================================================
                                 STATUT
                            ==================================================== --}}
                            <td>

                                @if($user->is_active)

                                    <span class="status-pill active">
                                        Actif
                                    </span>

                                @else

                                    <span class="status-pill inactive">
                                        Inactif
                                    </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                 DATE
                            ==================================================== --}}
                            <td>

                                <span class="date-value">

                                    {{ $user->created_at
                                        ? $user->created_at->format('d/m/Y')
                                        : '-' }}

                                </span>

                            </td>


                            {{-- ====================================================
                                 ACTIONS
                            ==================================================== --}}
                            <td>

                                <div class="table-actions">

                                    {{-- VOIR --}}
                                    <a
                                        href="{{ route(
                                            'superadmin.users.show',
                                            $user
                                        ) }}"
                                        class="action-btn view"
                                        title="Voir"
                                    >
                                        Voir
                                    </a>


                                    {{-- MODIFIER --}}
                                    <a
                                        href="{{ route(
                                            'superadmin.users.edit',
                                            $user
                                        ) }}"
                                        class="action-btn edit"
                                        title="Modifier"
                                    >
                                        Modifier
                                    </a>


                                    {{-- ACTIVER / DÉSACTIVER --}}
                                    <form
                                        action="{{ route(
                                            'superadmin.users.toggle-status',
                                            $user
                                        ) }}"
                                        method="POST"
                                    >

                                        @csrf

                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="action-btn toggle"
                                        >
                                            {{ $user->is_active
                                                ? 'Désactiver'
                                                : 'Activer' }}
                                        </button>

                                    </form>


                                    {{-- SUPPRIMER --}}
                                    <form
                                        action="{{ route(
                                            'superadmin.users.destroy',
                                            $user
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Voulez-vous vraiment supprimer cet utilisateur ?'
                                        )"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action-btn delete"
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
                                class="empty-table"
                            >

                                <div class="empty-icon">
                                    👥
                                </div>

                                <strong>
                                    Aucun utilisateur trouvé
                                </strong>

                                <span>
                                    Essayez de modifier vos critères
                                    de recherche.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================
             PAGINATION
        ========================================================= --}}
        @if($users->hasPages())

            <div class="module-pagination">

                {{ $users->links() }}

            </div>

        @endif

    </div>

</div>

@endsection