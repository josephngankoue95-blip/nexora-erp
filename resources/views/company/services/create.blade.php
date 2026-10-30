@extends('layouts.company')

@section('title', 'Nouvelle prestation')

@section('content')

<div class="services-page">

    <div class="services-header">

        <div>

            <div class="services-eyebrow">
                CATALOGUE / PRESTATIONS
            </div>

            <h1>Nouvelle prestation</h1>

            <p>
                Ajoutez un service proposé par votre entreprise.
            </p>

        </div>

        <a
            href="{{ route('company.services.index') }}"
            class="services-secondary-button"
        >
            ← Retour
        </a>

    </div>


    @if($errors->any())

        <div class="services-alert services-alert-danger">

            <div>

                <strong>
                    Vérifiez les informations saisies.
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    <div class="services-form-card">

        <div class="services-form-header">

            <div class="services-form-icon">
                🔧
            </div>

            <div>

                <h2>Informations de la prestation</h2>

                <p>
                    Définissez le service et son tarif.
                </p>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('company.services.store') }}"
            class="services-form"
        >

            @csrf


            <div class="services-form-grid">

                <div class="services-field services-field-full">

                    <label for="name">
                        Nom de la prestation
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ex. Maintenance ordinateur"
                        required
                    >

                </div>


                <div class="services-field">

                    <label for="reference">
                        Référence
                    </label>

                    <input
                        type="text"
                        id="reference"
                        name="reference"
                        value="{{ old('reference') }}"
                        placeholder="Ex. SERV-001"
                    >

                    <small>
                        La référence doit être unique dans votre entreprise.
                    </small>

                </div>


                <div class="services-field">

                    <label for="category">
                        Catégorie
                    </label>

                    <input
                        type="text"
                        id="category"
                        name="category"
                        value="{{ old('category') }}"
                        placeholder="Ex. Maintenance"
                    >

                </div>


                <div class="services-field">

                    <label for="price">
                        Prix
                        <span>*</span>
                    </label>

                    <div class="services-price-input">

                        <input
                            type="number"
                            id="price"
                            name="price"
                            value="{{ old('price', 0) }}"
                            min="0"
                            step="0.01"
                            required
                        >

                        <span>FCFA</span>

                    </div>

                </div>


                <div class="services-field">

                    <label for="tax">
                        Taxe
                    </label>

                    <div class="services-price-input">

                        <input
                            type="number"
                            id="tax"
                            name="tax"
                            value="{{ old('tax', 0) }}"
                            min="0"
                            max="100"
                            step="0.01"
                        >

                        <span>%</span>

                    </div>

                </div>


                <div class="services-field services-field-full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Décrivez la prestation..."
                    >{{ old('description') }}</textarea>

                </div>


                <div class="services-field services-field-full">

                    <label class="services-checkbox">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', true))
                        >

                        <span class="services-checkbox-mark"></span>

                        <span>

                            <strong>
                                Prestation active
                            </strong>

                            <small>
                                Cette prestation pourra être sélectionnée
                                lors de la création d'une facture.
                            </small>

                        </span>

                    </label>

                </div>

            </div>


            <div class="services-form-footer">

                <a
                    href="{{ route('company.services.index') }}"
                    class="services-secondary-button"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="services-primary-button"
                >
                    Enregistrer la prestation
                </button>

            </div>

        </form>

    </div>

</div>

@endsection