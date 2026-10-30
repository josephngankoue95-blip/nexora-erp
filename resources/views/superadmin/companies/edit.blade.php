@extends('layouts.superadmin')

@section('title', 'Modifier une entreprise')

@section('content')

<div class="page-header">

    <div>

        <h1>Modifier l'entreprise</h1>

        <p>
            Modifiez les informations de
            <strong>{{ $company->name }}</strong>.
        </p>

    </div>


    <a
        href="{{ route('superadmin.companies.show', $company) }}"
        class="btn btn-secondary">

        ← Retour aux détails

    </a>

</div>


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


<div class="form-card">

    <div class="form-card-header">

        <div>

            <h2>
                Informations de l'entreprise
            </h2>

            <p>
                Modifiez les informations générales.
            </p>

        </div>


        <span class="company-id">

            ID #{{ $company->id }}

        </span>

    </div>


    <form
        method="POST"
        action="{{ route('superadmin.companies.update', $company) }}">

        @csrf

        @method('PUT')


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
                    value="{{ old('name', $company->name) }}"
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
                    Adresse email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $company->email) }}"
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
                    value="{{ old('phone', $company->phone) }}"
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
                    value="{{ old('address', $company->address) }}"
                >

                @error('address')

                    <small class="field-error">
                        {{ $message }}
                    </small>

                @enderror

            </div>


            {{-- STATUT --}}

            <div class="form-group">

                <label for="status">
                    Statut
                </label>

                <select
                    id="status"
                    name="status">

                    <option
                        value="1"
                        {{ old('status', $company->status) ? 'selected' : '' }}>

                        Active

                    </option>

                    <option
                        value="0"
                        {{ !old('status', $company->status) ? 'selected' : '' }}>

                        Inactive

                    </option>

                </select>

            </div>

        </div>


        <div class="form-divider"></div>


        <div class="form-actions">

            <a
                href="{{ route('superadmin.companies.show', $company) }}"
                class="btn btn-secondary">

                Annuler

            </a>


            <button
                type="submit"
                class="btn btn-primary">

                Enregistrer les modifications

            </button>

        </div>

    </form>

</div>

@endsection