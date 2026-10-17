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
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('title');
            $table->string('meta_description', 300)->nullable()->after('meta_title');
            $table->string('canonical_url')->nullable()->after('meta_description');
            $table->text('excerpt')->nullable()->after('description');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->unsignedBigInteger('views')->default(0)->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'canonical_url', 'excerpt', 'is_featured', 'views']);
        });
    }
};
