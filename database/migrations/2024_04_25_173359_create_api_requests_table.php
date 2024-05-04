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

        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('loggable'); // Polymorphic relationship to associate with multiple entities
            $table->unsignedBigInteger('external_connection_id')->nullable();
            $table->string('user')->nullable(); // store username
            $table->string('method');
            $table->string('path');
            $table->longText('request_body')->nullable();
            $table->longText('response_body')->nullable();
            $table->number('status_code');
            $table->ipAddress('ip_address');
            $table->timestamp('created_at')->nullable();

            $table->foreign('external_connection_id')->references('id')->on('external_connections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
