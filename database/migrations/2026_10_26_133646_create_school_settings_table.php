
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->unique()
                ->constrained('companies')
                ->cascadeOnDelete();

            // Identité
            $table->string('school_name');
            $table->string('school_code')->nullable();
            $table->string('school_type')->nullable();
            $table->string('authorization_number')->nullable();
            $table->string('motto')->nullable();

            // Images
            $table->string('logo',100)->nullable();
            $table->string('stamp',100)->nullable();

            // Coordonnées
            $table->string('country',100)->default('Cameroun');
            $table->string('region',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('address',100)->nullable();
            $table->string('phone',100)->nullable();
            $table->string('email',100)->nullable();
            $table->string('website',100)->nullable();

            // Responsables
            $table->string('principal_name',100)->nullable();
            $table->string('director_name',100)->nullable();

            // Préférences pédagogiques
            $table->string('academic_system',100)->default('both');
            $table->unsignedTinyInteger('grading_scale')->default(20);

            // Documents
            $table->text('bulletin_footer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
