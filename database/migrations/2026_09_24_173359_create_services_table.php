<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            
            $table->string('reference', 100)
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->string('category', 100)
                ->nullable();

            $table->decimal('price', 15, 2)
                ->default(0);

            $table->decimal('tax', 5, 2)
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'company_id',
                'is_active'
            ]);

            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};