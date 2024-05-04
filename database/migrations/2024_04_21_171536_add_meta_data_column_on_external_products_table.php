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
        Schema::table('external_products', function (Blueprint $table) {
            if (!Schema::hasColumn('external_products', 'additional_data')) {
                $table->json('additional_data')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_products', function (Blueprint $table) {
            if (Schema::hasColumn('external_products', 'additional_data')) {
                $table->dropColumn('additional_data');
            }
        });
    }
};
