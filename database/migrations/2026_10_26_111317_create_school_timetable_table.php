<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_timetable_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index();
            $table->string('name', 120);
            $table->unsignedInteger('capacity')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'name']);
            $table->unique(['company_id', 'id']);
        });

        Schema::create('school_timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index();
            $table->foreignId('school_year_id')->index();
            $table->foreignId('school_class_id')->index();
            $table->foreignId('school_class_subject_id')->index();
            $table->unsignedBigInteger('teacher_id')->nullable()->index();
            $table->foreignId('room_id')->nullable()->index();
            $table->unsignedTinyInteger('weekday'); // 1=lundi ... 6=samedi
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('note', 255)->nullable();
            $table->timestamps();
            $table->index(['company_id', 'school_year_id', 'weekday', 'starts_at', 'ends_at'], 'stt_schedule_idx');
            $table->foreign(['company_id', 'room_id'])->references(['company_id', 'id'])->on('school_timetable_rooms')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_timetable_slots');
        Schema::dropIfExists('school_timetable_rooms');
    }
};
