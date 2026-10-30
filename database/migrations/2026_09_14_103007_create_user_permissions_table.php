<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | UTILISATEUR
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | PERMISSION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | AUTORISATION
            |--------------------------------------------------------------------------
            */

            $table->boolean('allowed')
                ->default(true);


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | UNICITÉ
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['user_id', 'permission_id'],
                'user_permission_unique'
            );
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};