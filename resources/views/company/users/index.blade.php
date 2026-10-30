@extends('layouts.company')

@section('title', 'Utilisateurs')

@section('content')

<div class="module-page">

<div class="module-header">

    <div>

        <h1>Utilisateurs</h1>

        <p>
            Gérez les utilisateurs et les accès de votre entreprise.
        </p>

    </div>

    @canPermission('users.create')

        <a href="{{ route('company.users.create') }}"
           class="module-btn module-btn-primary">

            + Nouvel utilisateur

        </a>

    @endcanPermission

</div>


{{-- =========================================================
   ALERTES
========================================================== --}}

@if(session('success'))

    <div class="module-alert module-alert-success">

        {{ session('success') }}

    </div>

@endif


@if($errors->any())

    <div class="module-alert module-alert-danger">

        {{ $errors->first() }}

    </div>

@endif


{{-- =========================================================
   STATISTIQUES
========================================================== --}}

<div class="user-stats">

    <div class="user-stat-card">

        <span>Total utilisateurs</span>

        <strong>
            {{ $totalUsers }}
        </strong>

    </div>


    <div class="user-stat-card">

        <span>Actifs</span>

        <strong>
            {{ $activeUsers }}
        </strong>

    </div>


    <div class="user-stat-card">

        <span>Inactifs</span>

        <strong>
            {{ $inactiveUsers }}
        </strong>

    </div>

</div>


{{-- =========================================================
   FILTRES
========================================================== --}}

<div class="module-card user-filter-card">

    <form method="GET"
          action="{{ route('company.users.index') }}"
          class="user-filters">

        <input
            type="text"
            name="search"
            class="module-input"
            placeholder="Rechercher un nom ou email..."
            value="{{ request('search') }}"
        >


        <select
            name="role"
            class="module-input"
        >

            <option value="">
                Tous les rôles
            </option>


            <option value="director"
                {{ request('role') === 'director' ? 'selected' : '' }}>
                Directeur
            </option>


            <option value="manager"
                {{ request('role') === 'manager' ? 'selected' : '' }}>
                Manager
            </option>


            <option value="seller"
                {{ request('role') === 'seller' ? 'selected' : '' }}>
                Vendeur
            </option>


            <option value="cashier"
                {{ request('role') === 'cashier' ? 'selected' : '' }}>
                Caissier
            </option>


            <option value="stock_manager"
                {{ request('role') === 'stock_manager' ? 'selected' : '' }}>
                Gestionnaire de stock
            </option>


            <option value="accountant"
                {{ request('role') === 'accountant' ? 'selected' : '' }}>
                Comptable
            </option>


            <option value="employee"
                {{ request('role') === 'employee' ? 'selected' : '' }}>
                Employé
            </option>

        </select>


        <div class="user-filter-actions">

            <button type="submit"
                    class="module-btn module-btn-primary">

                Rechercher

            </button>


            <a href="{{ route('company.users.index') }}"
               class="module-btn module-btn-secondary">

                Réinitialiser

            </a>

        </div>

    </form>

</div>


{{-- =========================================================
   TABLEAU
========================================================== --}}

<div class="module-card">

    <div class="user-table-wrapper">

        <table class="module-table">

            <thead>

                <tr>

                    <th>
                        Utilisateur
                    </th>

                    <th>
                        Email
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

                    <th>
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($users as $user)

                    <tr>

                        {{-- UTILISATEUR --}}

                        <td>

                            <strong>
                                {{ $user->name }}
                            </strong>

                        </td>


                        {{-- EMAIL --}}

                        <td>

                            {{ $user->email }}

                        </td>


                        {{-- RÔLE --}}

                        <td>

                            <span class="user-role">

                                {{ $user->roleLabel() }}

                            </span>

                        </td>


                        {{-- STATUT --}}

                        <td>

                            @if($user->is_active)

                                <span class="user-status user-status-active">

                                    Actif

                                </span>

                            @else

                                <span class="user-status user-status-inactive">

                                    Inactif

                                </span>

                            @endif

                        </td>


                        {{-- DATE --}}

                        <td>

                            {{ $user->created_at->format('d/m/Y') }}

                        </td>


                        {{-- ACTIONS --}}

                        <td>

                            <div class="user-actions">

                                {{-- MODIFICATION --}}

                                @canPermission('users.edit')

                                    <a
                                        href="{{ route('company.users.edit', $user) }}"
                                        class="module-btn module-btn-small module-btn-secondary"
                                    >

                                        ✏️ Modifier

                                    </a>


                                    {{-- PERMISSIONS --}}

                                    <a
                                        href="{{ route('company.users.permissions.edit', $user) }}"
                                        class="btn btn-permissions"
                                    >

                                        🔐 Permissions

                                    </a>


                                    {{-- ACTIVER / DÉSACTIVER --}}

                                    <form
                                        method="POST"
                                        action="{{ route('company.users.toggle-status', $user) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="module-btn module-btn-small"
                                        >

                                            {{ $user->is_active
                                                ? 'Désactiver'
                                                : 'Activer'
                                            }}

                                        </button>

                                    </form>

                                @endcanPermission


                                {{-- SUPPRESSION --}}

                                @canPermission('users.delete')

                                    <form
                                        method="POST"
                                        action="{{ route('company.users.destroy', $user) }}"
                                        onsubmit="return confirm('Supprimer cet utilisateur ?');"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="module-btn module-btn-small user-delete-btn"
                                        >

                                            Supprimer

                                        </button>

                                    </form>

                                @endcanPermission


                                {{-- CONSULTATION UNIQUEMENT --}}

                                @if(
                                    !auth()->user()->hasPermission('users.edit') &&
                                    !auth()->user()->hasPermission('users.delete')
                                )

                                    <span class="user-view-only">

                                        Consultation uniquement

                                    </span>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6">

                            <div class="module-empty">

                                Aucun utilisateur trouvé.

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
       PAGINATION
    ====================================================== --}}

    @if($users->hasPages())

        <div class="module-pagination">

            {{ $users->links() }}

        </div>

    @endif

</div>

</div>

@endsection
