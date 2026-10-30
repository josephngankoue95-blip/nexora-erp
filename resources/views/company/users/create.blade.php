@extends('layouts.company')

@section('title', 'Nouvel utilisateur')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>

            <h1>Nouvel utilisateur</h1>

            <p>
                Créez un compte utilisateur pour votre entreprise.
            </p>

        </div>

        <a href="{{ route('company.users.index') }}"
           class="module-btn module-btn-secondary">

            Retour

        </a>

    </div>


    <div class="module-card">

        <form method="POST"
              action="{{ route('company.users.store') }}"
              class="user-form">

            @csrf


            <div class="user-form-grid">

                <div class="module-field">

                    <label>
                        Nom complet *
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="module-input"
                        value="{{ old('name') }}"
                        required
                    >

                    @error('name')

                        <small class="module-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                <div class="module-field">

                    <label>
                        Adresse email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="module-input"
                        value="{{ old('email') }}"
                        required
                    >

                    @error('email')

                        <small class="module-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                <div class="module-field">

                    <label>
                        Rôle *
                    </label>

                    <select
                        name="role"
                        class="module-input"
                        required
                    >

                        <option value="">
                            Sélectionner un rôle
                        </option>


                        <option value="director"
                            {{ old('role') === 'director' ? 'selected' : '' }}>
                            Directeur
                        </option>


                        <option value="manager"
                            {{ old('role') === 'manager' ? 'selected' : '' }}>
                            Manager
                        </option>


                        <option value="seller"
                            {{ old('role') === 'seller' ? 'selected' : '' }}>
                            Vendeur
                        </option>


                        <option value="cashier"
                            {{ old('role') === 'cashier' ? 'selected' : '' }}>
                            Caissier
                        </option>


                        <option value="stock_manager"
                            {{ old('role') === 'stock_manager' ? 'selected' : '' }}>
                            Gestionnaire de stock
                        </option>


                        <option value="accountant"
                            {{ old('role') === 'accountant' ? 'selected' : '' }}>
                            Comptable
                        </option>


                        <option value="employee"
                            {{ old('role') === 'employee' ? 'selected' : '' }}>
                            Employé
                        </option>

                    </select>

                    @error('role')

                        <small class="module-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                <div class="module-field">
                    &nbsp;
                </div>


                <div class="module-field">

                    <label>
                        Mot de passe *
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="module-input"
                        required
                    >

                    @error('password')

                        <small class="module-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                <div class="module-field">

                    <label>
                        Confirmation *
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="module-input"
                        required
                    >

                </div>

            </div>


            <div class="user-form-footer">

                <a href="{{ route('company.users.index') }}"
                   class="module-btn module-btn-secondary">

                    Annuler

                </a>


                <button type="submit"
                        class="module-btn module-btn-primary">

                    Créer l'utilisateur

                </button>

            </div>

        </form>

    </div>

</div>

@endsection