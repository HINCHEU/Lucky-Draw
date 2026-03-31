<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('winners')) {
            DB::statement('ALTER TABLE `winners` MODIFY `code` VARCHAR(64) NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('winners')) {
            DB::statement('ALTER TABLE `winners` MODIFY `code` VARCHAR(4) NOT NULL');
        }
    }
};
