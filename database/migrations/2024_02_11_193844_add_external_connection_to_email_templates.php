<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'ticketsender';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('EmailTemplates', function (Blueprint $table) {
            $table->unsignedBigInteger('external_connection_id')->nullable();

            $table->foreign('external_connection_id')->references('id')->on('external_connections')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('EmailTemplates', function (Blueprint $table) {
            $this->$table->dropForeign('external_connection_id');
            $this->$table->dropColumn('external_connection_id');
        });
    }
};
