<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bahan Bangunan',
            'Peralatan Tukang',
            'Material Kayu',
            'Baja',
            'Pelapis',
            'Jendela',
            'Perlengkapan Listrik',
            'Saniter',
            'Material Atap',
            'Cat',
            'Pintu',
            'Pipa',
            'Dapur',
            'Keramik',
            'Perlengkapan Kamar Mandi',
            'Alat Pelindung Diri (APD)'
        ];
        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category)
            ]);
        }
    }
}
