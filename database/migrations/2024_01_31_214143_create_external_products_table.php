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
        Schema::create('external_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_connection_id');
            $table->string('external_product_id', 40);
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_products');
    }
};
