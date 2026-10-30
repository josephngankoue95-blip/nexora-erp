<?php

namespace App\Services\Payments;

use App\Models\CompanyPaymentSetting;
use RuntimeException;

class MtnMomoService
{
    public function __construct(
        protected CompanyPaymentSetting $settings
    ) {
    }

    public function isConfigured(): bool
    {
        return $this->settings->mtn_enabled
            && !empty($this->settings->mtn_api_user)
            && !empty($this->settings->mtn_api_key)
            && !empty($this->settings->mtn_subscription_key);
    }

    public function createPayment(
        float $amount,
        string $phone,
        string $reference
    ): array {

        if (!$this->isConfigured()) {
            throw new RuntimeException(
                'MTN Mobile Money n\'est pas correctement configuré.'
            );
        }

        /*
         * L'appel API réel sera ajouté lorsque les
         * credentials et l'environnement MTN MoMo
         * seront disponibles.
         */

        return [
            'status' => 'pending',
            'reference' => $reference,
            'amount' => $amount,
            'phone' => $phone,
        ];
    }
}