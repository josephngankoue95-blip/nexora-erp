<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ecommerce_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            // Pas de FK sur order_id : à adapter si la table des commandes porte un autre nom.
            $table->unsignedBigInteger('order_id');
            $table->string('reference', 40)->unique();
            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 40);
            $table->string('recipient_email', 190)->nullable();
            $table->string('address_line', 255);
            $table->string('city', 120);
            $table->string('zone', 120)->nullable();
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->string('carrier_name', 150)->nullable();
            $table->string('carrier_phone', 40)->nullable();
            $table->string('status', 30)->default('pending');
            $table->date('scheduled_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_deliveries');
    }
};
