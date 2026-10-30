<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('school_students', function(Blueprint $t){
   $t->id(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
   $t->string('student_number',60); $t->string('last_name'); $t->string('first_name')->nullable();
   $t->date('date_of_birth')->nullable(); $t->string('place_of_birth')->nullable();
   $t->string('gender',10)->nullable(); $t->string('guardian_name')->nullable();
   $t->string('guardian_phone',40)->nullable(); $t->string('guardian_email')->nullable();
   $t->string('address' ,100)->nullable(); $t->timestamps();
   $t->unique(['company_id','student_number']);
  });
  Schema::create('school_enrollments', function(Blueprint $t){
   $t->id(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
   $t->foreignId('school_student_id')->constrained('school_students')->restrictOnDelete();
   $t->foreignId('school_year_id')->constrained('school_years')->restrictOnDelete();
   $t->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
   $t->string('status',20)->default('enrolled'); $t->date('enrolled_on'); $t->timestamps();
   $t->unique(['school_student_id','school_year_id']);
  });
 }
 public function down(): void { Schema::dropIfExists('school_enrollments');Schema::dropIfExists('school_students'); }
};
