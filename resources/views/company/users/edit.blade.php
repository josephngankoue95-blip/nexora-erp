@extends('layouts.company')

@section('title', 'Modifier utilisateur')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>

            <h1>Modifier utilisateur</h1>

            <p>
                Modifiez les informations et le rôle de cet utilisateur.
            </p>

        </div>

        <a href="{{ route('company.users.index') }}"
           class="module-btn module-btn-secondary">

            Retour

        </a>

    </div>


    <div class="module-card">

        <form method="POST"
              action="{{ route('company.users.update', $user) }}"
              class="user-form">

            @csrf
            @method('PUT')


            <div class="user-form-grid">

                <div class="module-field">

                    <label>
                        Nom complet *
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="module-input"
                        value="{{ old('name', $user->name) }}"
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
                        Email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="module-input"
                        value="{{ old('email', $user->email) }}"
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

                        @foreach([
                            'director' => 'Directeur',
                            'manager' => 'Manager',
                            'seller' => 'Vendeur',
                            'cashier' => 'Caissier',
                            'stock_manager' => 'Gestionnaire de stock',
                            'accountant' => 'Comptable',
                            'employee' => 'Employé',
                        ] as $value => $label)

                            <option value="{{ $value }}"
                                {{ old('role', $user->role) === $value ? 'selected' : '' }}>

                                {{ $label }}

                            </option>

                        @endforeach

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
                        Nouveau mot de passe
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="module-input"
                    >

                    <small>
                        Laissez vide pour conserver l'ancien.
                    </small>

                    @error('password')

                        <small class="module-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                <div class="module-field">

                    <label>
                        Confirmation
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="module-input"
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

                    Enregistrer

                </button>

            </div>

        </form>

    </div>

</div>

@endsection