<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_orders', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | ENTREPRISE
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('company_id');

            /*
            |--------------------------------------------------------------------------
            | CLIENT ERP
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('client_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | IDENTIFICATION COMMANDE
            |--------------------------------------------------------------------------
            */

            // 100 caractères au lieu de 255
            // afin de garantir que l'index composé reste suffisamment court.
            $table->string('reference', 100);

            $table->string('status', 50)
                ->default('pending');


            /*
            |--------------------------------------------------------------------------
            | INFORMATIONS CLIENT
            |--------------------------------------------------------------------------
            */

            $table->string('customer_name', 150);

            $table->string('customer_email', 150)
                ->nullable();

            $table->string('customer_phone', 30)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | ADRESSE DE LIVRAISON
            |--------------------------------------------------------------------------
            */

            $table->string('shipping_address', 255)
                ->nullable();

            $table->string('shipping_city', 100)
                ->nullable();

            $table->string('shipping_region', 100)
                ->nullable();

            $table->string('shipping_country', 100)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | MONTANTS
            |--------------------------------------------------------------------------
            */

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->decimal('discount', 15, 2)
                ->default(0);

            $table->decimal('shipping_amount', 15, 2)
                ->default(0);

            $table->decimal('tax', 15, 2)
                ->default(0);

            $table->decimal('total', 15, 2)
                ->default(0);


            /*
            |--------------------------------------------------------------------------
            | PAIEMENT
            |--------------------------------------------------------------------------
            */

            $table->string('payment_method', 50)
                ->nullable();

            $table->string('payment_status', 50)
                ->default('pending');


            /*
            |--------------------------------------------------------------------------
            | NOTES
            |--------------------------------------------------------------------------
            */

            $table->text('customer_note')
                ->nullable();

            $table->text('internal_note')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | DATES
            |--------------------------------------------------------------------------
            */

            $table->timestamp('confirmed_at')
                ->nullable();

            $table->timestamp('shipped_at')
                ->nullable();

            $table->timestamp('delivered_at')
                ->nullable();

            $table->timestamp('cancelled_at')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            // Index unique court et maîtrisé
            $table->unique(
                ['company_id', 'reference'],
                'ecommerce_orders_company_reference_unique'
            );

            $table->index(
                ['company_id', 'status'],
                'ecommerce_orders_company_status_index'
            );

            $table->index(
                ['company_id', 'payment_status'],
                'ecommerce_orders_company_payment_status_index'
            );

            $table->index(
                ['company_id', 'created_at'],
                'ecommerce_orders_company_created_at_index'
            );


            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEYS
            |--------------------------------------------------------------------------
            */

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_orders');
    }
};
