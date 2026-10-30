<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index();
            $table->foreignId('actor_user_id')->index();
            $table->foreignId('target_user_id')->index();
            $table->string('action', 60);
            $table->string('result', 30);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_access_logs');
    }
};
