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
    Schema::create('subscriptions', function (Blueprint $table) {

        $table->id();

        $table->foreignId('company_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('name');

        $table->decimal('price', 15, 2)
            ->default(0);

        $table->unsignedInteger('duration_days');

        $table->date('starts_at');

        $table->date('ends_at');

        $table->enum('status', [
            'active',
            'expired',
            'cancelled',
        ])->default('active');

        $table->text('notes')
            ->nullable();

        $table->timestamps();

        $table->index([
            'company_id',
            'status',
        ]);

        $table->index('ends_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
