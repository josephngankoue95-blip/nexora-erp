<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_products', function (Blueprint $table) {

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
            | PRODUIT ERP
            |--------------------------------------------------------------------------
            */

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | PUBLICATION
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_published')
                ->default(false);

            $table->boolean('is_featured')
                ->default(false);

            $table->timestamp('published_at')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | URL / SEO
            |--------------------------------------------------------------------------
            */

            $table->string('slug', 191)
                ->nullable();

            $table->string('seo_title')
                ->nullable();

            $table->text('seo_description')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | CONTENU E-COMMERCE
            |--------------------------------------------------------------------------
            */

            $table->text('short_description')
                ->nullable();

            $table->longText('description')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | ORDRE D'AFFICHAGE
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')
                ->default(0);


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'company_id',
                'product_id',
            ]);

            $table->index([
                'company_id',
                'is_published',
            ]);

            $table->index([
                'company_id',
                'is_featured',
            ]);

            $table->index([
                'company_id',
                'slug',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ecommerce_products');
    }
};