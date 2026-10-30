<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
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

        $totalSales = $salesQuery->count();

        $totalRevenue = (float) $salesQuery->sum('total');


        /*
        |--------------------------------------------------------------------------
        | ACHATS
        |--------------------------------------------------------------------------
        */

        $purchasesQuery = Purchase::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalPurchases = $purchasesQuery->count();

        $totalPurchasesAmount = (float) $purchasesQuery->sum('total');


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DES VENTES
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = SalePayment::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalPaid = (float) $paymentsQuery->sum('amount');

        $cashPayments = (float) (clone $paymentsQuery)
            ->where('method', 'cash')
            ->sum('amount');

        $mobileMoneyPayments = (float) (clone $paymentsQuery)
            ->where('method', 'mobile_money')
            ->sum('amount');

        $cardPayments = (float) (clone $paymentsQuery)
            ->where('method', 'card')
            ->sum('amount');

        $bankPayments = (float) (clone $paymentsQuery)
            ->where('method', 'bank_transfer')
            ->sum('amount');

        $otherPayments = (float) (clone $paymentsQuery)
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

        $totalPurchasePaid = (float) $purchasePaymentsQuery->sum('amount');

        $purchaseCashPayments = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'cash')
            ->sum('amount');

        $purchaseMobileMoneyPayments = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'mobile_money')
            ->sum('amount');

        $purchaseCardPayments = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'card')
            ->sum('amount');

        $purchaseBankPayments = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'bank_transfer')
            ->sum('amount');

        $purchaseOtherPayments = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'other')
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | SOLDE NET
        |--------------------------------------------------------------------------
        */

        $netCashFlow = $totalPaid - $totalPurchasePaid;


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
        | CRÉANCES CLIENTS
        |--------------------------------------------------------------------------
        */

        $allCompletedSales = Sale::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['id', 'total']);

        $allSalePayments = SalePayment::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['sale_id', 'amount']);

        $paidBySale = $allSalePayments
            ->groupBy('sale_id')
            ->map(fn ($payments) => (float) $payments->sum('amount'));

        $totalReceivables = 0;

        foreach ($allCompletedSales as $sale) {

            $paid = $paidBySale->get($sale->id, 0);

            $remaining = max(
                0,
                (float) $sale->total - $paid
            );

            $totalReceivables += $remaining;
        }


        /*
        |--------------------------------------------------------------------------
        | DETTES FOURNISSEURS
        |--------------------------------------------------------------------------
        */

        $allCompletedPurchases = Purchase::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['id', 'total']);

        $allPurchasePayments = PurchasePayment::where('company_id', $companyId)
            ->where('status', 'completed')
            ->get(['purchase_id', 'amount']);

        $paidByPurchase = $allPurchasePayments
            ->groupBy('purchase_id')
            ->map(fn ($payments) => (float) $payments->sum('amount'));

        $totalPayables = 0;

        foreach ($allCompletedPurchases as $purchase) {

            $paid = $paidByPurchase->get($purchase->id, 0);

            $remaining = max(
                0,
                (float) $purchase->total - $paid
            );

            $totalPayables += $remaining;
        }


        /*
        |--------------------------------------------------------------------------
        | VENTES RÉCENTES
        |--------------------------------------------------------------------------
        */

        $recentSales = Sale::with(['client', 'user'])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(8)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ACHATS RÉCENTS
        |--------------------------------------------------------------------------
        */

        $recentPurchases = Purchase::with(['supplier', 'user'])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(8)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS RÉCENTS — VENTES
        |--------------------------------------------------------------------------
        */

        $recentPayments = SalePayment::with(['sale', 'user'])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(8)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS RÉCENTS — ACHATS
        |--------------------------------------------------------------------------
        */

        $recentPurchasePayments = PurchasePayment::with(['purchase', 'user'])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(8)
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
        | MOBILE MONEY — VENTES
        |--------------------------------------------------------------------------
        */

        $orangeMoney = (float) (clone $paymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'orange_money')
            ->sum('amount');

        $mtnMomo = (float) (clone $paymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'mtn_momo')
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | MOBILE MONEY — ACHATS
        |--------------------------------------------------------------------------
        */

        $purchaseOrangeMoney = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'orange_money')
            ->sum('amount');

        $purchaseMtnMomo = (float) (clone $purchasePaymentsQuery)
            ->where('method', 'mobile_money')
            ->where('provider', 'mtn_momo')
            ->sum('amount');


        return view('company.finance.index', compact(

            'period',
            'startDate',
            'endDate',

            'totalSales',
            'totalRevenue',

            'totalPurchases',
            'totalPurchasesAmount',

            'totalPaid',

            'cashPayments',
            'mobileMoneyPayments',
            'cardPayments',
            'bankPayments',
            'otherPayments',

            'totalPurchasePaid',

            'purchaseCashPayments',
            'purchaseMobileMoneyPayments',
            'purchaseCardPayments',
            'purchaseBankPayments',
            'purchaseOtherPayments',

            'netCashFlow',

            'pendingPayments',
            'pendingSalePayments',
            'pendingPurchasePayments',

            'totalReceivables',
            'totalPayables',

            'recentSales',
            'recentPurchases',

            'recentPayments',
            'recentPurchasePayments',

            'dailySales',
            'dailyPurchases',

            'orangeMoney',
            'mtnMomo',

            'purchaseOrangeMoney',
            'purchaseMtnMomo'
        ));
    }
}