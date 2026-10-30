<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_consultations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained('medical_patients')
                ->restrictOnDelete();

            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained('medical_appointments')
                ->nullOnDelete();

            $table->foreignId('practitioner_id')
                ->nullable()
                ->constrained('medical_practitioners')
                ->nullOnDelete();

            $table->dateTime('consultation_date');

            $table->string('reason',100)->nullable();

            $table->text('symptoms')->nullable();
            $table->text('clinical_observations')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();

            $table->enum('status', [
                'draft',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('draft');

            $table->timestamps();

            $table->index(['company_id', 'consultation_date']);
            $table->index(['company_id', 'patient_id']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_consultations');
    }
};