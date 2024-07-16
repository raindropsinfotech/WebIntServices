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
        Schema::create('external_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_product_id');
            $table->string('name');
            $table->text('value');
            $table->timestamps();
            $table->foreign('external_product_id')->references('id')->on('external_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_product_attributes');
    }
};
