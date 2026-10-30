<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcommercePaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = EcommerceOrderPayment::query()
            ->where('company_id', $companyId)
            ->with([
                'order',
            ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'reference',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'transaction_id',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas('order', function ($orderQuery) use ($search) {

                    $orderQuery
                        ->where(
                            'reference',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'customer_name',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'customer_phone',
                            'like',
                            "%{$search}%"
                        );
                });
            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE STATUT
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('status') &&
            in_array(
                $request->status,
                [
                    'pending',
                    'completed',
                    'failed',
                    'refunded',
                ],
                true
            )
        ) {
            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE MÉTHODE
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('method') &&
            in_array(
                $request->method,
                [
                    'cash',
                    'mobile_money',
                    'card',
                    'bank_transfer',
                    'other',
                ],
                true
            )
        ) {
            $query->where(
                'method',
                $request->method
            );
        }


        $payments = $query
            ->paginate(20)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        $base = EcommerceOrderPayment::query()
            ->where('company_id', $companyId);

        $stats = [

            'total' => (clone $base)->count(),

            'completed' => (clone $base)
                ->where('status', 'completed')
                ->count(),

            'pending' => (clone $base)
                ->where('status', 'pending')
                ->count(),

            'failed' => (clone $base)
                ->where('status', 'failed')
                ->count(),

            'amount' => (clone $base)
                ->where('status', 'completed')
                ->sum('amount'),
        ];


        return view(
            'company.ecommerce.payments.index',
            compact(
                'payments',
                'stats'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $companyId = auth()->user()->company_id;

        $orders = EcommerceOrder::query()
            ->where('company_id', $companyId)
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->get();

        return view(
            'company.ecommerce.payments.create',
            compact('orders')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
    Request $request,
    EcommerceOrder $order
) {
    $this->authorizeCompany($order);

    $remaining = $order->remainingAmount();

    $validated = $request->validate([

        'amount' => [
            'required',
            'numeric',
            'min:0.01',
            'max:' . $remaining,
        ],

        'method' => [
            'required',
            'in:cash,mobile_money,card,bank_transfer,other',
        ],

        'provider' => [
            'nullable',
            'string',
            'max:100',
        ],

        'transaction_id' => [
            'nullable',
            'string',
            'max:150',
        ],

        'status' => [
            'required',
            'in:pending,completed,failed',
        ],

        'note' => [
            'nullable',
            'string',
            'max:2000',
        ],
    ]);

    DB::transaction(function () use (
        $validated,
        $order
    ) {

        EcommerceOrderPayment::create([

            'company_id' =>
                $order->company_id,

            'ecommerce_order_id' =>
                $order->id,

            'reference' =>
                $this->generateReference(
                    $order->company_id
                ),

            'amount' =>
                $validated['amount'],

            'method' =>
                $validated['method'],

            'provider' =>
                $validated['provider'] ?? null,

            'transaction_id' =>
                $validated['transaction_id'] ?? null,

            'status' =>
                $validated['status'],

            'note' =>
                $validated['note'] ?? null,

            'paid_at' =>
                $validated['status'] === 'completed'
                    ? now()
                    : null,
        ]);


        $paid = EcommerceOrderPayment::query()
            ->where(
                'ecommerce_order_id',
                $order->id
            )
            ->where(
                'status',
                'completed'
            )
            ->sum('amount');


        if ($paid >= $order->total) {

            $order->update([
                'payment_status' => 'paid',
            ]);

        } elseif ($paid > 0) {

            $order->update([
                'payment_status' => 'partially_paid',
            ]);

        } else {

            $order->update([
                'payment_status' => 'pending',
            ]);
        }
    });


    return redirect()
        ->route(
            'company.ecommerce.orders.show',
            $order
        )
        ->with(
            'success',
            'Paiement enregistré avec succès.'
        );
}

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        EcommerceOrderPayment $payment
    ) {
        $this->authorizePayment($payment);

        $payment->load('order');

        return view(
            'company.ecommerce.payments.show',
            compact('payment')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY / ANNULATION
    |--------------------------------------------------------------------------
    */

    public function destroy(
        EcommerceOrderPayment $payment
    ) {
        $this->authorizePayment($payment);

        if ($payment->status === 'refunded') {

            return back()->with(
                'error',
                'Ce paiement est déjà remboursé.'
            );
        }

        $order = $payment->order;

        DB::transaction(function () use (
            $payment,
            $order
        ) {

            $payment->update([
                'status' => 'refunded',
            ]);


            $paid = EcommerceOrderPayment::query()
                ->where(
                    'ecommerce_order_id',
                    $order->id
                )
                ->where(
                    'status',
                    'completed'
                )
                ->sum('amount');


            if ($paid >= $order->total) {

                $order->update([
                    'payment_status' => 'paid',
                ]);

            } elseif ($paid > 0) {

                $order->update([
                    'payment_status' => 'partially_paid',
                ]);

            } else {

                $order->update([
                    'payment_status' => 'pending',
                ]);
            }
        });


        return back()->with(
            'success',
            'Le paiement a été marqué comme remboursé.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REFERENCE
    |--------------------------------------------------------------------------
    */

    private function generateReference(
        int $companyId
    ): string {

        do {

            $reference =
                'PAY-' .
                now()->format('Ym') .
                '-' .
                strtoupper(
                    substr(
                        uniqid(),
                        -6
                    )
                );

        } while (
            EcommerceOrderPayment::query()
                ->where(
                    'company_id',
                    $companyId
                )
                ->where(
                    'reference',
                    $reference
                )
                ->exists()
        );


        return $reference;
    }


    /*
    |--------------------------------------------------------------------------
    | COMPANY SECURITY
    |--------------------------------------------------------------------------
    */

    private function authorizeCompany(
        EcommerceOrder $order
    ): void {

        if (
            $order->company_id !==
            auth()->user()->company_id
        ) {
            abort(404);
        }
    }


    private function authorizePayment(
        EcommerceOrderPayment $payment
    ): void {

        if (
            $payment->company_id !==
            auth()->user()->company_id
        ) {
            abort(404);
        }
    }
}