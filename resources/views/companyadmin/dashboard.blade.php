@extends('layouts.company')

@section('title', 'Tableau de bord')

@section('content')

<div class="company-dashboard">

    {{-- =========================================================
        HERO
    ========================================================== --}}

    <div class="dashboard-hero">

        <div class="dashboard-hero-content">

            <span class="dashboard-kicker">
                TABLEAU DE BORD
            </span>

            <h1>
                Bonjour {{ $user->name }} 👋
            </h1>

            <p>
                Bienvenue dans l'espace de gestion
                <strong>{{ $company->name }}</strong>.
            </p>

            @if($isDirector)
                <div class="dashboard-supervision">
                    Supervision générale
                </div>
            @endif

        </div>

        <div class="dashboard-date">
            {{ now()->translatedFormat('l d F Y') }}
        </div>

    </div>


    {{-- =========================================================
        STATISTIQUES GENERALES
    ========================================================== --}}

    <div class="dashboard-stats">

        @if($canViewProducts)

            <div class="dashboard-stat-card">

                <div class="dashboard-stat-icon">
                    📦
                </div>

                <div class="dashboard-stat-content">

                    <span>Produits</span>

                    <strong
                        class="dashboard-counter"
                        data-value="{{ $productsCount }}"
                    >
                        0
                    </strong>

                    <small>
                        Produits actifs
                    </small>

                </div>

            </div>

        @endif


        @if($canViewStock)

            <div class="dashboard-stat-card">

                <div class="dashboard-stat-icon">
                    🏷️
                </div>

                <div class="dashboard-stat-content">

                    <span>Stock</span>

                    <strong
                        class="dashboard-counter"
                        data-value="{{ $stockQuantity }}"
                    >
                        0
                    </strong>

                    <small>
                        Quantité disponible
                    </small>

                </div>

            </div>

        @endif


        @if($canViewClients)

            <div class="dashboard-stat-card">

                <div class="dashboard-stat-icon">
                    👥
                </div>

                <div class="dashboard-stat-content">

                    <span>Clients</span>

                    <strong
                        class="dashboard-counter"
                        data-value="{{ $clientsCount }}"
                    >
                        0
                    </strong>

                    <small>
                        Clients actifs
                    </small>

                </div>

            </div>

        @endif


        @if($canViewServices)

            <div class="dashboard-stat-card">

                <div class="dashboard-stat-icon">
                    🛠️
                </div>

                <div class="dashboard-stat-content">

                    <span>Services</span>

                    <strong
                        class="dashboard-counter"
                        data-value="{{ $servicesCount }}"
                    >
                        0
                    </strong>

                    <small>
                        Services actifs
                    </small>

                </div>

            </div>

        @endif


        {{-- VENTES --}}

        @if($canViewSales || $canViewFinance || $canViewReports)

            <div class="dashboard-stat-card stat-sales">

                <div class="dashboard-stat-icon">
                    💰
                </div>

                <div class="dashboard-stat-content">

                    <span>Chiffre d'affaires</span>

                    <strong>
                        {{ number_format($totalRevenue, 0, ',', ' ') }}
                        FCFA
                    </strong>

                    <small>
                        {{ $salesCount }} vente(s) terminée(s)
                    </small>

                </div>

            </div>

        @endif


        {{-- ACHATS --}}

        @if($canViewPurchases || $canViewFinance || $canViewReports)

            <div class="dashboard-stat-card stat-purchases">

                <div class="dashboard-stat-icon">
                    🛒
                </div>

                <div class="dashboard-stat-content">

                    <span>Total achats</span>

                    <strong>
                        {{ number_format($totalPurchases, 0, ',', ' ') }}
                        FCFA
                    </strong>

                    <small>
                        {{ $purchasesCount }} achat(s) terminé(s)
                    </small>

                </div>

            </div>

        @endif


        {{-- ENCAISSEMENTS --}}

        @if($canViewPayments || $canViewFinance || $canViewReports)

            <div class="dashboard-stat-card stat-paid">

                <div class="dashboard-stat-icon">
                    💳
                </div>

                <div class="dashboard-stat-content">

                    <span>Encaissé</span>

                    <strong>
                        {{ number_format($totalPaid, 0, ',', ' ') }}
                        FCFA
                    </strong>

                    <small>
                        Paiements clients
                    </small>

                </div>

            </div>

        @endif


        {{-- PAIEMENTS FOURNISSEURS --}}

        @if($canViewPurchases || $canViewFinance || $canViewReports)

            <div class="dashboard-stat-card stat-purchase-paid">

                <div class="dashboard-stat-icon">
                    💸
                </div>

                <div class="dashboard-stat-content">

                    <span>Payé fournisseurs</span>

                    <strong>
                        {{ number_format($totalPurchasePaid, 0, ',', ' ') }}
                        FCFA
                    </strong>

                    <small>
                        Paiements achats
                    </small>

                </div>

            </div>

        @endif


        {{-- A ENCAISSER --}}

        @if($canViewSales || $canViewFinance || $canViewReports)

            <div class="dashboard-stat-card stat-warning">

                <div class="dashboard-stat-icon">
                    ⏳
                </div>

                <div class="dashboard-stat-content">

                    <span>À encaisser</span>

                    <strong>
                        {{ number_format($totalRemaining, 0, ',', ' ') }}
                        FCFA
                    </strong>

                    <small>
                        Créances clients
                    </small>

                </div>

            </div>

        @endif


        {{-- A PAYER --}}

        @if($canViewPurchases || $canViewFinance || $canViewReports)

            <div class="dashboard-stat-card stat-danger">

                <div class="dashboard-stat-icon">
                    📋
                </div>

                <div class="dashboard-stat-content">

                    <span>À payer</span>

                    <strong>
                        {{ number_format($totalPurchaseRemaining, 0, ',', ' ') }}
                        FCFA
                    </strong>

                    <small>
                        Dettes fournisseurs
                    </small>

                </div>

            </div>

        @endif


        {{-- FLUX NET --}}

        @if($canViewFinance || $canViewReports)

            <div class="dashboard-stat-card stat-net">

                <div class="dashboard-stat-icon">
                    📊
                </div>

                <div class="dashboard-stat-content">

                    <span>Flux net</span>

                    <strong>
                        {{ number_format($netCashFlow, 0, ',', ' ') }}
                        FCFA
                    </strong>

                    <small>
                        Encaissements - paiements
                    </small>

                </div>

            </div>

        @endif

    </div>


    {{-- =========================================================
        GRAPHIQUES
    ========================================================== --}}

    @if($canViewSales || $canViewPurchases || $canViewFinance || $canViewReports)

        <div class="dashboard-charts">

            {{-- VENTES VS ACHATS --}}

            <div class="dashboard-card dashboard-chart-card">

                <div class="dashboard-card-header">

                    <div>
                        <span class="dashboard-card-kicker">
                            PERFORMANCE
                        </span>

                        <h2>
                            Ventes vs Achats
                        </h2>

                        <p>
                            Évolution des six derniers mois
                        </p>
                    </div>

                    <div class="chart-legend-inline">

                        <span>
                            <i class="chart-dot chart-dot-sales"></i>
                            Ventes
                        </span>

                        <span>
                            <i class="chart-dot chart-dot-purchases"></i>
                            Achats
                        </span>

                    </div>

                </div>

                <div class="dashboard-chart-wrapper">

                    <canvas
                        id="revenueChart"
                    ></canvas>

                </div>

            </div>


            {{-- ACTIVITE --}}

            <div class="dashboard-card dashboard-chart-card">

                <div class="dashboard-card-header">

                    <div>
                        <span class="dashboard-card-kicker">
                            ACTIVITÉ
                        </span>

                        <h2>
                            Ventes vs Achats
                        </h2>

                        <p>
                            Activité des sept derniers jours
                        </p>
                    </div>

                    <div class="chart-legend-inline">

                        <span>
                            <i class="chart-dot chart-dot-sales"></i>
                            Ventes
                        </span>

                        <span>
                            <i class="chart-dot chart-dot-purchases"></i>
                            Achats
                        </span>

                    </div>

                </div>

                <div class="dashboard-chart-wrapper">

                    <canvas
                        id="salesChart"
                    ></canvas>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        BLOCS BAS
    ========================================================== --}}

    <div class="dashboard-bottom-grid">


        {{-- STOCK FAIBLE --}}

        @if($canViewStock)

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="dashboard-card-kicker">
                            STOCK
                        </span>

                        <h2>
                            Stock faible
                        </h2>

                    </div>

                    <span class="dashboard-card-badge">
                        {{ $lowStockProducts->count() }}
                    </span>

                </div>


                @if($lowStockProducts->count())

                    <div class="dashboard-list">

                        @foreach($lowStockProducts as $product)

                            <div class="dashboard-list-item">

                                <div class="dashboard-list-main">

                                    <strong>
                                        {{ $product->name }}
                                    </strong>

                                    <span>
                                        Réf. {{ $product->reference }}
                                    </span>

                                </div>

                                <div class="dashboard-stock-value">

                                    <strong>
                                        {{ $product->stock_quantity }}
                                    </strong>

                                    <span>
                                        restant(s)
                                    </span>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="dashboard-empty">
                        Aucun produit en stock faible.
                    </div>

                @endif

            </div>

        @endif


        {{-- REPARTITION VENTES --}}

        @if($canViewSales || $canViewReports)

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="dashboard-card-kicker">
                            VENTES
                        </span>

                        <h2>
                            Répartition
                        </h2>

                    </div>

                </div>


                <div class="dashboard-distribution">

                    <div class="distribution-item">

                        <div class="distribution-circle success">
                            {{ $completedSalesPercentage }}%
                        </div>

                        <div>

                            <strong>
                                Ventes terminées
                            </strong>

                            <span>
                                {{ $salesCount }}
                            </span>

                        </div>

                    </div>


                    <div class="distribution-item">

                        <div class="distribution-circle danger">
                            {{ $cancelledSalesPercentage }}%
                        </div>

                        <div>

                            <strong>
                                Ventes annulées
                            </strong>

                            <span>
                                {{ $cancelledSales }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        @endif


        {{-- REPARTITION ACHATS --}}

        @if($canViewPurchases || $canViewReports)

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="dashboard-card-kicker">
                            ACHATS
                        </span>

                        <h2>
                            Répartition
                        </h2>

                    </div>

                </div>


                <div class="dashboard-distribution">

                    <div class="distribution-item">

                        <div class="distribution-circle success">
                            {{ $completedPurchasesPercentage }}%
                        </div>

                        <div>

                            <strong>
                                Achats terminés
                            </strong>

                            <span>
                                {{ $purchasesCount }}
                            </span>

                        </div>

                    </div>


                    <div class="distribution-item">

                        <div class="distribution-circle danger">
                            {{ $cancelledPurchasesPercentage }}%
                        </div>

                        <div>

                            <strong>
                                Achats annulés
                            </strong>

                            <span>
                                {{ $cancelledPurchases }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        @endif


        {{-- DERNIERES VENTES --}}

        @if($canViewSales)

            <div class="dashboard-card dashboard-recent-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="dashboard-card-kicker">
                            COMMERCIAL
                        </span>

                        <h2>
                            Dernières ventes
                        </h2>

                    </div>

                    <a
                        href="{{ route('company.sales.index') }}"
                        class="dashboard-card-link"
                    >
                        Voir tout →
                    </a>

                </div>


                @if($recentSales->count())

                    <div class="dashboard-list">

                        @foreach($recentSales as $sale)

                            <a
                                href="{{ route('company.sales.show', $sale) }}"
                                class="dashboard-list-item dashboard-link-item"
                            >

                                <div class="dashboard-list-main">

                                    <strong>
                                        Vente #{{ $sale->id }}
                                    </strong>

                                    <span>
                                        {{ $sale->client->name ?? 'Client comptant' }}
                                    </span>

                                </div>

                                <div class="dashboard-list-value">

                                    <strong>
                                        {{ number_format($sale->total, 0, ',', ' ') }}
                                        FCFA
                                    </strong>

                                    <span>
                                        {{ $sale->created_at->format('d/m/Y') }}
                                    </span>

                                </div>

                            </a>

                        @endforeach

                    </div>

                @else

                    <div class="dashboard-empty">
                        Aucune vente récente.
                    </div>

                @endif

            </div>

        @endif


        {{-- DERNIERS ACHATS --}}

        @if($canViewPurchases)

            <div class="dashboard-card dashboard-recent-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="dashboard-card-kicker">
                            APPROVISIONNEMENT
                        </span>

                        <h2>
                            Derniers achats
                        </h2>

                    </div>

                    <a
                        href="{{ route('company.purchases.index') }}"
                        class="dashboard-card-link"
                    >
                        Voir tout →
                    </a>

                </div>


                @if($recentPurchases->count())

                    <div class="dashboard-list">

                        @foreach($recentPurchases as $purchase)

                            <a
                                href="{{ route('company.purchases.show', $purchase) }}"
                                class="dashboard-list-item dashboard-link-item"
                            >

                                <div class="dashboard-list-main">

                                    <strong>
                                        Achat #{{ $purchase->id }}
                                    </strong>

                                    <span>
                                        {{ $purchase->supplier->name ?? 'Fournisseur non renseigné' }}
                                    </span>

                                </div>

                                <div class="dashboard-list-value">

                                    <strong>
                                        {{ number_format($purchase->total, 0, ',', ' ') }}
                                        FCFA
                                    </strong>

                                    <span>
                                        {{ $purchase->created_at->format('d/m/Y') }}
                                    </span>

                                </div>

                            </a>

                        @endforeach

                    </div>

                @else

                    <div class="dashboard-empty">
                        Aucun achat récent.
                    </div>

                @endif

            </div>

        @endif


    </div>

</div>


{{-- =============================================================
    JAVASCRIPT DU DASHBOARD
============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | COMPTEURS
    |--------------------------------------------------------------------------
    */

    const counters = document.querySelectorAll('.dashboard-counter');

    counters.forEach(function (counter) {

        const target = Number(counter.dataset.value || 0);

        let current = 0;

        const duration = 900;

        const start = performance.now();

        function animate(time) {

            const progress = Math.min(
                (time - start) / duration,
                1
            );

            current = Math.floor(
                progress * target
            );

            counter.textContent =
                current.toLocaleString('fr-FR');

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }

        requestAnimationFrame(animate);

    });


    /*
    |--------------------------------------------------------------------------
    | DONNEES GRAPHIQUES
    |--------------------------------------------------------------------------
    */

    const monthlyLabels = @json($monthlyLabels);
    const monthlyRevenue = @json($monthlyRevenue);
    const monthlyPurchases = @json($monthlyPurchases);

    const dailyLabels = @json($dailyLabels);
    const dailySales = @json($dailySales);
    const dailyPurchases = @json($dailyPurchases);


    /*
    |--------------------------------------------------------------------------
    | COULEURS
    |--------------------------------------------------------------------------
    */

    const salesColor = '#315bea';
    const purchaseColor = '#f59e0b';
    const gridColor = '#e5e7eb';
    const textColor = '#64748b';


    /*
    |--------------------------------------------------------------------------
    | OUTILS CANVAS
    |--------------------------------------------------------------------------
    */

    function roundRect(
        ctx,
        x,
        y,
        width,
        height,
        radius
    ) {

        const r = Math.min(
            radius,
            width / 2,
            height / 2
        );

        ctx.beginPath();

        ctx.moveTo(x + r, y);

        ctx.arcTo(
            x + width,
            y,
            x + width,
            y + height,
            r
        );

        ctx.arcTo(
            x + width,
            y + height,
            x,
            y + height,
            r
        );

        ctx.arcTo(
            x,
            y + height,
            x,
            y,
            r
        );

        ctx.arcTo(
            x,
            y,
            x + width,
            y,
            r
        );

        ctx.closePath();

    }


    /*
    |--------------------------------------------------------------------------
    | GRAPHIQUE BARRES VENTES / ACHATS
    |--------------------------------------------------------------------------
    */

    function drawGroupedBarChart(
        canvas,
        labels,
        sales,
        purchases
    ) {

        if (!canvas) {
            return;
        }

        const ctx = canvas.getContext('2d');

        const ratio = window.devicePixelRatio || 1;

        const width = canvas.clientWidth || 600;

        const height = 300;

        canvas.width = width * ratio;
        canvas.height = height * ratio;

        ctx.setTransform(
            ratio,
            0,
            0,
            ratio,
            0,
            0
        );

        ctx.clearRect(
            0,
            0,
            width,
            height
        );

        const padding = {
            top: 20,
            right: 20,
            bottom: 40,
            left: 55
        };

        const chartWidth =
            width -
            padding.left -
            padding.right;

        const chartHeight =
            height -
            padding.top -
            padding.bottom;

        const allValues = [
            ...sales,
            ...purchases
        ];

        const maxValue =
            Math.max(...allValues, 1);

        const steps = 5;

        ctx.font = '11px Arial';

        /*
        |--------------------------------------------------------------------------
        | GRILLE
        |--------------------------------------------------------------------------
        */

        for (let i = 0; i <= steps; i++) {

            const value =
                maxValue -
                (maxValue / steps) * i;

            const y =
                padding.top +
                (chartHeight / steps) * i;

            ctx.beginPath();

            ctx.moveTo(
                padding.left,
                y
            );

            ctx.lineTo(
                width - padding.right,
                y
            );

            ctx.strokeStyle = gridColor;

            ctx.stroke();

            ctx.fillStyle = textColor;

            ctx.textAlign = 'right';

            ctx.fillText(
                Math.round(value).toLocaleString('fr-FR'),
                padding.left - 8,
                y + 4
            );

        }


        /*
        |--------------------------------------------------------------------------
        | BARRES
        |--------------------------------------------------------------------------
        */

        const groupWidth =
            chartWidth / labels.length;

        const barWidth =
            Math.min(
                24,
                groupWidth * 0.25
            );

        labels.forEach(function (label, index) {

            const center =
                padding.left +
                groupWidth * index +
                groupWidth / 2;

            const salesHeight =
                (sales[index] / maxValue) *
                chartHeight;

            const purchaseHeight =
                (purchases[index] / maxValue) *
                chartHeight;

            const salesX =
                center -
                barWidth -
                3;

            const purchaseX =
                center +
                3;

            const salesY =
                padding.top +
                chartHeight -
                salesHeight;

            const purchaseY =
                padding.top +
                chartHeight -
                purchaseHeight;


            ctx.fillStyle = salesColor;

            roundRect(
                ctx,
                salesX,
                salesY,
                barWidth,
                salesHeight,
                5
            );

            ctx.fill();


            ctx.fillStyle = purchaseColor;

            roundRect(
                ctx,
                purchaseX,
                purchaseY,
                barWidth,
                purchaseHeight,
                5
            );

            ctx.fill();


            ctx.fillStyle = textColor;

            ctx.textAlign = 'center';

            ctx.fillText(
                label,
                center,
                height - 14
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | GRAPHIQUE LIGNES VENTES / ACHATS
    |--------------------------------------------------------------------------
    */

    function drawDualLineChart(
        canvas,
        labels,
        sales,
        purchases
    ) {

        if (!canvas) {
            return;
        }

        const ctx = canvas.getContext('2d');

        const ratio =
            window.devicePixelRatio || 1;

        const width =
            canvas.clientWidth || 600;

        const height = 300;

        canvas.width =
            width * ratio;

        canvas.height =
            height * ratio;

        ctx.setTransform(
            ratio,
            0,
            0,
            ratio,
            0,
            0
        );

        ctx.clearRect(
            0,
            0,
            width,
            height
        );

        const padding = {
            top: 20,
            right: 20,
            bottom: 40,
            left: 45
        };

        const chartWidth =
            width -
            padding.left -
            padding.right;

        const chartHeight =
            height -
            padding.top -
            padding.bottom;

        const maxValue =
            Math.max(
                ...sales,
                ...purchases,
                1
            );

        /*
        |--------------------------------------------------------------------------
        | GRILLE
        |--------------------------------------------------------------------------
        */

        const steps = 5;

        ctx.font = '11px Arial';

        for (let i = 0; i <= steps; i++) {

            const value =
                maxValue -
                (maxValue / steps) * i;

            const y =
                padding.top +
                (chartHeight / steps) * i;

            ctx.beginPath();

            ctx.moveTo(
                padding.left,
                y
            );

            ctx.lineTo(
                width - padding.right,
                y
            );

            ctx.strokeStyle = gridColor;

            ctx.stroke();

            ctx.fillStyle = textColor;

            ctx.textAlign = 'right';

            ctx.fillText(
                Math.round(value),
                padding.left - 8,
                y + 4
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FONCTION POUR DESSINER UNE LIGNE
        |--------------------------------------------------------------------------
        */

        function drawLine(
            values,
            color
        ) {

            if (!values.length) {
                return;
            }

            const points = [];

            values.forEach(function (
                value,
                index
            ) {

                const x =
                    padding.left +
                    (
                        chartWidth /
                        Math.max(values.length - 1, 1)
                    ) *
                    index;

                const y =
                    padding.top +
                    chartHeight -
                    (
                        value /
                        maxValue
                    ) *
                    chartHeight;

                points.push({
                    x: x,
                    y: y
                });

            });


            /*
            |--------------------------------------------------------------------------
            | LIGNE
            |--------------------------------------------------------------------------
            */

            ctx.beginPath();

            points.forEach(function (
                point,
                index
            ) {

                if (index === 0) {

                    ctx.moveTo(
                        point.x,
                        point.y
                    );

                } else {

                    ctx.lineTo(
                        point.x,
                        point.y
                    );

                }

            });

            ctx.strokeStyle = color;

            ctx.lineWidth = 3;

            ctx.lineJoin = 'round';

            ctx.lineCap = 'round';

            ctx.stroke();


            /*
            |--------------------------------------------------------------------------
            | POINTS
            |--------------------------------------------------------------------------
            */

            points.forEach(function (
                point
            ) {

                ctx.beginPath();

                ctx.arc(
                    point.x,
                    point.y,
                    4,
                    0,
                    Math.PI * 2
                );

                ctx.fillStyle = '#ffffff';

                ctx.fill();

                ctx.beginPath();

                ctx.arc(
                    point.x,
                    point.y,
                    3,
                    0,
                    Math.PI * 2
                );

                ctx.fillStyle = color;

                ctx.fill();

            });

        }


        drawLine(
            sales,
            salesColor
        );

        drawLine(
            purchases,
            purchaseColor
        );


        /*
        |--------------------------------------------------------------------------
        | LABELS
        |--------------------------------------------------------------------------
        */

        labels.forEach(function (
            label,
            index
        ) {

            const x =
                padding.left +
                (
                    chartWidth /
                    Math.max(labels.length - 1, 1)
                ) *
                index;

            ctx.fillStyle = textColor;

            ctx.textAlign = 'center';

            ctx.font = '11px Arial';

            ctx.fillText(
                label,
                x,
                height - 14
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | INITIALISATION
    |--------------------------------------------------------------------------
    */

    function drawCharts() {

        drawGroupedBarChart(
            document.getElementById(
                'revenueChart'
            ),
            monthlyLabels,
            monthlyRevenue,
            monthlyPurchases
        );

        drawDualLineChart(
            document.getElementById(
                'salesChart'
            ),
            dailyLabels,
            dailySales,
            dailyPurchases
        );

    }


    drawCharts();


    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    let resizeTimeout;

    window.addEventListener(
        'resize',
        function () {

            clearTimeout(
                resizeTimeout
            );

            resizeTimeout = setTimeout(
                drawCharts,
                150
            );

        }
    );

});

</script>

@endsection