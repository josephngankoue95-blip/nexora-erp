@extends('layouts.company')

@section('title', 'Nouvel achat | Gestion')

@section('content')

<div class="module-page purchase-page">

    {{-- HEADER --}}
    <div class="module-header">

        <div>

            <span class="module-kicker">
                ACHATS
            </span>

            <h1 class="module-title">
                Nouvel achat
            </h1>

            <p class="module-description">
                Enregistrez un achat fournisseur et mettez automatiquement le stock à jour.
            </p>

        </div>

        <a
            href="{{ route('company.purchases.index') }}"
            class="module-btn module-btn-secondary"
        >
            ← Retour
        </a>

    </div>


    {{-- ERREURS --}}
    @if($errors->any())

        <div class="module-alert module-alert-danger">

            <strong>
                Vérifiez les informations suivantes :
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
        action="{{ route('company.purchases.store') }}"
        id="purchase-form"
    >

        @csrf


        {{-- ========================================================
             INFORMATIONS ACHAT
        ========================================================= --}}

        <div class="module-card purchase-form-section">

            <div class="purchase-section-header">

                <div>

                    <h2>
                        Informations de l'achat
                    </h2>

                    <p>
                        Sélectionnez le fournisseur et la date de l'opération.
                    </p>

                </div>

            </div>


            <div class="module-form-grid">

                <div class="module-form-group">

                    <label for="supplier_id">
                        Fournisseur *
                    </label>

                    <select
                        name="supplier_id"
                        id="supplier_id"
                        required
                    >

                        <option value="">
                            Sélectionner un fournisseur
                        </option>

                        @foreach($suppliers as $supplier)

                            <option
                                value="{{ $supplier->id }}"
                                @selected(
                                    old('supplier_id') == $supplier->id
                                )
                            >
                                {{ $supplier->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="module-form-group">

                    <label for="purchase_date">
                        Date d'achat *
                    </label>

                    <input
                        type="date"
                        name="purchase_date"
                        id="purchase_date"
                        value="{{ old(
                            'purchase_date',
                            now()->format('Y-m-d')
                        ) }}"
                        required
                    >

                </div>

            </div>

        </div>


        {{-- ========================================================
             PRODUITS
        ========================================================= --}}

        <div class="module-card purchase-form-section">

            <div class="purchase-section-header">

                <div>

                    <h2>
                        Produits
                    </h2>

                    <p>
                        Ajoutez les produits reçus du fournisseur.
                    </p>

                </div>

                <button
                    type="button"
                    class="module-btn module-btn-secondary"
                    id="add-product"
                >
                    + Ajouter un produit
                </button>

            </div>


            <div
                id="purchase-items"
                class="purchase-items"
            >

                <div
                    class="purchase-item-row"
                    data-index="0"
                >

                    <div class="purchase-item-product">

                        <label>
                            Produit *
                        </label>

                        <select
                            name="items[0][product_id]"
                            class="product-select"
                            required
                        >

                            <option value="">
                                Sélectionner un produit
                            </option>

                            @foreach($products as $product)

                                <option
                                    value="{{ $product->id }}"
                                    data-price="{{ $product->purchase_price }}"
                                    data-stock="{{ $product->stock_quantity }}"
                                >
                                    {{ $product->name }}
                                    @if($product->reference)
                                        — {{ $product->reference }}
                                    @endif
                                </option>

                            @endforeach

                        </select>

                        <small class="product-stock">
                            Stock actuel : —
                        </small>

                    </div>


                    <div>

                        <label>
                            Quantité *
                        </label>

                        <input
                            type="number"
                            name="items[0][quantity]"
                            class="quantity-input"
                            min="0.001"
                            step="0.001"
                            value="1"
                            required
                        >

                    </div>


                    <div>

                        <label>
                            Prix unitaire *
                        </label>

                        <input
                            type="number"
                            name="items[0][unit_price]"
                            class="price-input"
                            min="0"
                            step="0.01"
                            value="0"
                            required
                        >

                    </div>


                    <div>

                        <label>
                            Total
                        </label>

                        <div class="purchase-line-total">
                            0 FCFA
                        </div>

                    </div>


                    <div class="purchase-item-remove-wrapper">

                        <label>
                            &nbsp;
                        </label>

                        <button
                            type="button"
                            class="module-action module-action-danger remove-product"
                        >
                            Supprimer
                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             NOTES
        ========================================================= --}}

        <div class="module-card purchase-form-section">

            <div class="module-form-group">

                <label for="notes">
                    Notes
                </label>

                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    placeholder="Informations complémentaires concernant cet achat..."
                >{{ old('notes') }}</textarea>

            </div>

        </div>


        {{-- ========================================================
             RÉSUMÉ FINANCIER
        ========================================================= --}}

        <div class="purchase-create-grid">


            <div class="module-card purchase-form-section">

                <div class="purchase-section-header">

                    <div>

                        <h2>
                            Règlement
                        </h2>

                        <p>
                            Indiquez le montant déjà payé au fournisseur.
                        </p>

                    </div>

                </div>


                <div class="module-form-grid">

                    <div class="module-form-group">

                        <label for="discount">
                            Remise
                        </label>

                        <input
                            type="number"
                            name="discount"
                            id="discount"
                            min="0"
                            step="0.01"
                            value="{{ old('discount', 0) }}"
                        >

                    </div>


                    <div class="module-form-group">

                        <label for="tax">
                            Taxe
                        </label>

                        <input
                            type="number"
                            name="tax"
                            id="tax"
                            min="0"
                            step="0.01"
                            value="{{ old('tax', 0) }}"
                        >

                    </div>


                    <div class="module-form-group">

                        <label for="amount_paid">
                            Montant payé
                        </label>

                        <input
                            type="number"
                            name="amount_paid"
                            id="amount_paid"
                            min="0"
                            step="0.01"
                            value="{{ old('amount_paid', 0) }}"
                        >

                    </div>

                </div>

            </div>


            <div class="module-card purchase-total-card">

                <div class="purchase-total-row">

                    <span>
                        Sous-total
                    </span>

                    <strong id="subtotal-display">
                        0 FCFA
                    </strong>

                </div>


                <div class="purchase-total-row">

                    <span>
                        Remise
                    </span>

                    <strong id="discount-display">
                        - 0 FCFA
                    </strong>

                </div>


                <div class="purchase-total-row">

                    <span>
                        Taxe
                    </span>

                    <strong id="tax-display">
                        + 0 FCFA
                    </strong>

                </div>


                <div class="purchase-total-row purchase-total-main">

                    <span>
                        TOTAL
                    </span>

                    <strong id="total-display">
                        0 FCFA
                    </strong>

                </div>


                <div class="purchase-total-row">

                    <span>
                        Payé
                    </span>

                    <strong id="paid-display">
                        0 FCFA
                    </strong>

                </div>


                <div class="purchase-total-row purchase-total-due">

                    <span>
                        Reste à payer
                    </span>

                    <strong id="due-display">
                        0 FCFA
                    </strong>

                </div>

            </div>

        </div>


        {{-- ACTIONS --}}
        <div class="purchase-form-actions">

            <a
                href="{{ route('company.purchases.index') }}"
                class="module-btn module-btn-secondary"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="module-btn module-btn-primary"
            >
                ✓ Enregistrer l'achat
            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const itemsContainer =
        document.getElementById('purchase-items');

    const addButton =
        document.getElementById('add-product');

    const discountInput =
        document.getElementById('discount');

    const taxInput =
        document.getElementById('tax');

    const paidInput =
        document.getElementById('amount_paid');


    let itemIndex = 1;


    function formatMoney(value) {

        return new Intl.NumberFormat(
            'fr-FR'
        ).format(
            Math.round(value)
        ) + ' FCFA';

    }


    function updateLine(row) {

        const productSelect =
            row.querySelector('.product-select');

        const quantityInput =
            row.querySelector('.quantity-input');

        const priceInput =
            row.querySelector('.price-input');

        const stockLabel =
            row.querySelector('.product-stock');

        const totalLabel =
            row.querySelector('.purchase-line-total');


        if (
            productSelect &&
            productSelect.selectedOptions.length
        ) {

            const option =
                productSelect.selectedOptions[0];

            const stock =
                option.dataset.stock ?? '0';

            stockLabel.textContent =
                'Stock actuel : ' + stock;


            if (
                priceInput.value === '' ||
                parseFloat(priceInput.value) === 0
            ) {

                const price =
                    parseFloat(
                        option.dataset.price || 0
                    );

                priceInput.value =
                    price.toFixed(2);

            }

        }


        const quantity =
            parseFloat(
                quantityInput.value || 0
            );

        const price =
            parseFloat(
                priceInput.value || 0
            );

        const total =
            quantity * price;


        totalLabel.textContent =
            formatMoney(total);


        updateSummary();

    }


    function updateSummary() {

        let subtotal = 0;


        document
            .querySelectorAll('.purchase-item-row')
            .forEach(function (row) {

                const quantity =
                    parseFloat(
                        row.querySelector(
                            '.quantity-input'
                        )?.value || 0
                    );

                const price =
                    parseFloat(
                        row.querySelector(
                            '.price-input'
                        )?.value || 0
                    );

                subtotal +=
                    quantity * price;

            });


        const discount =
            parseFloat(
                discountInput.value || 0
            );

        const tax =
            parseFloat(
                taxInput.value || 0
            );

        const paid =
            parseFloat(
                paidInput.value || 0
            );


        const total =
            Math.max(
                0,
                subtotal -
                discount +
                tax
            );


        const due =
            Math.max(
                0,
                total - paid
            );


        document.getElementById(
            'subtotal-display'
        ).textContent =
            formatMoney(subtotal);


        document.getElementById(
            'discount-display'
        ).textContent =
            '- ' +
            formatMoney(discount);


        document.getElementById(
            'tax-display'
        ).textContent =
            '+ ' +
            formatMoney(tax);


        document.getElementById(
            'total-display'
        ).textContent =
            formatMoney(total);


        document.getElementById(
            'paid-display'
        ).textContent =
            formatMoney(paid);


        document.getElementById(
            'due-display'
        ).textContent =
            formatMoney(due);

    }


    function bindRow(row) {

        const productSelect =
            row.querySelector(
                '.product-select'
            );

        const quantityInput =
            row.querySelector(
                '.quantity-input'
            );

        const priceInput =
            row.querySelector(
                '.price-input'
            );

        const removeButton =
            row.querySelector(
                '.remove-product'
            );


        productSelect.addEventListener(
            'change',
            function () {

                const option =
                    this.selectedOptions[0];

                if (option) {

                    const price =
                        parseFloat(
                            option.dataset.price || 0
                        );

                    priceInput.value =
                        price.toFixed(2);

                }

                updateLine(row);

            }
        );


        quantityInput.addEventListener(
            'input',
            function () {
                updateLine(row);
            }
        );


        priceInput.addEventListener(
            'input',
            function () {
                updateLine(row);
            }
        );


        removeButton.addEventListener(
            'click',
            function () {

                const rows =
                    document.querySelectorAll(
                        '.purchase-item-row'
                    );

                if (rows.length <= 1) {

                    alert(
                        'Vous devez conserver au moins un produit.'
                    );

                    return;
                }

                row.remove();

                updateSummary();

            }
        );

    }


    bindRow(
        document.querySelector(
            '.purchase-item-row'
        )
    );


    addButton.addEventListener(
        'click',
        function () {

            const firstRow =
                document.querySelector(
                    '.purchase-item-row'
                );

            const newRow =
                firstRow.cloneNode(true);


            newRow.dataset.index =
                itemIndex;


            newRow
                .querySelector(
                    '.product-select'
                )
                .name =
                `items[${itemIndex}][product_id]`;

            newRow
                .querySelector(
                    '.product-select'
                )
                .value = '';


            newRow
                .querySelector(
                    '.quantity-input'
                )
                .name =
                `items[${itemIndex}][quantity]`;

            newRow
                .querySelector(
                    '.quantity-input'
                )
                .value = 1;


            newRow
                .querySelector(
                    '.price-input'
                )
                .name =
                `items[${itemIndex}][unit_price]`;

            newRow
                .querySelector(
                    '.price-input'
                )
                .value = 0;


            newRow.querySelector(
                '.product-stock'
            ).textContent =
                'Stock actuel : —';


            newRow.querySelector(
                '.purchase-line-total'
            ).textContent =
                '0 FCFA';


            itemsContainer.appendChild(
                newRow
            );


            bindRow(newRow);

            itemIndex++;

            updateSummary();

        }
    );


    discountInput.addEventListener(
        'input',
        updateSummary
    );

    taxInput.addEventListener(
        'input',
        updateSummary
    );

    paidInput.addEventListener(
        'input',
        updateSummary
    );


    updateSummary();

});

</script>

@endsection