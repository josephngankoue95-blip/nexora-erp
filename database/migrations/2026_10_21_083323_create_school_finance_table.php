<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('school_fee_structures', function(Blueprint $t) {
   $t->id(); $t->foreignId('company_id')->constrained()->restrictOnDelete();
   $t->foreignId('school_year_id')->constrained('school_years')->restrictOnDelete();
   $t->foreignId('school_level_id')->constrained('school_levels')->restrictOnDelete();
   $t->unsignedBigInteger('registration_fee')->default(0);
   $t->unsignedBigInteger('installment_1')->default(0);
   $t->unsignedBigInteger('installment_2')->default(0);
   $t->unsignedBigInteger('installment_3')->default(0);
   $t->timestamps(); $t->unique(['company_id','school_year_id','school_level_id'],'school_fee_unique');
  });
  Schema::create('school_student_fees', function(Blueprint $t) {
   $t->id(); $t->foreignId('company_id')->constrained()->restrictOnDelete();
   $t->foreignId('school_enrollment_id')->constrained('school_enrollments')->restrictOnDelete();
   $t->foreignId('school_fee_structure_id')->nullable()->constrained('school_fee_structures')->nullOnDelete();
   $t->unsignedBigInteger('registration_fee')->default(0);
   $t->unsignedBigInteger('installment_1')->default(0);
   $t->unsignedBigInteger('installment_2')->default(0);
   $t->unsignedBigInteger('installment_3')->default(0);
   $t->timestamps(); $t->unique(['company_id','school_enrollment_id'],'school_student_fee_unique');
  });
  Schema::create('school_payments', function(Blueprint $t) {
   $t->id(); $t->foreignId('company_id')->constrained()->restrictOnDelete();
   $t->foreignId('school_student_fee_id')->constrained('school_student_fees')->restrictOnDelete();
   $t->string('reference',60); $t->unsignedBigInteger('amount');
   $t->string('fee_type',24); $t->string('method',24);
   $t->date('paid_on'); $t->string('external_reference',120)->nullable();
   $t->text('notes')->nullable(); $t->timestamps();
   $t->unique(['company_id','reference']);
  });
 }
 public function down(): void {Schema::dropIfExists('school_payments');Schema::dropIfExists('school_student_fees');Schema::dropIfExists('school_fee_structures');}
};
