
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport E-commerce</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #172033;
        }

        h1 {
            color: #1d4ed8;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 20px;
        }

        .stats {
            width: 100%;
            margin-bottom: 20px;
        }

        .stats td {
            width: 25%;
            padding: 12px;
            background: #eff6ff;
            border: 3px solid white;
        }

        .stats strong {
            display: block;
            margin-top: 7px;
            font-size: 14px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        .data th {
            background: #1d4ed8;
            color: white;
            padding: 9px;
            text-align: left;
        }

        .data td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <h1>Rapport E-commerce</h1>

    <div class="subtitle">
        Période :
        {{ $startDate->format('d/m/Y') }}
        au
        {{ $endDate->format('d/m/Y') }}
    </div>

    <table class="stats">
        <tr>
            <td>
                Commandes
                <strong>{{ $totalOrders }}</strong>
            </td>

            <td>
                Valeur des commandes
                <strong>{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</strong>
            </td>

            <td>
                Paiements encaissés
                <strong>{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</strong>
            </td>

            <td>
                Reste à encaisser
                <strong>{{ number_format($remainingAmount, 0, ',', ' ') }} FCFA</strong>
            </td>
        </tr>
    </table>

    <h2 class="section-title">Détail des commandes</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Client</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->reference }}</td>
                    <td>{{ $order->customer_name ?: '—' }}</td>
                    <td>{{ $order->created_at?->format('d/m/Y') }}</td>
                    <td>{{ $order->status }}</td>
                    <td class="text-right">
                        {{ number_format($order->total, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aucune commande sur cette période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="section-title">Produits les plus vendus</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Référence</th>
                <th>Quantité</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>

        <tbody>
            @forelse($topProducts as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->product_reference }}</td>
                    <td>{{ number_format($product->total_quantity, 3, ',', ' ') }}</td>
                    <td class="text-right">
                        {{ number_format($product->total_sales, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Aucun produit vendu.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
