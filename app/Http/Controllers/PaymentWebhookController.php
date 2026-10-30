<?php

namespace App\Http\Controllers;

use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentWebhookController extends Controller
{
    public function orange(Request $request)
    {
        return $this->process(
            $request,
            'orange_money'
        );
    }


    public function mtn(Request $request)
    {
        return $this->process(
            $request,
            'mtn_momo'
        );
    }


    private function process(
        Request $request,
        string $provider
    ) {

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Le format exact de la notification sera adapté
        | aux spécifications officielles de l'opérateur.
        |
        */

        $reference = $request->input(
            'transaction_reference'
        );


        if (!$reference) {

            return response()->json([
                'success' => false,
                'message' => 'Référence absente.',
            ], 400);
        }


        $payment = SalePayment::where(
            'provider',
            $provider
        )
        ->where(
            'transaction_reference',
            $reference
        )
        ->first();


        if (!$payment) {

            return response()->json([
                'success' => false,
                'message' => 'Paiement introuvable.',
            ], 404);
        }


        DB::transaction(function () use (
            $payment,
            $request
        ) {

            /*
            |--------------------------------------------------------------------------
            | Idempotence
            |--------------------------------------------------------------------------
            |
            | Si l'opérateur envoie deux fois la même notification,
            | nous ne devons pas modifier deux fois le paiement.
            |
            */

            if ($payment->status !== 'pending') {
                return;
            }


            $operatorStatus = $request->input(
                'status'
            );


            if ($operatorStatus === 'success') {

                $payment->update([
                    'status' => 'completed',
                ]);

            } elseif ($operatorStatus === 'failed') {

                $payment->update([
                    'status' => 'cancelled',
                ]);
            }

        });


        return response()->json([
            'success' => true,
        ]);
    }
}