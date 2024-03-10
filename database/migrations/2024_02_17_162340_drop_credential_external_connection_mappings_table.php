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
        Schema::dropIfExists('credential_external_connection_mappings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
