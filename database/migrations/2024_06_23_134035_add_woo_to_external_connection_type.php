<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The connection name for the migration.
     *
     * @var string
     */
    protected $connection = 'ticketsender';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `external_connections` MODIFY `connection_type` ENUM('ecwid', 'bokun', 'rayna', 'woo') NOT NULL");
        // Schema::table('external_connections', function (Blueprint $table) {
        //      $table->enum('connection_type', ['ecwid', 'bokun', 'rayna', 'woo'])->nullable()->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('external_connections', function (Blueprint $table) {
        //     $table->enum('connection_type', ['ecwid', 'bokun', 'rayna'])->nullable()->change();
        // });
    }
};
