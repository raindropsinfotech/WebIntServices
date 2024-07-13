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
        DB::connection('ticketsender')->statement("
            UPDATE OrderItems
            SET OrderItemStatus =
                CASE
                    WHEN IsProcessed = 1 THEN 2
                    WHEN IsProcessed = 0 AND PostpondDelivery = 1 THEN 3
                    ELSE 1
                END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
