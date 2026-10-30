<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_patients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->string('patient_number', 30);

            $table->string('first_name', 100);
            $table->string('last_name', 100);

            $table->date('date_of_birth')->nullable();

            $table->enum('gender', [
                'male',
                'female',
                'other'
            ])->nullable();

            $table->string('phone', 30)->nullable();
            $table->string('email',100)->nullable();

            $table->string('address',100)->nullable();
            $table->string('city', 100)->nullable();

            $table->string('emergency_contact_name',100)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();

            $table->unique([
                'company_id',
                'patient_number'
            ]);

            $table->index([
                'company_id',
                'last_name',
                'first_name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_patients');
    }
};