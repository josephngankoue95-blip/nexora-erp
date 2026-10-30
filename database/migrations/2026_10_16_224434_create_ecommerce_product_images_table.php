
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_product_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('ecommerce_product_id')
                ->constrained('ecommerce_products')
                ->cascadeOnDelete();

            $table->string('image_path', 100);

            $table->string('alt_text', 100)->nullable();

            $table->boolean('is_primary')->default(false);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index([
                'company_id',
                'ecommerce_product_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_product_images');
    }
};
