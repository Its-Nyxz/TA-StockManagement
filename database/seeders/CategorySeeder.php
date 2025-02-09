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
            'Semen',
            'Cat',
            'Paku',
            'Sekrup',
            'Baut',
            'Bor',
            'Mortar',
            'Baja',
            'Pelapis',
            'Jendela',
            'Kuas',
            'Pintu',
            'Pipa',
            'Keramik',
            'Material Kayu',
            'Material Atap',
            'Peralatan Tukang',
            'Perlengkapan Listrik',
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
