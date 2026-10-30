@extends('layouts.ecommerce')

@section('title', 'Commande ' . $order->reference)

@section('content')

<div class="ecommerce-order-show">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                COMMANDE E-COMMERCE
            </div>

            <h1>
                {{ $order->reference }}
            </h1>

            <p>
                Créée le
                {{ $order->created_at->format('d/m/Y à H:i') }}
            </p>

        </div>


        <div class="header-actions">

            <a
                href="{{ route('company.ecommerce.orders.index') }}"
                class="btn btn-secondary"
            >
                ← Commandes
            </a>

        </div>

    </div>


    {{-- =========================================================
         STATUS
    ========================================================== --}}

    <div class="order-status-card">

        <div>

            <span class="status-label">
                Statut actuel
            </span>

            <span class="status-badge large status-{{ $order->status }}">

                @switch($order->status)

                    @case('pending')
                        En attente
                        @break

                    @case('confirmed')
                        Confirmée
                        @break

                    @case('preparing')
                        En préparation
                        @break

                    @case('shipped')
                        Expédiée
                        @break

                    @case('delivered')
                        Livrée
                        @break

                    @case('cancelled')
                        Annulée
                        @break

                @endswitch

            </span>

        </div>


        @if(
            auth()->user()->hasPermission('ecommerce.edit') &&
            !in_array($order->status, ['delivered', 'cancelled'])
        )

            <form
                method="POST"
                action="{{ route('company.ecommerce.orders.status', $order) }}"
                class="status-form"
            >

                @csrf
                @method('PATCH')

                <select name="status">

                    <option value="pending"
                        @selected($order->status === 'pending')>
                        En attente
                    </option>

                    <option value="confirmed"
                        @selected($order->status === 'confirmed')>
                        Confirmée
                    </option>

                    <option value="preparing"
                        @selected($order->status === 'preparing')>
                        En préparation
                    </option>

                    <option value="shipped"
                        @selected($order->status === 'shipped')>
                        Expédiée
                    </option>

                    <option value="delivered"
                        @selected($order->status === 'delivered')>
                        Livrée
                    </option>

                    <option value="cancelled">
                        Annulée
                    </option>

                </select>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Mettre à jour
                </button>

            </form>

        @endif

    </div>


    <div class="order-layout">


        {{-- =====================================================
             COLONNE PRINCIPALE
        ====================================================== --}}

        <div class="order-main">


            {{-- ARTICLES --}}

            <div class="detail-card">

                <div class="detail-card-header">

                    <div>
                        <h2>Articles</h2>

                        <span>
                            {{ $order->items->count() }}
                            ligne(s)
                        </span>
                    </div>

                </div>


                <div class="order-items-list">

                    @foreach($order->items as $item)

                        <div class="order-detail-item">

                            <div class="product-thumb">

                                @if(
                                    $item->product &&
                                    $item->product->image
                                )

                                    <img
                                        src="{{ asset('storage/' . $item->product->image) }}"
                                        alt="{{ $item->product_name }}"
                                    >

                                @else

                                    <span>📦</span>

                                @endif

                            </div>


                            <div class="product-detail">

                                <strong>
                                    {{ $item->product_name }}
                                </strong>

                                @if($item->product_reference)

                                    <small>
                                        Réf. {{ $item->product_reference }}
                                    </small>

                                @endif

                                <span>
                                    {{ number_format($item->quantity, 3, ',', ' ') }}
                                    ×
                                    {{ number_format($item->unit_price, 0, ',', ' ') }}
                                    FCFA
                                </span>

                            </div>


                            <strong class="item-total">

                                {{ number_format($item->total, 0, ',', ' ') }}

                                FCFA

                            </strong>

                        </div>

                    @endforeach

                </div>


                <div class="totals">

                    <div>
                        <span>Sous-total</span>

                        <strong>
                            {{ number_format($order->subtotal, 0, ',', ' ') }}
                            FCFA
                        </strong>
                    </div>


                    <div>
                        <span>Remise</span>

                        <strong>
                            -
                            {{ number_format($order->discount, 0, ',', ' ') }}
                            FCFA
                        </strong>
                    </div>


                    <div>
                        <span>Livraison</span>

                        <strong>
                            {{ number_format($order->shipping_amount, 0, ',', ' ') }}
                            FCFA
                        </strong>
                    </div>


                    <div>
                        <span>Taxes</span>

                        <strong>
                            {{ number_format($order->tax, 0, ',', ' ') }}
                            FCFA
                        </strong>
                    </div>


                    <div class="total-final">

                        <span>Total</span>

                        <strong>
                            {{ number_format($order->total, 0, ',', ' ') }}
                            FCFA
                        </strong>

                    </div>

                </div>

            </div>


            {{-- NOTES --}}

            @if($order->customer_note || $order->internal_note)

                <div class="detail-card">

                    <div class="detail-card-header">
                        <h2>Notes</h2>
                    </div>

                    <div class="notes-grid">

                        @if($order->customer_note)

                            <div class="note-box">

                                <span>
                                    Note client
                                </span>

                                <p>
                                    {{ $order->customer_note }}
                                </p>

                            </div>

                        @endif


                        @if($order->internal_note)

                            <div class="note-box internal">

                                <span>
                                    Note interne
                                </span>

                                <p>
                                    {{ $order->internal_note }}
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            @endif

        </div>


        {{-- =====================================================
             SIDEBAR
        ====================================================== --}}

        <aside class="order-sidebar">


            {{-- CLIENT --}}

            <div class="detail-card">

                <div class="detail-card-header">
                    <h2>Client</h2>
                </div>

                <div class="customer-profile">

                    <div class="customer-avatar large">
                        {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                    </div>

                    <strong>
                        {{ $order->customer_name }}
                    </strong>

                </div>


                <div class="info-list">

                    @if($order->customer_phone)

                        <div>
                            <span>Téléphone</span>
                            <strong>
                                {{ $order->customer_phone }}
                            </strong>
                        </div>

                    @endif


                    @if($order->customer_email)

                        <div>
                            <span>E-mail</span>
                            <strong>
                                {{ $order->customer_email }}
                            </strong>
                        </div>

                    @endif

                </div>

            </div>


            {{-- LIVRAISON --}}

            <div class="detail-card">

                <div class="detail-card-header">
                    <h2>Livraison</h2>
                </div>

                @if($order->shipping_address)

                    <div class="address-box">

                        <strong>
                            Adresse
                        </strong>

                        <p>
                            {{ $order->shipping_address }}
                        </p>

                        <p>

                            {{ $order->shipping_city }}

                            @if($order->shipping_region)
                                — {{ $order->shipping_region }}
                            @endif

                            @if($order->shipping_country)
                                <br>
                                {{ $order->shipping_country }}
                            @endif

                        </p>

                    </div>

                @else

                    <div class="muted-box">
                        Aucune adresse de livraison.
                    </div>

                @endif

            </div>


            {{-- PAIEMENT --}}

            <div class="detail-card">

                <div class="detail-card-header">
                    <h2>Paiement</h2>
                </div>

                <div class="info-list">

                    <div>

                        <span>Mode</span>

                        <strong>

                            @switch($order->payment_method)

                                @case('cash')
                                    Espèces
                                    @break

                                @case('mobile_money')
                                    Mobile Money
                                    @break

                                @case('card')
                                    Carte bancaire
                                    @break

                                @case('bank_transfer')
                                    Virement bancaire
                                    @break

                                @default
                                    Non défini

                            @endswitch

                        </strong>

                    </div>

                    {{-- ============================================================
     PAIEMENTS DE LA COMMANDE
============================================================ --}}

<div class="detail-card ecommerce-order-payments-card">

    <div class="detail-card-header">

        <div>

            <span class="page-eyebrow">
                PAIEMENTS
            </span>

            <h2>
                Règlements
            </h2>

        </div>

        @if(
            auth()->user()->hasPermission('ecommerce.create') &&
            $order->remainingAmount() > 0
        )

            <a
                href="{{ route(
                    'company.ecommerce.payments.create',
                    ['order_id' => $order->id]
                ) }}"
                class="btn btn-primary"
            >
                + Ajouter un paiement
            </a>

        @endif

    </div>


    @php
        $paidAmount = $order->paidAmount();
        $remainingAmount = $order->remainingAmount();
    @endphp


    <div class="payment-summary">

        <div>

            <span>
                Total commande
            </span>

            <strong>
                {{ number_format(
                    $order->total,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </strong>

        </div>


        <div>

            <span>
                Déjà payé
            </span>

            <strong class="payment-paid-value">
                {{ number_format(
                    $paidAmount,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </strong>

        </div>


        <div>

            <span>
                Reste à payer
            </span>

            <strong class="payment-remaining-value">
                {{ number_format(
                    $remainingAmount,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </strong>

        </div>

    </div>


    @if($order->payments->count())

        <div class="table-wrapper">

            <table class="orders-table">

                <thead>

                    <tr>

                        <th>
                            Référence
                        </th>

                        <th>
                            Méthode
                        </th>

                        <th>
                            Montant
                        </th>

                        <th>
                            Statut
                        </th>

                        <th>
                            Date
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($order->payments as $payment)

                        <tr>

                            <td>
                                {{ $payment->reference }}
                            </td>

                            <td>
                                {{ $payment->method_label }}
                            </td>

                            <td>

                                <strong class="order-total">

                                    {{ number_format(
                                        $payment->amount,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    FCFA

                                </strong>

                            </td>

                            <td>

                                <span
                                    class="payment-status-badge payment-status-{{ $payment->status }}"
                                >
                                    {{ match($payment->status) {
                                        'completed' => 'Confirmé',
                                        'pending' => 'En attente',
                                        'failed' => 'Échec',
                                        'refunded' => 'Remboursé',
                                        default => ucfirst($payment->status),
                                    } }}
                                </span>

                            </td>

                            <td>
                                {{ $payment->created_at->format(
                                    'd/m/Y H:i'
                                ) }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                💳
            </div>

            <h3>
                Aucun paiement
            </h3>

            <p>
                Aucun règlement n'a encore été enregistré
                pour cette commande.
            </p>

        </div>

    @endif

</div>


                    <div>

                        <span>État</span>

                        <strong>

                            <span class="payment-badge payment-{{ $order->payment_status }}">

                                @switch($order->payment_status)

                                    @case('paid')
                                        Payé
                                        @break

                                    @case('partially_paid')
                                        Partiellement payé
                                        @break

                                    @case('failed')
                                        Échec
                                        @break

                                    @case('refunded')
                                        Remboursé
                                        @break

                                    @default
                                        En attente

                                @endswitch

                            </span>

                        </strong>

                    </div>

                </div>

            </div>


            {{-- ANNULATION --}}

            @if(
                auth()->user()->hasPermission('ecommerce.delete') &&
                !in_array($order->status, ['delivered', 'cancelled'])
            )

                <div class="danger-card">

                    <h3>
                        Annuler la commande
                    </h3>

                    <p>
                        Cette action modifiera définitivement
                        le statut de la commande.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('company.ecommerce.orders.destroy', $order) }}"
                        onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?')"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Annuler la commande
                        </button>

                    </form>

                </div>

            @endif

        </aside>

    </div>

</div>

@endsection