<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class EcommerceReportExport implements
    FromArray,
    WithHeadings,
    WithTitle
{
    public function __construct(
        private array $data
    ) {}

    public function headings(): array
    {
        return [
            'Référence',
            'Client',
            'Date',
            'Statut',
            'Statut paiement',
            'Sous-total',
            'Remise',
            'Livraison',
            'Taxe',
            'Total',
        ];
    }

    public function array(): array
    {
        return $this->data['orders']
            ->map(function ($order) {
                return [
                    $order->reference,
                    $order->customer_name,
                    $order->created_at?->format('d/m/Y H:i'),
                    $order->status,
                    $order->payment_status,
                    (float) $order->subtotal,
                    (float) $order->discount,
                    (float) $order->shipping_amount,
                    (float) $order->tax,
                    (float) $order->total,
                ];
            })
            ->all();
    }

    public function title(): string
    {
        return 'Commandes E-commerce';
    }
}