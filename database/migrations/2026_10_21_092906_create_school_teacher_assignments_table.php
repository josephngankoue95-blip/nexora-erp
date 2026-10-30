
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_teacher_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('school_teachers')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('school_subjects')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('school_classes')
                ->cascadeOnDelete();

            $table->foreignId('school_year_id')
                ->constrained('school_years')
                ->cascadeOnDelete();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['company_id', 'teacher_id', 'subject_id', 'class_id', 'school_year_id'],
                'school_teacher_assignment_unique'
            );

            $table->index(['company_id', 'school_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_teacher_assignments');
    }
};
