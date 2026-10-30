<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_class_councils', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('school_year_id');
            $table->unsignedBigInteger('school_class_id');
            $table->string('period_kind', 20);
            $table->unsignedTinyInteger('period_number')->default(1);
            $table->string('status', 20)->default('draft');
            $table->text('general_observation')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->timestamps();
            $table->unique(['company_id','school_year_id','school_class_id','period_kind','period_number'], 'school_council_period_unique');
            $table->index(['company_id','status'], 'scc_company_status_idx');
        });

        Schema::create('school_class_council_decisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('school_class_council_id');
            $table->unsignedBigInteger('school_student_id');
            $table->decimal('overall_average', 5, 2)->nullable();
            $table->unsignedInteger('class_rank')->nullable();
            $table->string('distinction', 32)->nullable();
            $table->string('decision', 32)->default('none');
            $table->text('appreciation')->nullable();
            $table->timestamps();
            $table->unique(['school_class_council_id','school_student_id'], 'school_council_student_unique');
            $table->index(['company_id','school_student_id'], 'sccd_company_student_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_class_council_decisions');
        Schema::dropIfExists('school_class_councils');
    }
};
