<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');          // Barang
            $table->unsignedBigInteger('from_unit_id');     // Satuan Asal
            $table->unsignedBigInteger('to_unit_id');       // Satuan Tujuan
            $table->float('conversion_factor');             // Faktor Konversi
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('from_unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('to_unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
    }
};
