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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('warranty_id')->nullable()->constrained('warranties')->nullOnDelete();
            $table->string('model')->nullable();
            $table->string('product_code')->nullable()->unique();
            $table->unsignedBigInteger('price_rial')->default(0);
            $table->unsignedBigInteger('discount_rial')->default(0);
            $table->integer('quantity')->default(0);
            $table->unsignedInteger('weight_gram')->nullable();
            $table->unsignedSmallInteger('length_cm')->nullable();
            $table->unsignedSmallInteger('width_cm')->nullable();
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->string('image')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('meta_description')->nullable();
            $table->json('keywords')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_special')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
