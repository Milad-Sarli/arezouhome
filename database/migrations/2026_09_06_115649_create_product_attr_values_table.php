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
        Schema::create('product_attr_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attr_id')->constrained('product_attrs')->cascadeOnDelete();
            $table->string('value');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attr_values');
    }
};
