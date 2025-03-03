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
        Schema::create('product_external_product', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->unsignedBigInteger('external_product_id');
            $table->timestamps();

            $table->foreign('product_id')->references('Id')->on('Products')->onDelete('cascade');
            $table->foreign('external_product_id')->references('id')->on('external_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_external_product');
    }
};
