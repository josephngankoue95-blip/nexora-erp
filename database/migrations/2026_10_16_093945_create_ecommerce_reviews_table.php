
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_reviews', function (Blueprint $table) {
            $table->id();

            // Entreprise propriétaire
            $table->unsignedBigInteger('company_id');
            $table->index('company_id', 'ecom_reviews_company_idx');

            // Produit concerné, facultatif
            $table->unsignedBigInteger('product_id')->nullable();
            $table->index('product_id', 'ecom_reviews_product_idx');

            $table->string('customer_name', 100);
            $table->string('customer_email', 100)->nullable();

            // Note de 1 à 5
            $table->unsignedTinyInteger('rating');

            $table->string('title', 100)->nullable();
            $table->text('comment');

            // Statut de l'avis
            $table->string('status', 20)->default('pending');
            $table->index('status', 'ecom_reviews_status_idx');

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_reviews');
    }
};
