@extends('layouts.superadmin')

@section('title', 'Détails de l’entreprise')

@section('content')

<div class="page-header">

    <div>

        <h1>{{ $company->name }}</h1>

        <p>
            Informations et gestion de l'entreprise.
        </p>

    </div>


    <div class="page-header-actions">

        <a
            href="{{ route('superadmin.companies.index') }}"
            class="btn btn-secondary">

            ← Retour

        </a>


        <a
            href="{{ route('superadmin.companies.edit', $company) }}"
            class="btn btn-primary">

            Modifier

        </a>

    </div>

</div>


{{-- =====================================================
     INFORMATIONS PRINCIPALES
====================================================== --}}

<div class="company-profile-card">

    <div class="company-profile-left">

        <div class="company-logo-placeholder">

            {{ strtoupper(substr($company->name, 0, 1)) }}

        </div>


        <div>

            <h2>
                {{ $company->name }}
            </h2>

            <p>
                {{ $company->slug }}
            </p>

        </div>

    </div>


    <div class="company-status">

        @if($company->status)

            <span class="badge badge-success">
                Active
            </span>

        @else

            <span class="badge badge-danger">
                Inactive
            </span>

        @endif

    </div>

</div>


{{-- =====================================================
     INFORMATIONS
====================================================== --}}

<div class="details-grid">


    <div class="detail-card">

        <span class="detail-label">
            Email
        </span>

        <strong>

            {{ $company->email ?? 'Non renseigné' }}

        </strong>

    </div>


    <div class="detail-card">

        <span class="detail-label">
            Téléphone
        </span>

        <strong>

            {{ $company->phone ?? 'Non renseigné' }}

        </strong>

    </div>


    <div class="detail-card">

        <span class="detail-label">
            Adresse
        </span>

        <strong>

            {{ $company->address ?? 'Non renseignée' }}

        </strong>

    </div>


    <div class="detail-card">

        <span class="detail-label">
            Date de création
        </span>

        <strong>

            {{ $company->created_at->format('d/m/Y à H:i') }}

        </strong>

    </div>


</div>


{{-- =====================================================
     UTILISATEURS
====================================================== --}}

<div class="dashboard-section company-users-section">

    <div class="dashboard-section-header">

        <div>

            <h2>
                Utilisateurs
            </h2>

            <p>
                Utilisateurs associés à cette entreprise.
            </p>

        </div>


        <div class="section-counter">

            {{ $company->users->count() }}

        </div>

    </div>


    @if($company->users->count())

        <div class="table-container">

            <table class="dashboard-table">

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
                            Créé le
                        </th>

                    </tr>

                </thead>


                <tbody>

                @foreach($company->users as $user)

                    <tr>

                        <td>

                            <strong>
                                {{ $user->name }}
                            </strong>

                        </td>


                        <td>

                            {{ $user->email }}

                        </td>


                        <td>

                            @if($user->role === 'admin')

                                <span class="badge badge-primary">
                                    Administrateur

                                </span>

                            @elseif($user->role === 'super_admin')

                                <span class="badge badge-warning">
                                    Super Admin
                                </span>

                            @else

                                <span class="badge badge-neutral">
                                    {{ ucfirst($user->role) }}
                                </span>

                            @endif

                        </td>


                        <td>

                            {{ $user->created_at->format('d/m/Y') }}

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                ♙
            </div>

            <strong>
                Aucun utilisateur
            </strong>

            <p>
                Aucun utilisateur n'est encore associé
                à cette entreprise.
            </p>

        </div>

    @endif

</div>


{{-- =====================================================
     ZONE DANGER
====================================================== --}}

<div class="danger-zone">

    <div>

        <h3>
            Zone dangereuse
        </h3>

        <p>
            La suppression de cette entreprise est une
            opération irréversible.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('superadmin.companies.destroy', $company) }}"
        onsubmit="return confirm('Attention ! Voulez-vous réellement supprimer cette entreprise ? Cette opération est irréversible.');">

        @csrf

        @method('DELETE')

        <button
            type="submit"
            class="btn btn-danger">

            Supprimer l'entreprise

        </button>

    </form>

</div>

@endsection