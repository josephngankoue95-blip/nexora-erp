<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained('medical_patients')
                ->restrictOnDelete();

            $table->foreignId('practitioner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('scheduled_at');

            $table->unsignedSmallInteger('duration_minutes')
                ->default(30);

            $table->string('reason', 255)->nullable();

            $table->enum('status', [
                'scheduled',
                'confirmed',
                'completed',
                'cancelled',
                'no_show'
            ])->default('scheduled');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'scheduled_at']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_appointments');
    }
};