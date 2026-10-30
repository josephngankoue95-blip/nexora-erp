<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_order_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('ecommerce_order_id')
                ->constrained('ecommerce_orders')
                ->cascadeOnDelete();

            $table->string('reference', 100);

            $table->decimal('amount', 15, 2);

            $table->string('method');

            $table->string('provider')->nullable();

            $table->string('transaction_id')->nullable();

            $table->string('status')
                ->default('pending');

            $table->text('note')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->unique([
                'company_id',
                'reference'
            ]);

            $table->index([
                'company_id',
                'ecommerce_order_id'
            ]);

            $table->index([
                'company_id',
                'status'
            ]);

            $table->index([
                'company_id',
                'method'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_order_payments');
    }

};