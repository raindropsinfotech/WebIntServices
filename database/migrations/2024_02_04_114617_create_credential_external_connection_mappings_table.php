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
        Schema::create('credential_external_connection_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('credential_id');
            $table->unsignedBigInteger('external_connection_id');
            $table->timestamps();

            $table->foreign('credential_id', 'cecm_credential_foreign')->references('Id')->on('Credentials')->onDelete('cascade');
            $table->foreign('external_connection_id', 'cecm_external_connection_foreign')->references('id')->on('external_connections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credential_external_connection_mappings');
    }
};
