<?php

namespace App\Services;

use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Active ou prolonge un abonnement après un paiement terminé.
     */
    public function activateFromPayment(Subscription $subscription): Subscription
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Déterminer la date de départ
        |--------------------------------------------------------------------------
        |
        | Si l'abonnement est encore actif aujourd'hui et possède
        | une date de fin aujourd'hui ou dans le futur, on prolonge
        | à partir de sa date de fin.
        |
        | Sinon, on repart d'aujourd'hui.
        |
        */

        if (
            $subscription->status === 'active'
            && $subscription->ends_at
            && $subscription->ends_at->startOfDay()->greaterThanOrEqualTo($today)
        ) {
            $startDate = $subscription->ends_at->copy();
        } else {
            $startDate = $today->copy();
        }

        /*
        |--------------------------------------------------------------------------
        | Calcul de la nouvelle date de fin
        |--------------------------------------------------------------------------
        */

        $endDate = $startDate->copy()
            ->addDays((int) $subscription->duration_days);

        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */

        $subscription->update([
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'status' => 'active',
        ]);

        return $subscription->fresh();
    }
}