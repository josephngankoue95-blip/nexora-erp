<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {

            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            // Référence limitée à 150 caractères
            $table->string('reference', 150);

            $table->date('purchase_date');

            $table->decimal('subtotal', 15, 2)->default(0);

            $table->decimal('discount', 15, 2)->default(0);

            $table->decimal('tax', 15, 2)->default(0);

            $table->decimal('total', 15, 2)->default(0);

            $table->decimal('amount_paid', 15, 2)->default(0);

            $table->decimal('amount_due', 15, 2)->default(0);

            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid',
            ])->default('unpaid');

            $table->enum('status', [
                'completed',
                'cancelled',
            ])->default('completed');

            $table->text('notes')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * Une référence doit être unique à l'intérieur
             * d'une même entreprise.
             */
            $table->unique([
                'company_id',
                'reference',
            ]);

            $table->index([
                'company_id',
                'supplier_id',
            ]);

            $table->index([
                'company_id',
                'status',
            ]);

            $table->index([
                'company_id',
                'purchase_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
