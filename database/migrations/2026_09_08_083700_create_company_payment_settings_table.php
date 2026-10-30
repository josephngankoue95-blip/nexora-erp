<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_payment_settings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | ORANGE MONEY
            |--------------------------------------------------------------------------
            */

            $table->boolean('orange_enabled')
                ->default(false);

            $table->string('orange_client_id')
                ->nullable();

            $table->text('orange_client_secret')
                ->nullable();

            $table->string('orange_merchant_id')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | MTN MOMO
            |--------------------------------------------------------------------------
            */

            $table->boolean('mtn_enabled')
                ->default(false);

            $table->string('mtn_api_user')
                ->nullable();

            $table->text('mtn_api_key')
                ->nullable();

            $table->string('mtn_subscription_key')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | ENVIRONNEMENT
            |--------------------------------------------------------------------------
            */

            $table->enum('environment', [
                'sandbox',
                'production',
            ])->default('sandbox');


            $table->timestamps();

            $table->unique('company_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_payment_settings');
    }
};