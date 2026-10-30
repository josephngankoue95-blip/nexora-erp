<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_payments', function (Blueprint $table) {

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
            | VENTE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('sale_id')
                ->constrained('sales')
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
            | PAIEMENT
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 15, 2);

            $table->enum('method', [
                'cash',
                'mobile_money',
                'card',
                'bank_transfer',
                'other',
            ]);

            /*
            |--------------------------------------------------------------------------
            | INFORMATIONS TRANSACTION
            |--------------------------------------------------------------------------
            */

            $table->string('transaction_reference')
                ->nullable();

            $table->string('phone')
                ->nullable();

            $table->text('note')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | STATUT
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'completed',
                'pending',
                'cancelled',
            ])->default('completed');


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['company_id', 'sale_id'],
                'payments_company_sale_index'
            );

            $table->index(
                ['company_id', 'method'],
                'payments_company_method_index'
            );

            $table->index(
                ['company_id', 'status'],
                'payments_company_status_index'
            );

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};