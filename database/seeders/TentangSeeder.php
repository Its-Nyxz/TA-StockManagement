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
            'judul' => 'Duta Sae',
            'logo' => null,
            'deskripsi' => 'Sistem manajemen stok di Toko Bangunan Duta Sae untuk membantu pencatatan barang toko.',
            'kontak_email' => null,
            'kontak_telepon' => 7685127,
            'alamat' => 'Ngantirejo, Malangjiwan, Kec. Colomadu, Kabupaten Karanganyar, Jawa Tengah 57177',
        ]);
    }
}
