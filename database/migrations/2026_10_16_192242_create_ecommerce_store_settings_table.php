
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_store_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->unique();

            $table->string('store_name', 100);
            $table->string('support_email', 100)->nullable();
            $table->string('support_phone', 100)->nullable();

            $table->text('description')->nullable();

            $table->string('currency', 10)->default('XAF');

            $table->decimal('free_shipping_threshold', 12, 2)
                ->nullable();

            $table->boolean('allow_guest_checkout')->default(true);
            $table->boolean('reviews_enabled')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_store_settings');
    }
};
