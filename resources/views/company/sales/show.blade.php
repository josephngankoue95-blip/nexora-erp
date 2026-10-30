@extends('layouts.company')

@section('title', 'Vente ' . $sale->reference)

@section('content')

<div class="module-page sale-show-page">

    <div class="module-header sale-show-header">

        <div>

            <div class="sale-breadcrumb">

                <a href="{{ route('company.sales.index') }}">
                    Ventes
                </a>

                <span>›</span>

                <span>
                    {{ $sale->reference }}
                </span>

            </div>

            <span class="module-kicker">
                DÉTAIL DE LA VENTE
            </span>

            <h1 class="module-title">
                {{ $sale->reference }}
            </h1>

            <p class="module-description">
                Consultez les produits, prestations, paiements et informations de cette vente.
            </p>

        </div>


        <div class="sale-show-actions">

            <a
                href="{{ route('company.sales.invoice', $sale) }}"
                target="_blank"
                class="module-btn module-btn-primary"
            >
                🧾 Facture
            </a>

            <a
                href="{{ route('company.sales.index') }}"
                class="module-btn module-btn-secondary"
            >
                ← Retour
            </a>

        </div>

    </div>


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

    <div class="sale-detail-grid">


        <div class="module-card sale-detail-card">

            <div class="sale-detail-card-header">

                <div class="sale-detail-icon">
                    🧾
                </div>

                <div>

                    <span>
                        Référence
                    </span>

                    <strong>
                        {{ $sale->reference }}
                    </strong>

                </div>

            </div>


            <div class="sale-detail-info-list">

                <div>

                    <span>
                        Date
                    </span>

                    <strong>
                        {{ $sale->created_at->format('d/m/Y à H:i') }}
                    </strong>

                </div>


                <div>

                    <span>
                        Vendeur
                    </span>

                    <strong>
                        {{ $sale->user?->name ?? '—' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Statut
                    </span>

                    @if($sale->status === 'completed')

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


        <div class="module-card sale-detail-card">

            <div class="sale-detail-card-header">

                <div class="sale-detail-icon">
                    👤
                </div>

                <div>

                    <span>
                        Client
                    </span>

                    <strong>
                        {{ $sale->client?->name ?? 'Client comptoir' }}
                    </strong>

                </div>

            </div>


            <div class="sale-detail-info-list">

                @if($sale->client)

                    <div>

                        <span>
                            Téléphone
                        </span>

                        <strong>
                            {{ $sale->client->phone ?? '—' }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Email
                        </span>

                        <strong>
                            {{ $sale->client->email ?? '—' }}
                        </strong>

                    </div>

                @else

                    <div>

                        <span>
                            Type
                        </span>

                        <strong>
                            Vente comptoir
                        </strong>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- ============================================================
         RÉSUMÉ FINANCIER
    ============================================================ --}}

    <div class="sale-payment-summary">


        <div class="sale-payment-summary-card">

            <div class="sale-payment-summary-icon">
                💰
            </div>

            <div>

                <span>
                    Total de la vente
                </span>

                <strong>

                    {{ number_format(
                        $sale->total,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $sale->company?->currency_symbol ?? 'FCFA' }}

                </strong>

            </div>

        </div>


        <div class="sale-payment-summary-card sale-paid-card">

            <div class="sale-payment-summary-icon">
                ✓
            </div>

            <div>

                <span>
                    Total payé
                </span>

                <strong>

                    {{ number_format(
                        $sale->paid_amount,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $sale->company?->currency_symbol ?? 'FCFA' }}

                </strong>

            </div>

        </div>


        <div class="sale-payment-summary-card sale-remaining-card">

            <div class="sale-payment-summary-icon">
                ⏳
            </div>

            <div>

                <span>
                    Reste à payer
                </span>

                <strong>

                    {{ number_format(
                        $sale->remaining_amount,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $sale->company?->currency_symbol ?? 'FCFA' }}

                </strong>

            </div>

        </div>


        <div class="sale-payment-summary-card">

            <div class="sale-payment-summary-icon">
                💳
            </div>

            <div>

                <span>
                    Statut paiement
                </span>

                @if($sale->payment_status === 'paid')

                    <strong class="sale-status-paid">
                        Payée
                    </strong>

                @elseif($sale->payment_status === 'partial')

                    <strong class="sale-status-partial">
                        Partiellement payée
                    </strong>

                @else

                    <strong class="sale-status-unpaid">
                        Impayée
                    </strong>

                @endif

            </div>

        </div>

    </div>


    {{-- ============================================================
         PROGRESSION PAIEMENT
    ============================================================ --}}

    <div class="module-card sale-payment-progress-card">

        <div class="sale-payment-progress-header">

            <div>

                <strong>
                    Progression du paiement
                </strong>

                <span>
                    {{ number_format($sale->paid_percentage, 0) }} % payé
                </span>

            </div>

            <strong>

                {{ number_format(
                    $sale->paid_amount,
                    0,
                    ',',
                    ' '
                ) }}

                /

                {{ number_format(
                    $sale->total,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

            </strong>

        </div>


        <div class="sale-payment-progress">

            <div
                class="sale-payment-progress-bar"
                style="width: {{ $sale->paid_percentage }}%;"
            ></div>

        </div>

    </div>


    {{-- ============================================================
         ARTICLES VENDUS
    ============================================================ --}}

    <div class="module-card sale-section">

        <div class="sale-section-header">

            <div>

                <h2>
                    Articles vendus
                </h2>

                <p>
                    Produits et prestations inclus dans cette vente.
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
                            Type
                        </th>

                        <th>
                            Désignation
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

                    @forelse($sale->items as $index => $item)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>

                                @if($item->type === 'service')

                                    <span class="module-badge">
                                        🔧 Prestation
                                    </span>

                                @else

                                    <span class="module-badge module-badge-success">
                                        📦 Produit
                                    </span>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ $item->item_name }}
                                </strong>

                                @if($item->item_reference)

                                    <small>
                                        Réf. {{ $item->item_reference }}
                                    </small>

                                @endif

                            </td>


                            <td>

                                {{ number_format(
                                    $item->unit_price,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

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

                                    {{ $sale->company?->currency_symbol ?? 'FCFA' }}

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

                                    {{ $sale->company?->currency_symbol ?? 'FCFA' }}

                                </strong>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7">

                                <div class="module-empty">

                                    <strong>
                                        Aucun article
                                    </strong>

                                    <p>
                                        Cette vente ne contient aucun article.
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

    <div class="module-card sale-section">

        <div class="sale-section-header">

            <div>

                <h2>
                    Paiements
                </h2>

                <p>
                    Historique des règlements associés à cette vente.
                </p>

            </div>

            @if(
                $sale->remaining_amount > 0 &&
                $sale->status === 'completed'
            )

                <span class="module-badge">

                    Reste :

                    {{ number_format(
                        $sale->remaining_amount,
                        0,
                        ',',
                        ' '
                    ) }}

                    {{ $sale->company?->currency_symbol ?? 'FCFA' }}

                </span>

            @endif

        </div>


        @if($sale->payments->count())

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

                        @foreach($sale->payments as $payment)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $payment->created_at->format('d/m/Y') }}
                                    </strong>

                                    <small>
                                        {{ $payment->created_at->format('H:i') }}
                                    </small>

                                </td>


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


                                <td>

                                    {{ $payment->provider_label ?? '—' }}

                                </td>


                                <td>

                                    <strong>

                                        {{ number_format(
                                            $payment->amount,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        {{ $sale->company?->currency_symbol ?? 'FCFA' }}

                                    </strong>

                                </td>


                                <td>

                                    {{ $payment->transaction_reference ?? '—' }}

                                </td>


                                <td>

                                    {{ $payment->user?->name ?? '—' }}

                                </td>


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


                                <td>

                                    @if($payment->status !== 'cancelled')

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'company.sales.payments.destroy',
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
                    Aucun règlement n'a encore été enregistré pour cette vente.
                </p>

            </div>

        @endif

    </div>


    {{-- ============================================================
         AJOUTER PAIEMENT
    ============================================================ --}}

    @if(
        $sale->status === 'completed'
        &&
        $sale->remaining_amount > 0
    )

        <div class="module-card sale-section">

            <div class="sale-section-header">

                <div>

                    <h2>
                        Enregistrer un paiement
                    </h2>

                    <p>
                        Ajoutez un nouveau règlement pour cette vente.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'company.sales.payments.store',
                    $sale
                ) }}"
                id="payment-form"
            >

                @csrf


                <div class="module-form-grid">


                    <div class="module-form-group">

                        <label for="amount">
                            Montant *
                        </label>

                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            min="0.01"
                            max="{{ $sale->remaining_amount }}"
                            step="0.01"
                            required
                            value="{{ old('amount') }}"
                            placeholder="0"
                        >

                        <small>

                            Maximum :

                            {{ number_format(
                                $sale->remaining_amount,
                                0,
                                ',',
                                ' '
                            ) }}

                            {{ $sale->company?->currency_symbol ?? 'FCFA' }}

                        </small>

                        @error('amount')

                            <span class="module-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


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

                    </div>

                </div>


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
                                Informations nécessaires pour le paiement.
                            </p>

                        </div>

                    </div>


                    <div class="module-form-grid">


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
                                        @selected(old('provider') === 'orange_money')
                                    >
                                        Orange Money
                                    </option>

                                @endif


                                @if($settings?->mtn_enabled)

                                    <option
                                        value="mtn_momo"
                                        @selected(old('provider') === 'mtn_momo')
                                    >
                                        MTN Mobile Money
                                    </option>

                                @endif

                            </select>

                        </div>


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

                        </div>


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

                        </div>

                    </div>

                </div>


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

                </div>


                <div class="sale-form-actions">

                    <button
                        type="submit"
                        class="module-btn module-btn-primary"
                    >
                        ✓ Enregistrer le paiement
                    </button>

                </div>

            </form>

        </div>

    @endif


    {{-- ============================================================
         NOTES
    ============================================================ --}}

    @if($sale->notes)

        <div class="module-card sale-section">

            <div class="sale-section-header">

                <div>

                    <h2>
                        Notes
                    </h2>

                </div>

            </div>


            <div class="sale-notes">

                {!! nl2br(e($sale->notes)) !!}

            </div>

        </div>

    @endif


    {{-- ============================================================
         RÉSUMÉ FINAL
    ============================================================ --}}

    <div class="module-card sale-final-summary">

        <div class="sale-final-row">

            <span>
                Sous-total
            </span>

            <strong>

                {{ number_format(
                    $sale->subtotal,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

            </strong>

        </div>


        <div class="sale-final-row">

            <span>
                Remise
            </span>

            <strong>

                -

                {{ number_format(
                    $sale->discount,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

            </strong>

        </div>


        <div class="sale-final-row">

            <span>
                Taxe
            </span>

            <strong>

                +

                {{ number_format(
                    $sale->tax,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

            </strong>

        </div>


        <div class="sale-final-row sale-final-total">

            <span>
                TOTAL
            </span>

            <strong>

                {{ number_format(
                    $sale->total,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

            </strong>

        </div>


        <div class="sale-final-row sale-final-paid">

            <span>
                PAYÉ
            </span>

            <strong>

                {{ number_format(
                    $sale->paid_amount,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

            </strong>

        </div>


        <div class="sale-final-row sale-final-remaining">

            <span>
                RESTE À PAYER
            </span>

            <strong>

                {{ number_format(
                    $sale->remaining_amount,
                    0,
                    ',',
                    ' '
                ) }}

                {{ $sale->company?->currency_symbol ?? 'FCFA' }}

            </strong>

        </div>

    </div>

</div>


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


    if (!method) {
        return;
    }


    function updateMobileMoney()
    {
        if (
            method.value ===
            'mobile_money'
        ) {

            mobileMoneyFields.style.display =
                'block';

            if (provider) {
                provider.required = true;
            }

            if (phone) {
                phone.required = true;
            }

        } else {

            mobileMoneyFields.style.display =
                'none';

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