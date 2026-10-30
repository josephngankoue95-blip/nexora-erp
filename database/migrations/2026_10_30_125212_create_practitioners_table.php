<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_practitioners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Compte NEXORA facultatif
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('registration_number', 50)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);

            $table->string('profession', 80);
            $table->string('specialty', 150)->nullable();

            $table->string('phone', 30)->nullable();
            $table->string('email',100)->nullable();

            $table->date('hire_date')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'on_leave'
            ])->default('active');

            $table->timestamps();

            $table->unique(['company_id', 'registration_number']);
            $table->unique(['company_id', 'user_id']);
            $table->index(['company_id', 'profession', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_practitioners');
    }
};