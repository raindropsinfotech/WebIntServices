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
        DB::statement("ALTER TABLE notifications MODIFY COLUMN status ENUM('new', 'processed_ok', 'processed_error', 'ignored') DEFAULT 'new' ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
