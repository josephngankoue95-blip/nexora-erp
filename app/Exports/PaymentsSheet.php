<?php

namespace App\Exports;

use App\Models\PurchasePayment;
use App\Models\SalePayment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PaymentsSheet implements
    FromCollection,
    WithHeadings,
    WithTitle,
    ShouldAutoSize
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

    public function title(): string
    {
        return 'Paiements';
    }

    public function collection(): Collection
    {
        $sales = SalePayment::with(['sale', 'user'])
            ->where('company_id', $this->companyId)
            ->whereBetween(
                'created_at',
                [$this->startDate, $this->endDate]
            )
            ->get()
            ->map(function ($payment) {
                return [
                    'Type' => 'Encaissement client',

                    'Référence' =>
                        $payment->sale?->reference ?? '—',

                    'Date' => $payment->created_at
                        ? $payment->created_at->format('d/m/Y H:i')
                        : '—',

                    'Méthode' =>
                        $this->paymentMethod($payment->method),

                    'Fournisseur / Client' =>
                        $payment->sale?->client?->name
                        ?? 'Client comptoir',

                    'Montant' =>
                        (float) ($payment->amount ?? 0),

                    'Statut' =>
                        $this->paymentStatus($payment->status),

                    'Utilisateur' =>
                        $payment->user?->name ?? '—',
                ];
            });

        $purchases = PurchasePayment::with(['purchase', 'user'])
            ->where('company_id', $this->companyId)
            ->whereBetween(
                'created_at',
                [$this->startDate, $this->endDate]
            )
            ->get()
            ->map(function ($payment) {
                return [
                    'Type' => 'Règlement fournisseur',

                    'Référence' =>
                        $payment->purchase?->reference ?? '—',

                    'Date' => $payment->created_at
                        ? $payment->created_at->format('d/m/Y H:i')
                        : '—',

                    'Méthode' =>
                        $this->paymentMethod($payment->method),

                    'Fournisseur / Client' =>
                        $payment->purchase?->supplier?->name
                        ?? 'Fournisseur',

                    'Montant' =>
                        (float) ($payment->amount ?? 0),

                    'Statut' =>
                        $this->paymentStatus($payment->status),

                    'Utilisateur' =>
                        $payment->user?->name ?? '—',
                ];
            });

        return $sales
            ->concat($purchases)
            ->sortByDesc('Date')
            ->values();
    }

    public function headings(): array
    {
        return [
            'Type',
            'Référence',
            'Date',
            'Méthode',
            'Fournisseur / Client',
            'Montant',
            'Statut',
            'Utilisateur',
        ];
    }

    protected function paymentMethod(?string $method): string
    {
        return match ($method) {
            'cash' => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'card' => 'Carte bancaire',
            'bank_transfer' => 'Virement bancaire',
            'other' => 'Autre',
            default => $method ?: '—',
        };
    }

    protected function paymentStatus(?string $status): string
    {
        return match ($status) {
            'completed' => 'Terminé',
            'pending' => 'En attente',
            'cancelled' => 'Annulé',
            default => $status ?: '—',
        };
    }
}