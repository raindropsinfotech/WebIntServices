<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The connection name for the migration.
     *
     * @var string
     */
    protected $connection = 'ticketsender';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_connections', function (Blueprint $table) {
            $table->id();
            $table->enum('connection_type', ['ecwid', 'bokun', 'rayna'])->nullable();
            $table->string('name');
            $table->string('external_id', 30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_connections');
    }
};
