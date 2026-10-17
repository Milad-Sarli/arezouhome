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
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slider_id')->constrained('sliders')->cascadeOnDelete();
            $table->string('image');
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->string('button_title')->nullable();
            $table->string('button_link')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
