@extends('layouts.superadmin')

@section('title', 'Modifier le paiement')

@section('content')

<div class="payment-page">

    <div class="payment-page-header">

        <div>

            <div class="payment-breadcrumb">

                <a href="{{ route('superadmin.payments.index') }}">
                    Paiements
                </a>

                <i data-lucide="chevron-right"></i>

                <a href="{{ route('superadmin.payments.show', $payment) }}">
                    {{ $payment->reference }}
                </a>

                <i data-lucide="chevron-right"></i>

                <strong>Modifier</strong>

            </div>

            <h1>
                <i data-lucide="pencil"></i>
                Modifier le paiement
            </h1>

            <p>
                Modifiez les informations de la transaction
                {{ $payment->reference }}.
            </p>

        </div>

        <a
            href="{{ route('superadmin.payments.show', $payment) }}"
            class="payment-secondary-btn"
        >
            <i data-lucide="arrow-left"></i>
            Retour au paiement
        </a>

    </div>


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


    <form
        method="POST"
        action="{{ route('superadmin.payments.update', $payment) }}"
        class="payment-form"
    >

        @csrf
        @method('PUT')

        <div class="payment-form-layout">

            <div class="payment-form-main">

                {{-- ==================================================
                     ENTREPRISE
                =================================================== --}}

                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="building-2"></i>
                        </div>

                        <div>
                            <h2>Entreprise et abonnement</h2>
                            <p>
                                Modifiez l'entreprise ou l'abonnement associé.
                            </p>
                        </div>

                    </div>


                    <div class="payment-form-grid">

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
                                        {{ old('company_id', $payment->company_id) == $company->id ? 'selected' : '' }}
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
                                        {{ old('subscription_id', $payment->subscription_id) == $subscription->id ? 'selected' : '' }}
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

                            @error('subscription_id')
                                <small class="payment-field-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- ==================================================
                     PAIEMENT
                =================================================== --}}

                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="credit-card"></i>
                        </div>

                        <div>
                            <h2>Informations du paiement</h2>
                            <p>
                                Modifiez les informations de la transaction.
                            </p>
                        </div>

                    </div>


                    <div class="payment-form-grid">

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
                                    value="{{ old('amount', $payment->amount) }}"
                                    min="1"
                                    step="0.01"
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


                        <div class="payment-field">

                            <label for="method">
                                Méthode
                                <span>*</span>
                            </label>

                            <select
                                name="method"
                                id="method"
                                required
                            >

                                <option
                                    value="mobile_money"
                                    {{ old('method', $payment->method) === 'mobile_money' ? 'selected' : '' }}
                                >
                                    Mobile Money
                                </option>

                                <option
                                    value="cash"
                                    {{ old('method', $payment->method) === 'cash' ? 'selected' : '' }}
                                >
                                    Espèces
                                </option>

                                <option
                                    value="bank_transfer"
                                    {{ old('method', $payment->method) === 'bank_transfer' ? 'selected' : '' }}
                                >
                                    Virement bancaire
                                </option>

                                <option
                                    value="card"
                                    {{ old('method', $payment->method) === 'card' ? 'selected' : '' }}
                                >
                                    Carte bancaire
                                </option>

                                <option
                                    value="other"
                                    {{ old('method', $payment->method) === 'other' ? 'selected' : '' }}
                                >
                                    Autre
                                </option>

                            </select>

                        </div>


                        <div class="payment-field">

                            <label for="provider">
                                Fournisseur
                            </label>

                            <input
                                type="text"
                                name="provider"
                                id="provider"
                                value="{{ old('provider', $payment->provider) }}"
                                placeholder="Ex. MTN, Orange..."
                            >

                        </div>


                        <div class="payment-field">

                            <label for="phone">
                                Téléphone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old('phone', $payment->phone) }}"
                                placeholder="Ex. 6XXXXXXXX"
                            >

                        </div>


                        <div class="payment-field payment-field-full">

                            <label for="transaction_reference">
                                Référence de transaction
                            </label>

                            <input
                                type="text"
                                name="transaction_reference"
                                id="transaction_reference"
                                value="{{ old('transaction_reference', $payment->transaction_reference) }}"
                                placeholder="Référence de la transaction"
                            >

                        </div>

                    </div>

                </div>


                {{-- ==================================================
                     STATUT
                =================================================== --}}

                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="activity"></i>
                        </div>

                        <div>
                            <h2>Statut</h2>
                            <p>
                                Modifiez l'état de la transaction.
                            </p>
                        </div>

                    </div>


                    <div class="payment-status-options">

                        @php
                            $currentStatus = old('status', $payment->status);
                        @endphp


                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="completed"
                                {{ $currentStatus === 'completed' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-green">
                                    <i data-lucide="check-circle"></i>
                                </span>

                                <span>
                                    <strong>Terminé</strong>
                                    <small>Paiement confirmé</small>
                                </span>

                            </span>

                        </label>


                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="pending"
                                {{ $currentStatus === 'pending' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-orange">
                                    <i data-lucide="clock-3"></i>
                                </span>

                                <span>
                                    <strong>En attente</strong>
                                    <small>Transaction en cours</small>
                                </span>

                            </span>

                        </label>


                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="failed"
                                {{ $currentStatus === 'failed' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-red">
                                    <i data-lucide="x-circle"></i>
                                </span>

                                <span>
                                    <strong>Échoué</strong>
                                    <small>Paiement non abouti</small>
                                </span>

                            </span>

                        </label>


                        <label class="payment-status-option">

                            <input
                                type="radio"
                                name="status"
                                value="refunded"
                                {{ $currentStatus === 'refunded' ? 'checked' : '' }}
                            >

                            <span class="payment-status-option-content">

                                <span class="payment-status-option-icon payment-option-blue">
                                    <i data-lucide="rotate-ccw"></i>
                                </span>

                                <span>
                                    <strong>Remboursé</strong>
                                    <small>Paiement remboursé</small>
                                </span>

                            </span>

                        </label>

                    </div>

                </div>


                {{-- ==================================================
                     NOTES
                =================================================== --}}

                <div class="payment-form-card">

                    <div class="payment-form-card-header">

                        <div class="payment-form-card-icon">
                            <i data-lucide="file-text"></i>
                        </div>

                        <div>
                            <h2>Notes</h2>
                            <p>
                                Informations complémentaires.
                            </p>
                        </div>

                    </div>

                    <div class="payment-field">

                        <textarea
                            name="notes"
                            id="notes"
                            rows="5"
                            placeholder="Ajoutez une note..."
                        >{{ old('notes', $payment->notes) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- ==================================================
                 SIDEBAR
            =================================================== --}}

            <aside class="payment-form-sidebar">

                <div class="payment-info-card">

                    <div class="payment-info-icon">
                        <i data-lucide="hash"></i>
                    </div>

                    <h3>
                        Référence
                    </h3>

                    <div class="payment-info-reference">
                        {{ $payment->reference }}
                    </div>

                    <p>
                        Cette référence identifie de manière unique
                        la transaction.
                    </p>

                </div>


                <div class="payment-info-card">

                    <div class="payment-info-icon">
                        <i data-lucide="calendar"></i>
                    </div>

                    <h3>
                        Créé le
                    </h3>

                    <strong>
                        {{ $payment->created_at->format('d/m/Y à H:i') }}
                    </strong>

                </div>

            </aside>

        </div>


        <div class="payment-form-actions">

            <a
                href="{{ route('superadmin.payments.show', $payment) }}"
                class="payment-secondary-btn"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="payment-primary-btn"
            >
                <i data-lucide="save"></i>
                Enregistrer les modifications
            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const companySelect = document.getElementById('company_id');
    const subscriptionSelect = document.getElementById('subscription_id');

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

            option.hidden =
                companyId !== '' &&
                option.dataset.company !== companyId;

        });

        const selected =
            subscriptionSelect.options[
                subscriptionSelect.selectedIndex
            ];

        if (selected && selected.hidden) {
            subscriptionSelect.value = '';
        }

    }

    companySelect.addEventListener(
        'change',
        filterSubscriptions
    );

    filterSubscriptions();

});

</script>

@endsection