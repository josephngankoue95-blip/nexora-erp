@extends('layouts.superadmin')

@section('title', 'Ajouter une entreprise')

@section('content')

<div class="page-header">

    <div>

        <h1>Ajouter une entreprise</h1>

        <p>
            Créez une entreprise et son administrateur principal.
        </p>

    </div>


    <a
        href="{{ route('superadmin.companies.index') }}"
        class="btn btn-secondary">

        ← Retour aux entreprises

    </a>

</div>


{{-- =====================================================
     ERREURS
====================================================== --}}

@if ($errors->any())

    <div class="alert alert-error">

        <strong>
            Veuillez corriger les erreurs suivantes :
        </strong>

        <ul class="form-errors">

            @foreach ($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


<form
    method="POST"
    action="{{ route('superadmin.companies.store') }}">

    @csrf


    {{-- =================================================
         ENTREPRISE
    ================================================== --}}

    <div class="form-card">

        <div class="form-card-header">

            <div>

                <h2>
                    Informations de l'entreprise
                </h2>

                <p>
                    Informations générales de la société.
                </p>

            </div>

        </div>


        <div class="form-card-body">

            <div class="form-grid">


                {{-- NOM --}}

                <div class="form-group">

                    <label for="name">

                        Nom de l'entreprise

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ex : Société ABC"
                        required
                    >

                    @error('name')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- EMAIL --}}

                <div class="form-group">

                    <label for="email">
                        Email de l'entreprise
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="contact@entreprise.com"
                    >

                    @error('email')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- TELEPHONE --}}

                <div class="form-group">

                    <label for="phone">
                        Téléphone
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="+237 6XX XXX XXX"
                    >

                    @error('phone')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- ADRESSE --}}

                <div class="form-group">

                    <label for="address">
                        Adresse
                    </label>

                    <input
                        type="text"
                        id="address"
                        name="address"
                        value="{{ old('address') }}"
                        placeholder="Ville, quartier..."
                    >

                    @error('address')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>



            {{-- STATUT --}}

            <div class="form-group">

                <label for="is_active">
                    Statut
                </label>

                <select
                    id="is_active"
                    name="is_active"
                >

                    <option
                        value="1"
                        {{ old('is_active', '1') == '1' ? 'selected' : '' }}
                    >
                        Active
                    </option>

                    <option
                        value="0"
                        {{ old('is_active') === '0' ? 'selected' : '' }}
                    >
                        Inactive
                    </option>

                </select>

                @error('is_active')

                    <small class="field-error">
                        {{ $message }}
                    </small>

                @enderror

            </div>

            </div>

        </div>

    </div>


    {{-- =================================================
         ADMINISTRATEUR
    ================================================== --}}

    <div class="form-card admin-form-card">

        <div class="form-card-header">

            <div>

                <h2>
                    Administrateur principal
                </h2>

                <p>
                    Ce compte permettra à l'entreprise
                    d'accéder à son espace de gestion.
                </p>

            </div>

            <span class="admin-badge">
                ADMIN
            </span>

        </div>


        <div class="form-card-body">

            <div class="form-grid">


                {{-- NOM ADMIN --}}

                <div class="form-group">

                    <label for="admin_name">

                        Nom de l'administrateur

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        id="admin_name"
                        name="admin_name"
                        value="{{ old('admin_name') }}"
                        placeholder="Ex : Jean Dupont"
                        required
                    >

                    @error('admin_name')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- EMAIL ADMIN --}}

                <div class="form-group">

                    <label for="admin_email">

                        Email de connexion

                        <span class="required">*</span>

                    </label>

                    <input
                        type="email"
                        id="admin_email"
                        name="admin_email"
                        value="{{ old('admin_email') }}"
                        placeholder="admin@entreprise.com"
                        required
                    >

                    @error('admin_email')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- MOT DE PASSE --}}

                <div class="form-group">

                    <label for="admin_password">

                        Mot de passe

                        <span class="required">*</span>

                    </label>

                    <input
                        type="password"
                        id="admin_password"
                        name="admin_password"
                        placeholder="Minimum 8 caractères"
                        required
                    >

                    @error('admin_password')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- CONFIRMATION --}}

                <div class="form-group">

                    <label for="admin_password_confirmation">

                        Confirmation du mot de passe

                        <span class="required">*</span>

                    </label>

                    <input
                        type="password"
                        id="admin_password_confirmation"
                        name="admin_password_confirmation"
                        placeholder="Répétez le mot de passe"
                        required
                    >

                </div>


            </div>


            {{-- INFORMATION --}}

            <div class="admin-info">

                <div class="admin-info-icon">
                    i
                </div>

                <div>

                    <strong>
                        Accès administrateur
                    </strong>

                    <p>
                        Cet utilisateur aura accès uniquement
                        aux données de cette entreprise.
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =================================================
         ACTIONS
    ================================================== --}}

    <div class="form-actions form-actions-bottom">

        <a
            href="{{ route('superadmin.companies.index') }}"
            class="btn btn-secondary">

            Annuler

        </a>


        <button
            type="submit"
            class="btn btn-primary">

            Créer l'entreprise

        </button>

    </div>

</form>

@endsection