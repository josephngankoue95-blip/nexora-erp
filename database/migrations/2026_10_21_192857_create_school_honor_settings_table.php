<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('school_honor_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->decimal('honor_min', 5, 2)->default(12);
            $table->decimal('encouragement_min', 5, 2)->default(14);
            $table->decimal('congratulation_min', 5, 2)->default(16);
            $table->decimal('excellence_min', 5, 2)->default(18);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('school_honor_settings'); }
};
