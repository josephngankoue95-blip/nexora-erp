<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_modules', function (Blueprint $table) {

            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('module_id')
                ->constrained('modules')
                ->cascadeOnDelete();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamp('activated_at')
                ->nullable();

            $table->timestamp('deactivated_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'company_id',
                'module_id'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_modules');
    }
};