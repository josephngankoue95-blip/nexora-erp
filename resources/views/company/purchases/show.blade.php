@extends('layouts.company')

@section('title', 'Achat ' . $purchase->reference)

@section('content')

<div class="module-page purchase-show-page">

    {{-- ============================================================
         HEADER
    ============================================================ --}}

    <div class="module-header purchase-show-header">

        <div>

            <div class="purchase-breadcrumb">

                <a href="{{ route('company.purchases.index') }}">
                    Achats
                </a>

                <span>›</span>

                <span>
                    {{ $purchase->reference }}
                </span>

            </div>

            <span class="module-kicker">
                DÉTAIL DE L'ACHAT
            </span>

            <h1 class="module-title">
                {{ $purchase->reference }}
            </h1>

            <p class="module-description">
                Consultez les produits, le fournisseur, les paiements et les informations financières de cet achat.
            </p>

        </div>


        <div class="purchase-show-actions">

            <a
                href="{{ route('company.purchases.invoice', $purchase) }}"
                class="module-btn module-btn-primary"
                target="_blank"
            >
                🧾 Facture
            </a>

            <a
                href="{{ route('company.purchases.index') }}"
                class="module-btn module-btn-secondary"
            >
                ← Retour
            </a>

        </div>

    </div>


    {{-- ============================================================
         ALERTES
    ============================================================ --}}

    @if(session('success'))

        <div class="module-alert module-alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div class="module-alert module-alert-danger">

            <strong>
                Une erreur est survenue :
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ============================================================
         INFORMATIONS PRINCIPALES
    ============================================================ --}}

    <div class="purchase-detail-grid">


        {{-- ========================================================
             INFORMATIONS ACHAT
    ========================================================= --}}

        <div class="module-card purchase-detail-card">

            <div class="purchase-detail-card-header">

                <div class="purchase-detail-icon">
                    🧾
                </div>

                <div>

                    <span>
                        Référence
                    </span>

                    <strong>
                        {{ $purchase->reference }}
                    </strong>

                </div>

            </div>


            <div class="purchase-detail-info-list">

                <div>

                    <span>
                        Date d'achat
                    </span>

                    <strong>
                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                    </strong>

                </div>


                <div>

                    <span>
                        Enregistré le
                    </span>

                    <strong>
                        {{ $purchase->created_at->format('d/m/Y à H:i') }}
                    </strong>

                </div>


                <div>

                    <span>
                        Responsable
                    </span>

                    <strong>
                        {{ $purchase->user?->name ?? '—' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Statut
                    </span>

                    @if($purchase->status === 'completed')

                        <span class="module-badge module-badge-success">
                            Terminée
                        </span>

                    @else

                        <span class="module-badge module-badge-danger">
                            Annulée
                        </span>

                    @endif

                </div>

            </div>

        </div>


        {{-- ========================================================
             FOURNISSEUR
        ========================================================= --}}

        <div class="module-card purchase-detail-card">

            <div class="purchase-detail-card-header">

                <div class="purchase-detail-icon">
                    🏢
                </div>

                <div>

                    <span>
                        Fournisseur
                    </span>

                    <strong>
                        {{ $purchase->supplier?->name ?? '—' }}
                    </strong>

                </div>

            </div>


            <div class="purchase-detail-info-list">

                @if($purchase->supplier)

                    <div>

                        <span>
                            Téléphone
                        </span>

                        <strong>
                            {{ $purchase->supplier->phone ?? '—' }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Email
                        </span>

                        <strong>
                            {{ $purchase->supplier->email ?? '—' }}
                        </strong>

                    </div>

                @else

                    <div>

                        <span>
                            Fournisseur
                        </span>

                        <strong>
                            —
                        </strong>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- ============================================================
         VARIABLES FINANCIÈRES
    ============================================================ --}}

    @php

        $total = (float) $purchase->total;

        $paid = (float) $purchase->amount_paid;

        $due = max(
            0,
            (float) $purchase->amount_due
        );

        $paidPercentage = $total > 0
            ? min(
                100,
                max(
                    0,
                    ($paid / $total) * 100
                )
            )
            : 0;

        $currency = $purchase->company?->currency_symbol ?? 'FCFA';

        $canAddPayment =
            $purchase->status === 'completed'
            &&
            $due > 0.001;

        $isFullyPaid =
            $purchase->status === 'completed'
            &&
            $due <= 0.001;

    @endphp


    {{-- ============================================================
         RÉSUMÉ FINANCIER
    ============================================================ --}}

    <div class="purchase-payment-summary">


        {{-- TOTAL --}}

        <div class="purchase-payment-summary-card">

            <div class="purchase-payment-summary-icon">
                💰
            </div>

            <div>

                <span>
                    Total de l'achat
                </span>

                <strong>

                    {{ number_format(
                        $total,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $currency }}

                </strong>

            </div>

        </div>


        {{-- PAYÉ --}}

        <div class="purchase-payment-summary-card purchase-paid-card">

            <div class="purchase-payment-summary-icon">
                ✓
            </div>

            <div>

                <span>
                    Total payé
                </span>

                <strong>

                    {{ number_format(
                        $paid,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $currency }}

                </strong>

            </div>

        </div>


        {{-- RESTANT --}}

        <div class="purchase-payment-summary-card purchase-remaining-card">

            <div class="purchase-payment-summary-icon">
                ⏳
            </div>

            <div>

                <span>
                    Reste à payer
                </span>

                <strong>

                    {{ number_format(
                        $due,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $currency }}

                </strong>

            </div>

        </div>


        {{-- STATUT PAIEMENT --}}

        <div class="purchase-payment-summary-card">

            <div class="purchase-payment-summary-icon">
                💳
            </div>

            <div>

                <span>
                    Statut paiement
                </span>

                @if($purchase->payment_status === 'paid')

                    <strong class="purchase-status-paid">
                        Payé
                    </strong>

                @elseif($purchase->payment_status === 'partial')

                    <strong class="purchase-status-partial">
                        Partiellement payé
                    </strong>

                @else

                    <strong class="purchase-status-unpaid">
                        Impayé
                    </strong>

                @endif

            </div>

        </div>

    </div>


    {{-- ============================================================
         PROGRESSION DU PAIEMENT
    ============================================================ --}}

    <div class="module-card purchase-payment-progress-card">

        <div class="purchase-payment-progress-header">

            <div>

                <strong>
                    Progression du paiement
                </strong>

                <span>
                    {{ number_format($paidPercentage, 0) }} % payé
                </span>

            </div>

            <strong>

                {{ number_format(
                    $paid,
                    0,
                    ',',
                    ' '
                ) }}

                /

                {{ number_format(
                    $total,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $currency }}

            </strong>

        </div>


        <div class="purchase-payment-progress">

            <div
                class="purchase-payment-progress-bar"
                style="width: {{ $paidPercentage }}%;"
            ></div>

        </div>

    </div>


    {{-- ============================================================
         PRODUITS ACHETÉS
    ============================================================ --}}

    <div class="module-card purchase-section">

        <div class="purchase-section-header">

            <div>

                <h2>
                    Produits achetés
                </h2>

                <p>
                    Produits et quantités inclus dans cet achat.
                </p>

            </div>

        </div>


        <div class="module-table-wrapper">

            <table class="module-table">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Produit
                        </th>

                        <th>
                            Référence
                        </th>

                        <th>
                            Prix unitaire
                        </th>

                        <th>
                            Quantité
                        </th>

                        <th>
                            Remise
                        </th>

                        <th>
                            Total
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($purchase->items as $index => $item)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>

                                <strong>
                                    {{ $item->product?->name ?? 'Produit supprimé' }}
                                </strong>

                            </td>


                            <td>

                                {{ $item->product?->reference ?? '—' }}

                            </td>


                            <td>

                                {{ number_format(
                                    $item->unit_price,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                {{ $currency }}

                            </td>


                            <td>

                                <strong>
                                    {{ $item->quantity }}
                                </strong>

                            </td>


                            <td>

                                @if((float) $item->discount > 0)

                                    -
                                    {{ number_format(
                                        $item->discount,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    {{ $currency }}

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                <strong>

                                    {{ number_format(
                                        $item->total,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    {{ $currency }}

                                </strong>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7">

                                <div class="module-empty">

                                    <strong>
                                        Aucun produit
                                    </strong>

                                    <p>
                                        Cet achat ne contient aucun produit.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ============================================================
         PAIEMENTS
    ============================================================ --}}

    <div class="module-card purchase-section">

        <div class="purchase-section-header">

            <div>

                <h2>
                    Paiements
                </h2>

                <p>
                    Historique des règlements effectués auprès du fournisseur.
                </p>

            </div>


            @if($due > 0.001 && $purchase->status === 'completed')

                <span class="module-badge">

                    Reste :

                    {{ number_format(
                        $due,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $currency }}

                </span>

            @elseif($isFullyPaid)

                <span class="module-badge module-badge-success">
                    ✓ Totalement payé
                </span>

            @endif

        </div>


        @if($purchase->payments->count())

            <div class="module-table-wrapper">

                <table class="module-table">

                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                Méthode
                            </th>

                            <th>
                                Opérateur
                            </th>

                            <th>
                                Montant
                            </th>

                            <th>
                                Référence
                            </th>

                            <th>
                                Enregistré par
                            </th>

                            <th>
                                Statut
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($purchase->payments as $payment)

                            <tr>

                                {{-- DATE --}}

                                <td>

                                    <strong>
                                        {{ $payment->created_at->format('d/m/Y') }}
                                    </strong>

                                    <small>
                                        {{ $payment->created_at->format('H:i') }}
                                    </small>

                                </td>


                                {{-- MÉTHODE --}}

                                <td>

                                    <strong>
                                        {{ $payment->method_label }}
                                    </strong>

                                    @if($payment->phone)

                                        <small>
                                            {{ $payment->phone }}
                                        </small>

                                    @endif

                                </td>


                                {{-- OPÉRATEUR --}}

                                <td>

                                    {{ $payment->provider_label ?? '—' }}

                                </td>


                                {{-- MONTANT --}}

                                <td>

                                    <strong>

                                        {{ number_format(
                                            $payment->amount,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        {{ $currency }}

                                    </strong>

                                </td>


                                {{-- RÉFÉRENCE --}}

                                <td>

                                    {{ $payment->transaction_reference ?? '—' }}

                                </td>


                                {{-- UTILISATEUR --}}

                                <td>

                                    {{ $payment->user?->name ?? '—' }}

                                </td>


                                {{-- STATUT --}}

                                <td>

                                    @if($payment->status === 'completed')

                                        <span class="module-badge module-badge-success">
                                            Terminé
                                        </span>

                                    @elseif($payment->status === 'pending')

                                        <span class="module-badge">
                                            En attente
                                        </span>

                                    @else

                                        <span class="module-badge module-badge-danger">
                                            Annulé
                                        </span>

                                    @endif

                                </td>


                                {{-- ACTION --}}

                                <td>

                                    @if($payment->status !== 'cancelled')

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'company.purchases.payments.destroy',
                                                $payment
                                            ) }}"
                                            onsubmit="return confirm(
                                                'Voulez-vous vraiment annuler ce paiement ?'
                                            );"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="module-action module-action-danger"
                                            >
                                                Annuler
                                            </button>

                                        </form>

                                    @else

                                        <span>
                                            —
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="module-empty">

                <strong>
                    Aucun paiement
                </strong>

                <p>
                    Aucun règlement n'a encore été enregistré pour cet achat.
                </p>

            </div>

        @endif

    </div>


    {{-- ============================================================
         AJOUTER UN PAIEMENT
    ============================================================ --}}

    @if($canAddPayment)

        <div class="module-card purchase-section">

            <div class="purchase-section-header">

                <div>

                    <h2>
                        Enregistrer un paiement
                    </h2>

                    <p>
                        Ajoutez un nouveau règlement pour cet achat.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'company.purchases.payments.store',
                    $purchase
                ) }}"
                id="purchase-payment-form"
            >

                @csrf


                {{-- =================================================
                     INFORMATIONS PAIEMENT
                ================================================== --}}

                <div class="module-form-grid">


                    {{-- MONTANT --}}

                    <div class="module-form-group">

                        <label for="amount">
                            Montant *
                        </label>

                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            min="0.01"
                            max="{{ $due }}"
                            step="0.01"
                            required
                            value="{{ old('amount') }}"
                            placeholder="0"
                        >

                        <small>

                            Maximum :

                            {{ number_format(
                                $due,
                                0,
                                ',',
                                ' '
                            ) }}

                            {{ $currency }}

                        </small>

                        @error('amount')

                            <span class="module-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- MÉTHODE --}}

                    <div class="module-form-group">

                        <label for="method">
                            Méthode de paiement *
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
                                value="cash"
                                @selected(old('method') === 'cash')
                            >
                                💵 Espèces
                            </option>

                            <option
                                value="mobile_money"
                                @selected(old('method') === 'mobile_money')
                            >
                                📱 Mobile Money
                            </option>

                            <option
                                value="card"
                                @selected(old('method') === 'card')
                            >
                                💳 Carte bancaire
                            </option>

                            <option
                                value="bank_transfer"
                                @selected(old('method') === 'bank_transfer')
                            >
                                🏦 Virement bancaire
                            </option>

                            <option
                                value="other"
                                @selected(old('method') === 'other')
                            >
                                Autre
                            </option>

                        </select>

                        @error('method')

                            <span class="module-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


                {{-- =================================================
                     MOBILE MONEY
                ================================================== --}}

                <div
                    id="mobileMoneyFields"
                    class="mobile-money-fields"
                    style="display:none;"
                >

                    <div class="mobile-money-header">

                        <div>

                            <strong>
                                📱 Paiement Mobile Money
                            </strong>

                            <p>
                                Informations nécessaires pour le règlement.
                            </p>

                        </div>

                    </div>


                    <div class="module-form-grid">


                        {{-- OPÉRATEUR --}}

                        <div class="module-form-group">

                            <label for="provider">
                                Opérateur *
                            </label>

                            <select
                                name="provider"
                                id="provider"
                            >

                                <option value="">
                                    Sélectionner
                                </option>


                                @if($settings?->orange_enabled)

                                    <option
                                        value="orange_money"
                                        @selected(
                                            old('provider') === 'orange_money'
                                        )
                                    >
                                        Orange Money
                                    </option>

                                @endif


                                @if($settings?->mtn_enabled)

                                    <option
                                        value="mtn_momo"
                                        @selected(
                                            old('provider') === 'mtn_momo'
                                        )
                                    >
                                        MTN Mobile Money
                                    </option>

                                @endif

                            </select>

                            @error('provider')

                                <span class="module-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        {{-- TÉLÉPHONE --}}

                        <div class="module-form-group">

                            <label for="phone">
                                Numéro de téléphone *
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old('phone') }}"
                                placeholder="6XXXXXXXX"
                            >

                            @error('phone')

                                <span class="module-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        {{-- RÉFÉRENCE --}}

                        <div class="module-form-group">

                            <label for="transaction_reference">
                                Référence transaction
                            </label>

                            <input
                                type="text"
                                name="transaction_reference"
                                id="transaction_reference"
                                value="{{ old('transaction_reference') }}"
                                placeholder="Référence opérateur"
                            >

                            @error('transaction_reference')

                                <span class="module-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     NOTE
                ================================================== --}}

                <div class="module-form-group">

                    <label for="note">
                        Note
                    </label>

                    <textarea
                        name="note"
                        id="note"
                        rows="3"
                        placeholder="Informations complémentaires..."
                    >{{ old('note') }}</textarea>

                    @error('note')

                        <span class="module-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- =================================================
                     ACTION
                ================================================== --}}

                <div class="purchase-form-actions">

                    <button
                        type="submit"
                        class="module-btn module-btn-primary"
                    >
                        ✓ Enregistrer le paiement
                    </button>

                </div>

            </form>

        </div>


    @elseif($isFullyPaid)

        {{-- ========================================================
             ACHAT TOTALLEMENT PAYÉ
        ========================================================= --}}

        <div class="module-card purchase-payment-complete-card">

            <div class="purchase-payment-complete-icon">
                ✓
            </div>

            <div>

                <strong>
                    Achat totalement payé
                </strong>

                <p>
                    Aucun paiement supplémentaire n'est nécessaire.
                    Le montant total de
                    <strong>
                        {{ number_format(
                            $total,
                            0,
                            ',',
                            ' '
                        ) }}
                        {{ $currency }}
                    </strong>
                    a déjà été réglé.
                </p>

            </div>

        </div>

    @endif


    {{-- ============================================================
         NOTES
    ============================================================ --}}

    @if($purchase->notes)

        <div class="module-card purchase-section">

            <div class="purchase-section-header">

                <div>

                    <h2>
                        Notes
                    </h2>

                    <p>
                        Informations complémentaires concernant cet achat.
                    </p>

                </div>

            </div>


            <div class="purchase-notes">

                {!! nl2br(e($purchase->notes)) !!}

            </div>

        </div>

    @endif


    {{-- ============================================================
         RÉSUMÉ FINAL
    ============================================================ --}}

    <div class="module-card purchase-final-summary">

        <div class="purchase-final-row">

            <span>
                Sous-total
            </span>

            <strong>

                {{ number_format(
                    $purchase->subtotal,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $currency }}

            </strong>

        </div>


        <div class="purchase-final-row">

            <span>
                Remise
            </span>

            <strong>

                -

                {{ number_format(
                    $purchase->discount,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $currency }}

            </strong>

        </div>


        <div class="purchase-final-row">

            <span>
                Taxe
            </span>

            <strong>

                +

                {{ number_format(
                    $purchase->tax,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $currency }}

            </strong>

        </div>


        <div class="purchase-final-row purchase-final-total">

            <span>
                TOTAL
            </span>

            <strong>

                {{ number_format(
                    $purchase->total,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $currency }}

            </strong>

        </div>


        <div class="purchase-final-row purchase-final-paid">

            <span>
                PAYÉ
            </span>

            <strong>

                {{ number_format(
                    $paid,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $currency }}

            </strong>

        </div>


        <div class="purchase-final-row purchase-final-remaining">

            <span>
                RESTE À PAYER
            </span>

            <strong>

                {{ number_format(
                    $due,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $currency }}

            </strong>

        </div>

    </div>


    {{-- ============================================================
         ANNULATION
    ============================================================ --}}

    @canPermission('purchases.cancel')

        @if($purchase->status !== 'cancelled')

            <div class="module-card purchase-cancel-card">

                <div>

                    <h2>
                        Annuler cet achat
                    </h2>

                    <p>
                        L'annulation inverse l'entrée de stock générée par cet achat.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route(
                        'company.purchases.destroy',
                        $purchase
                    ) }}"
                    onsubmit="return confirm(
                        'Voulez-vous vraiment annuler cet achat ? Le stock sera également ajusté.'
                    );"
                >

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="module-btn module-btn-danger"
                    >
                        Annuler l'achat
                    </button>

                </form>

            </div>

        @else

            <div class="module-card purchase-cancelled-card">

                <strong>
                    ⚠️ Achat annulé
                </strong>

                <p>
                    Cet achat a été annulé et son entrée de stock a été inversée.
                </p>

            </div>

        @endif

    @endcanPermission

</div>


{{-- ================================================================
     JAVASCRIPT
================================================================ --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const method =
        document.getElementById('method');

    const mobileMoneyFields =
        document.getElementById('mobileMoneyFields');

    const provider =
        document.getElementById('provider');

    const phone =
        document.getElementById('phone');


    if (!method || !mobileMoneyFields) {
        return;
    }


    function updateMobileMoney()
    {

        if (method.value === 'mobile_money') {

            mobileMoneyFields.style.display = 'block';


            if (provider) {
                provider.required = true;
            }


            if (phone) {
                phone.required = true;
            }

        } else {

            mobileMoneyFields.style.display = 'none';


            if (provider) {
                provider.required = false;
            }


            if (phone) {
                phone.required = false;
            }

        }

    }


    method.addEventListener(
        'change',
        updateMobileMoney
    );


    updateMobileMoney();

});

</script>

@endsection