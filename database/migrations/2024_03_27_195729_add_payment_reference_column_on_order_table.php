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
        Schema::table('Orders', function (Blueprint $table) {
            if (!Schema::hasColumn('Orders', 'PaymentReference')) {
                $table->string('PaymentReference')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Orders', function (Blueprint $table) {
            if (Schema::hasColumn('Orders', 'PaymentReference')) {
                $table->dropColumn('PaymentReference');
            }
        });
    }
};
