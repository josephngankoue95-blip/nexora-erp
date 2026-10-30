<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        /*
        |--------------------------------------------------------------------------
        | PÉRIODE
        |--------------------------------------------------------------------------
        */

        $period = $request->input('period', 'month');

        switch ($period) {

            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;

            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;

            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;

            case 'month':
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $period = 'month';
                break;
        }


        /*
        |--------------------------------------------------------------------------
        | VENTES
        |--------------------------------------------------------------------------
        */

        $salesQuery = Sale::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $salesCount = $salesQuery->count();

        $salesTotal = (float) $salesQuery->sum('total');

        $salesSubtotal = (float) $salesQuery->sum('subtotal');

        $salesDiscount = (float) $salesQuery->sum('discount');

        $salesTax = (float) $salesQuery->sum('tax');


        /*
        |--------------------------------------------------------------------------
        | ACHATS
        |--------------------------------------------------------------------------
        */

        $purchasesQuery = Purchase::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $purchasesCount = $purchasesQuery->count();

        $purchasesTotal = (float) $purchasesQuery->sum('total');

        $purchasesSubtotal = (float) $purchasesQuery->sum('subtotal');

        $purchasesDiscount = (float) $purchasesQuery->sum('discount');

        $purchasesTax = (float) $purchasesQuery->sum('tax');


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DES VENTES
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = SalePayment::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $paymentsTotal = (float) $paymentsQuery->sum('amount');

        $cashTotal = (float) (clone $paymentsQuery)
            ->where('method', 'cash')
            ->sum('amount');

        $mobileMoneyTotal = (float) (clone $paymentsQuery)
            ->where('method', 'mobile_money')
            ->sum('amount');

        $cardTotal = (float) (clone $paymentsQuery)
            ->where('method', 'card')
            ->sum('amount');

        $bankTransferTotal = (float) (clone $paymentsQuery)
            ->where('method', 'bank_transfer')
            ->sum('amount');

        $otherPaymentsTotal = (float) (clone $paymentsQuery)
            ->where('method', 'other')
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DES ACHATS
        |--------------------------------------------------------------------------
        */

        $purchasePaymentsQuery = PurchasePayment::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $purchasePaymentsTotal = (float) $purchasePaymentsQuery->sum('amount');

        $purchaseCashTotal = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'cash')
            ->sum('amount');

        $purchaseMobileMoneyTotal = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'mobile_money')
            ->sum('amount');

        $purchaseCardTotal = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'card')
            ->sum('amount');

        $purchaseBankTransferTotal = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'bank_transfer')
            ->sum('amount');

        $purchaseOtherPaymentsTotal = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'other')
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | SOLDE NET
        |--------------------------------------------------------------------------
        */

        $netCashFlow = $paymentsTotal - $purchasePaymentsTotal;


        /*
        |--------------------------------------------------------------------------
        | MOBILE MONEY
        |--------------------------------------------------------------------------
        */

        $orangeMoneyTotal = (float) (clone $paymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'orange_money')
            ->sum('amount');

        $mtnMomoTotal = (float) (clone $paymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'mtn_momo')
            ->sum('amount');

        $purchaseOrangeMoneyTotal = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'orange_money')
            ->sum('amount');

        $purchaseMtnMomoTotal = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'mtn_momo')
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS EN ATTENTE
        |--------------------------------------------------------------------------
        */

        $pendingSalePayments = (float) SalePayment::where('company_id', $companyId)
            ->where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $pendingPurchasePayments = (float) PurchasePayment::where('company_id', $companyId)
            ->where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $pendingPayments = $pendingSalePayments + $pendingPurchasePayments;


        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        */

        $products = Product::where('company_id', $companyId)
            ->get();

        $productsCount = $products->count();

        $stockQuantity = (int) $products->sum('stock_quantity');

        $stockValuePurchase = (float) $products->sum(
            fn ($product) =>
                $product->stock_quantity * $product->purchase_price
        );

        $stockValueSelling = (float) $products->sum(
            fn ($product) =>
                $product->stock_quantity * $product->selling_price
        );

        $lowStock = $products->filter(
            fn ($product) => $product->isLowStock()
        )->count();

        $outOfStock = $products->filter(
            fn ($product) => $product->isOutOfStock()
        )->count();


        /*
        |--------------------------------------------------------------------------
        | MOUVEMENTS STOCK
        |--------------------------------------------------------------------------
        */

        $stockEntries = (int) StockMovement::where('company_id', $companyId)
            ->where('type', 'entry')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('quantity');

        $stockExits = (int) StockMovement::where('company_id', $companyId)
            ->where('type', 'exit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('quantity');

        $stockAdjustments = (int) StockMovement::where('company_id', $companyId)
            ->where('type', 'adjustment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('quantity');


        /*
        |--------------------------------------------------------------------------
        | CRÉANCES CLIENTS
        |--------------------------------------------------------------------------
        */

        $completedSales = Sale::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['id', 'total']);

        $payments = SalePayment::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['sale_id', 'amount']);

        $paidBySale = $payments
            ->groupBy('sale_id')
            ->map(fn ($items) => (float) $items->sum('amount'));

        $receivablesTotal = 0;

        foreach ($completedSales as $sale) {

            $paid = $paidBySale->get($sale->id, 0);

            $receivablesTotal += max(
                0,
                (float) $sale->total - $paid
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DETTES FOURNISSEURS
        |--------------------------------------------------------------------------
        */

        $completedPurchases = Purchase::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['id', 'total']);

        $purchasePayments = PurchasePayment::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['purchase_id', 'amount']);

        $paidByPurchase = $purchasePayments
            ->groupBy('purchase_id')
            ->map(fn ($items) => (float) $items->sum('amount'));

        $payablesTotal = 0;

        foreach ($completedPurchases as $purchase) {

            $paid = $paidByPurchase->get($purchase->id, 0);

            $payablesTotal += max(
                0,
                (float) $purchase->total - $paid
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MEILLEURS PRODUITS
        |--------------------------------------------------------------------------
        */

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.company_id', $companyId)
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as quantity'),
                DB::raw('SUM(sale_items.total) as total')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity')
            ->limit(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | VENTES PAR JOUR
        |--------------------------------------------------------------------------
        */

        $dailySales = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ACHATS PAR JOUR
        |--------------------------------------------------------------------------
        */

        $dailyPurchases = Purchase::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS RÉCENTS — VENTES
        |--------------------------------------------------------------------------
        */

        $recentPayments = SalePayment::with([
                'sale',
                'user'
            ])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS RÉCENTS — ACHATS
        |--------------------------------------------------------------------------
        */

        $recentPurchasePayments = PurchasePayment::with([
                'purchase',
                'user'
            ])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(10)
            ->get();


        return view('company.reports.index', compact(

            'period',
            'startDate',
            'endDate',

            'salesCount',
            'salesTotal',
            'salesSubtotal',
            'salesDiscount',
            'salesTax',

            'purchasesCount',
            'purchasesTotal',
            'purchasesSubtotal',
            'purchasesDiscount',
            'purchasesTax',

            'paymentsTotal',
            'cashTotal',
            'mobileMoneyTotal',
            'cardTotal',
            'bankTransferTotal',
            'otherPaymentsTotal',

            'purchasePaymentsTotal',
            'purchaseCashTotal',
            'purchaseMobileMoneyTotal',
            'purchaseCardTotal',
            'purchaseBankTransferTotal',
            'purchaseOtherPaymentsTotal',

            'netCashFlow',

            'orangeMoneyTotal',
            'mtnMomoTotal',

            'purchaseOrangeMoneyTotal',
            'purchaseMtnMomoTotal',

            'pendingPayments',
            'pendingSalePayments',
            'pendingPurchasePayments',

            'productsCount',
            'stockQuantity',
            'stockValuePurchase',
            'stockValueSelling',
            'lowStock',
            'outOfStock',

            'stockEntries',
            'stockExits',
            'stockAdjustments',

            'receivablesTotal',
            'payablesTotal',

            'topProducts',
            'dailySales',
            'dailyPurchases',

            'recentPayments',
            'recentPurchasePayments'
        ));
    }
}