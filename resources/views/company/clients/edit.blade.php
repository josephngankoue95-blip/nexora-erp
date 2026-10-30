@extends('layouts.company')

@section('title', 'Modifier le client')
@section('page-title', 'Modifier le client')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                CLIENTS
            </span>

            <h1>Modifier le client</h1>

            <p>
                {{ $client->name }}
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

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('company.clients.update', $client->id) }}"
    >

        @csrf
        @method('PUT')


        <div class="module-card">

            <div class="module-card-header">

                <h2>
                    Informations du client
                </h2>

            </div>


            <div class="form-grid">

                <div class="form-group form-group-full">

                    <label>
                        Nom / raison sociale *
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $client->name) }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Téléphone</label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $client->phone) }}"
                    >

                </div>


                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $client->email) }}"
                    >

                </div>


                <div class="form-group">

                    <label>Ville</label>

                    <input
                        type="text"
                        name="city"
                        value="{{ old('city', $client->city) }}"
                    >

                </div>


                <div class="form-group">

                    <label>Pays *</label>

                    <input
                        type="text"
                        name="country"
                        value="{{ old('country', $client->country) }}"
                        required
                    >

                </div>


                <div class="form-group form-group-full">

                    <label>Adresse</label>

                    <input
                        type="text"
                        name="address"
                        value="{{ old('address', $client->address) }}"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Numéro contribuable
                    </label>

                    <input
                        type="text"
                        name="tax_number"
                        value="{{ old('tax_number', $client->tax_number) }}"
                    >

                </div>


                <div class="form-group">

                    <label>Statut</label>

                    <select name="is_active">

                        <option
                            value="1"
                            @selected(old('is_active', $client->is_active ? '1' : '0') === '1')
                        >
                            Actif
                        </option>

                        <option
                            value="0"
                            @selected(old('is_active', $client->is_active ? '1' : '0') === '0')
                        >
                            Inactif
                        </option>

                    </select>

                </div>


                <div class="form-group form-group-full">

                    <label>Notes</label>

                    <textarea
                        name="notes"
                        rows="4"
                    >{{ old('notes', $client->notes) }}</textarea>

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
                Enregistrer
            </button>

        </div>

    </form>

</div>

@endsection