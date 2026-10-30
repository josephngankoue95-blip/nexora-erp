
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_promotions', function (Blueprint $table) {
            $table->id();

            // Entreprise propriétaire de la promotion
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->string('code', 100)->nullable();

            // percentage = pourcentage, fixed = montant fixe
            $table->enum('type', ['percentage', 'fixed'])
                ->default('percentage');

            $table->decimal('value', 12, 2);
            $table->decimal('minimum_order_amount', 12, 2)
                ->default(0);

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Un code ne peut être utilisé que par une promotion
            // de la même entreprise.
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_promotions');
    }
};
