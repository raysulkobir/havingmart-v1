<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('combined_orders')) {
            return;
        }

        DB::statement('ALTER TABLE `combined_orders` MODIFY `id` INT NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        if (!Schema::hasTable('combined_orders')) {
            return;
        }

        DB::statement('ALTER TABLE `combined_orders` MODIFY `id` INT NOT NULL');
    }
};
