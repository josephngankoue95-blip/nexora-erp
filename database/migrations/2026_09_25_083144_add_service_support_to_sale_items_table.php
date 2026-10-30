<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {

            $table->string('type', 20)
                ->default('product')
                ->after('sale_id');

            $table->foreignId('service_id')
                ->nullable()
                ->after('product_id')
                ->constrained('services')
                ->nullOnDelete();

        });

        Schema::table('sale_items', function (Blueprint $table) {

            $table->foreignId('product_id')
                ->nullable()
                ->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {

            $table->index(
                ['sale_id', 'type'],
                'sale_items_sale_type_index'
            );

            $table->index(
                ['type', 'product_id'],
                'sale_items_type_product_index'
            );

            $table->index(
                ['type', 'service_id'],
                'sale_items_type_service_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {

            $table->dropIndex(
                'sale_items_sale_type_index'
            );

            $table->dropIndex(
                'sale_items_type_product_index'
            );

            $table->dropIndex(
                'sale_items_type_service_index'
            );

            $table->dropForeign([
                'service_id'
            ]);

            $table->dropColumn([
                'type',
                'service_id',
            ]);
        });

        Schema::table('sale_items', function (Blueprint $table) {

            $table->foreignId('product_id')
                ->nullable(false)
                ->change();
        });
    }
};