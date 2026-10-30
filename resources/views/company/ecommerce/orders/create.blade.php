@extends('layouts.ecommerce')

@section('title', 'Nouvelle commande')

@section('content')

<div class="ecommerce-order-create">

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                E-COMMERCE
            </div>

            <h1>Nouvelle commande</h1>

            <p>
                Enregistrez une commande client depuis votre boutique.
            </p>

        </div>

        <a
            href="{{ route('company.ecommerce.orders.index') }}"
            class="btn btn-secondary"
        >
            ← Retour aux commandes
        </a>

    </div>


    <form
        method="POST"
        action="{{ route('company.ecommerce.orders.store') }}"
        id="orderForm"
    >

        @csrf


        {{-- =====================================================
             CLIENT
        ====================================================== --}}

        <div class="form-card">

            <div class="form-card-header">

                <div>
                    <h2>Informations client</h2>

                    <p>
                        Coordonnées de la personne qui passe la commande.
                    </p>
                </div>

            </div>


            <div class="form-grid">

                <div class="form-group">

                    <label for="client_id">
                        Client ERP
                    </label>

                    <select
                        name="client_id"
                        id="client_id"
                    >

                        <option value="">
                            Nouveau / client non enregistré
                        </option>

                        @foreach($clients as $client)

                            <option
                                value="{{ $client->id }}"
                                @selected(old('client_id') == $client->id)
                            >
                                {{ $client->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="form-group">

                    <label for="customer_name">
                        Nom du client *
                    </label>

                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        value="{{ old('customer_name') }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="customer_phone">
                        Téléphone
                    </label>

                    <input
                        type="text"
                        id="customer_phone"
                        name="customer_phone"
                        value="{{ old('customer_phone') }}"
                    >

                </div>


                <div class="form-group">

                    <label for="customer_email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="customer_email"
                        name="customer_email"
                        value="{{ old('customer_email') }}"
                    >

                </div>

            </div>

        </div>


        {{-- =====================================================
             PRODUITS
        ====================================================== --}}

        <div class="form-card">

            <div class="form-card-header">

                <div>

                    <h2>Articles de la commande</h2>

                    <p>
                        Sélectionnez les produits publiés dans votre catalogue.
                    </p>

                </div>

                <button
                    type="button"
                    class="btn btn-secondary"
                    id="addProductBtn"
                >
                    ＋ Ajouter un produit
                </button>

            </div>


            <div id="orderItems">

                <div class="order-item">

                    <div class="form-group product-field">

                        <label>
                            Produit *
                        </label>

                        <select
                            name="items[0][ecommerce_product_id]"
                            class="product-select"
                            required
                        >

                            <option value="">
                                Sélectionner un produit
                            </option>

                            @foreach($products as $ecommerceProduct)

                                @php
                                    $product = $ecommerceProduct->product;
                                @endphp

                                @if($product)

                                    <option
                                        value="{{ $ecommerceProduct->id }}"
                                        data-price="{{ $product->selling_price }}"
                                    >
                                        {{ $product->name }}
                                        @if($product->reference)
                                            — {{ $product->reference }}
                                        @endif
                                        — {{ number_format($product->selling_price, 0, ',', ' ') }} FCFA
                                    </option>

                                @endif

                            @endforeach

                        </select>

                    </div>


                    <div class="form-group quantity-field">

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


                    <div class="form-group price-field">

                        <label>
                            Prix unitaire
                        </label>

                        <input
                            type="number"
                            name="items[0][unit_price]"
                            class="price-input"
                            min="0"
                            step="0.01"
                        >

                    </div>


                    <div class="line-total">
                        0 FCFA
                    </div>


                    <button
                        type="button"
                        class="remove-item"
                        title="Supprimer"
                    >
                        ×
                    </button>

                </div>

            </div>


            <div class="order-summary">

                <div>
                    <span>Sous-total</span>
                    <strong id="subtotalDisplay">
                        0 FCFA
                    </strong>
                </div>

            </div>

        </div>


        {{-- =====================================================
             LIVRAISON
        ====================================================== --}}

        <div class="form-card">

            <div class="form-card-header">

                <div>

                    <h2>Livraison</h2>

                    <p>
                        Adresse de livraison du client.
                    </p>

                </div>

            </div>


            <div class="form-grid">

                <div class="form-group form-full">

                    <label for="shipping_address">
                        Adresse
                    </label>

                    <textarea
                        name="shipping_address"
                        id="shipping_address"
                        rows="3"
                    >{{ old('shipping_address') }}</textarea>

                </div>


                <div class="form-group">

                    <label for="shipping_city">
                        Ville
                    </label>

                    <input
                        type="text"
                        name="shipping_city"
                        id="shipping_city"
                        value="{{ old('shipping_city') }}"
                    >

                </div>


                <div class="form-group">

                    <label for="shipping_region">
                        Région
                    </label>

                    <input
                        type="text"
                        name="shipping_region"
                        id="shipping_region"
                        value="{{ old('shipping_region') }}"
                    >

                </div>


                <div class="form-group">

                    <label for="shipping_country">
                        Pays
                    </label>

                    <input
                        type="text"
                        name="shipping_country"
                        id="shipping_country"
                        value="{{ old('shipping_country', 'Cameroun') }}"
                    >

                </div>

            </div>

        </div>


        {{-- =====================================================
             PAIEMENT
        ====================================================== --}}

        <div class="form-card">

            <div class="form-card-header">

                <div>

                    <h2>Paiement et montant</h2>

                    <p>
                        Informations financières de la commande.
                    </p>

                </div>

            </div>


            <div class="form-grid">

                <div class="form-group">

                    <label for="payment_method">
                        Mode de paiement
                    </label>

                    <select
                        name="payment_method"
                        id="payment_method"
                    >

                        <option value="">
                            À définir
                        </option>

                        <option value="cash">
                            Espèces
                        </option>

                        <option value="mobile_money">
                            Mobile Money
                        </option>

                        <option value="card">
                            Carte bancaire
                        </option>

                        <option value="bank_transfer">
                            Virement bancaire
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="shipping_amount">
                        Frais de livraison
                    </label>

                    <input
                        type="number"
                        name="shipping_amount"
                        id="shipping_amount"
                        value="{{ old('shipping_amount', 0) }}"
                        min="0"
                        step="0.01"
                    >

                </div>


                <div class="form-group">

                    <label for="discount">
                        Remise
                    </label>

                    <input
                        type="number"
                        name="discount"
                        id="discount"
                        value="{{ old('discount', 0) }}"
                        min="0"
                        step="0.01"
                    >

                </div>


                <div class="form-group">

                    <label for="tax">
                        Taxes
                    </label>

                    <input
                        type="number"
                        name="tax"
                        id="tax"
                        value="{{ old('tax', 0) }}"
                        min="0"
                        step="0.01"
                    >

                </div>

            </div>

        </div>


        {{-- =====================================================
             NOTES
        ====================================================== --}}

        <div class="form-card">

            <div class="form-grid">

                <div class="form-group">

                    <label for="customer_note">
                        Note du client
                    </label>

                    <textarea
                        name="customer_note"
                        id="customer_note"
                        rows="4"
                    >{{ old('customer_note') }}</textarea>

                </div>


                <div class="form-group">

                    <label for="internal_note">
                        Note interne
                    </label>

                    <textarea
                        name="internal_note"
                        id="internal_note"
                        rows="4"
                    >{{ old('internal_note') }}</textarea>

                </div>

            </div>

        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}

        <div class="form-actions">

            <a
                href="{{ route('company.ecommerce.orders.index') }}"
                class="btn btn-secondary"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Enregistrer la commande
            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('orderItems');
    const addButton = document.getElementById('addProductBtn');

    const subtotalDisplay =
        document.getElementById('subtotalDisplay');

    let itemIndex = 1;


    function updateTotals() {

        let subtotal = 0;

        container
            .querySelectorAll('.order-item')
            .forEach(function (row) {

                const select =
                    row.querySelector('.product-select');

                const quantity =
                    row.querySelector('.quantity-input');

                const price =
                    row.querySelector('.price-input');

                const lineTotal =
                    row.querySelector('.line-total');


                const selected =
                    select.options[select.selectedIndex];

                let unitPrice =
                    parseFloat(price.value || 0);

                if (
                    !price.value &&
                    selected &&
                    selected.dataset.price
                ) {
                    unitPrice =
                        parseFloat(selected.dataset.price);

                    price.value = unitPrice;
                }


                const qty =
                    parseFloat(quantity.value || 0);

                const total =
                    unitPrice * qty;

                subtotal += total;

                lineTotal.textContent =
                    new Intl.NumberFormat('fr-FR')
                        .format(total) +
                    ' FCFA';
            });


        subtotalDisplay.textContent =
            new Intl.NumberFormat('fr-FR')
                .format(subtotal) +
            ' FCFA';
    }


    function attachRow(row) {

        const select =
            row.querySelector('.product-select');

        const quantity =
            row.querySelector('.quantity-input');

        const price =
            row.querySelector('.price-input');

        const remove =
            row.querySelector('.remove-item');


        select.addEventListener('change', function () {

            const option =
                select.options[select.selectedIndex];

            if (option.dataset.price) {

                price.value =
                    option.dataset.price;
            }

            updateTotals();
        });


        quantity.addEventListener(
            'input',
            updateTotals
        );

        price.addEventListener(
            'input',
            updateTotals
        );


        remove.addEventListener(
            'click',
            function () {

                const rows =
                    container.querySelectorAll(
                        '.order-item'
                    );

                if (rows.length > 1) {

                    row.remove();

                    updateTotals();
                }
            }
        );
    }


    attachRow(
        container.querySelector('.order-item')
    );


    addButton.addEventListener(
        'click',
        function () {

            const first =
                container.querySelector('.order-item');

            const clone =
                first.cloneNode(true);


            clone
                .querySelectorAll('input, select')
                .forEach(function (field) {

                    const name =
                        field.getAttribute('name');

                    if (name) {

                        field.setAttribute(
                            'name',
                            name.replace(
                                /items\[\d+\]/,
                                'items[' + itemIndex + ']'
                            )
                        );
                    }

                    if (field.classList.contains('quantity-input')) {
                        field.value = 1;
                    } else {
                        field.value = '';
                    }
                });


            clone.querySelector('.line-total')
                .textContent = '0 FCFA';


            container.appendChild(clone);

            attachRow(clone);

            itemIndex++;

            updateTotals();
        }
    );


    updateTotals();

});

</script>

@endsection