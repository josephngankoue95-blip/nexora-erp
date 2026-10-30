<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\EcommerceOrderPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcommerceReportController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | ENTREPRISE CONNECTÉE
        |--------------------------------------------------------------------------
        */

        $companyId = auth()->user()?->company_id;

        abort_unless(
            $companyId,
            403,
            'Aucune entreprise associée à cet utilisateur.'
        );

        /*
        |--------------------------------------------------------------------------
        | FILTRE PAR PÉRIODE
        |--------------------------------------------------------------------------
        */

        $period = $request->query('period', 'month');

        if (!in_array($period, [
            'today',
            'week',
            'month',
            'year',
        ], true)) {
            $period = 'month';
        }

        $now = Carbon::now();

        $startDate = match ($period) {
            'today' => $now->copy()->startOfDay(),
            'week'  => $now->copy()->startOfWeek(),
            'year'  => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        $endDate = $now->copy()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | REQUÊTE DES COMMANDES
        |--------------------------------------------------------------------------
        */

        $ordersQuery = EcommerceOrder::query()
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [
                $startDate,
                $endDate,
            ]);

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES DES COMMANDES
        |--------------------------------------------------------------------------
        */

        $totalOrders = (clone $ordersQuery)->count();

        $pendingOrders = (clone $ordersQuery)
            ->where('status', 'pending')
            ->count();

        $confirmedOrders = (clone $ordersQuery)
            ->where('status', 'confirmed')
            ->count();

        $preparingOrders = (clone $ordersQuery)
            ->where('status', 'preparing')
            ->count();

        $shippedOrders = (clone $ordersQuery)
            ->where('status', 'shipped')
            ->count();

        $deliveredOrders = (clone $ordersQuery)
            ->where('status', 'delivered')
            ->count();

        $cancelledOrders = (clone $ordersQuery)
            ->where('status', 'cancelled')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CHIFFRE D'AFFAIRES DES COMMANDES
        |--------------------------------------------------------------------------
        |
        | Les commandes annulées sont exclues.
        |
        */

        $revenueQuery = (clone $ordersQuery)
            ->where('status', '!=', 'cancelled');

        $totalRevenue = (float) (clone $revenueQuery)
            ->sum('total');

        $totalSubtotal = (float) (clone $revenueQuery)
            ->sum('subtotal');

        $totalDiscount = (float) (clone $revenueQuery)
            ->sum('discount');

        $totalShipping = (float) (clone $revenueQuery)
            ->sum('shipping_amount');

        $totalTax = (float) (clone $revenueQuery)
            ->sum('tax');

        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS ENCAISSÉS
        |--------------------------------------------------------------------------
        |
        | Un paiement est encaissé si status = completed.
        | La période est basée sur paid_at.
        |
        */

        $paymentsQuery = EcommerceOrderPayment::query()
            ->where('company_id', $companyId)
            ->whereBetween('paid_at', [
                $startDate,
                $endDate,
            ])
            ->where('status', 'completed')
            ->whereHas('order', function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->where('status', '!=', 'cancelled');
            });

        $totalPaid = (float) (clone $paymentsQuery)
            ->sum('amount');

        $completedPayments = (clone $paymentsQuery)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RÉPARTITION DES PAIEMENTS
        |--------------------------------------------------------------------------
        */

        $paymentsByMethod = (clone $paymentsQuery)
            ->select(
                'method',
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SOLDE DES COMMANDES DE LA PÉRIODE
        |--------------------------------------------------------------------------
        |
        | Ici, nous calculons les montants déjà encaissés
        | sur les commandes créées pendant la période.
        | Cela évite de soustraire des paiements de commandes
        | anciennes au CA des nouvelles commandes.
        |
        */

        $orderIds = (clone $revenueQuery)
            ->select('id');

        $paidOnPeriodOrders = (float) EcommerceOrderPayment::query()
            ->where('company_id', $companyId)
            ->whereIn('ecommerce_order_id', $orderIds)
            ->where('status', 'completed')
            ->sum('amount');

        $remainingAmount = max(
            0,
            $totalRevenue - $paidOnPeriodOrders
        );

        /*
        |--------------------------------------------------------------------------
        | PRODUITS VENDUS
        |--------------------------------------------------------------------------
        */

        $itemsQuery = EcommerceOrderItem::query()
            ->where('company_id', $companyId)
            ->whereHas('order', function ($query) use (
                $companyId,
                $startDate,
                $endDate
            ) {
                $query
                    ->where('company_id', $companyId)
                    ->whereBetween('created_at', [
                        $startDate,
                        $endDate,
                    ])
                    ->where('status', '!=', 'cancelled');
            });

        $totalItemsSold = (float) (clone $itemsQuery)
            ->sum('quantity');

        $topProducts = (clone $itemsQuery)
            ->select(
                'product_name',
                'product_reference',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy(
                'product_name',
                'product_reference'
            )
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DERNIÈRES COMMANDES
        |--------------------------------------------------------------------------
        */

        $recentOrders = (clone $ordersQuery)
            ->latest()
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DERNIERS PAIEMENTS ENCAISSÉS
        |--------------------------------------------------------------------------
        */

        $recentPayments = (clone $paymentsQuery)
            ->with('order')
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ÉVOLUTION DES VENTES
        |--------------------------------------------------------------------------
        |
        | Regroupement quotidien pour aujourd'hui,
        | la semaine et le mois ; mensuel pour l'année.
        |
        */

        $groupFormat = $period === 'year'
            ? '%Y-%m'
            : '%Y-%m-%d';

        $salesEvolution = (clone $revenueQuery)
            ->selectRaw(
                "DATE_FORMAT(created_at, ?) as period_label",
                [$groupFormat]
            )
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(total) as total')
            ->groupBy('period_label')
            ->orderBy('period_label')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DONNÉES POUR LA VUE
        |--------------------------------------------------------------------------
        */

        return view('company.ecommerce.reports.index', compact(
            'period',
            'startDate',
            'endDate',

            'totalOrders',
            'pendingOrders',
            'confirmedOrders',
            'preparingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',

            'totalRevenue',
            'totalSubtotal',
            'totalDiscount',
            'totalShipping',
            'totalTax',

            'totalPaid',
            'completedPayments',
            'paymentsByMethod',
            'paidOnPeriodOrders',
            'remainingAmount',

            'totalItemsSold',
            'topProducts',

            'recentOrders',
            'recentPayments',
            'salesEvolution'
        ));
    }
}