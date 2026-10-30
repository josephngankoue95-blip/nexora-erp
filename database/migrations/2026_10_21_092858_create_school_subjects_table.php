
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_subjects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->constrained('school_sections')
                ->cascadeOnDelete();

            $table->foreignId('subject_group_id')
                ->nullable()
                ->constrained('school_subject_groups')
                ->nullOnDelete();

            $table->string('name',100);
            $table->string('code', 30)->nullable();

            $table->decimal('coefficient', 5, 2)
                ->default(1);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['company_id', 'section_id', 'name'], 'ss_company_section_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_subjects');
    }
};
