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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->nullOnDelete();
            $table->foreignId('discount_code_id')->nullable()->constrained('discount_codes')->nullOnDelete();
            $table->foreignId('installment_plan_id')->nullable()->constrained('installment_plans')->nullOnDelete();
            $table->string('ref_code')->unique();
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'canceled', 'returned'])->default('pending');
            $table->unsignedBigInteger('post_price_rial')->default(0);
            $table->unsignedBigInteger('discount_price_rial')->default(0);
            $table->unsignedBigInteger('final_price_rial')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
