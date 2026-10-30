@extends('layouts.superadmin')

@section('title', 'Détails utilisateur')

@section('content')

@php

    $roleLabels = [
        'super_admin' => 'Super Admin',
        'companyadmin' => 'Administrateur entreprise',
        'manager' => 'Manager',
        'seller' => 'Vendeur',
        'cashier' => 'Caissier',
        'stock_manager' => 'Gestionnaire de stock',
        'accountant' => 'Comptable',
        'employee' => 'Employé',
    ];

@endphp


<div class="superadmin-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                Administration
            </span>

            <h1>
                Détails utilisateur
            </h1>

            <p>
                Informations détaillées du compte.
            </p>

        </div>

        <div class="detail-header-actions">

            <a
                href="{{ route('superadmin.users.edit', $user) }}"
                class="module-primary-btn"
            >
                Modifier
            </a>

            <a
                href="{{ route('superadmin.users.index') }}"
                class="module-secondary-btn"
            >
                ← Retour
            </a>

        </div>

    </div>


    <div class="user-detail-grid">

        {{-- PROFIL --}}

        <div class="module-panel user-profile-card">

            <div class="large-user-avatar">

                {{ strtoupper(
                    substr($user->name, 0, 2)
                ) }}

            </div>

            <h2>
                {{ $user->name }}
            </h2>

            <p>
                {{ $user->email }}
            </p>


            <div class="profile-status">

                @if($user->is_active)

                    <span class="status-pill active">
                        Compte actif
                    </span>

                @else

                    <span class="status-pill inactive">
                        Compte inactif
                    </span>

                @endif

            </div>

        </div>


        {{-- INFORMATIONS --}}

        <div class="module-panel">

            <div class="detail-panel-header">

                <div>
                    <h2>
                        Informations du compte
                    </h2>

                    <p>
                        Données principales de l'utilisateur.
                    </p>
                </div>

            </div>


            <div class="detail-list">

                <div class="detail-row">

                    <span>
                        Nom complet
                    </span>

                    <strong>
                        {{ $user->name }}
                    </strong>

                </div>


                <div class="detail-row">

                    <span>
                        Email
                    </span>

                    <strong>
                        {{ $user->email }}
                    </strong>

                </div>


                <div class="detail-row">

                    <span>
                        Rôle
                    </span>

                    <strong>

                        {{ $roleLabels[$user->role]
                            ?? $user->role }}

                    </strong>

                </div>


                <div class="detail-row">

                    <span>
                        Entreprise
                    </span>

                    <strong>

                        {{ $user->company?->name
                            ?? 'Aucune entreprise' }}

                    </strong>

                </div>


                <div class="detail-row">

                    <span>
                        Créé le
                    </span>

                    <strong>

                        {{ $user->created_at
                            ? $user->created_at->format(
                                'd/m/Y à H:i'
                            )
                            : '-' }}

                    </strong>

                </div>


                <div class="detail-row">

                    <span>
                        Dernière modification
                    </span>

                    <strong>

                        {{ $user->updated_at
                            ? $user->updated_at->format(
                                'd/m/Y à H:i'
                            )
                            : '-' }}

                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- ACTIONS --}}

    <div class="module-panel detail-actions-panel">

        <div>

            <h2>
                Actions du compte
            </h2>

            <p>
                Gérez rapidement l'état de cet utilisateur.
            </p>

        </div>


        <div class="detail-actions">

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
                    class="action-large toggle"
                >

                    {{ $user->is_active
                        ? 'Désactiver le compte'
                        : 'Activer le compte' }}

                </button>

            </form>


            @if($user->role !== 'super_admin')

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
                        class="action-large delete"
                    >
                        Supprimer l'utilisateur
                    </button>

                </form>

            @endif

        </div>

    </div>

</div>

@endsection