<?php

use App\Enums\OrderItemStatus;
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
        // TO add the OrderItemStatus enum property on the OrderItems table
        Schema::table('OrderItems', function (Blueprint $table) {
            $table->unsignedTinyInteger('OrderItemStatus')->default(0)->after('IsProcessed');
        });



        // Update existing rows based on IsProcessed
        //\DB::table('OrderItems')->where('IsProcessed', true)->update(['OrderItemStatus' => 'Processed']);

        // update table on specific database 'ticketsender'
        // \DB::connection('ticketsender')
        //     ->table('OrderItems')
        //     ->where('IsProcessed', true)
        //     ->update(['OrderItemStatus' => \App\Enums\OrderItemStatus::processed()->value]);

        // This query will updatet the status to Cancelled for All order items which are set to pending for proessing.
        // \DB::connection('ticketsender')
        //     ->table('OrderItems')
        //     ->where('IsProcessed', false)
        //     ->where('PostpondDelivery', true)
        //     ->update(['OrderItemStatus' => \App\Enums\OrderItemStatus::cancelled()->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('OrderItems', function (Blueprint $table) {
            $table->dropColumn('OrderItemStatus');
        });
    }
};
