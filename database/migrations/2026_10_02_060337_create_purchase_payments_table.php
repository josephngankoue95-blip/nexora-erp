<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_payments', function (Blueprint $table) {

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
            | ACHAT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('purchase_id')
                ->constrained('purchases')
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
            | MONTANT
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 15, 2);

            /*
            |--------------------------------------------------------------------------
            | MÉTHODE DE PAIEMENT
            |--------------------------------------------------------------------------
            */

            $table->enum('method', [
                'cash',
                'mobile_money',
                'card',
                'bank_transfer',
                'other',
            ]);

            /*
            |--------------------------------------------------------------------------
            | FOURNISSEUR DE PAIEMENT
            |--------------------------------------------------------------------------
            */

            $table->string('provider', 50)
                ->nullable();

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
                ['company_id', 'purchase_id'],
                'payments_company_purchase_index'
            );

            $table->index(
                ['company_id', 'method'],
                'payments_company_purchase_method_index'
            );

            $table->index(
                ['company_id', 'status'],
                'payments_company_purchase_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};