<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    protected int $companyId;
    protected $startDate;
    protected $endDate;

    public function __construct(int $companyId, $startDate, $endDate)
    {
        $this->companyId = $companyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new ReportsSummarySheet(
                $this->companyId,
                $this->startDate,
                $this->endDate
            ),

            new SalesSheet(
                $this->companyId,
                $this->startDate,
                $this->endDate
            ),

            new PurchasesSheet(
                $this->companyId,
                $this->startDate,
                $this->endDate
            ),

            new PaymentsSheet(
                $this->companyId,
                $this->startDate,
                $this->endDate
            ),
        ];
    }
}