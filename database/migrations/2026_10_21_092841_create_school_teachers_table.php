
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_teachers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('matricule', 50);
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('email',100)->nullable();
            $table->string('phone', 30)->nullable();

            $table->string('speciality',100)->nullable();
            $table->string('qualification',100)->nullable();
            $table->date('recruitment_date')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'suspended'
            ])->default('active');

            $table->timestamps();

            $table->unique(['company_id', 'matricule']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_teachers');
    }
};
