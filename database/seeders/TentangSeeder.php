<?php

namespace Database\Seeders;

use App\Models\Tentang;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TentangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tentang::create([
            'judul' => 'Universitas Sahid Surakarta',
            'logo' => null,
            'deskripsi' => 'Tugas akhir mahasiswa program studi informatika',
            'kontak_email' => null,
            'kontak_telepon' => null,
            'alamat' => null,
        ]);
    }
}
