
@extends('layouts.ecommerce')

@section('title', 'Nouvelle catégorie')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ecommerce-categories.css') }}">
@endpush

@section('content')

<div class="ec-category-page">

    <div class="ec-category-header">
        <div>
            <h1>Nouvelle catégorie</h1>
            <p>Ajoutez une catégorie à votre boutique E-commerce.</p>
        </div>

        <a href="{{ route('company.ecommerce.categories.index') }}"
           class="ec-btn ec-btn-secondary">
            ← Retour
        </a>
    </div>

    <form action="{{ route('company.ecommerce.categories.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="ec-form">

        @csrf

        <div class="ec-panel">
            <h2>Informations générales</h2>

            <div class="ec-form-grid">

                <div class="ec-field">
                    <label for="name">Nom de la catégorie *</label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="ec-input"
                           value="{{ old('name') }}"
                           maxlength="255"
                           required>
                    @error('name')
                        <small class="ec-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="ec-field">
                    <label for="parent_id">Catégorie parente</label>
                    <select name="parent_id"
                            id="parent_id"
                            class="ec-input">
                        <option value="">Catégorie principale</option>

                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <small class="ec-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="ec-field ec-field-full">
                    <label for="description">Description</label>
                    <textarea name="description"
                              id="description"
                              class="ec-input ec-textarea"
                              rows="5"
                              maxlength="5000">{{ old('description') }}</textarea>
                    @error('description')
                        <small class="ec-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="ec-field">
                    <label for="sort_order">Ordre d'affichage</label>
                    <input type="number"
                           name="sort_order"
                           id="sort_order"
                           min="0"
                           class="ec-input"
                           value="{{ old('sort_order', 0) }}">
                    @error('sort_order')
                        <small class="ec-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="ec-field">
                    <label for="image">Image de catégorie</label>
                    <input type="file"
                           name="image"
                           id="image"
                           class="ec-input"
                           accept="image/jpeg,image/png,image/webp">
                    <small>JPG, PNG ou WEBP — 2 Mo maximum.</small>
                    @error('image')
                        <small class="ec-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="ec-field ec-field-full">
                    <label class="ec-check-label">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', 1) ? 'checked' : '' }}>
                        Catégorie active
                    </label>
                </div>

            </div>
        </div>

        <div class="ec-form-actions">
            <a href="{{ route('company.ecommerce.categories.index') }}"
               class="ec-btn ec-btn-secondary">
                Annuler
            </a>

            <button type="submit" class="ec-btn ec-btn-primary">
                Enregistrer la catégorie
            </button>
        </div>

    </form>

</div>

@endsection
