@extends('layouts.company')

@section('title', 'Modifier la catégorie')

@section('content')

<div class="page-header">

    <div>

        <h1>
            Modifier la catégorie
        </h1>

        <p>
            Modifiez les informations de cette catégorie.
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
        action="{{ route('company.categories.update', $category) }}"
    >

        @csrf

        @method('PUT')


        <div class="form-group">

            <label for="name">
                Nom de la catégorie *
            </label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $category->name) }}"
                required
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
            >{{ old('description', $category->description) }}</textarea>

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

                <option
                    value="1"
                    {{ $category->is_active ? 'selected' : '' }}
                >
                    Active
                </option>

                <option
                    value="0"
                    {{ !$category->is_active ? 'selected' : '' }}
                >
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
                Enregistrer les modifications
            </button>

        </div>

    </form>

</div>

@endsection