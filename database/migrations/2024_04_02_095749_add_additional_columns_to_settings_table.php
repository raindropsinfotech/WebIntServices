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
        Schema::table('Settings', function (Blueprint $table) {
            if (!Schema::hasColumn('Settings', 'EnableNotifications')) {
                $table->boolean('EnableNotifications')->default(false);
            };

            if (!Schema::hasColumn('Settings', 'NotificationKey')) {
                $table->text('NotificationKey')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Settings', function (Blueprint $table) {
            if (Schema::hasColumn('Settings', 'EnableNotifications')) {
                $table->dropColumn('EnableNotifications');
            };

            if (Schema::hasColumn('Settings', 'NotificationKey')) {
                $table->dropColumn('NotificationKey');
            }
        });
    }
};
