<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderPayment;
use App\Models\EcommerceOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EcommerceReportExport;

class EcommerceReportExportController extends Controller
{
    private function getReportData(Request $request): array
    {
        $companyId = auth()->user()?->company_id;

        abort_unless($companyId, 403);

        $period = $request->query('period', 'month');

        if (!in_array($period, [
            'today', 'week', 'month', 'year'
        ], true)) {
            $period = 'month';
        }

        $now = Carbon::now();

        $startDate = match ($period) {
            'today' => $now->copy()->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'year' => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        $endDate = $now->copy()->endOfDay();

        $orders = EcommerceOrder::query()
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->get();

        $validOrders = $orders->where('status', '!=', 'cancelled');

        $totalOrders = $orders->count();
        $totalRevenue = (float) $validOrders->sum('total');

        $totalPaid = (float) EcommerceOrderPayment::query()
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->whereHas('order', function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->where('status', '!=', 'cancelled');
            })
            ->sum('amount');

        $paidOnPeriodOrders = (float) EcommerceOrderPayment::query()
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereIn('ecommerce_order_id', $validOrders->pluck('id'))
            ->sum('amount');

        $remainingAmount = max(
            0,
            $totalRevenue - $paidOnPeriodOrders
        );

        $topProducts = EcommerceOrderItem::query()
            ->where('company_id', $companyId)
            ->whereHas('order', function ($query) use (
                $companyId,
                $startDate,
                $endDate
            ) {
                $query->where('company_id', $companyId)
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [
                        $startDate,
                        $endDate
                    ]);
            })
            ->select(
                'product_name',
                'product_reference',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy('product_name', 'product_reference')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return compact(
            'period',
            'startDate',
            'endDate',
            'orders',
            'totalOrders',
            'totalRevenue',
            'totalPaid',
            'remainingAmount',
            'topProducts'
        );
    }

    public function pdf(Request $request)
    {
        $data = $this->getReportData($request);

        return Pdf::loadView(
            'company.ecommerce.reports.pdf',
            $data
        )
        ->setPaper('a4', 'landscape')
        ->download('rapport-ecommerce-' . $data['period'] . '.pdf');
    }

    public function excel(Request $request)
    {
        $data = $this->getReportData($request);

        return Excel::download(
            new EcommerceReportExport($data),
            'rapport-ecommerce-' . $data['period'] . '.xlsx'
        );
    }
}
