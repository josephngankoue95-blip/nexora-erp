<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('school_class_subjects', function (Blueprint $table) {
            $table->unsignedTinyInteger('group_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('school_class_subjects', function (Blueprint $table) {
            $table->dropColumn('group_number');
        });
    }
};
