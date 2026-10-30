@extends('layouts.ecommerce')

@section('title', 'Nouveau paiement')

@section('content')

<div class="ecommerce-payment-create">

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                E-COMMERCE
            </div>

            <h1>
                Nouveau paiement
            </h1>

            <p>
                Enregistrez un règlement pour une commande.
            </p>

        </div>

        <a
            href="{{ route('company.ecommerce.payments.index') }}"
            class="btn btn-secondary"
        >
            ← Retour
        </a>

    </div>


    <div class="form-card">

            <form
                method="POST"
                action="{{ $orders->isNotEmpty()
                    ? route('company.ecommerce.orders.payments.store', ['order' => $orders->first()->id])
                    : '#' }}"
                id="ecommerce-payment-form"
            >

            @csrf

            <div class="form-grid">

                {{-- COMMANDE --}}

                <div class="form-group full">

                    <label for="order_id">
                        Commande *
                    </label>

                    <select
                        name="order_id"
                        id="order_id"
                        required
                    >

                        <option value="">
                            Sélectionner une commande
                        </option>

                        @foreach($orders as $order)

                            <option
                                value="{{ $order->id }}"
                                data-total="{{ $order->total }}"
                                @selected(
                                    old('order_id', request('order_id'))
                                    == $order->id
                                )
                            >

                                {{ $order->reference }}
                                —
                                {{ $order->customer_name }}
                                —
                                {{ number_format(
                                    $order->total,
                                    0,
                                    ',',
                                    ' '
                                ) }}
                                FCFA

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- MONTANT --}}

                <div class="form-group">

                    <label for="amount">
                        Montant *
                    </label>

                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        min="0.01"
                        step="0.01"
                        value="{{ old('amount') }}"
                        required
                        placeholder="0"
                    >

                    <small id="remainingAmount">
                        Sélectionnez une commande.
                    </small>

                </div>


                {{-- MÉTHODE --}}

                <div class="form-group">

                    <label for="method">
                        Méthode *
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
                            Espèces
                        </option>

                        <option
                            value="mobile_money"
                            @selected(old('method') === 'mobile_money')
                        >
                            Mobile Money
                        </option>

                        <option
                            value="card"
                            @selected(old('method') === 'card')
                        >
                            Carte bancaire
                        </option>

                        <option
                            value="bank_transfer"
                            @selected(old('method') === 'bank_transfer')
                        >
                            Virement bancaire
                        </option>

                        <option
                            value="other"
                            @selected(old('method') === 'other')
                        >
                            Autre
                        </option>

                    </select>

                </div>


                {{-- FOURNISSEUR --}}

                <div class="form-group">

                    <label for="provider">
                        Fournisseur
                    </label>

                    <select
                        name="provider"
                        id="provider"
                    >

                        <option value="">
                            Aucun
                        </option>

                        <option
                            value="orange_money"
                            @selected(
                                old('provider')
                                === 'orange_money'
                            )
                        >
                            Orange Money
                        </option>

                        <option
                            value="mtn_momo"
                            @selected(
                                old('provider')
                                === 'mtn_momo'
                            )
                        >
                            MTN MoMo
                        </option>

                    </select>

                </div>


                {{-- TRANSACTION --}}

                <div class="form-group">

                    <label for="transaction_id">
                        Référence transaction
                    </label>

                    <input
                        type="text"
                        name="transaction_id"
                        id="transaction_id"
                        value="{{ old('transaction_id') }}"
                        placeholder="Ex. TXN-123456"
                    >

                </div>


                {{-- STATUT --}}

                <div class="form-group">

                    <label for="status">
                        Statut *
                    </label>

                    <select
                        name="status"
                        id="status"
                        required
                    >

                        <option
                            value="completed"
                            @selected(
                                old(
                                    'status',
                                    'completed'
                                ) === 'completed'
                            )
                        >
                            Confirmé
                        </option>

                        <option
                            value="pending"
                            @selected(
                                old('status') === 'pending'
                            )
                        >
                            En attente
                        </option>

                        <option
                            value="failed"
                            @selected(
                                old('status') === 'failed'
                            )
                        >
                            Échec
                        </option>

                    </select>

                </div>


                {{-- NOTE --}}

                <div class="form-group full">

                    <label for="note">
                        Note
                    </label>

                    <textarea
                        name="note"
                        id="note"
                        rows="4"
                        placeholder="Informations complémentaires..."
                    >{{ old('note') }}</textarea>

                </div>

            </div>


            <div class="form-actions">

                <a
                    href="{{ route(
                        'company.ecommerce.payments.index'
                    ) }}"
                    class="btn btn-secondary"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Enregistrer le paiement
                </button>

            </div>

        </form>

    </div>

</div>


<script>
    
document.addEventListener('DOMContentLoaded', function () {
    const orderSelect = document.getElementById('order_id');
    const amountInput = document.getElementById('amount');
    const remaining = document.getElementById('remainingAmount');
    const form = document.getElementById('ecommerce-payment-form');

    if (!orderSelect || !amountInput || !remaining || !form) {
        return;
    }

    function updateAmount() {
        const option = orderSelect.options[orderSelect.selectedIndex];
        const orderId = orderSelect.value;

        if (!orderId || !option || !option.dataset.total) {
            remaining.textContent = 'Sélectionnez une commande.';
            form.action = '#';
            amountInput.removeAttribute('max');
            return;
        }

        // Mettre à jour l'URL avec l'identifiant de la commande
        form.action = form.action.replace(
            /\/orders\/\d+\/payments$/,
            ''
        );

        const baseUrl = form.dataset.baseUrl;

        if (baseUrl) {
            form.action = baseUrl.replace('__ORDER__', orderId);
        }

        const total = parseFloat(option.dataset.total);

        remaining.textContent =
            'Total commande : ' +
            new Intl.NumberFormat('fr-FR').format(total) +
            ' FCFA';

        amountInput.max = total;

        if (!amountInput.value) {
            amountInput.value = total;
        } else if (parseFloat(amountInput.value) > total) {
            amountInput.value = total;
        }
    }

    // Construire une URL modèle à partir de la route Laravel
    const firstOrderId = orderSelect.options.length > 1
        ? orderSelect.options[1].value
        : '';

    if (firstOrderId) {
        const templateUrl = @json(route(
            'company.ecommerce.orders.payments.store',
            ['order' => '__ORDER__']
        ));

        form.dataset.baseUrl = templateUrl.replace(
            '__ORDER__',
            '__ORDER__'
        );
    }

    orderSelect.addEventListener('change', updateAmount);

    // Initialiser le formulaire
    const selectedOrder = orderSelect.value;

    if (selectedOrder) {
        const templateUrl = @json(route(
            'company.ecommerce.orders.payments.store',
            ['order' => '__ORDER__']
        ));

        form.action = templateUrl.replace('__ORDER__', selectedOrder);
    }

    updateAmount();
});
</script>

@endsection