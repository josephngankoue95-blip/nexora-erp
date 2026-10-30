<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('school_grading_configs',function(Blueprint $t){$t->id();$t->foreignId('company_id')->index();$t->foreignId('school_year_id')->index();$t->enum('mode',['sequences','trimesters']);$t->timestamps();$t->unique(['company_id','school_year_id']);});
  Schema::create('school_evaluation_periods',function(Blueprint $t){$t->id();$t->foreignId('company_id')->index();$t->foreignId('school_year_id')->index();$t->string('name',80);$t->enum('kind',['sequence','trimester']);$t->unsignedTinyInteger('number');$t->unsignedTinyInteger('trimester_number');$t->timestamps();$t->unique(['company_id','school_year_id','kind','number'],'period_tenant_year_kind_number');});
  Schema::create('school_evaluations',function(Blueprint $t){$t->id();$t->foreignId('company_id')->index();$t->foreignId('school_year_id')->index();$t->foreignId('school_class_id')->index();$t->foreignId('school_class_subject_id')->index();$t->foreignId('school_evaluation_period_id')->index();$t->string('title',120);$t->decimal('max_score',6,2)->default(20);$t->decimal('weight',6,2)->default(1);$t->boolean('is_locked')->default(false);$t->timestamps();});
  Schema::create('school_grades',function(Blueprint $t){$t->id();$t->foreignId('company_id')->index();$t->foreignId('school_evaluation_id')->index();$t->foreignId('school_student_id')->index();$t->decimal('score',6,2)->nullable();$t->foreignId('entered_by')->nullable()->index();$t->timestamps();$t->unique(['school_evaluation_id','school_student_id']);});
 }
 public function down(): void {Schema::dropIfExists('school_grades');Schema::dropIfExists('school_evaluations');Schema::dropIfExists('school_evaluation_periods');Schema::dropIfExists('school_grading_configs');}
};
