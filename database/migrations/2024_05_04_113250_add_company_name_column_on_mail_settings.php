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
        Schema::table('MailSettings', function (Blueprint $table) {
            if (!Schema::hasColumn('MailSettings', 'CompanyName')) {
                $table->string('CompanyName')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('MailSettings', function (Blueprint $table) {
            if (Schema::hasColumn('MailSettings', 'CompanyName')) {
                $table->dropColumn('CompanyName');
            }
        });
    }
};
