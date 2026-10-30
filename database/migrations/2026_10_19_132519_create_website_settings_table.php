<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->string('logo_path',100)->nullable();
            $table->string('hero_path',100)->nullable();
            $table->string('primary_color', 7)->default('#2563eb');
            $table->string('secondary_color', 7)->default('#172033');
            $table->string('hero_title',100)->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('footer_text',100)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('website_settings'); }
};
