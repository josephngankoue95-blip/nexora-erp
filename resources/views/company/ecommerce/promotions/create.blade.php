
@extends('layouts.ecommerce')

@section('title', 'Créer une promotion')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ecommerce-promotions.css') }}">
@endpush

@section('content')
<div class="promo-page">

    <header class="promo-header">
        <div>
            <span class="promo-eyebrow">MARKETING / NOUVELLE OFFRE</span>
            <h1>Créer une promotion</h1>
            <p>Définissez les conditions de votre offre commerciale.</p>
        </div>

        <a href="{{ route('company.ecommerce.promotions.index') }}"
           class="promo-btn promo-btn-light">
            ← Retour
        </a>
    </header>

    @if($errors->any())
        <div class="promo-alert promo-alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('company.ecommerce.promotions.store') }}"
          class="promo-panel promo-form">

        @csrf

        <div class="promo-section-title">
            <h2>Informations générales</h2>
            <p>Identifiez votre promotion et son code éventuel.</p>
        </div>

        <div class="promo-form-grid">
            <label class="promo-field">
                <span>Nom de la promotion *</span>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       maxlength="255"
                       required
                       placeholder="Ex. Offre de bienvenue">
            </label>

            <label class="promo-field">
                <span>Code de réduction</span>
                <input type="text"
                       name="code"
                       value="{{ old('code') }}"
                       maxlength="80"
                       placeholder="Ex. BIENVENUE10">
                <small>Laisse ce champ vide pour une promotion sans code.</small>
            </label>
        </div>

        <div class="promo-section-title">
            <h2>Conditions de réduction</h2>
            <p>Déterminez le montant de l'avantage accordé.</p>
        </div>

        <div class="promo-form-grid">
            <label class="promo-field">
                <span>Type de réduction *</span>
                <select name="type" id="promo-type" required>
                    <option value="percentage" @selected(old('type', 'percentage') === 'percentage')>
                        Pourcentage (%)
                    </option>
                    <option value="fixed" @selected(old('type') === 'fixed')>
                        Montant fixe (FCFA)
                    </option>
                </select>
            </label>

            <label class="promo-field">
                <span>Valeur de la réduction *</span>
                <input type="number"
                       name="value"
                       id="promo-value"
                       min="0.01"
                       max="100"
                       step="0.01"
                       value="{{ old('value') }}"
                       required>
                <small id="promo-value-help">Pourcentage compris entre 0 et 100.</small>
            </label>

            <label class="promo-field">
                <span>Montant minimal de commande (FCFA)</span>
                <input type="number"
                       name="minimum_order_amount"
                       min="0"
                       step="1"
                       value="{{ old('minimum_order_amount', 0) }}">
            </label>

            <label class="promo-field">
                <span>Nombre maximal d'utilisations</span>
                <input type="number"
                       name="usage_limit"
                       min="1"
                       step="1"
                       value="{{ old('usage_limit') }}"
                       placeholder="Illimité si vide">
            </label>
        </div>

        <div class="promo-section-title">
            <h2>Durée de validité</h2>
            <p>Choisissez quand l'offre doit commencer et se terminer.</p>
        </div>

        <div class="promo-form-grid">
            <label class="promo-field">
                <span>Date de début</span>
                <input type="datetime-local"
                       name="starts_at"
                       value="{{ old('starts_at') }}">
            </label>

            <label class="promo-field">
                <span>Date de fin</span>
                <input type="datetime-local"
                       name="ends_at"
                       value="{{ old('ends_at') }}">
            </label>
        </div>

        <div class="promo-form-footer">
            <a href="{{ route('company.ecommerce.promotions.index') }}"
               class="promo-btn promo-btn-light">
                Annuler
            </a>

            <button type="submit" class="promo-btn promo-btn-primary">
                Enregistrer la promotion
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('promo-type');
    const value = document.getElementById('promo-value');
    const help = document.getElementById('promo-value-help');

    function updateReductionField() {
        const isPercentage = type.value === 'percentage';

        value.max = isPercentage ? '100' : '';
        help.textContent = isPercentage
            ? 'Pourcentage compris entre 0 et 100.'
            : 'Montant fixe de réduction en FCFA.';
    }

    type.addEventListener('change', updateReductionField);
    updateReductionField();
});
</script>
@endsection
