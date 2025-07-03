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
        // database/migrations/xxxx_xx_xx_create_payments_table.php

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_intent_id')->unique();
            $table->string('payment_method_id')->nullable();
            $table->integer('amount');
            $table->string('currency', 3);
            $table->string('status'); // 'succeeded', 'requires_action', 'failed', etc.
            $table->text('description')->nullable();
            $table->json('response')->nullable(); // raw Stripe response (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
