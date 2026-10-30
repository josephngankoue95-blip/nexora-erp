<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('school_class_subjects', function(Blueprint $table) {
   $table->id();
   $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
   $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
   $table->foreignId('school_subject_id')->constrained('school_subjects')->restrictOnDelete();
   $table->foreignId('school_subject_group_id')->nullable()->constrained('school_subject_groups')->restrictOnDelete();
   $table->decimal('coefficient',5,2)->default(1);
   $table->unsignedSmallInteger('sort_order')->default(0);
   $table->boolean('is_active')->default(true);
   $table->timestamps();
   $table->unique(['school_class_id','school_subject_id'],'school_class_subject_unique');
   $table->index(['company_id','school_class_id']);
  });
 }
 public function down(): void { Schema::dropIfExists('school_class_subjects'); }
};
