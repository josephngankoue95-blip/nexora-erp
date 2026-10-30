<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('school_user_permission_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('permission', 100);
            $table->boolean('allowed');
            $table->timestamps();
            $table->unique(['company_id','user_id','permission'], 'school_permission_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('school_user_permission_overrides'); }
};
