<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_attendance_justifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('school_attendance_id');
            $table->text('reason');
            $table->string('attachment_path',100)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('submitted_by');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
            $table->unique('school_attendance_id', 'school_att_just_unique');
            $table->index(['company_id', 'status'], 'school_att_just_status_idx');
            $table->foreign('school_attendance_id')->references('id')->on('school_attendances')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('school_attendance_justifications');
    }
};
