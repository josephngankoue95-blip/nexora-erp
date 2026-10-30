<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {

            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('email')->nullable();

            $table->string('phone', 30)->nullable();

            $table->string('address')->nullable();

            $table->string('city')->nullable();

            $table->string('country')->default('Cameroun');

            $table->string('tax_number')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['company_id', 'name'],
                'clients_company_name_index'
            );

            $table->index(
                ['company_id', 'phone'],
                'clients_company_phone_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};