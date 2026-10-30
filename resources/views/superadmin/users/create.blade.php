@extends('layouts.superadmin')

@section('title', 'Nouvel utilisateur')

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
                Nouvel utilisateur
            </h1>

            <p>
                Créez un utilisateur et rattachez-le à une entreprise.
            </p>
        </div>

        <a
            href="{{ route('superadmin.users.index') }}"
            class="module-secondary-btn"
        >
            ← Retour aux utilisateurs
        </a>

    </div>


    {{-- ============================================================
         ERREURS
    ============================================================ --}}
    @if($errors->any())

        <div class="module-alert module-alert-error">

            <strong>
                Vérifiez les informations saisies.
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- ============================================================
         FORMULAIRE
    ============================================================ --}}
    <form
        action="{{ route('superadmin.users.store') }}"
        method="POST"
    >

        @csrf


        <div class="user-form-grid">


            {{-- ====================================================
                 INFORMATIONS PERSONNELLES
            ==================================================== --}}
            <div class="module-panel">

                <div class="form-section-header">

                    <div class="form-section-icon">
                        👤
                    </div>

                    <div>
                        <h2>
                            Informations personnelles
                        </h2>

                        <p>
                            Informations principales du compte.
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    {{-- NOM --}}
                    <div class="form-group form-full">

                        <label for="name">
                            Nom complet
                            <span>*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ex : Jean Dupont"
                            autocomplete="name"
                            required
                        >

                    </div>


                    {{-- EMAIL --}}
                    <div class="form-group form-full">

                        <label for="email">
                            Adresse email
                            <span>*</span>
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="exemple@entreprise.com"
                            autocomplete="email"
                            required
                        >

                    </div>

                </div>

            </div>



            {{-- ====================================================
                 ENTREPRISE / RÔLE
            ==================================================== --}}
            <div class="module-panel">

                <div class="form-section-header">

                    <div class="form-section-icon">
                        🏢
                    </div>

                    <div>
                        <h2>
                            Affectation
                        </h2>

                        <p>
                            Définissez l'entreprise et le rôle de l'utilisateur.
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    {{-- ENTREPRISE --}}
                    <div class="form-group form-full">

                        <label for="company_id">
                            Entreprise
                            <span>*</span>
                        </label>

                        <select
                            id="company_id"
                            name="company_id"
                            required
                        >

                            <option value="">
                                Sélectionner une entreprise
                            </option>

                            @foreach($companies as $company)

                                <option
                                    value="{{ $company->id }}"
                                    @selected(
                                        old('company_id') == $company->id
                                    )
                                >
                                    {{ $company->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- RÔLE --}}
                    <div class="form-group form-full">

                        <label for="role">
                            Rôle
                            <span>*</span>
                        </label>

                        <select
                            id="role"
                            name="role"
                            required
                        >

                            <option value="">
                                Sélectionner un rôle
                            </option>

                            @foreach($roles as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(
                                        old('role') === $value
                                    )
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                        <small class="form-help">
                            Le rôle détermine les accès et permissions de l'utilisateur.
                        </small>

                    </div>

                </div>

            </div>



            {{-- ====================================================
                 MOT DE PASSE
            ==================================================== --}}
            <div class="module-panel">

                <div class="form-section-header">

                    <div class="form-section-icon">
                        🔐
                    </div>

                    <div>
                        <h2>
                            Sécurité
                        </h2>

                        <p>
                            Définissez le mot de passe initial du compte.
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    {{-- MOT DE PASSE --}}
                    <div class="form-group">

                        <label for="password">
                            Mot de passe
                            <span>*</span>
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Minimum 8 caractères"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >

                    </div>


                    {{-- CONFIRMATION --}}
                    <div class="form-group">

                        <label for="password_confirmation">
                            Confirmation
                            <span>*</span>
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Confirmer le mot de passe"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >

                    </div>

                </div>

            </div>



            {{-- ====================================================
                 STATUT
            ==================================================== --}}
            <div class="module-panel">

                <div class="form-section-header">

                    <div class="form-section-icon">
                        ⚙️
                    </div>

                    <div>
                        <h2>
                            Statut du compte
                        </h2>

                        <p>
                            Déterminez si le compte peut immédiatement se connecter.
                        </p>
                    </div>

                </div>


                <label class="status-switch">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(old('is_active', true))
                    >

                    <span class="switch-slider"></span>

                    <span class="switch-text">
                        Compte actif
                    </span>

                </label>

            </div>

        </div>



        {{-- ========================================================
             ACTIONS
        ========================================================= --}}
        <div class="form-actions">

            <a
                href="{{ route('superadmin.users.index') }}"
                class="module-secondary-btn"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="module-primary-btn"
            >
                Créer l'utilisateur
            </button>

        </div>

    </form>

</div>

@endsection