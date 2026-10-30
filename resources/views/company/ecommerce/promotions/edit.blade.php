
@extends('layouts.ecommerce')

@section('title', 'Modifier une promotion')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ecommerce-promotions.css') }}">
@endpush

@section('content')
<div class="promo-page">

    <header class="promo-header">
        <div>
            <span class="promo-eyebrow">MARKETING / MODIFICATION</span>
            <h1>Modifier la promotion</h1>
            <p>Mettez à jour les conditions de cette offre.</p>
        </div>

        <a href="{{ route('company.ecommerce.promotions.index') }}"
           class="promo-btn promo-btn-light">
            ← Retour à la liste
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
          action="{{ route('company.ecommerce.promotions.update', $promotion) }}"
          class="promo-panel promo-form">

        @csrf
        @method('PUT')

        <div class="promo-section-title">
            <h2>Informations générales</h2>
            <p>Modifiez le nom et le code de la promotion.</p>
        </div>

        <div class="promo-form-grid">
            <label class="promo-field">
                <span>Nom de la promotion *</span>
                <input type="text"
                       name="name"
                       value="{{ old('name', $promotion->name) }}"
                       maxlength="255"
                       required>
            </label>

            <label class="promo-field">
                <span>Code de réduction</span>
                <input type="text"
                       name="code"
                       value="{{ old('code', $promotion->code) }}"
                       maxlength="80">
                <small>Le code peut être laissé vide.</small>
            </label>
        </div>

        <div class="promo-section-title">
            <h2>Conditions de réduction</h2>
        </div>

        <div class="promo-form-grid">
            <label class="promo-field">
                <span>Type de réduction *</span>
                <select name="type" id="promo-type" required>
                    <option value="percentage" @selected(old('type', $promotion->type) === 'percentage')>
                        Pourcentage (%)
                    </option>
                    <option value="fixed" @selected(old('type', $promotion->type) === 'fixed')>
                        Montant fixe (FCFA)
                    </option>
                </select>
            </label>

            <label class="promo-field">
                <span>Valeur *</span>
                <input type="number"
                       name="value"
                       id="promo-value"
                       min="0.01"
                       step="0.01"
                       value="{{ old('value', $promotion->value) }}"
                       required>
                <small id="promo-value-help"></small>
            </label>

            <label class="promo-field">
                <span>Montant minimal (FCFA)</span>
                <input type="number"
                       name="minimum_order_amount"
                       min="0"
                       step="1"
                       value="{{ old('minimum_order_amount', $promotion->minimum_order_amount) }}">
            </label>

            <label class="promo-field">
                <span>Nombre maximal d'utilisations</span>
                <input type="number"
                       name="usage_limit"
                       min="1"
                       step="1"
                       value="{{ old('usage_limit', $promotion->usage_limit) }}"
                       placeholder="Illimité si vide">
            </label>

            <label class="promo-field">
                <span>Date de début</span>
                <input type="datetime-local"
                       name="starts_at"
                       value="{{ old('starts_at', $promotion->starts_at?->format('Y-m-d\TH:i')) }}">
            </label>

            <label class="promo-field">
                <span>Date de fin</span>
                <input type="datetime-local"
                       name="ends_at"
                       value="{{ old('ends_at', $promotion->ends_at?->format('Y-m-d\TH:i')) }}">
            </label>
        </div>

        <div class="promo-form-footer">
            <a href="{{ route('company.ecommerce.promotions.index') }}"
               class="promo-btn promo-btn-light">
                Annuler
            </a>

            <button type="submit" class="promo-btn promo-btn-primary">
                Enregistrer les modifications
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
