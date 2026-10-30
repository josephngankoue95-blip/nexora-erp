<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            // Entreprise propriétaire du produit
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Catégorie
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('name');

            // Référence interne
            $table->string('sku');

            // Code-barres
            $table->string('barcode')->nullable();

            $table->text('description')->nullable();

            // Prix
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);

            // Stock actuel
            $table->integer('stock_quantity')->default(0);

            // Seuil d'alerte
            $table->integer('min_stock')->default(0);

            // Produit actif/inactif
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Un SKU est unique uniquement dans une entreprise
            $table->unique(
                ['company_id', 'sku'],
                'products_company_sku_unique'
            );

            $table->index(
                ['company_id', 'category_id'],
                'products_company_category_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};