<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('school_year_id');
            $table->unsignedBigInteger('school_class_id');
            $table->date('attendance_date');
            $table->string('session_label', 60)->default('journee');
            $table->unsignedBigInteger('recorded_by');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['company_id','school_class_id','attendance_date','session_label'], 'school_att_session_unique');
            $table->index(['company_id','school_year_id','attendance_date'], 'school_att_session_lookup');
        });
        Schema::create('school_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('school_attendance_session_id');
            $table->unsignedBigInteger('school_student_id');
            $table->enum('status', ['present','absent','late','excused']);
            $table->unsignedSmallInteger('minutes_late')->nullable();
            $table->string('reason', 500)->nullable();
            $table->unsignedBigInteger('recorded_by');
            $table->timestamps();
            $table->unique(['school_attendance_session_id','school_student_id'], 'school_att_student_unique');
            $table->index(['company_id','school_student_id'], 'school_att_student_lookup');
            $table->foreign('school_attendance_session_id','school_att_session_fk')->references('id')->on('school_attendance_sessions')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('school_attendances');
        Schema::dropIfExists('school_attendance_sessions');
    }
};
