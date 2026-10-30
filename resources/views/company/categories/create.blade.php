@extends('layouts.company')

@section('title', 'Ajouter une catégorie')

@section('content')

<div class="page-header">

    <div>

        <h1>
            Ajouter une catégorie
        </h1>

        <p>
            Créez une nouvelle catégorie pour vos produits.
        </p>

    </div>

    <a
        href="{{ route('company.categories.index') }}"
        class="btn btn-secondary"
    >
        ← Retour
    </a>

</div>


<div class="dashboard-section">

    <div class="dashboard-section-header">

        <div>

            <h2>
                Informations de la catégorie
            </h2>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('company.categories.store') }}"
    >

        @csrf


        <div class="form-group">

            <label for="name">
                Nom de la catégorie *
            </label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                placeholder="Ex : Informatique"
            >

            @error('name')
                <span class="error-message">
                    {{ $message }}
                </span>
            @enderror

        </div>


        <div class="form-group">

            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                placeholder="Description de la catégorie..."
            >{{ old('description') }}</textarea>

            @error('description')
                <span class="error-message">
                    {{ $message }}
                </span>
            @enderror

        </div>


        <div class="form-group">

            <label for="is_active">
                Statut
            </label>

            <select
                id="is_active"
                name="is_active"
            >

                <option value="1">
                    Active
                </option>

                <option value="0">
                    Inactive
                </option>

            </select>

        </div>


        <div class="form-actions">

            <a
                href="{{ route('company.categories.index') }}"
                class="btn btn-secondary"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Enregistrer
            </button>

        </div>

    </form>

</div>

@endsection