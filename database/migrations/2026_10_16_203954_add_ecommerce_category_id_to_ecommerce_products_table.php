
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_products', function (Blueprint $table) {

            $table->foreignId('ecommerce_category_id')
                ->nullable()
                ->after('product_id')
                ->constrained('ecommerce_categories')
                ->restrictOnDelete();

            $table->index([
                'company_id',
                'ecommerce_category_id'
            ], 'ec_products_company_category_idx');

        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_products', function (Blueprint $table) {

            $table->dropIndex('ec_products_company_category_idx');

            $table->dropConstrainedForeignId(
                'ecommerce_category_id'
            );

        });
    }
};
