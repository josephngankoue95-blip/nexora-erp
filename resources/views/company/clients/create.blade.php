@extends('layouts.company')

@section('title', 'Nouveau client')
@section('page-title', 'Nouveau client')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                CLIENTS
            </span>

            <h1>Nouveau client</h1>

            <p>
                Ajoutez un client à votre entreprise.
            </p>

        </div>

        <a
            href="{{ route('company.clients.index') }}"
            class="module-btn module-btn-light"
        >
            ← Retour
        </a>

    </div>


    @if($errors->any())

        <div class="module-alert module-alert-danger">

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


    <form
        method="POST"
        action="{{ route('company.clients.store') }}"
    >

        @csrf


        <div class="module-card">

            <div class="module-card-header">

                <h2>
                    Informations du client
                </h2>

                <span>
                    Coordonnées principales
                </span>

            </div>


            <div class="form-grid">

                <div class="form-group form-group-full">

                    <label>
                        Nom / raison sociale *
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ex : ETS MBOG"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Téléphone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Ex : 690 00 00 00"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="client@email.com"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Ville
                    </label>

                    <input
                        type="text"
                        name="city"
                        value="{{ old('city') }}"
                        placeholder="Ex : Yaoundé"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Pays *
                    </label>

                    <input
                        type="text"
                        name="country"
                        value="{{ old('country', 'Cameroun') }}"
                        required
                    >

                </div>


                <div class="form-group form-group-full">

                    <label>
                        Adresse
                    </label>

                    <input
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        placeholder="Adresse complète"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Numéro contribuable
                    </label>

                    <input
                        type="text"
                        name="tax_number"
                        value="{{ old('tax_number') }}"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Statut
                    </label>

                    <select name="is_active">

                        <option value="1">
                            Actif
                        </option>

                        <option value="0">
                            Inactif
                        </option>

                    </select>

                </div>


                <div class="form-group form-group-full">

                    <label>
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        rows="4"
                        placeholder="Informations complémentaires..."
                    >{{ old('notes') }}</textarea>

                </div>

            </div>

        </div>


        <div class="form-actions">

            <a
                href="{{ route('company.clients.index') }}"
                class="module-btn module-btn-light"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="module-btn module-btn-primary"
            >
                Enregistrer le client
            </button>

        </div>

    </form>

</div>

@endsection