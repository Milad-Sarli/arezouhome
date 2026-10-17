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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('authority')->nullable();
            $table->string('ref_id')->nullable();
            $table->unsignedBigInteger('amount_rial')->default(0);
            $table->string('gateway')->nullable();
            $table->enum('status', ['start_payment', 'paid', 'failed', 'canceled'])->default('start_payment');
            $table->unsignedInteger('installment_count')->nullable();
            $table->unsignedBigInteger('installment_amount_rial')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
