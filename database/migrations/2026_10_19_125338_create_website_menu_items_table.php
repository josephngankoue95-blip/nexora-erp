<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('website_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('label', 100);
            $table->string('type', 20); // page, external
            $table->foreignId('website_page_id')->nullable()->constrained('website_pages')->nullOnDelete();
            $table->string('url', 2048)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['company_id', 'is_visible', 'sort_order']);
        });
    }
    public function down(): void { Schema::dropIfExists('website_menu_items'); }
};
