<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('website_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 180);
            $table->string('excerpt', 500)->nullable();
            $table->longText('content')->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('show_in_menu')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'slug']);
            $table->index(['company_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('website_pages');
    }
};
