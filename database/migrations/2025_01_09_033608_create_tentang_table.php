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
        Schema::create('tentang', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable(); // Judul "Tentang"
            $table->text('deskripsi')->nullable(); // Deskripsi atau konten
            $table->string('logo')->nullable(); // URL atau path logo
            $table->string('kontak_email')->nullable(); // Email kontak
            $table->string('kontak_telepon')->nullable(); // Nomor telepon kontak
            $table->string('alamat')->nullable(); // Alamat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tentang');
    }
};
