<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reference');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', [
                'completed',
                'cancelled',
            ])->default('completed');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['company_id', 'reference'],
                'sales_company_reference_unique'
            );

            $table->index(
                ['company_id', 'client_id'],
                'sales_company_client_index'
            );

            $table->index(
                ['company_id', 'status'],
                'sales_company_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};