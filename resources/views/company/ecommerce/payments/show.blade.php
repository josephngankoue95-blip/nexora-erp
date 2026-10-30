@extends('layouts.ecommerce')

@section('title', 'Paiement ' . $payment->reference)

@section('content')

<div class="ecommerce-payment-show">

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                PAIEMENT E-COMMERCE
            </div>

            <h1>
                {{ $payment->reference }}
            </h1>

            <p>
                Détails du règlement associé à la commande.
            </p>

        </div>

        <a
            href="{{ route('company.ecommerce.payments.index') }}"
            class="btn btn-secondary"
        >
            ← Retour aux paiements
        </a>

    </div>


    <div class="payment-detail-grid">

        {{-- MONTANT --}}

        <div class="payment-amount-card">

            <span>
                Montant
            </span>

            <strong>

                {{ number_format(
                    $payment->amount,
                    0,
                    ',',
                    ' '
                ) }}

                FCFA

            </strong>

            <span
                class="payment-status-badge payment-status-{{ $payment->status }}"
            >

                @switch($payment->status)

                    @case('completed')
                        Confirmé
                        @break

                    @case('pending')
                        En attente
                        @break

                    @case('failed')
                        Échec
                        @break

                    @case('refunded')
                        Remboursé
                        @break

                @endswitch

            </span>

        </div>


        {{-- INFORMATIONS --}}

        <div class="detail-card">

            <div class="detail-card-header">

                <div>

                    <h2>
                        Informations du paiement
                    </h2>

                </div>

            </div>


            <div class="payment-info-grid">

                <div>

                    <span>Référence</span>

                    <strong>
                        {{ $payment->reference }}
                    </strong>

                </div>


                <div>

                    <span>Méthode</span>

                    <strong>
                        {{ $payment->method_label }}
                    </strong>

                </div>


                <div>

                    <span>Fournisseur</span>

                    <strong>
                        {{ $payment->provider_label ?? '—' }}
                    </strong>

                </div>


                <div>

                    <span>Transaction</span>

                    <strong>
                        {{ $payment->transaction_id ?? '—' }}
                    </strong>

                </div>


                <div>

                    <span>Date</span>

                    <strong>
                        {{ $payment->created_at->format(
                            'd/m/Y H:i'
                        ) }}
                    </strong>

                </div>


                <div>

                    <span>Payé le</span>

                    <strong>
                        {{ $payment->paid_at?->format(
                            'd/m/Y H:i'
                        ) ?? '—' }}
                    </strong>

                </div>

            </div>

        </div>


        {{-- COMMANDE --}}

        @if($payment->order)

            <div class="detail-card">

                <div class="detail-card-header">

                    <div>

                        <h2>
                            Commande associée
                        </h2>

                    </div>

                    <a
                        href="{{ route(
                            'company.ecommerce.orders.show',
                            $payment->order
                        ) }}"
                        class="table-action"
                    >
                        Voir la commande
                    </a>

                </div>


                <div class="payment-order-summary">

                    <div>

                        <span>
                            Référence
                        </span>

                        <strong>
                            {{ $payment->order->reference }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Client
                        </span>

                        <strong>
                            {{ $payment->order->customer_name }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Total commande
                        </span>

                        <strong>
                            {{ number_format(
                                $payment->order->total,
                                0,
                                ',',
                                ' '
                            ) }}
                            FCFA
                        </strong>

                    </div>

                </div>

            </div>

        @endif


        {{-- NOTE --}}

        @if($payment->note)

            <div class="detail-card">

                <div class="detail-card-header">

                    <h2>
                        Note
                    </h2>

                </div>

                <div class="payment-note">
                    {{ $payment->note }}
                </div>

            </div>

        @endif


        {{-- ACTION --}}

        @if(
            $payment->status !== 'refunded' &&
            auth()->user()->hasPermission('ecommerce.delete')
        )

            <div class="danger-card">

                <div>

                    <strong>
                        Rembourser ce paiement
                    </strong>

                    <p>
                        Le paiement sera marqué comme remboursé
                        et ne sera plus considéré comme encaissé.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route(
                        'company.ecommerce.payments.destroy',
                        $payment
                    ) }}"
                    onsubmit="return confirm(
                        'Confirmer le remboursement de ce paiement ?'
                    )"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Rembourser
                    </button>

                </form>

            </div>

        @endif

    </div>

</div>

@endsection