@extends('layouts.company')

@section('title', 'Modifier la prestation')

@section('content')

<div class="services-page">

    <div class="services-header">

        <div>

            <div class="services-eyebrow">
                CATALOGUE / PRESTATIONS
            </div>

            <h1>Modifier la prestation</h1>

            <p>
                Modifiez les informations de cette prestation.
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

                <h2>
                    {{ $service->name }}
                </h2>

                <p>
                    Modification de la prestation
                </p>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route(
                'company.services.update',
                $service
            ) }}"
            class="services-form"
        >

            @csrf
            @method('PUT')


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
                        value="{{ old(
                            'name',
                            $service->name
                        ) }}"
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
                        value="{{ old(
                            'reference',
                            $service->reference
                        ) }}"
                    >

                </div>


                <div class="services-field">

                    <label for="category">
                        Catégorie
                    </label>

                    <input
                        type="text"
                        id="category"
                        name="category"
                        value="{{ old(
                            'category',
                            $service->category
                        ) }}"
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
                            value="{{ old(
                                'price',
                                $service->price
                            ) }}"
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
                            value="{{ old(
                                'tax',
                                $service->tax
                            ) }}"
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
                    >{{ old(
                        'description',
                        $service->description
                    ) }}</textarea>

                </div>


                <div class="services-field services-field-full">

                    <label class="services-checkbox">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old(
                                'is_active',
                                $service->is_active
                            ))
                        >

                        <span class="services-checkbox-mark"></span>

                        <span>

                            <strong>
                                Prestation active
                            </strong>

                            <small>
                                Une prestation inactive ne pourra pas
                                être sélectionnée pour une nouvelle facture.
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
                    Enregistrer les modifications
                </button>

            </div>

        </form>

    </div>

</div>

@endsection