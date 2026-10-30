<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {

            $table->id();

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
            | PRODUIT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | UTILISATEUR
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | TYPE DE MOUVEMENT
            |--------------------------------------------------------------------------
            |
            | entry       = entrée
            | exit        = sortie
            | adjustment  = ajustement
            |
            */

            $table->enum('type', [
                'entry',
                'exit',
                'adjustment',
            ]);


            /*
            |--------------------------------------------------------------------------
            | QUANTITÉ
            |--------------------------------------------------------------------------
            */

            $table->integer('quantity');


            /*
            |--------------------------------------------------------------------------
            | STOCK AVANT / APRÈS
            |--------------------------------------------------------------------------
            */

            $table->integer('stock_before')->default(0);

            $table->integer('stock_after')->default(0);


            /*
            |--------------------------------------------------------------------------
            | MOTIF
            |--------------------------------------------------------------------------
            */

            $table->string('reason')->nullable();


            /*
            |--------------------------------------------------------------------------
            | NOTE
            |--------------------------------------------------------------------------
            */

            $table->text('note')->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['company_id', 'product_id'],
                'stock_company_product_index'
            );

            $table->index(
                ['company_id', 'type'],
                'stock_company_type_index'
            );

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};