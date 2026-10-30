
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_collection_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ecommerce_collection_id')
                ->constrained('ecommerce_collections')
                ->cascadeOnDelete();

            $table->foreignId('ecommerce_product_id')
                ->constrained('ecommerce_products')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(
                ['ecommerce_collection_id', 'ecommerce_product_id'],
                'ec_collection_product_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_collection_product');
    }
};
