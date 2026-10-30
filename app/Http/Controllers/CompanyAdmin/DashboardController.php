<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Service;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $company = $user->company;

        abort_if(!$company, 403, 'Aucune entreprise associée à ce compte.');

        $companyId = $company->id;

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $canViewProducts = $user->hasPermission('products.view');
        $canViewStock = $user->hasPermission('stock.view');
        $canViewClients = $user->hasPermission('clients.view');
        $canViewSales = $user->hasPermission('sales.view');
        $canViewPurchases = $user->hasPermission('purchases.view');
        $canViewPayments = $user->hasPermission('payments.view');
        $canViewFinance = $user->hasPermission('finance.view');
        $canViewServices = $user->hasPermission('services.view');
        $canViewReports = $user->hasPermission('reports.view');

        $isDirector = $user->role === 'director';

        /*
        |--------------------------------------------------------------------------
        | VALEURS PAR DEFAUT
        |--------------------------------------------------------------------------
        */

        $productsCount = 0;
        $stockQuantity = 0;
        $clientsCount = 0;
        $servicesCount = 0;

        /*
        |--------------------------------------------------------------------------
        | VENTES
        |--------------------------------------------------------------------------
        */

        $salesCount = 0;
        $cancelledSales = 0;

        $totalRevenue = 0;
        $totalPaid = 0;
        $totalRemaining = 0;

        /*
        |--------------------------------------------------------------------------
        | ACHATS
        |--------------------------------------------------------------------------
        */

        $purchasesCount = 0;
        $cancelledPurchases = 0;

        $totalPurchases = 0;
        $totalPurchasePaid = 0;
        $totalPurchaseRemaining = 0;

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS VENTES
        |--------------------------------------------------------------------------
        */

        $pendingPayments = 0;
        $pendingPaymentsCount = 0;

        $cashPayments = 0;
        $mobileMoneyPayments = 0;
        $orangeMoneyPayments = 0;
        $mtnMomoPayments = 0;
        $cardPayments = 0;
        $bankPayments = 0;
        $otherPayments = 0;

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS ACHATS
        |--------------------------------------------------------------------------
        */

        $pendingPurchasePayments = 0;
        $pendingPurchasePaymentsCount = 0;

        $purchaseCashPayments = 0;
        $purchaseMobileMoneyPayments = 0;
        $purchaseOrangeMoneyPayments = 0;
        $purchaseMtnMomoPayments = 0;
        $purchaseCardPayments = 0;
        $purchaseBankPayments = 0;
        $purchaseOtherPayments = 0;

        /*
        |--------------------------------------------------------------------------
        | PRODUITS
        |--------------------------------------------------------------------------
        */

        if ($canViewProducts) {
            $productsCount = Product::query()
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        */

        if ($canViewStock) {
            $stockQuantity = Product::query()
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->sum('stock_quantity');
        }

        /*
        |--------------------------------------------------------------------------
        | CLIENTS
        |--------------------------------------------------------------------------
        */

        if ($canViewClients) {
            $clientsCount = Client::query()
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | SERVICES
        |--------------------------------------------------------------------------
        */

        if ($canViewServices) {
            $servicesCount = Service::query()
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | VENTES
        |--------------------------------------------------------------------------
        */

        if (
            $canViewSales ||
            $canViewFinance ||
            $canViewReports
        ) {
            $salesQuery = Sale::query()
                ->where('company_id', $companyId);

            $salesCount = (clone $salesQuery)
                ->where('status', 'completed')
                ->count();

            $totalRevenue = (clone $salesQuery)
                ->where('status', 'completed')
                ->sum('total');

            $cancelledSales = (clone $salesQuery)
                ->where('status', 'cancelled')
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | ACHATS
        |--------------------------------------------------------------------------
        */

        if (
            $canViewPurchases ||
            $canViewFinance ||
            $canViewReports
        ) {
            $purchaseQuery = Purchase::query()
                ->where('company_id', $companyId);

            $purchasesCount = (clone $purchaseQuery)
                ->where('status', 'completed')
                ->count();

            $totalPurchases = (clone $purchaseQuery)
                ->where('status', 'completed')
                ->sum('total');

            $cancelledPurchases = (clone $purchaseQuery)
                ->where('status', 'cancelled')
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DES VENTES
        |--------------------------------------------------------------------------
        */

        if (
            $canViewPayments ||
            $canViewFinance ||
            $canViewReports
        ) {
            $salePaymentQuery = SalePayment::query()
                ->where('company_id', $companyId);

            $totalPaid = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->sum('amount');

            $totalRemaining = max(
                0,
                $totalRevenue - $totalPaid
            );

            $pendingPayments = (clone $salePaymentQuery)
                ->where('status', 'pending')
                ->sum('amount');

            $pendingPaymentsCount = (clone $salePaymentQuery)
                ->where('status', 'pending')
                ->count();

            $cashPayments = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'cash')
                ->sum('amount');

            $mobileMoneyPayments = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'mobile_money')
                ->sum('amount');

            $orangeMoneyPayments = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'mobile_money')
                ->where('provider', 'orange')
                ->sum('amount');

            $mtnMomoPayments = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'mobile_money')
                ->where('provider', 'mtn')
                ->sum('amount');

            $cardPayments = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'card')
                ->sum('amount');

            $bankPayments = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'bank_transfer')
                ->sum('amount');

            $otherPayments = (clone $salePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'other')
                ->sum('amount');
        }

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DES ACHATS
        |--------------------------------------------------------------------------
        */

        if (
            $canViewPurchases ||
            $canViewPayments ||
            $canViewFinance ||
            $canViewReports
        ) {
            $purchasePaymentQuery = PurchasePayment::query()
                ->where('company_id', $companyId);

            $totalPurchasePaid = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->sum('amount');

            $totalPurchaseRemaining = max(
                0,
                $totalPurchases - $totalPurchasePaid
            );

            $pendingPurchasePayments = (clone $purchasePaymentQuery)
                ->where('status', 'pending')
                ->sum('amount');

            $pendingPurchasePaymentsCount = (clone $purchasePaymentQuery)
                ->where('status', 'pending')
                ->count();

            $purchaseCashPayments = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'cash')
                ->sum('amount');

            $purchaseMobileMoneyPayments = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'mobile_money')
                ->sum('amount');

            $purchaseOrangeMoneyPayments = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'mobile_money')
                ->where('provider', 'orange')
                ->sum('amount');

            $purchaseMtnMomoPayments = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'mobile_money')
                ->where('provider', 'mtn')
                ->sum('amount');

            $purchaseCardPayments = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'card')
                ->sum('amount');

            $purchaseBankPayments = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'bank_transfer')
                ->sum('amount');

            $purchaseOtherPayments = (clone $purchasePaymentQuery)
                ->where('status', 'completed')
                ->where('method', 'other')
                ->sum('amount');
        }

        /*
        |--------------------------------------------------------------------------
        | FLUX DE TRESORERIE
        |--------------------------------------------------------------------------
        */

        $netCashFlow = $totalPaid - $totalPurchasePaid;

        /*
        |--------------------------------------------------------------------------
        | ALERTES STOCK
        |--------------------------------------------------------------------------
        */

        $stockAlertThreshold = $company->stock_alert_threshold ?? 5;

        $lowStockProducts = collect();

        if ($canViewStock) {
            $lowStockProducts = Product::query()
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->where('stock_quantity', '<=', $stockAlertThreshold)
                ->orderBy('stock_quantity')
                ->limit(6)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | DERNIERES VENTES
        |--------------------------------------------------------------------------
        */

        $recentSales = collect();

        if ($canViewSales) {
            $recentSales = Sale::query()
                ->where('company_id', $companyId)
                ->with(['client', 'user'])
                ->latest()
                ->limit(6)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | DERNIERS ACHATS
        |--------------------------------------------------------------------------
        */

        $recentPurchases = collect();

        if ($canViewPurchases) {
            $recentPurchases = Purchase::query()
                ->where('company_id', $companyId)
                ->with(['supplier', 'user'])
                ->latest()
                ->limit(6)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | DERNIERS PAIEMENTS CLIENTS
        |--------------------------------------------------------------------------
        */

        $recentPayments = collect();

        if ($canViewPayments) {
            $recentPayments = SalePayment::query()
                ->where('company_id', $companyId)
                ->with('sale')
                ->latest()
                ->limit(6)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | GRAPHIQUE MENSUEL
        |--------------------------------------------------------------------------
        */

        $monthlyLabels = [];
        $monthlyRevenue = [];
        $monthlyPurchases = [];

        for ($i = 5; $i >= 0; $i--) {

            $date = Carbon::now()->subMonths($i);

            $monthlyLabels[] = $date->translatedFormat('M');

            $monthlyRevenue[] = Sale::query()
                ->where('company_id', $companyId)
                ->where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total');

            $monthlyPurchases[] = Purchase::query()
                ->where('company_id', $companyId)
                ->where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total');
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIVITE DES 7 DERNIERS JOURS
        |--------------------------------------------------------------------------
        */

        $dailyLabels = [];
        $dailySales = [];
        $dailyPurchases = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $dailyLabels[] = $date->translatedFormat('D d');

            $dailySales[] = Sale::query()
                ->where('company_id', $companyId)
                ->where('status', 'completed')
                ->whereDate('created_at', $date)
                ->count();

            $dailyPurchases[] = Purchase::query()
                ->where('company_id', $companyId)
                ->where('status', 'completed')
                ->whereDate('created_at', $date)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | REPARTITION DES VENTES
        |--------------------------------------------------------------------------
        */

        $salesTotal = $salesCount + $cancelledSales;

        $completedSalesPercentage = $salesTotal > 0
            ? round(($salesCount / $salesTotal) * 100)
            : 0;

        $cancelledSalesPercentage = $salesTotal > 0
            ? round(($cancelledSales / $salesTotal) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | REPARTITION DES ACHATS
        |--------------------------------------------------------------------------
        */

        $purchasesTotal = $purchasesCount + $cancelledPurchases;

        $completedPurchasesPercentage = $purchasesTotal > 0
            ? round(($purchasesCount / $purchasesTotal) * 100)
            : 0;

        $cancelledPurchasesPercentage = $purchasesTotal > 0
            ? round(($cancelledPurchases / $purchasesTotal) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | REPARTITION PAIEMENTS
        |--------------------------------------------------------------------------
        */

        $paymentDistribution = [
            'Espèces' => $cashPayments,
            'Mobile Money' => $mobileMoneyPayments,
            'Carte' => $cardPayments,
            'Virement' => $bankPayments,
            'Autres' => $otherPayments,
        ];

        /*
        |--------------------------------------------------------------------------
        | VUE
        |--------------------------------------------------------------------------
        */

        return view('companyadmin.dashboard', compact(
            'company',
            'user',

            'canViewProducts',
            'canViewStock',
            'canViewClients',
            'canViewSales',
            'canViewPurchases',
            'canViewPayments',
            'canViewFinance',
            'canViewServices',
            'canViewReports',

            'isDirector',

            'productsCount',
            'stockQuantity',
            'clientsCount',
            'servicesCount',

            'salesCount',
            'cancelledSales',
            'totalRevenue',
            'totalPaid',
            'totalRemaining',

            'purchasesCount',
            'cancelledPurchases',
            'totalPurchases',
            'totalPurchasePaid',
            'totalPurchaseRemaining',

            'pendingPayments',
            'pendingPaymentsCount',

            'cashPayments',
            'mobileMoneyPayments',
            'orangeMoneyPayments',
            'mtnMomoPayments',
            'cardPayments',
            'bankPayments',
            'otherPayments',

            'pendingPurchasePayments',
            'pendingPurchasePaymentsCount',

            'purchaseCashPayments',
            'purchaseMobileMoneyPayments',
            'purchaseOrangeMoneyPayments',
            'purchaseMtnMomoPayments',
            'purchaseCardPayments',
            'purchaseBankPayments',
            'purchaseOtherPayments',

            'netCashFlow',

            'stockAlertThreshold',
            'lowStockProducts',

            'recentSales',
            'recentPurchases',
            'recentPayments',

            'monthlyLabels',
            'monthlyRevenue',
            'monthlyPurchases',

            'dailyLabels',
            'dailySales',
            'dailyPurchases',

            'completedSalesPercentage',
            'cancelledSalesPercentage',

            'completedPurchasesPercentage',
            'cancelledPurchasesPercentage',

            'paymentDistribution'
        ));
    }
}