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
        Schema::create('external_connection_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_connection_id');
            $table->integer('shop_credential_id')->nullable();
            $table->integer('payment_provider_credential_id')->nullable();
            $table->integer('mail_setting_id')->nullable();
            $table->timestamps();

            $table->foreign('external_connection_id')->references('id')->on('external_connections')->onDelete('restrict');
            $table->foreign('shop_credential_id', 'ecp_shop_credential_foreign')->references('Id')->on('Credentials')->onDelete('restrict');
            $table->foreign('payment_provider_credential_id', 'ecp_payment_provider_credential_foreign')->references('Id')->on('Credentials')->onDelete('restrict');
            $table->foreign('mail_setting_id')->references('Id')->on('MailSettings')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_connection_mappings');
    }
};
