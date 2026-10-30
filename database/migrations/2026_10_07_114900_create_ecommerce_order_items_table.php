<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_order_items', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | COMMANDE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('ecommerce_order_id')
                ->constrained('ecommerce_orders')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | ENTREPRISE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | PRODUIT ERP
            |--------------------------------------------------------------------------
            */

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | PRODUIT E-COMMERCE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('ecommerce_product_id')
                ->nullable()
                ->constrained('ecommerce_products')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | SNAPSHOT PRODUIT
            |--------------------------------------------------------------------------
            |
            | Même si le produit est ensuite modifié dans l'ERP,
            | la commande doit conserver ce qui a réellement été vendu.
            |
            */

            $table->string('product_name');

            $table->string('product_reference')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | QUANTITÉ / PRIX
            |--------------------------------------------------------------------------
            */

            $table->decimal('quantity', 15, 3);

            $table->decimal('unit_price', 15, 2);

            $table->decimal('discount', 15, 2)
                ->default(0);

            $table->decimal('tax', 15, 2)
                ->default(0);

            $table->decimal('total', 15, 2);


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index([
                'company_id',
                'product_id',
            ]);

            $table->index([
                'company_id',
                'ecommerce_product_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_order_items');
    }
};