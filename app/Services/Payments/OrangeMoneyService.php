<?php

namespace App\Services\Payments;

use App\Models\CompanyPaymentSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OrangeMoneyService
{
    public function __construct(
        protected CompanyPaymentSetting $settings
    ) {
    }

    /**
     * Vérifie que Orange Money est correctement configuré.
     */
    public function isConfigured(): bool
    {
        return $this->settings->orange_enabled
            && !empty($this->settings->orange_client_id)
            && !empty($this->settings->orange_client_secret);
    }

    /**
     * Obtenir un token OAuth.
     *
     * La structure exacte de la requête doit suivre
     * la documentation/API credentials fournies
     * par Orange Money Cameroun pour le compte marchand.
     */
    public function getAccessToken(): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException(
                'Orange Money n\'est pas correctement configuré.'
            );
        }

        $baseUrl = $this->settings->environment === 'sandbox'
            ? config('services.orange_money.base_url')
            : config('services.orange_money.base_url');

        /*
         * Nous ne codons volontairement pas ici
         * un endpoint OAuth inventé.
         *
         * Il sera renseigné selon les credentials
         * et la documentation officielle du compte.
         */

        throw new RuntimeException(
            'Endpoint OAuth Orange Money à configurer selon les identifiants API.'
        );
    }
}