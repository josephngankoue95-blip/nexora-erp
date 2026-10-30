@extends('layouts.company')

@section('title', 'Nouvelle vente | Gestion')

@section('content')

<div class="module-page">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                VENTES
            </span>

            <h1 class="module-title">
                Nouvelle vente
            </h1>

            <p class="module-description">
                Enregistrez une vente contenant des produits et/ou des prestations.
            </p>

        </div>

        <a
            href="{{ route('company.sales.index') }}"
            class="module-btn module-btn-secondary"
        >
            ← Retour
        </a>

    </div>


    @if($errors->any())

        <div class="module-alert module-alert-danger">

            <strong>
                Veuillez corriger les erreurs suivantes :
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


    <form
        method="POST"
        action="{{ route('company.sales.store') }}"
        id="sale-form"
    >

        @csrf


        {{-- ============================================================
             INFORMATIONS VENTE
        ============================================================ --}}

        <div class="module-card sale-section">

            <div class="sale-section-header">

                <div>

                    <h2>
                        Informations de la vente
                    </h2>

                    <p>
                        Sélectionnez le client et ajoutez les articles vendus.
                    </p>

                </div>

            </div>


            <div class="sale-top-grid">

                <div class="module-field">

                    <label for="client_id">
                        Client
                    </label>

                    <select
                        name="client_id"
                        id="client_id"
                    >

                        <option value="">
                            Client comptoir / aucun client
                        </option>

                        @foreach($clients as $client)

                            <option
                                value="{{ $client->id }}"
                                @selected(old('client_id') == $client->id)
                            >

                                {{ $client->name }}

                                @if($client->phone)
                                    — {{ $client->phone }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="sale-info-box">

                    <span>
                        Date
                    </span>

                    <strong>
                        {{ now()->format('d/m/Y H:i') }}
                    </strong>

                </div>

            </div>

        </div>


        {{-- ============================================================
             ARTICLES
        ============================================================ --}}

        <div class="module-card sale-section">

            <div class="sale-section-header">

                <div>

                    <h2>
                        Articles de la vente
                    </h2>

                    <p>
                        Ajoutez des produits ou des prestations.
                    </p>

                </div>

            </div>


            <div class="sale-product-selector">

                <div class="module-form-grid">


                    {{-- TYPE --}}

                    <div class="module-form-group">

                        <label for="item-type-selector">
                            Type
                        </label>

                        <select id="item-type-selector">

                            <option value="product">
                                📦 Produit
                            </option>

                            <option value="service">
                                🔧 Prestation
                            </option>

                        </select>

                    </div>


                    {{-- PRODUIT --}}

                    <div
                        class="module-form-group"
                        id="product-selector-wrapper"
                    >

                        <label for="product-selector">
                            Produit
                        </label>

                        <select id="product-selector">

                            <option value="">
                                Sélectionner un produit
                            </option>

                            @foreach($products as $product)

                                <option
                                    value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->selling_price }}"
                                    data-stock="{{ $product->stock_quantity }}"
                                    data-reference="{{ $product->reference }}"
                                >

                                    {{ $product->name }}

                                    —

                                    {{ number_format(
                                        $product->selling_price,
                                        0,
                                        ',',
                                        ' '
                                    ) }}
                                    FCFA

                                    —

                                    Stock :
                                    {{ $product->stock_quantity }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- SERVICE --}}

                    <div
                        class="module-form-group"
                        id="service-selector-wrapper"
                        style="display:none;"
                    >

                        <label for="service-selector">
                            Prestation
                        </label>

                        <select id="service-selector">

                            <option value="">
                                Sélectionner une prestation
                            </option>

                            @foreach($services as $service)

                                <option
                                    value="{{ $service->id }}"
                                    data-name="{{ $service->name }}"
                                    data-price="{{ $service->price }}"
                                    data-reference="{{ $service->reference }}"
                                >

                                    {{ $service->name }}

                                    —

                                    {{ number_format(
                                        $service->price,
                                        0,
                                        ',',
                                        ' '
                                    ) }}
                                    FCFA

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- AJOUT --}}

                    <div class="module-form-group">

                        <label>
                            Action
                        </label>

                        <button
                            type="button"
                            class="module-btn module-btn-primary"
                            id="add-item-btn"
                        >
                            + Ajouter
                        </button>

                    </div>

                </div>

            </div>


            <div class="sale-table-wrapper">

                <table class="module-table sale-table">

                    <thead>

                        <tr>

                            <th>
                                Type
                            </th>

                            <th>
                                Désignation
                            </th>

                            <th>
                                Stock
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

                            <th></th>

                        </tr>

                    </thead>


                    <tbody id="sale-items">

                        <tr id="empty-sale-row">

                            <td colspan="8">

                                <div class="module-empty">

                                    <strong>
                                        Aucun article ajouté
                                    </strong>

                                    <p>
                                        Ajoutez un produit ou une prestation.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ============================================================
             NOTES + RÉSUMÉ
        ============================================================ --}}

        <div class="sale-bottom-grid">


            <div class="module-card sale-section">

                <div class="sale-section-header">

                    <div>

                        <h2>
                            Notes
                        </h2>

                    </div>

                </div>


                <div class="module-field">

                    <label for="notes">
                        Note de vente
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        rows="6"
                        placeholder="Informations complémentaires..."
                    >{{ old('notes') }}</textarea>

                </div>

            </div>


            <div class="module-card sale-summary">

                <h2>
                    Résumé
                </h2>


                <div class="sale-summary-row">

                    <span>
                        Sous-total
                    </span>

                    <strong id="subtotal-display">
                        0 FCFA
                    </strong>

                </div>


                <div class="sale-summary-row sale-input-row">

                    <label for="discount">
                        Remise
                    </label>

                    <div class="sale-money-input">

                        <input
                            type="number"
                            name="discount"
                            id="discount"
                            min="0"
                            step="0.01"
                            value="{{ old('discount', 0) }}"
                        >

                        <span>
                            FCFA
                        </span>

                    </div>

                </div>


                <div class="sale-summary-row sale-input-row">

                    <label for="tax">
                        Taxe
                    </label>

                    <div class="sale-money-input">

                        <input
                            type="number"
                            name="tax"
                            id="tax"
                            min="0"
                            step="0.01"
                            value="{{ old('tax', 0) }}"
                        >

                        <span>
                            FCFA
                        </span>

                    </div>

                </div>


                <div class="sale-total">

                    <span>
                        TOTAL
                    </span>

                    <strong id="total-display">
                        0 FCFA
                    </strong>

                </div>

            </div>

        </div>


        {{-- ============================================================
             PAIEMENT INITIAL
        ============================================================ --}}

        <div class="module-card sale-section sale-initial-payment-card">

            <div class="sale-section-header">

                <div>

                    <h2>
                        Paiement initial
                    </h2>

                    <p>
                        Enregistrez éventuellement un premier règlement.
                    </p>

                </div>

            </div>


            <div class="sale-payment-summary-create">

                <div>

                    <span>
                        Total
                    </span>

                    <strong id="payment-total-display">
                        0 FCFA
                    </strong>

                </div>


                <div>

                    <span>
                        Montant payé
                    </span>

                    <strong id="payment-paid-display">
                        0 FCFA
                    </strong>

                </div>


                <div>

                    <span>
                        Reste à payer
                    </span>

                    <strong id="payment-remaining-display">
                        0 FCFA
                    </strong>

                </div>

            </div>


            <div class="module-form-grid">


                <div class="module-form-group">

                    <label for="initial_payment_amount">
                        Montant payé
                    </label>

                    <input
                        type="number"
                        name="initial_payment_amount"
                        id="initial_payment_amount"
                        min="0"
                        step="0.01"
                        value="{{ old('initial_payment_amount', 0) }}"
                        placeholder="0"
                    >

                    <small>
                        Laissez 0 si le client ne paie rien maintenant.
                    </small>

                    @error('initial_payment_amount')

                        <span class="module-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                <div class="module-form-group">

                    <label for="initial_payment_method">
                        Méthode de paiement
                    </label>

                    <select
                        name="initial_payment_method"
                        id="initial_payment_method"
                    >

                        <option value="">
                            Aucun paiement
                        </option>

                        <option
                            value="cash"
                            @selected(old('initial_payment_method') === 'cash')
                        >
                            💵 Espèces
                        </option>

                        <option
                            value="mobile_money"
                            @selected(old('initial_payment_method') === 'mobile_money')
                        >
                            📱 Mobile Money
                        </option>

                        <option
                            value="card"
                            @selected(old('initial_payment_method') === 'card')
                        >
                            💳 Carte bancaire
                        </option>

                        <option
                            value="bank_transfer"
                            @selected(old('initial_payment_method') === 'bank_transfer')
                        >
                            🏦 Virement bancaire
                        </option>

                        <option
                            value="other"
                            @selected(old('initial_payment_method') === 'other')
                        >
                            Autre
                        </option>

                    </select>

                </div>

            </div>


            <div
                id="initialMobileMoneyFields"
                class="mobile-money-fields"
                style="display:none;"
            >

                <div class="mobile-money-header">

                    <div>

                        <strong>
                            Paiement Mobile Money
                        </strong>

                        <p>
                            Sélectionnez l'opérateur et renseignez les informations.
                        </p>

                    </div>

                </div>


                <div class="module-form-grid">


                    <div class="module-form-group">

                        <label for="initial_payment_provider">
                            Opérateur *
                        </label>

                        <select
                            name="initial_payment_provider"
                            id="initial_payment_provider"
                        >

                            <option value="">
                                Sélectionner l'opérateur
                            </option>

                            <option
                                value="orange_money"
                                @selected(old('initial_payment_provider') === 'orange_money')
                            >
                                Orange Money
                            </option>

                            <option
                                value="mtn_momo"
                                @selected(old('initial_payment_provider') === 'mtn_momo')
                            >
                                MTN Mobile Money
                            </option>

                        </select>

                    </div>


                    <div class="module-form-group">

                        <label for="initial_payment_phone">
                            Numéro Mobile Money *
                        </label>

                        <input
                            type="text"
                            id="initial_payment_phone"
                            name="initial_payment_phone"
                            value="{{ old('initial_payment_phone') }}"
                            placeholder="Ex : 6XXXXXXXX"
                        >

                    </div>


                    <div class="module-form-group">

                        <label for="initial_payment_transaction_reference">
                            Référence transaction
                        </label>

                        <input
                            type="text"
                            id="initial_payment_transaction_reference"
                            name="initial_payment_transaction_reference"
                            value="{{ old('initial_payment_transaction_reference') }}"
                            placeholder="Référence opérateur"
                        >

                    </div>

                </div>

            </div>


            <div class="module-form-group">

                <label for="initial_payment_note">
                    Note du paiement
                </label>

                <textarea
                    id="initial_payment_note"
                    name="initial_payment_note"
                    rows="3"
                    placeholder="Informations complémentaires..."
                >{{ old('initial_payment_note') }}</textarea>

            </div>

        </div>


        {{-- ============================================================
             ACTIONS
        ============================================================ --}}

        <div class="sale-create-actions">

            <a
                href="{{ route('company.sales.index') }}"
                class="module-btn module-btn-secondary"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="module-btn module-btn-primary sale-submit-btn"
            >
                ✓ Valider la vente
            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const typeSelector =
        document.getElementById('item-type-selector');

    const productWrapper =
        document.getElementById('product-selector-wrapper');

    const serviceWrapper =
        document.getElementById('service-selector-wrapper');

    const productSelector =
        document.getElementById('product-selector');

    const serviceSelector =
        document.getElementById('service-selector');

    const addItemBtn =
        document.getElementById('add-item-btn');

    const saleItems =
        document.getElementById('sale-items');

    const discountInput =
        document.getElementById('discount');

    const taxInput =
        document.getElementById('tax');

    const subtotalDisplay =
        document.getElementById('subtotal-display');

    const totalDisplay =
        document.getElementById('total-display');

    const initialPaymentAmount =
        document.getElementById('initial_payment_amount');

    const initialPaymentMethod =
        document.getElementById('initial_payment_method');

    const mobileMoneyFields =
        document.getElementById('initialMobileMoneyFields');

    const paymentProvider =
        document.getElementById('initial_payment_provider');

    const paymentPhone =
        document.getElementById('initial_payment_phone');

    const paymentTotalDisplay =
        document.getElementById('payment-total-display');

    const paymentPaidDisplay =
        document.getElementById('payment-paid-display');

    const paymentRemainingDisplay =
        document.getElementById('payment-remaining-display');

    let itemIndex = 0;


    function formatMoney(value)
    {
        return new Intl.NumberFormat('fr-FR', {
            maximumFractionDigits: 0
        }).format(Math.max(0, value)) + ' FCFA';
    }


    function updateTypeSelector()
    {
        if (typeSelector.value === 'service') {

            productWrapper.style.display = 'none';

            serviceWrapper.style.display = 'block';

            productSelector.value = '';

        } else {

            productWrapper.style.display = 'block';

            serviceWrapper.style.display = 'none';

            serviceSelector.value = '';
        }
    }


    function calculateTotals()
    {
        let subtotal = 0;

        document.querySelectorAll('.sale-item-row')
            .forEach(function (row) {

                const quantityInput =
                    row.querySelector('.sale-quantity');

                const priceInput =
                    row.querySelector('.sale-price');

                const discountInput =
                    row.querySelector('.sale-line-discount');

                const lineTotal =
                    row.querySelector('.sale-line-total');

                const quantity =
                    parseInt(quantityInput.value) || 0;

                const price =
                    parseFloat(priceInput.value) || 0;

                const lineDiscount =
                    parseFloat(discountInput.value) || 0;

                const lineSubtotal =
                    quantity * price;

                const total =
                    Math.max(
                        0,
                        lineSubtotal - lineDiscount
                    );

                lineTotal.textContent =
                    formatMoney(total);

                subtotal += total;
            });


        let discount =
            parseFloat(discountInput.value) || 0;

        const tax =
            parseFloat(taxInput.value) || 0;

        if (discount > subtotal) {
            discount = subtotal;
        }

        const total =
            Math.max(
                0,
                subtotal - discount + tax
            );

        subtotalDisplay.textContent =
            formatMoney(subtotal);

        totalDisplay.textContent =
            formatMoney(total);

        updatePaymentSummary(total);
    }


    function updatePaymentSummary(total)
    {
        let paid =
            parseFloat(
                initialPaymentAmount.value
            ) || 0;

        if (paid < 0) {
            paid = 0;
        }

        if (paid > total) {

            paid = total;

            initialPaymentAmount.value =
                total;
        }

        const remaining =
            Math.max(
                0,
                total - paid
            );

        paymentTotalDisplay.textContent =
            formatMoney(total);

        paymentPaidDisplay.textContent =
            formatMoney(paid);

        paymentRemainingDisplay.textContent =
            formatMoney(remaining);
    }


    function itemAlreadyAdded(type, id)
    {
        let exists = false;

        document.querySelectorAll('.sale-item-row')
            .forEach(function (row) {

                const rowType =
                    row.querySelector('.sale-item-type').value;

                const rowId =
                    row.querySelector('.sale-item-reference-id').value;

                if (
                    rowType === type &&
                    rowId == id
                ) {
                    exists = true;
                }

            });

        return exists;
    }


    function addItem()
    {
        const type =
            typeSelector.value;

        let option = null;
        let id = null;
        let name = '';
        let price = 0;
        let stock = null;
        let reference = '';


        if (type === 'product') {

            option =
                productSelector.options[
                    productSelector.selectedIndex
                ];

            if (!productSelector.value) {

                alert(
                    'Veuillez sélectionner un produit.'
                );

                return;
            }

            id =
                productSelector.value;

            name =
                option.dataset.name;

            price =
                parseFloat(
                    option.dataset.price
                ) || 0;

            stock =
                parseInt(
                    option.dataset.stock
                ) || 0;

            reference =
                option.dataset.reference || '';

            if (stock <= 0) {

                alert(
                    'Ce produit est actuellement en rupture de stock.'
                );

                return;
            }

        } else {

            option =
                serviceSelector.options[
                    serviceSelector.selectedIndex
                ];

            if (!serviceSelector.value) {

                alert(
                    'Veuillez sélectionner une prestation.'
                );

                return;
            }

            id =
                serviceSelector.value;

            name =
                option.dataset.name;

            price =
                parseFloat(
                    option.dataset.price
                ) || 0;

            reference =
                option.dataset.reference || '';
        }


        if (itemAlreadyAdded(type, id)) {

            alert(
                'Cet article est déjà présent dans la vente.'
            );

            return;
        }


        const emptyRow =
            document.getElementById(
                'empty-sale-row'
            );

        if (emptyRow) {
            emptyRow.remove();
        }


        const row =
            document.createElement('tr');

        row.classList.add(
            'sale-item-row'
        );


        const typeLabel =
            type === 'product'
                ? '📦 Produit'
                : '🔧 Prestation';


        const stockHtml =
            type === 'product'
                ? `<span class="sale-stock">${stock}</span>`
                : `<span>—</span>`;


        const productInput =
            type === 'product'
                ? `
                    <input
                        type="hidden"
                        name="items[${itemIndex}][product_id]"
                        value="${id}"
                    >
                `
                : `
                    <input
                        type="hidden"
                        name="items[${itemIndex}][service_id]"
                        value="${id}"
                    >
                `;


        row.innerHTML = `

            <td>

                <strong>
                    ${typeLabel}
                </strong>

                <input
                    type="hidden"
                    name="items[${itemIndex}][type]"
                    value="${type}"
                    class="sale-item-type"
                >

                <input
                    type="hidden"
                    value="${id}"
                    class="sale-item-reference-id"
                >

                ${productInput}

            </td>


            <td>

                <strong>
                    ${name}
                </strong>

                ${
                    reference
                        ? `<small>Réf. ${reference}</small>`
                        : ''
                }

            </td>


            <td>

                ${stockHtml}

            </td>


            <td>

                <div class="sale-price-wrapper">

                    <input
                        type="number"
                        name="items[${itemIndex}][unit_price]"
                        value="${price}"
                        min="0"
                        step="0.01"
                        class="sale-price"
                    >

                    <span>
                        FCFA
                    </span>

                </div>

            </td>


            <td>

                <div class="sale-quantity-wrapper">

                    <button
                        type="button"
                        class="sale-quantity-btn decrease"
                    >
                        −
                    </button>

                    <input
                        type="number"
                        name="items[${itemIndex}][quantity]"
                        value="1"
                        min="1"
                        ${
                            type === 'product'
                                ? `max="${stock}"`
                                : ''
                        }
                        class="sale-quantity"
                    >

                    <button
                        type="button"
                        class="sale-quantity-btn increase"
                    >
                        +
                    </button>

                </div>

            </td>


            <td>

                <div class="sale-price-wrapper">

                    <input
                        type="number"
                        name="items[${itemIndex}][discount]"
                        value="0"
                        min="0"
                        step="0.01"
                        class="sale-line-discount"
                    >

                    <span>
                        FCFA
                    </span>

                </div>

            </td>


            <td>

                <strong class="sale-line-total">
                    ${formatMoney(price)}
                </strong>

            </td>


            <td>

                <button
                    type="button"
                    class="sale-remove-btn"
                    title="Supprimer"
                >
                    ×
                </button>

            </td>

        `;


        saleItems.appendChild(row);

        itemIndex++;

        productSelector.value = '';

        serviceSelector.value = '';

        calculateTotals();
    }


    saleItems.addEventListener(
        'click',
        function (event) {

            const row =
                event.target.closest(
                    '.sale-item-row'
                );

            if (!row) {
                return;
            }


            if (
                event.target.classList.contains(
                    'sale-remove-btn'
                )
            ) {

                row.remove();

                if (
                    document.querySelectorAll(
                        '.sale-item-row'
                    ).length === 0
                ) {

                    saleItems.innerHTML = `

                        <tr id="empty-sale-row">

                            <td colspan="8">

                                <div class="module-empty">

                                    <strong>
                                        Aucun article ajouté
                                    </strong>

                                    <p>
                                        Ajoutez un produit ou une prestation.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    `;
                }

                calculateTotals();

                return;
            }


            if (
                event.target.classList.contains(
                    'decrease'
                )
            ) {

                const input =
                    row.querySelector(
                        '.sale-quantity'
                    );

                const current =
                    parseInt(input.value) || 1;

                input.value =
                    Math.max(
                        1,
                        current - 1
                    );

                calculateTotals();

                return;
            }


            if (
                event.target.classList.contains(
                    'increase'
                )
            ) {

                const input =
                    row.querySelector(
                        '.sale-quantity'
                    );

                const current =
                    parseInt(input.value) || 1;

                const max =
                    parseInt(input.max);

                if (max) {

                    input.value =
                        Math.min(
                            max,
                            current + 1
                        );

                } else {

                    input.value =
                        current + 1;
                }

                calculateTotals();
            }
        }
    );


    saleItems.addEventListener(
        'input',
        function (event) {

            if (
                event.target.classList.contains(
                    'sale-quantity'
                ) ||
                event.target.classList.contains(
                    'sale-price'
                ) ||
                event.target.classList.contains(
                    'sale-line-discount'
                )
            ) {

                if (
                    event.target.classList.contains(
                        'sale-quantity'
                    )
                ) {

                    const max =
                        parseInt(
                            event.target.max
                        );

                    let value =
                        parseInt(
                            event.target.value
                        ) || 1;

                    value =
                        Math.max(
                            1,
                            value
                        );

                    if (max) {

                        value =
                            Math.min(
                                max,
                                value
                            );
                    }

                    event.target.value =
                        value;
                }

                calculateTotals();
            }
        }
    );


    discountInput.addEventListener(
        'input',
        calculateTotals
    );

    taxInput.addEventListener(
        'input',
        calculateTotals
    );


    initialPaymentAmount.addEventListener(
        'input',
        function () {

            calculateTotals();
        }
    );


    function updateInitialPaymentFields()
    {
        if (
            initialPaymentMethod.value ===
            'mobile_money'
        ) {

            mobileMoneyFields.style.display =
                'block';

            paymentProvider.required =
                true;

            paymentPhone.required =
                true;

        } else {

            mobileMoneyFields.style.display =
                'none';

            paymentProvider.required =
                false;

            paymentPhone.required =
                false;

            paymentProvider.value =
                '';

            paymentPhone.value =
                '';
        }
    }


    initialPaymentMethod.addEventListener(
        'change',
        updateInitialPaymentFields
    );


    typeSelector.addEventListener(
        'change',
        updateTypeSelector
    );


    addItemBtn.addEventListener(
        'click',
        addItem
    );


    document.getElementById('sale-form')
        .addEventListener(
            'submit',
            function (event) {

                const rows =
                    document.querySelectorAll(
                        '.sale-item-row'
                    );

                if (rows.length === 0) {

                    event.preventDefault();

                    alert(
                        'Veuillez ajouter au moins un produit ou une prestation.'
                    );

                    return;
                }


                const total =
                    calculateCurrentTotal();

                const paid =
                    parseFloat(
                        initialPaymentAmount.value
                    ) || 0;

                if (paid > total) {

                    event.preventDefault();

                    alert(
                        'Le montant payé ne peut pas être supérieur au total de la vente.'
                    );

                    return;
                }


                let valid = true;

                rows.forEach(function (row) {

                    const quantity =
                        parseInt(
                            row.querySelector(
                                '.sale-quantity'
                            ).value
                        ) || 0;

                    const max =
                        parseInt(
                            row.querySelector(
                                '.sale-quantity'
                            ).max
                        ) || 0;

                    if (
                        quantity < 1 ||
                        (max && quantity > max)
                    ) {
                        valid = false;
                    }

                });


                if (!valid) {

                    event.preventDefault();

                    alert(
                        'Vérifiez les quantités et le stock disponible.'
                    );
                }
            }
        );


    function calculateCurrentTotal()
    {
        let subtotal = 0;

        document.querySelectorAll('.sale-item-row')
            .forEach(function (row) {

                const quantity =
                    parseFloat(
                        row.querySelector(
                            '.sale-quantity'
                        ).value
                    ) || 0;

                const price =
                    parseFloat(
                        row.querySelector(
                            '.sale-price'
                        ).value
                    ) || 0;

                const lineDiscount =
                    parseFloat(
                        row.querySelector(
                            '.sale-line-discount'
                        ).value
                    ) || 0;

                subtotal +=
                    Math.max(
                        0,
                        quantity * price -
                        lineDiscount
                    );
            });


        const discount =
            Math.min(
                subtotal,
                parseFloat(
                    discountInput.value
                ) || 0
            );

        const tax =
            parseFloat(
                taxInput.value
            ) || 0;

        return Math.max(
            0,
            subtotal -
            discount +
            tax
        );
    }


    updateTypeSelector();

    updateInitialPaymentFields();

    calculateTotals();

});

</script>

@endsection