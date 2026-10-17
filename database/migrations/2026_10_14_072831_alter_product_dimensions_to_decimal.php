<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Real-world dimensions need decimals (e.g. 59.5 cm width).
     * Raw statements: doctrine/dbal is not installed, so ->change() is unavailable.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return; // SQLite is dynamically typed; tests don't need the alteration.
        }
        DB::statement('ALTER TABLE `products` MODIFY `length_cm` DECIMAL(8,1) UNSIGNED NULL');
        DB::statement('ALTER TABLE `products` MODIFY `width_cm` DECIMAL(8,1) UNSIGNED NULL');
        DB::statement('ALTER TABLE `products` MODIFY `height_cm` DECIMAL(8,1) UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('ALTER TABLE `products` MODIFY `length_cm` SMALLINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `products` MODIFY `width_cm` SMALLINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `products` MODIFY `height_cm` SMALLINT UNSIGNED NULL');
    }
};
