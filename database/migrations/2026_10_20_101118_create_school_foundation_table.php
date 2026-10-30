<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->string('school_name', 180)->nullable();
            $table->string('school_code', 60)->nullable();
            $table->string('school_type', 30)->default('secondary');
            $table->string('principal_name', 180)->nullable();
            $table->string('motto', 255)->nullable();
            $table->string('logo_path', 100)->nullable();
            $table->string('stamp_path', 100)->nullable();
            $table->timestamps();
        });
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name', 30);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->unique(['company_id', 'name']);
            $table->index(['company_id', 'is_active']);
        });
        Schema::create('school_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name', 80);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'code']);
        });
        Schema::create('school_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('school_section_id')->constrained('school_sections')->restrictOnDelete();
            $table->string('name', 80);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_exam_class')->default(false);
            $table->timestamps();
            $table->unique(['company_id', 'school_section_id', 'name'], 'school_levels_company_section_name_unique');
        });
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->restrictOnDelete();
            $table->foreignId('school_level_id')->constrained('school_levels')->restrictOnDelete();
            $table->string('name', 100);
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'school_year_id', 'name'], 'school_classes_company_year_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
        Schema::dropIfExists('school_levels');
        Schema::dropIfExists('school_sections');
        Schema::dropIfExists('school_years');
        Schema::dropIfExists('school_settings');
    }
};
