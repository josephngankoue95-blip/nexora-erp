@extends('layouts.superadmin')

@section('title', 'Nouveau paiement')

@section('content')

<div class="payment-page">

    {{-- ==========================================================
         EN-TÊTE
    =========================================================== --}}

    <div class="payment-page-header">

        <div>

            <div class="payment-breadcrumb">
                <a href="{{ route('superadmin.payments.index') }}">
                    Paiements
                </a>

                <i data-lucide="chevron-right"></i>

                <strong>Nouveau paiement</strong>
            </div>

            <h1>
                <i data-lucide="wallet-cards"></i>
                Enregistrer un paiement
            </h1>

            <p>
                Enregistrez un paiement et associez-le à l'abonnement d'une entreprise.
            </p>

        </div>

        <a
            href="{{ route('superadmin.payments.index') }}"
            class="payment-secondary-btn"
        >
            <i data-lucide="arrow-left"></i>
            Retour aux paiements
        </a>

    </div>


    {{-- ==========================================================
         ERREURS
    =========================================================== --}}

    @if($errors->any())

        <div class="payment-alert payment-alert-danger">

            <div class="payment-alert-icon">
                <i data-lucide="triangle-alert"></i>
            </div>

            <div>

                <strong>
                    Vérifiez les informations saisies
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        </div>

    @endif


    {{-- ==========================================================
         FORMULAIRE
    =========================================================== --}}

    <form
        method="POST"
        action="{{ route('superadmin.payments.store') }}"
        class="payment-form"
    >

        @csrf

        <div class="payment-form-layout">

            {{-- ==================================================
                 COLONNE PRINCIPALE
            =================================================== --}}

            <div class="payment-form-main">

                {{-- Entreprise + abonnement --}}
                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="building-2"></i>
                        </div>

                        <div>
                            <h2>
                                Entreprise et abonnement
                            </h2>

                            <p>
                                Sélectionnez l'entreprise et l'abonnement concernés.
                            </p>
                        </div>

                    </div>


                    <div class="payment-form-grid">

                        {{-- Entreprise --}}
                        <div class="payment-field">

                            <label for="company_id">
                                Entreprise
                                <span>*</span>
                            </label>

                            <select
                                name="company_id"
                                id="company_id"
                                required
                            >

                                <option value="">
                                    Sélectionner une entreprise
                                </option>

                                @foreach($companies as $company)

                                    <option
                                        value="{{ $company->id }}"
                                        {{ old('company_id') == $company->id ? 'selected' : '' }}
                                    >
                                        {{ $company->name }}
                                        @if($company->email)
                                            — {{ $company->email }}
                                        @endif
                                    </option>

                                @endforeach

                            </select>

                            @error('company_id')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- Abonnement --}}
                        <div class="payment-field">

                            <label for="subscription_id">
                                Abonnement
                            </label>

                            <select
                                name="subscription_id"
                                id="subscription_id"
                            >

                                <option value="">
                                    Aucun abonnement
                                </option>

                                @foreach($subscriptions as $subscription)

                                    <option
                                        value="{{ $subscription->id }}"
                                        data-company="{{ $subscription->company_id }}"
                                        data-price="{{ $subscription->price }}"
                                        {{ old('subscription_id') == $subscription->id ? 'selected' : '' }}
                                    >

                                        {{ $subscription->company->name ?? 'Entreprise' }}
                                        —
                                        {{ $subscription->name }}

                                        @if($subscription->plan)
                                            — {{ $subscription->plan->name }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            <small class="payment-field-help">
                                Un paiement terminé doit être associé à un abonnement.
                            </small>

                            @error('subscription_id')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- Informations paiement --}}
                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="credit-card"></i>
                        </div>

                        <div>
                            <h2>
                                Informations du paiement
                            </h2>

                            <p>
                                Indiquez le montant et le mode de règlement.
                            </p>
                        </div>

                    </div>


                    <div class="payment-form-grid">

                        {{-- Montant --}}
                        <div class="payment-field">

                            <label for="amount">
                                Montant
                                <span>*</span>
                            </label>

                            <div class="payment-input-suffix">

                                <input
                                    type="number"
                                    name="amount"
                                    id="amount"
                                    value="{{ old('amount') }}"
                                    min="1"
                                    step="0.01"
                                    placeholder="0"
                                    required
                                >

                                <span>FCFA</span>

                            </div>

                            @error('amount')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- Méthode --}}
                        <div class="payment-field">

                            <label for="method">
                                Méthode de paiement
                                <span>*</span>
                            </label>

                            <select
                                name="method"
                                id="method"
                                required
                            >

                                <option value="">
                                    Sélectionner
                                </option>

                                <option
                                    value="mobile_money"
                                    {{ old('method') === 'mobile_money' ? 'selected' : '' }}
                                >
                                    Mobile Money
                                </option>

                                <option
                                    value="cash"
                                    {{ old('method') === 'cash' ? 'selected' : '' }}
                                >
                                    Espèces
                                </option>

                                <option
                                    value="bank_transfer"
                                    {{ old('method') === 'bank_transfer' ? 'selected' : '' }}
                                >
                                    Virement bancaire
                                </option>

                                <option
                                    value="card"
                                    {{ old('method') === 'card' ? 'selected' : '' }}
                                >
                                    Carte bancaire
                                </option>

                                <option
                                    value="other"
                                    {{ old('method') === 'other' ? 'selected' : '' }}
                                >
                                    Autre
                                </option>

                            </select>

                            @error('method')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- Fournisseur --}}
                        <div class="payment-field">

                            <label for="provider">
                                Fournisseur
                            </label>

                            <input
                                type="text"
                                name="provider"
                                id="provider"
                                value="{{ old('provider') }}"
                                placeholder="Ex. MTN, Orange, Express Union..."
                            >

                            @error('provider')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- Téléphone --}}
                        <div class="payment-field">

                            <label for="phone">
                                Téléphone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old('phone') }}"
                                placeholder="Ex. 6XXXXXXXX"
                            >

                            @error('phone')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- Référence transaction --}}
                        <div class="payment-field payment-field-full">

                            <label for="transaction_reference">
                                Référence de transaction
                            </label>

                            <input
                                type="text"
                                name="transaction_reference"
                                id="transaction_reference"
                                value="{{ old('transaction_reference') }}"
                                placeholder="Référence fournie par le moyen de paiement"
                            >

                            @error('transaction_reference')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- Statut --}}
                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="activity"></i>
                        </div>

                        <div>
                            <h2>
                                Statut de la transaction
                            </h2>

                            <p>
                                Définissez l'état actuel du paiement.
                            </p>
                        </div>

                    </div>


                    <div class="payment-status-options">

                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="completed"
                                {{ old('status', 'completed') === 'completed' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-green">
                                    <i data-lucide="check-circle"></i>
                                </span>

                                <span>
                                    <strong>Terminé</strong>
                                    <small>Paiement confirmé et encaissé</small>
                                </span>

                            </span>

                        </label>


                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="pending"
                                {{ old('status') === 'pending' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-orange">
                                    <i data-lucide="clock-3"></i>
                                </span>

                                <span>
                                    <strong>En attente</strong>
                                    <small>Transaction en cours de vérification</small>
                                </span>

                            </span>

                        </label>


                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="failed"
                                {{ old('status') === 'failed' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-red">
                                    <i data-lucide="x-circle"></i>
                                </span>

                                <span>
                                    <strong>Échoué</strong>
                                    <small>Le paiement n'a pas abouti</small>
                                </span>

                            </span>

                        </label>


                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="refunded"
                                {{ old('status') === 'refunded' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-blue">
                                    <i data-lucide="rotate-ccw"></i>
                                </span>

                                <span>
                                    <strong>Remboursé</strong>
                                    <small>Montant remboursé au client</small>
                                </span>

                            </span>

                        </label>

                    </div>

                </div>


                {{-- Notes --}}
                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="file-text"></i>
                        </div>

                        <div>
                            <h2>
                                Notes
                            </h2>

                            <p>
                                Informations complémentaires sur cette transaction.
                            </p>
                        </div>

                    </div>

                    <div class="payment-field">

                        <textarea
                            name="notes"
                            id="notes"
                            rows="5"
                            placeholder="Ajoutez une note si nécessaire..."
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <small class="payment-field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- ==================================================
                 COLONNE LATÉRALE
            =================================================== --}}

            <aside class="payment-form-sidebar">

                <div class="payment-info-card">

                    <div class="payment-info-icon">
                        <i data-lucide="shield-check"></i>
                    </div>

                    <h3>
                        Enregistrement sécurisé
                    </h3>

                    <p>
                        Une référence unique sera automatiquement générée
                        pour cette transaction.
                    </p>

                </div>


                <div class="payment-info-card payment-info-warning">

                    <div class="payment-info-icon">
                        <i data-lucide="info"></i>
                    </div>

                    <h3>
                        Activation de l'abonnement
                    </h3>

                    <p>
                        Lorsqu'un paiement est enregistré comme
                        <strong>terminé</strong>, l'abonnement associé
                        sera automatiquement activé ou prolongé.
                    </p>

                </div>

            </aside>

        </div>


        {{-- ==================================================
             ACTIONS
        =================================================== --}}

        <div class="payment-form-actions">

            <a
                href="{{ route('superadmin.payments.index') }}"
                class="payment-secondary-btn"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="payment-primary-btn"
            >
                <i data-lucide="check"></i>
                Enregistrer le paiement
            </button>

        </div>

    </form>

</div>


{{-- ==========================================================
     JS
=========================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const companySelect = document.getElementById('company_id');
    const subscriptionSelect = document.getElementById('subscription_id');
    const amountInput = document.getElementById('amount');

    if (!companySelect || !subscriptionSelect) {
        return;
    }

    function filterSubscriptions() {

        const companyId = companySelect.value;

        Array.from(subscriptionSelect.options).forEach(function (option) {

            if (!option.value) {
                option.hidden = false;
                return;
            }

            const optionCompany = option.dataset.company;

            option.hidden =
                companyId !== '' &&
                optionCompany !== companyId;

        });

        const selected = subscriptionSelect.options[
            subscriptionSelect.selectedIndex
        ];

        if (
            selected &&
            selected.value &&
            selected.hidden
        ) {
            subscriptionSelect.value = '';

            if (amountInput) {
                amountInput.value = '';
            }
        }

    }

    function updateAmountFromSubscription() {

        const selected =
            subscriptionSelect.options[
                subscriptionSelect.selectedIndex
            ];

        if (
            selected &&
            selected.value &&
            selected.dataset.price &&
            amountInput &&
            !amountInput.value
        ) {
            amountInput.value = selected.dataset.price;
        }

    }

    companySelect.addEventListener(
        'change',
        filterSubscriptions
    );

    subscriptionSelect.addEventListener(
        'change',
        updateAmountFromSubscription
    );

    filterSubscriptions();
    updateAmountFromSubscription();

});

</script>

@endsection