<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);

            // 191 évite les problèmes d'index avec certaines versions
            // MySQL / MariaDB utilisant utf8mb4.
            $table->string('slug', 191)->unique();

            $table->text('description')->nullable();

            $table->decimal('price', 15, 2)->default(0);

            $table->unsignedInteger('duration_days')->default(30);

            // NULL = illimité
            $table->unsignedInteger('max_users')->nullable();
            $table->unsignedInteger('max_products')->nullable();

            $table->json('features')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};