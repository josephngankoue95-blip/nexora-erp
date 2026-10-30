@php
    $company = $purchase->company;

    $currencySymbol = $company?->currency_symbol ?? 'FCFA';
    $dateFormat = $company?->date_format ?? 'd/m/Y';
    $primaryColor = $company?->primary_color ?? '#2563eb';

    $invoiceNumber = $purchase->reference;

    $paidAmount = (float) $purchase->amount_paid;
    $remainingAmount = (float) $purchase->amount_due;
    $totalAmount = (float) $purchase->total;

    $paidPercentage = $totalAmount > 0
        ? min(
            100,
            max(
                0,
                ($paidAmount / $totalAmount) * 100
            )
        )
        : 0;
@endphp

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Facture achat {{ $invoiceNumber }}
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #eef1f5;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }


        /* =========================================================
           PAGE A4
        ========================================================= */

        .invoice-page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 14mm;
            background: #ffffff;
            box-shadow: 0 10px 35px rgba(0, 0, 0, .08);
        }


        /* =========================================================
           ACTIONS
        ========================================================= */

        .invoice-actions {
            position: fixed;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
            z-index: 1000;
        }

        .invoice-actions a,
        .invoice-actions button {
            border: 0;
            padding: 9px 14px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-back {
            background: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db !important;
        }

        .btn-print {
            background: {{ $primaryColor }};
            color: #ffffff;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .invoice-header {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid {{ $primaryColor }};
        }

        .company {
            display: flex;
            gap: 12px;
            flex: 1;
        }

        .company-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 5px;
        }

        .company-placeholder {
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: {{ $primaryColor }};
            color: #ffffff;
            border-radius: 6px;
            font-size: 22px;
            font-weight: 800;
        }

        .company-info h1 {
            margin: 0 0 3px;
            font-size: 19px;
            color: #111827;
        }

        .company-slogan {
            margin-bottom: 5px;
            color: #6b7280;
            font-size: 9px;
            font-style: italic;
        }

        .company-info p {
            margin: 2px 0;
            color: #6b7280;
            font-size: 9px;
        }

        .invoice-heading {
            min-width: 145px;
            text-align: right;
        }

        .invoice-heading h2 {
            margin: 0;
            color: {{ $primaryColor }};
            font-size: 28px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .invoice-number {
            margin-top: 5px;
            color: #111827;
            font-size: 11px;
            font-weight: 700;
        }

        .invoice-date {
            margin-top: 2px;
            color: #6b7280;
            font-size: 9px;
        }

        .status {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-partial {
            background: #fef3c7;
            color: #92400e;
        }

        .status-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }


        /* =========================================================
           INFORMATIONS
        ========================================================= */

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 15px;
        }

        .info-box {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }

        .info-title {
            margin-bottom: 6px;
            color: {{ $primaryColor }};
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .supplier-name {
            margin: 0 0 5px;
            color: #111827;
            font-size: 13px;
            font-weight: 700;
        }

        .info-box p {
            margin: 2px 0;
            color: #6b7280;
            font-size: 9px;
        }

        .info-box strong {
            color: #374151;
        }


        /* =========================================================
           SECTION
        ========================================================= */

        .section {
            margin-top: 17px;
        }

        .section-title {
            margin-bottom: 7px;
            color: #111827;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }


        /* =========================================================
           TABLE ACHAT
        ========================================================= */

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dfe3e8;
        }

        .invoice-table th {
            padding: 8px;
            background: #f5f7fa;
            border-bottom: 1px solid #dfe3e8;
            color: #374151;
            font-size: 9px;
            font-weight: 800;
            text-align: left;
            text-transform: uppercase;
        }

        .invoice-table td {
            padding: 8px;
            border-bottom: 1px solid #edf0f2;
            color: #4b5563;
            font-size: 9px;
        }

        .invoice-table tr:last-child td {
            border-bottom: 0;
        }

        .invoice-table th:last-child,
        .invoice-table td:last-child {
            text-align: right;
        }

        .invoice-table th:nth-child(1),
        .invoice-table td:nth-child(1) {
            text-align: center;
        }

        .invoice-table th:nth-child(4),
        .invoice-table td:nth-child(4) {
            text-align: center;
        }

        .product-name {
            display: block;
            color: #111827;
            font-weight: 700;
        }

        .product-reference {
            display: block;
            margin-top: 1px;
            color: #9ca3af;
            font-size: 8px;
        }


        /* =========================================================
           BAS DE FACTURE
        ========================================================= */

        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 25px;
            margin-top: 15px;
        }

        .payment-message {
            flex: 1;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }

        .payment-message strong {
            display: block;
            margin-bottom: 4px;
            color: #374151;
            font-size: 10px;
        }

        .payment-message span {
            color: #9ca3af;
            font-size: 8px;
        }

        .payment-progress {
            margin-top: 10px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            color: #6b7280;
            font-size: 8px;
        }

        .progress {
            height: 5px;
            background: #e5e7eb;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar {
            width: {{ $paidPercentage }}%;
            height: 100%;
            background: {{ $primaryColor }};
        }

        .summary {
            width: 245px;
            border: 1px solid #dfe3e8;
            border-radius: 5px;
            overflow: hidden;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 10px;
            border-bottom: 1px solid #edf0f2;
            font-size: 9px;
        }

        .summary-row span {
            color: #6b7280;
        }

        .summary-row strong {
            color: #374151;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: {{ $primaryColor }};
            color: #ffffff;
        }

        .total-row span {
            font-size: 9px;
            font-weight: 800;
        }

        .total-row strong {
            font-size: 14px;
        }

        .paid-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 10px;
            color: #15803d;
            font-size: 9px;
            font-weight: 700;
        }

        .remaining-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 10px;
            background: #fff7ed;
            color: #c2410c;
            font-size: 9px;
            font-weight: 700;
        }


        /* =========================================================
           PAIEMENTS
        ========================================================= */

        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th {
            padding: 6px;
            background: #f5f7fa;
            color: #6b7280;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        .payments-table td {
            padding: 6px;
            border-bottom: 1px solid #edf0f2;
            color: #4b5563;
            font-size: 8px;
        }

        .payments-table th:last-child,
        .payments-table td:last-child {
            text-align: right;
        }


        /* =========================================================
           NOTES
        ========================================================= */

        .notes {
            margin-top: 13px;
            padding: 9px 11px;
            border-left: 3px solid {{ $primaryColor }};
            background: #f8fafc;
        }

        .notes-title {
            margin-bottom: 3px;
            color: #374151;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .notes-content {
            color: #6b7280;
            font-size: 8px;
        }


        /* =========================================================
           SIGNATURES
        ========================================================= */

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature {
            min-height: 75px;
            padding-top: 7px;
            border-top: 1px solid #d1d5db;
            text-align: center;
        }

        .signature-title {
            color: #374151;
            font-size: 9px;
            font-weight: 700;
        }

        .signature-name {
            margin-top: 35px;
            color: #6b7280;
            font-size: 8px;
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .invoice-footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 8px;
        }

        .invoice-footer p {
            margin: 2px 0;
        }

        .thank-you {
            color: {{ $primaryColor }};
            font-weight: 700;
        }


        /* =========================================================
           IMPRESSION A4
        ========================================================= */

        @media print {

            @page {
                size: A4;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                min-height: 297mm;
                background: #ffffff;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-actions {
                display: none !important;
            }

            .invoice-page {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                padding: 14mm;
                box-shadow: none;
            }

            .invoice-table {
                page-break-inside: auto;
            }

            .invoice-table tr {
                page-break-inside: avoid;
            }

            .info-box,
            .summary,
            .payment-message,
            .notes,
            .signatures {
                page-break-inside: avoid;
            }

            .payments-table {
                page-break-inside: avoid;
            }
        }


        /* =========================================================
           ÉCRAN
        ========================================================= */

        @media screen and (max-width: 900px) {

            .invoice-page {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 20px;
                box-shadow: none;
            }

            .invoice-actions {
                position: static;
                justify-content: flex-end;
                padding: 10px;
                background: #ffffff;
            }
        }


        @media screen and (max-width: 650px) {

            .invoice-header {
                flex-direction: column;
            }

            .invoice-heading {
                text-align: left;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .bottom-section {
                flex-direction: column;
            }

            .summary {
                width: 100%;
            }

            .signatures {
                grid-template-columns: 1fr;
            }

            .invoice-table {
                font-size: 8px;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 6px 4px;
            }
        }

    </style>

</head>


<body>


    {{-- =========================================================
         ACTIONS
    ========================================================== --}}

    <div class="invoice-actions">

        <button
            type="button"
            class="btn-print"
            onclick="window.print()"
        >
            🖨 Imprimer
        </button>

    </div>


    {{-- =========================================================
         FACTURE
    ========================================================== --}}

    <div class="invoice-page">


        {{-- =====================================================
             EN-TÊTE
        ====================================================== --}}

        <header class="invoice-header">


            <div class="company">


                {{-- LOGO --}}

                @if($company?->logo)

                    <img
                        src="{{ asset('storage/' . $company->logo) }}"
                        alt="Logo"
                        class="company-logo"
                    >

                @else

                    <div class="company-placeholder">

                        {{ strtoupper(
                            substr(
                                $company?->name ?? 'ERP',
                                0,
                                1
                            )
                        ) }}

                    </div>

                @endif


                {{-- INFORMATIONS ENTREPRISE --}}

                <div class="company-info">

                    <h1>
                        {{ $company?->name ?? 'Votre entreprise' }}
                    </h1>


                    @if($company?->slogan)

                        <div class="company-slogan">
                            {{ $company->slogan }}
                        </div>

                    @endif


                    @if($company?->address)

                        <p>
                            {{ $company->address }}
                        </p>

                    @endif


                    @if(
                        $company?->city ||
                        $company?->country
                    )

                        <p>

                            {{ $company?->city }}

                            @if(
                                $company?->city &&
                                $company?->country
                            )
                                ,
                            @endif

                            {{ $company?->country }}

                        </p>

                    @endif


                    @if($company?->phone)

                        <p>
                            Tél. : {{ $company->phone }}
                        </p>

                    @endif


                    @if($company?->email)

                        <p>
                            Email : {{ $company->email }}
                        </p>

                    @endif


                    @if($company?->tax_number)

                        <p>
                            N° contribuable :
                            {{ $company->tax_number }}
                        </p>

                    @endif


                    @if($company?->rccm)

                        <p>
                            RCCM :
                            {{ $company->rccm }}
                        </p>

                    @endif

                </div>

            </div>


            {{-- INFORMATIONS FACTURE --}}

            <div class="invoice-heading">

                <h2>
                    Achat
                </h2>


                <div class="invoice-number">
                    N° {{ $invoiceNumber }}
                </div>


                <div class="invoice-date">

                    Date :
                    {{ \Carbon\Carbon::parse(
                        $purchase->purchase_date
                    )->format($dateFormat) }}

                </div>


                @if($purchase->status === 'cancelled')

                    <span class="status status-cancelled">
                        Annulé
                    </span>

                @elseif($purchase->payment_status === 'paid')

                    <span class="status status-paid">
                        Payé
                    </span>

                @elseif($purchase->payment_status === 'partial')

                    <span class="status status-partial">
                        Partiellement payé
                    </span>

                @else

                    <span class="status status-unpaid">
                        Impayé
                    </span>

                @endif

            </div>

        </header>


        {{-- =====================================================
             FOURNISSEUR + ACHAT
        ====================================================== --}}

        <section class="info-grid">


            {{-- FOURNISSEUR --}}

            <div class="info-box">

                <div class="info-title">
                    Fournisseur
                </div>


                <h3 class="supplier-name">

                    {{ $purchase->supplier?->name ?? 'Fournisseur inconnu' }}

                </h3>


                @if($purchase->supplier?->phone)

                    <p>
                        Téléphone :
                        {{ $purchase->supplier->phone }}
                    </p>

                @endif


                @if($purchase->supplier?->email)

                    <p>
                        Email :
                        {{ $purchase->supplier->email }}
                    </p>

                @endif


                @if($purchase->supplier?->address)

                    <p>
                        Adresse :
                        {{ $purchase->supplier->address }}
                    </p>

                @endif

            </div>


            {{-- INFORMATIONS ACHAT --}}

            <div class="info-box">

                <div class="info-title">
                    Informations
                </div>


                <p>

                    <strong>
                        Responsable :
                    </strong>

                    {{ $purchase->user?->name ?? '—' }}

                </p>


                <p>

                    <strong>
                        Référence :
                    </strong>

                    {{ $purchase->reference }}

                </p>


                <p>

                    <strong>
                        Devise :
                    </strong>

                    {{ $company?->currency ?? 'XAF' }}

                    ({{ $currencySymbol }})

                </p>


                <p>

                    <strong>
                        Date d'achat :
                    </strong>

                    {{ \Carbon\Carbon::parse(
                        $purchase->purchase_date
                    )->format($dateFormat) }}

                </p>

            </div>

        </section>


        {{-- =====================================================
             DÉTAIL DE L'ACHAT
        ====================================================== --}}

        <section class="section">

            <div class="section-title">
                Détail de l'achat
            </div>


            <table class="invoice-table">


                <thead>

                    <tr>

                        <th style="width: 8%;">
                            #
                        </th>

                        <th style="width: 42%;">
                            Produit
                        </th>

                        <th style="width: 18%;">
                            Prix unitaire
                        </th>

                        <th style="width: 10%;">
                            Qté
                        </th>

                        <th style="width: 22%;">
                            Montant
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse(
                        $purchase->items
                        as $index => $item
                    )

                        <tr>


                            {{-- NUMÉRO --}}

                            <td>
                                {{ $index + 1 }}
                            </td>


                            {{-- PRODUIT --}}

                            <td>

                                <span class="product-name">

                                    {{ $item->product?->name
                                        ?? 'Produit supprimé' }}

                                </span>


                                @if($item->product?->reference)

                                    <span class="product-reference">

                                        Réf.
                                        {{ $item->product->reference }}

                                    </span>

                                @endif

                            </td>


                            {{-- PRIX UNITAIRE --}}

                            <td>

                                {{ number_format(
                                    (float) $item->unit_price,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                {{ $currencySymbol }}

                            </td>


                            {{-- QUANTITÉ --}}

                            <td>

                                {{ $item->quantity }}

                            </td>


                            {{-- MONTANT --}}

                            <td>

                                {{ number_format(
                                    (float) $item->total,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                {{ $currencySymbol }}

                            </td>


                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="5"
                                style="text-align:center;"
                            >
                                Aucun produit enregistré.
                            </td>

                        </tr>

                    @endforelse


                </tbody>

            </table>

        </section>


        {{-- =====================================================
             TOTALS
        ====================================================== --}}

        <section class="bottom-section">


            {{-- MESSAGE PAIEMENT --}}

            <div class="payment-message">

                <strong>
                    Suivi du règlement fournisseur.
                </strong>


                <span>
                    Cette facture récapitule les produits
                    achetés auprès du fournisseur.
                </span>


                <div class="payment-progress">


                    <div class="progress-label">

                        <span>
                            Paiement
                        </span>

                        <strong>
                            {{ number_format(
                                $paidPercentage,
                                0
                            ) }} %
                        </strong>

                    </div>


                    <div class="progress">

                        <div class="progress-bar"></div>

                    </div>

                </div>

            </div>


            {{-- RÉSUMÉ FINANCIER --}}

            <div class="summary">


                {{-- SOUS-TOTAL --}}

                <div class="summary-row">

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

                        {{ $currencySymbol }}

                    </strong>

                </div>


                {{-- REMISE --}}

                @if(
                    (float) $purchase->discount > 0
                )

                    <div class="summary-row">

                        <span>
                            Remise
                        </span>

                        <strong>

                            −

                            {{ number_format(
                                $purchase->discount,
                                0,
                                ',',
                                ' '
                            ) }}

                            {{ $currencySymbol }}

                        </strong>

                    </div>

                @endif


                {{-- TAXE --}}

                @if(
                    (float) $purchase->tax > 0
                )

                    <div class="summary-row">

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

                            {{ $currencySymbol }}

                        </strong>

                    </div>

                @endif


                {{-- TOTAL --}}

                <div class="total-row">

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

                        {{ $currencySymbol }}

                    </strong>

                </div>


                {{-- MONTANT PAYÉ --}}

                <div class="paid-row">

                    <span>
                        Montant payé
                    </span>

                    <strong>

                        {{ number_format(
                            $paidAmount,
                            0,
                            ',',
                            ' '
                        ) }}

                        {{ $currencySymbol }}

                    </strong>

                </div>


                {{-- RESTE À PAYER --}}

                <div class="remaining-row">

                    <span>
                        Reste à payer
                    </span>

                    <strong>

                        {{ number_format(
                            $remainingAmount,
                            0,
                            ',',
                            ' '
                        ) }}

                        {{ $currencySymbol }}

                    </strong>

                </div>

            </div>

        </section>


        {{-- =====================================================
             HISTORIQUE PAIEMENTS
        ====================================================== --}}

        @if(
            $purchase->payments
                ->where('status', '!=', 'cancelled')
                ->count()
        )

            <section class="section">

                <div class="section-title">
                    Paiements fournisseur
                </div>


                <table class="payments-table">


                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                Méthode
                            </th>

                            <th>
                                Référence
                            </th>

                            <th>
                                Montant
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @foreach(
                            $purchase->payments
                            as $payment
                        )


                            @if(
                                $payment->status !== 'cancelled'
                            )

                                <tr>


                                    <td>

                                        {{ $payment->created_at->format(
                                            $dateFormat
                                        ) }}

                                    </td>


                                    <td>

                                        {{ $payment->method_label }}

                                        @if(
                                            $payment->provider_label
                                        )

                                            —
                                            {{ $payment->provider_label }}

                                        @endif

                                    </td>


                                    <td>

                                        {{ $payment->transaction_reference
                                            ?? '—' }}

                                    </td>


                                    <td>

                                        {{ number_format(
                                            $payment->amount,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        {{ $currencySymbol }}

                                    </td>


                                </tr>

                            @endif


                        @endforeach


                    </tbody>

                </table>

            </section>

        @endif


        {{-- =====================================================
             NOTES
        ====================================================== --}}

        @if($purchase->notes)

            <div class="notes">

                <div class="notes-title">
                    Notes
                </div>

                <div class="notes-content">

                    {!! nl2br(
                        e($purchase->notes)
                    ) !!}

                </div>

            </div>

        @endif


        {{-- =====================================================
             SIGNATURES
        ====================================================== --}}

        <section class="signatures">


            <div class="signature">

                <div class="signature-title">
                    Signature du fournisseur
                </div>

                <div class="signature-name">

                    {{ $purchase->supplier?->name
                        ?? 'Fournisseur' }}

                </div>

            </div>


            <div class="signature">

                <div class="signature-title">
                    Signature du responsable
                </div>

                <div class="signature-name">

                    {{ $purchase->user?->name
                        ?? 'Responsable' }}

                </div>

            </div>


        </section>


        {{-- =====================================================
             FOOTER
        ====================================================== --}}

        <footer class="invoice-footer">


            @if($company?->invoice_footer)

                <p>

                    {!! nl2br(
                        e($company->invoice_footer)
                    ) !!}

                </p>

            @endif


            <p class="thank-you">

                Document d'achat —
                {{ $company?->name ?? 'Votre entreprise' }}

            </p>


            <p>

                {{ $company?->name ?? 'Votre entreprise' }}

                @if($company?->phone)
                    — {{ $company->phone }}
                @endif

                @if($company?->email)
                    — {{ $company->email }}
                @endif

            </p>


        </footer>


    </div>


</body>

</html>