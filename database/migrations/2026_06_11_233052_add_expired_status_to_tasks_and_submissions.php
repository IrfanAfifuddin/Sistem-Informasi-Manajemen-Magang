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
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM('pending', 'submitted', 'graded', 'expired') NOT NULL DEFAULT 'submitted'");
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'submitted', 'graded', 'expired') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM('pending', 'submitted', 'graded') NOT NULL DEFAULT 'submitted'");
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'submitted', 'graded') NOT NULL DEFAULT 'pending'");
        }
    }
};
