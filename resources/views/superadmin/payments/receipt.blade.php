<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Reçu {{ $payment->reference }}
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f1f5f9;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
        }

        .receipt-wrapper {
            width: 100%;
            max-width: 780px;
            margin: 0 auto;
        }

        .receipt {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
        }

        .receipt-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;
            padding: 30px;
            border-bottom: 1px solid #e5e7eb;
        }

        .receipt-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .receipt-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            overflow: hidden;
            color: #2563eb;
            background: #eff6ff;
            border-radius: 12px;
        }

        .receipt-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .receipt-brand h1 {
            margin: 0;
            font-size: 20px;
        }

        .receipt-brand p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .receipt-title {
            text-align: right;
        }

        .receipt-title h2 {
            margin: 0;
            color: #2563eb;
            font-size: 25px;
            text-transform: uppercase;
        }

        .receipt-title p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .receipt-body {
            padding: 30px;
        }

        .receipt-reference {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 17px;
            margin-bottom: 25px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .receipt-reference span {
            color: #64748b;
            font-size: 11px;
        }

        .receipt-reference strong {
            font-family: monospace;
            font-size: 14px;
        }

        .receipt-section {
            margin-bottom: 28px;
        }

        .receipt-section-title {
            margin: 0 0 12px;
            padding-bottom: 8px;
            color: #172033;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .receipt-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .receipt-item {
            padding: 12px;
            background: #fafafa;
            border-radius: 8px;
        }

        .receipt-item span {
            display: block;
            margin-bottom: 5px;
            color: #94a3b8;
            font-size: 10px;
        }

        .receipt-item strong {
            font-size: 12px;
        }

        .receipt-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 20px;
            margin-top: 10px;
            color: #ffffff;
            background: #2563eb;
            border-radius: 12px;
        }

        .receipt-total span {
            font-size: 12px;
        }

        .receipt-total strong {
            font-size: 24px;
        }

        .receipt-status {
            display: inline-flex;
            padding: 6px 10px;
            color: #15803d;
            background: #dcfce7;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }

        .receipt-notes {
            padding: 14px;
            color: #475569;
            background: #f8fafc;
            border-radius: 9px;
            font-size: 11px;
            line-height: 1.7;
            white-space: pre-line;
        }

        .receipt-footer {
            padding: 20px 30px;
            color: #64748b;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            text-align: center;
        }

        .receipt-print {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 18px;
        }

        .receipt-print button {
            padding: 11px 18px;
            color: #ffffff;
            background: #2563eb;
            border: 0;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        @media print {

            body {
                padding: 0;
                background: #ffffff;
            }

            .receipt {
                border: 0;
                border-radius: 0;
            }

            .receipt-print {
                display: none;
            }

        }

        @media (max-width: 600px) {

            body {
                padding: 10px;
            }

            .receipt-header {
                flex-direction: column;
                padding: 20px;
            }

            .receipt-title {
                text-align: left;
            }

            .receipt-body {
                padding: 20px;
            }

            .receipt-grid {
                grid-template-columns: 1fr;
            }

            .receipt-total {
                align-items: flex-start;
                flex-direction: column;
            }

        }

    </style>
</head>


<body>

<div class="receipt-wrapper">

    <div class="receipt">

        <div class="receipt-header">

            <div class="receipt-brand">

                <div class="receipt-logo">

                    @if($payment->company?->logo)

                        <img
                            src="{{ asset('storage/' . $payment->company->logo) }}"
                            alt="{{ $payment->company->name }}"
                        >

                    @else

                        ERP

                    @endif

                </div>

                <div>

                    <h1>
                        {{ $payment->company->name ?? 'ERP SaaS' }}
                    </h1>

                    @if($payment->company?->email)
                        <p>
                            {{ $payment->company->email }}
                        </p>
                    @endif

                </div>

            </div>


            <div class="receipt-title">

                <h2>
                    Reçu
                </h2>

                <p>
                    {{ $payment->created_at->format('d/m/Y H:i') }}
                </p>

            </div>

        </div>


        <div class="receipt-body">

            <div class="receipt-reference">

                <span>
                    Référence du paiement
                </span>

                <strong>
                    {{ $payment->reference }}
                </strong>

            </div>


            <div class="receipt-section">

                <h3 class="receipt-section-title">
                    Informations du paiement
                </h3>

                <div class="receipt-grid">

                    <div class="receipt-item">
                        <span>Méthode</span>
                        <strong>
                            {{ $payment->method_label }}
                        </strong>
                    </div>

                    <div class="receipt-item">
                        <span>Statut</span>

                        <strong>
                            @if($payment->status === 'completed')
                                <span class="receipt-status">
                                    Terminé
                                </span>
                            @else
                                {{ $payment->status_label }}
                            @endif
                        </strong>

                    </div>

                    <div class="receipt-item">
                        <span>Fournisseur</span>
                        <strong>
                            {{ $payment->provider ?: '—' }}
                        </strong>
                    </div>

                    <div class="receipt-item">
                        <span>Téléphone</span>
                        <strong>
                            {{ $payment->phone ?: '—' }}
                        </strong>
                    </div>

                    <div class="receipt-item">
                        <span>Référence transaction</span>
                        <strong>
                            {{ $payment->transaction_reference ?: '—' }}
                        </strong>
                    </div>

                    <div class="receipt-item">
                        <span>Date du paiement</span>
                        <strong>
                            {{ $payment->paid_at?->format('d/m/Y H:i') ?? '—' }}
                        </strong>
                    </div>

                </div>

            </div>


            @if($payment->subscription)

                <div class="receipt-section">

                    <h3 class="receipt-section-title">
                        Abonnement
                    </h3>

                    <div class="receipt-grid">

                        <div class="receipt-item">
                            <span>Abonnement</span>
                            <strong>
                                {{ $payment->subscription->name }}
                            </strong>
                        </div>

                        <div class="receipt-item">
                            <span>Plan</span>
                            <strong>
                                {{ $payment->subscription->plan?->name ?? '—' }}
                            </strong>
                        </div>

                        <div class="receipt-item">
                            <span>Date de début</span>
                            <strong>
                                {{ $payment->subscription->starts_at?->format('d/m/Y') ?? '—' }}
                            </strong>
                        </div>

                        <div class="receipt-item">
                            <span>Date de fin</span>
                            <strong>
                                {{ $payment->subscription->ends_at?->format('d/m/Y') ?? '—' }}
                            </strong>
                        </div>

                    </div>

                </div>

            @endif


            <div class="receipt-total">

                <span>
                    Montant payé
                </span>

                <strong>
                    {{ number_format($payment->amount, 0, ',', ' ') }}
                    FCFA
                </strong>

            </div>


            @if($payment->notes)

                <div class="receipt-section" style="margin-top: 25px;">

                    <h3 class="receipt-section-title">
                        Notes
                    </h3>

                    <div class="receipt-notes">
                        {{ $payment->notes }}
                    </div>

                </div>

            @endif

        </div>


        <div class="receipt-footer">

            Merci pour votre paiement.

            <br>

            Document généré automatiquement par ERP SaaS.

        </div>

    </div>


    <div class="receipt-print">

        <button onclick="window.print()">
            Imprimer le reçu
        </button>

    </div>

</div>

</body>

</html>